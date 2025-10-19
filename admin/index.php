<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require_once "../system/conecta.php";
require_once "../" . ADMIN . "/home.php";

// Eliminar Bug de LUGAR == site
if (LUGAR == 'site') {
    echo '<script>window.parent.location="' . DIR . '/admin/";</script>';
    exit();
}
