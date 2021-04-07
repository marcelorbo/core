<?php

namespace Estartar\Core;

use \Exception;

class File
{
    private $object;
    public $error;

    public $filePath;
    public $fileName;
    public $fileExtension;
    public $fileType;
    public $fileSize;

    public function __construct($inputfile)
    {
        try {
            $this->object = $inputfile;
            $this->fileType = $this->object["type"];
            $this->fileSize = $this->object["size"];

            $path_parts = pathinfo($this->object["name"]);
            $this->fileExtension = $path_parts['extension'];
            $this->fileName = $path_parts['filename'];
        } 
        catch (Exception $e) {
            echo $e->getMessage();
            die();
        }      
    }

    function path($param): File
    {
        try {
          $this->filePath = $param;
        } 
        catch (Exception $e) {
          $this->error = $e;
        }
        return $this;
    }

    function name($param): File
    {
        try {
            $this->fileName = $param;
        } 
        catch (Exception $e) {
            echo $e->getMessage();
            die();
        }      
        return $this;
    }   

    function fullPath()
    {
        return $this->filePath . "/". $this->fileName . "." . $this->fileExtension;
    }

    function save(): File
    {
        try {
            if (!is_dir(CONFIG["BASEDIR"]. $this->filePath)) {
                mkdir(CONFIG["BASEDIR"]. $this->filePath, 0755, true);
            }          
            if(move_uploaded_file($this->object['tmp_name'], CONFIG["BASEDIR"]. $this->filePath. "/". $this->fileName . ".". $this->fileExtension)) {
                $this->error = null;
            }          
        } 
        catch (Exception $e) {
          $this->error = $e;
        }      

        return $this;
    }

    // TODO: 
    function load($fullpath)
    {

    }

}