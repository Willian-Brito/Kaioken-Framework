<?php

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Server\RestServer;
use KaiokenFramework\Session\Session;

/**
 * Classe responsável por manipular rotas
 * @author Willian Brito (h1s0k4)
*/
class Router
{
    #region Propriedades da Classe

    private static $template;
    private static $class;
    private static $content;
    #endregion

    #region Metodos

    #region run
    public static function run()
    {
        // # http://localhost:8081/Apps/AreaContador/index.php/api/NFeXml/import/40129685000195
        
        #region API
        if(self::ehAPI())
        {
            $server = new RestServer;
            $response = $server->run($_REQUEST);

            print $response;
            return;
        }
        #endregion

        #region WEB
        self::authentication();
        self::handle();
        #endregion
    }
    #endregion

    #region authentication
    public static function authentication()
    {
        Session::start();

        if(Session::getValue('logado'))
        {
            self::$template = Template::create();
            self::$class = isset($_GET['class']) ? $_GET['class'] : '';

            if(class_exists(self::$class))
            {
                if($_GET['class'] == "Template")       
                    self::$template = ''; 
            }
            else 
            {
                $link = "index.php?class=DashboardList";
                JScript::redirect($link);
            }
        }
        else
        {
            $nomeClasse = isset($_GET['class']) ? $_GET['class'] : '';

            if($nomeClasse == 'RecuperarSenhaForm') 
            {
                self::$template = file_get_contents('Frontend/Page/Sistema/Login/RecuperarSenha.html');
                self::$class = 'RecuperarSenhaForm';
            }
            else
            {
                self::$template = file_get_contents('Frontend/Page/Sistema/Login/Login.html');
                self::$class = 'LoginForm';
            }
        }
    }
    #endregion

    #region handle
    public static function handle()
    {
        try
        {
            self::content();
        }
        catch (Exception $e)
        {
            self::$content = self::exception($e);
        }

        $output = str_replace('{content}', self::$content, self::$template);
        $output = str_replace('{class}',   self::$class, $output);

        echo $output;
    }
    #endregion

    #region content
    public static function content()
    {
        self::$content = '';

        if(class_exists(self::$class))
        {
            $pagina = new self::$class;

            ob_start();
                $pagina->show();
                self::$content = ob_get_contents();
            ob_end_clean();
        }
    }
    #endregion

    #region exception
    public static function exception($e)
    {
        // DEBUG: self::$content = $e->getMessage() . '<br>' .$e->getTraceAsString();
        if(Session::getValue('logado'))
        {
            self::$content = file_get_contents('Frontend/Page/Sistema/Error/ErrorForm.html');
            $messageError = $e->getMessage();
            self::$content = str_replace('{{Error-Message}}', $messageError, self::$content);
        }
        else
        {
            self::$content = ""; 
        }

        return self::$content;
    }
    #endregion

    #region ehAPI
    private static function ehAPI()
    {
        $ehAPI = false;

        $temURI = !empty($_SERVER["PATH_INFO"]);
            
        if($temURI)
        {
            $uri = explode('/', $_SERVER["PATH_INFO"]);
            
            //retirando valores nulos
            $uri = array_filter($uri,function($value){
                return !empty($value);
            });

            $api = !empty($uri[1]) ? array_shift($uri) : "";

            if($api === "api")
            {
                $ehAPI = true;            
                $_REQUEST['api'] = $uri;
            }
        }

        return $ehAPI;
    }
    #endregion

    #endregion
}