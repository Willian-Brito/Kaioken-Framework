<?php

date_default_timezone_set('America/Sao_Paulo');

#region AutoLoad

#region Framework

require_once 'framework/Core/FrameworkLoader.php';

$al= new KaiokenFramework\Core\FrameworkLoader;
$al->addNamespace('KaiokenFramework', 'framework/');
$al->register();
#endregion

#region App

require_once 'framework/Core/AppLoader.php';

$al= new KaiokenFramework\Core\AppLoader;
$al->addDirectory('Backend/Controller');
$al->addDirectory('Backend/Model');
$al->addDirectory('Backend/Database/View');
$al->addDirectory('Backend/Service');
$al->addDirectory('Backend/Router');
$al->addDirectory('Backend/Reports');
$al->addDirectory('Frontend/Page');
$al->register();
#endregion

#endregion

Router::run();