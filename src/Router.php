<?php

namespace Estartar\Core;

use stdClass;
use \Exception;

class Router
{
    public static function View($pagename, $viewbag, $master = null)
    {
        try 
        {
            ob_start();

            if(!file_exists('./views/' . $pagename . '.phtml')) 
            {
                throw new Exception("Page not exists");
                exit;
            }

            require('./views/' . $pagename . '.phtml');
            $view = ob_get_clean();
    
            if($master) 
            {
                if(!file_exists("./views/Master/". $master . ".phtml")) 
                {
                    throw new Exception("Master not exists");
                    exit;
                }                
                require("./views/Master/". $master . ".phtml");
            } 
            else 
            {
                echo $view;
            }

        } catch (Exception $exception) 
        {    
            echo $exception->getMessage();
            exit;
        }

    }

    public static function Redirect($path, $uri)
    {
        $REQUEST_URI = implode("/", $uri);

        $arrayURI = array_filter( 
            explode("/", $REQUEST_URI, 3) 
        );        

        $u = new stdClass();

        $u->controller = !empty($arrayURI[0]) ? proper($arrayURI[0]) : CONFIG["BASECONTROLLER"];
        $u->method = !empty($arrayURI[1]) ? proper($arrayURI[1]) : CONFIG["BASEMETHOD"];
        $u->parameters = !empty($arrayURI[2]) ? explode("/", $arrayURI[2]) : [];

        try 
        {
            $controller = $u->controller .= "Controller";
            $method = $u->method;
            $parameters = $u->parameters;            

            if(!file_exists("{$path}/" . $controller. ".php")) {
                throw new Exception("Page not exists");
                exit;
            }

            require "{$path}/" . $controller. ".php";

            $class = new $controller();
            $class->$method($parameters);

        } catch (Exception $exception) {    

            echo $exception->getMessage();
            exit;

        }        
        
    }  

}

