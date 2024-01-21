<?php

date_default_timezone_set('America/Sao_Paulo');

#region Caminhos
define("PATH_TMP", 'app/Backend/Tmp');
define("PATH_IMG", 'app/Frontend/assets/img/icon');
define("PATH_FRONT_END", 'app/Frontend');
define("PATH_BACK_END", 'app/Backend');
define("PATH_FRAMEWORK_DOWNLOAD", '../../Apps/AreaContador/Backend/Tmp/');
#endregion

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
$al->addDirectory('app/Backend/Controller');
$al->addDirectory('app/Backend/Model');
$al->addDirectory('app/Backend/Database/View');
$al->addDirectory('app/Backend/Service');
$al->addDirectory('app/Backend/Router');
$al->addDirectory('app/Backend/Reports');
$al->addDirectory('app/Frontend/Page');
$al->register();
#endregion

#endregion

Router::run();