<?php

namespace Estartar\Core;

use \Exception;
use \PDO;
use \PDOException;
use \Core\Database;

abstract class ActiveRecord
{
    private $content;
    protected $table = NULL;
    protected $idfield = NULL;
    protected $logtimestamp;

    protected $columns;    
    protected $where;  
    protected $order;    
    protected $statement;
    protected $limit;    
    protected $offset;
    public $error;
 
    public function __construct()
    {
        if (!is_bool($this->logtimestamp)) {
            $this->logtimestamp = TRUE;
        }
         if ($this->table == NULL) {
            $this->table = strtolower(get_class($this));
        }
        if ($this->idfield == NULL) {
            $this->idfield = 'id';
        }
    }
 
    public function __set($parameter, $value)
    {
        $this->content[$parameter] = $value;
    }
 
    public function __get($parameter)
    {
        return $this->content[$parameter];
    }
 
    public function __isset($parameter)
    {
        return isset($this->content[$parameter]);
    }
 
    public function __unset($parameter)
    {
        if (isset($parameter)) {
            unset($this->content[$parameter]);
            return true;
        }
        return false;
    }
 
    private function __clone()
    {
        if (isset($this->content[$this->idfield])) {
            unset($this->content[$this->idfield]);
        }
    }
 
    public function toArray()
    {
        return $this->content;
    }
 
    public function fromArray(array $array)
    {
        $this->content = $array;
    }
 
    public function toJson()
    {
        return json_encode($this->content);
    }
 
    public function fromJson(string $json)
    {
        $this->content = json_decode($json);
    }

    private function format($value)
    {
        if (is_string($value) && !empty($value)) {
            return "'" . addslashes($value) . "'";
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else if ($value !== '') {
            return $value;
        } else {
            return "NULL";
        }
    }
    
    private function convertContent()
    {
        $newContent = array();
        foreach ($this->content as $key => $value) {
            if (is_scalar($value)) {
                $newContent[$key] = $this->format($value);
            }
        }
        return $newContent;
    }

    public function select($columns): ActiveRecord
    {
        $this->columns = $columns;
        return $this;
    }        

    public function where($where): ActiveRecord
    {
        if(!empty($this->where)) {
            if(is_array($where)) {
                foreach($where as $item) {
                    array_push($this->where, $item);
                }
            } else {
                array_push($this->where, $where);
            }
        } else {
            $this->where = !is_array($where) ? [$where] : $where;
        }
        return $this;
    }  
    
    public function and($where): ActiveRecord
    {
        if(!empty($this->where)) {
            if(is_array($where)) {
                foreach($where as $item) {
                    array_push($this->where, $item);
                }
            } else {
                array_push($this->where, $where);
            }
        } else {
            $this->where = !is_array($where) ? [$where] : $where;
        }
        return $this;
    }       

    public function order($order): ActiveRecord
    {
        $this->order = $order;
        return $this;
    }       

    public function limit($limit): ActiveRecord
    {
        $this->limit = $limit;
        return $this;
    }           

    public function find($id = null)
    {
        $class = get_called_class();
        $idfield = (new $class())->idfield;
        $table = (new $class())->table;
        $columns = $this->columns;
        $where = $this->where;
        $order = $this->order;
        $limit = $this->limit;
    
        $paramsToBind = [];
        $sqlClause = [];      

        foreach($where as $filter) {

            $filterParts = explode(" ", $filter);
            $paramsToBind[$filterParts[0]] = $filterParts[2];
            array_push($sqlClause, "{$filterParts[0]} {$filterParts[1]} :{$filterParts[0]}");            
        }

        $sql = "SELECT " . (is_null($columns) ? "*" : $columns) . " FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= !is_null($where) ? " WHERE " . implode(' AND ', $sqlClause) : " WHERE id = :id";
        $sql .= !is_null($order) ? " ORDER BY {$order}" : " ORDER BY id DESC";
        $sql .= !is_null($limit) ? " LIMIT {$limit}" : "";

        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare($sql);

            if(!empty($id)) {

                $stmt->bindParam(":id", $id);
                $stmt->execute();                
                return $stmt->fetchObject($class);   
            }

            foreach(array_keys($paramsToBind) as $param) {
                $stmt->bindParam(":{$param}", $paramsToBind[$param]);
            }                

            $stmt->execute();                
            return $stmt->fetchAll(PDO::FETCH_CLASS, $class);                

        } catch (PDOException $exception) {

            $this->error = $exception;
            return null;
        }

    }   

