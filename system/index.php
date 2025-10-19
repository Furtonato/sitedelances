<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require_once('../system/head.php');

function autoloader($class_name)
{

    // Classes
    if (file_exists('../app/Classes/' . $class_name . '.php')) {
        require_once('../app/Classes/' . $class_name . '.php');

        // Models
    } elseif (file_exists('../app/Models/' . $class_name . '.php')) {
        require_once('../app/Models/' . $class_name . '.php');

        // TNG
    } else if (file_exists('../plugins/Tng/tng/triggers/' . $class_name . '.class.php')) {
        //require_once('../plugins/Tng/tng/triggers/'.$class_name.'.class.php');
    }
}

spl_autoload_register('autoloader');

if (!isset($nao_rodar_system)) {
    $star->run();
}
