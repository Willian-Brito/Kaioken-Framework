<?php
namespace KaiokenFramework\Router;

use Exception;

/**
 * Route handler
 * @author Willian Brito (h1s0k4)
*/
class Router
{
    #region Rotas

    // private $router = [
    //     "GET" => [
    //         "/" => $this->load("HomeController", "index"),
    //         "/contact" => $this->load("ContactController", "index")
    //     ],
    //     "POST" => [
    //         "/contact" => $this->load("ContactController", "index")
    //     ]
    // ];
    #endregion
    
    #region load
    public function load(string $controller, string $action)
    {
        try
        {
            $controllerNamespace = "app\\controllers\\{$controller}";

            if(!class_exists($controllerNamespace))
                throw new Exception("O controller {$controller} não existe!");
    
            $controllerInstance = new $controllerNamespace();
    
            if(!method_exists($controllerInstance, $action))
                throw new Exception("O método {$action} não existe no controller {$controller}!");
    
            $controllerInstance->$action;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
    #endregion

    #region redirect
    public static function redirect($rota, $time = 1000)
    {
        echo "<script> setTimeout(function(){ window.location.href = '$rota'}, $time)</script>";
    }
    #endregion

    #endregion
}
