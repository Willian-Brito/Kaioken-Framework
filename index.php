<?php

date_default_timezone_set('America/Sao_Paulo');

#region AutoLoad

#region Framework

require_once '../../KaiokenFramework/Core/FrameworkLoader.php';

$al= new KaiokenFramework\Core\FrameworkLoader;
$al->addNamespace('KaiokenFramework', '../../KaiokenFramework/');
$al->register();
#endregion

#region App

require_once '../../KaiokenFramework/Core/AppLoader.php';

$al= new KaiokenFramework\Core\AppLoader;
$al->addDirectory('Backend/Controller');
$al->addDirectory('Backend/Model');
$al->addDirectory('Frontend/Page');
$al->register();
#endregion

#endregion

#region Autenticação

use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Session\Session;

Session::start();

if(Session::getValue('logado'))
{
    $template = file_get_contents('Frontend/Template/Template-backup.html');
    $class = isset($_GET['class']) ? $_GET['class'] : '';
}
#endregion

#region Conteudo

$content = '';

if(class_exists($class))
{
    try
    {
        $pagina = new $class;

        ob_start();
            $pagina->show();
            $content = ob_get_contents();
        ob_end_clean();
    }
    catch (Exception $e)
    {
        // DEBUG: $content = $e->getMessage() . '<br>' .$e->getTraceAsString();
        $content = $e->getMessage();
        new Message('error', $content, 5000);
    }
}

$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}',   $class, $output);

echo $output;
#endregion