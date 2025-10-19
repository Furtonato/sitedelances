<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

define('LUGAR', 'admin');
define('DIR', $_SERVER['HTTP_HOST']);

// Auto Load Class
function __autoload($class_name)
{
    // Classes
    if (file_exists("../../app/Classes/" . $class_name . ".php")) {
        require_once("../../app/Classes/" . $class_name . ".php");
    } elseif (file_exists("../app/Classes/" . $class_name . ".php")) {
        require_once("../app/Classes/" . $class_name . ".php");
        // TNG
    } else if (file_exists("../../plugins/Tng/tng/triggers/" . $class_name . ".class.php")) {
        //require_once("../../plugins/Tng/tng/triggers/".$class_name.".class.php");
    }
}

require_once '../../plugins/cn/config.php';
require_once '../../system/mysql.php';

$db = $mysql->getDB();
