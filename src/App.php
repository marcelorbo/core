<?php

namespace Estartar\Core;

// TODO: Review
require_once __DIR__ . '/Util.php';

use \stdClass;
use \Exception;

class App
{
    public $uri;

    public function __construct()
    {
        $arrayURI = array_filter( 
            explode("/", str_replace(CONFIG["BASEFOLDER"], "", $_SERVER['REQUEST_URI']), 4) 
        );        

        $this->uri = new stdClass();

        $this->uri->controller = !empty($arrayURI[1]) ? proper($arrayURI[1]) : CONFIG["BASECONTROLLER"];
        $this->uri->method = !empty($arrayURI[2]) ? proper($arrayURI[2]) : CONFIG["BASEMETHOD"];
        $this->uri->parameters = !empty($arrayURI[3]) ? explode("/", $arrayURI[3]) : [];
    }

    public function Start()
    {
        try 
        {
            $controller = $this->uri->controller .= "Controller";
            $method = $this->uri->method;
            $parameters = $this->uri->parameters;            

            if(!file_exists("./controllers/" . $controller. ".php")) {

                throw new Exception("Page not exists", 1);
                
            } else {

                require_once "./controllers/" . $controller. ".php";

                $class = new $controller();
                $class->$method($parameters);

            }

        } catch (Exception $exception) {    

            echo $exception->getMessage();
            exit;

        }

    }

}