    public function first()
    {
        $class = get_called_class();
        $idfield = (new $class())->idfield;
        $table = (new $class())->table;
        $columns = $this->columns;
        $where = $this->where;

        $paramsToBind = [];
        $sqlClause = [];      

        foreach($where as $filter) {

            $filterParts = explode(" ", $filter);
            $paramsToBind[$filterParts[0]] = $filterParts[2];
            array_push($sqlClause, "{$filterParts[0]} {$filterParts[1]} :{$filterParts[0]}");            
        } 

        $sql = "SELECT " . (is_null($columns) ? "*" : $columns) . " FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= is_null($where) ? "" : " WHERE " . implode(' AND ', $sqlClause);

        try {
            $pdo = Database::getInstance();

            if(empty($pdo)) {
                
                throw new \Exception("Error Processing Request", 1);

            } else {
                $stmt = $pdo->prepare($sql);

                foreach(array_keys($paramsToBind) as $param) {
                    $stmt->bindParam(":{$param}", $paramsToBind[$param]);
                }

                $stmt->execute();
                return $stmt->fetchObject($class);                
            }

        } catch (\PDOException $exception) {

            $this->error = $exception;
            return $this;

        } catch (\Exception $exception) {

            $this->error = $exception;
            return $this;
        }

    }       
    
    public function all()
    {
        $class = get_called_class();
        $idfield = (new $class())->idfield;
        $table = (new $class())->table;
        $columns = $this->columns;
        $where = $this->where;
        $order = $this->order;
        $limit = $this->limit;

        $sql = "SELECT " . (is_null($columns) ? "*" : $columns) . " FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= !is_null($order) ? " ORDER BY {$order}" : " ORDER BY id DESC";
        $sql .= !is_null($limit) ? " LIMIT {$limit}" : "";

        try {
            $pdo = Database::getInstance();

            if(empty($pdo)) {
                
                throw new \Exception("Error Processing Request", 1);

            } else {

                $pdo = Database::getInstance();

                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_CLASS, $class);                
            }

        } catch (\PDOException $exception) {

            $this->error = $exception;
            return $this;

        } catch (\Exception $exception) {

            $this->error = $exception;
            return $this;
        }

    }        

    public function save(): ?Int
    {
        $newContent = $this->convertContent();

        try {        
            $pdo = Database::getInstance();

            if (isset($this->content[$this->idfield])) {

                $sets = array();
                foreach ($newContent as $key => $value) {
                    if ($key === $this->idfield)
                        continue;
                    $sets[] = "{$key} = :{$key}";
                }

                $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->idfield} = :id ";
                $this->statement = $sql;
                $stmt = $pdo->prepare($this->statement);

                foreach (array_keys($newContent) as $field) {
                    $stmt->bindParam(":{$field}", $this->__get($field));    
                }

                $stmt->execute();   

            } else {

                $values = [];
                foreach(array_keys($newContent) as $field) {
                    $values[] = ":{$field}";
                }

                $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($newContent)) . ') VALUES (' . implode(', ', $values) . ');';
                $this->statement = $sql;
                $stmt = $pdo->prepare($this->statement);

                foreach (array_keys($newContent) as $field) {
                    $stmt->bindParam(":{$field}", $this->__get($field));    
                }

                $stmt->execute();  
                $this->id = $pdo->lastInsertId();

            }  

            return $this->id;

        } catch (\PDOException $exception) {

            $this->error = $exception;
            return null;

        } catch (\Exception $exception) {
            
            $this->error = $exception;
            return null;
        }

    }

    public function count(): Int
    {
        $class = get_called_class();
        $idfield = (new $class())->idfield;
        $table = (new $class())->table;
        $columns = $this->columns;
        $where = $this->where;

        $paramsToBind = [];
        $sqlClause = [];      

        foreach($where as $filter) {

            $filterParts = explode(" ", $filter);
            $paramsToBind[$filterParts[0]] = $filterParts[2];
            array_push($sqlClause, "{$filterParts[0]} {$filterParts[1]} :{$filterParts[0]}");            
        }    
     
        $sql = "SELECT COUNT(id) FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= is_null($where) ? "" : " WHERE " . implode(' AND ', $sqlClause);

        try {
            $pdo = Database::getInstance();

            if(empty($pdo)) {
                
                throw new \Exception("Error Processing Request", 1);

            } else {
                $stmt = $pdo->prepare($sql);

                foreach(array_keys($paramsToBind) as $param) {
                    $stmt->bindParam(":{$param}", $paramsToBind[$param]);
                }

                $stmt->execute();
                $total = (int)$stmt->fetchColumn(0);
                return $total;
            }

        } catch (\PDOException $exception) {

            $this->error = $exception;
            return $this;

        } catch (\Exception $exception) {

            $this->error = $exception;
            return $this;
        }

    }     
    
    public function delete()
    {
        if (isset($this->content[$this->idfield])) {
            $sql = "DELETE FROM {$this->table} WHERE {$this->idfield} = {$this->content[$this->idfield]};";
            $this->statement = $sql;
            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare($this->statement);
                $stmt->execute();  
                return true;
            } catch (PDOException $exception) {
                $this->error = $exception;
                return null;
            }
        }
    }  
    
    public function query($rawquery)
    {
        try {
            $pdo = \Core\Database::getInstance();
            $stmt = $pdo->prepare($rawquery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $exception) {
            $this->error = $exception;
            return null;
        }        
    }    


}