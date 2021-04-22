<?php

namespace Estartar\Core;

use \Estartar\Core\Router;
use \Estartar\Core\ViewBag;

class Controller 
{
    public $viewbag;

    public function __construct()
    {
        $this->viewbag = new ViewBag();
        
        if(!empty($_POST)) {
            // $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);            
        }

        $this->viewbag->URI = array_filter( 
            explode("/", str_replace(CONFIG["BASEFOLDER"], "", $_SERVER['REQUEST_URI']), 5) 
        );        
    }

    public function method()
    {
        return ($_SERVER['REQUEST_METHOD']);
    }

    public function Auth($redirectTo = null)
    {
        if(!isset($_SESSION["user"])) {
            header("Location: " . CONFIG["BASEURL"] . $redirectTo ?? "/account/login");
            exit;                        
        }
    }

    public function View($page, $master)
    {
        return Router::View($page, $this->viewbag, $master);
        exit;
    }

    public function Redirect($path, $uri)
    {
        return Router::Redirect($path, $uri);
        exit;
    }    

    public function isPostBack()
    {
       return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }    


}