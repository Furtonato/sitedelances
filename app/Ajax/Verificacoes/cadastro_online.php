<?php

ob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';

$mysql = new Mysql();

$arr = array();


if (isset($_SESSION['x_site']->id)) {
    $mysql->prepare = array($_SESSION['x_site']->id);
    $mysql->filtro = " WHERE `id` = ? ";
    $mysql->campo['ult_acesso'] = date('c');
    $mysql->update('cadastro');
}


echo json_encode($arr);
?>