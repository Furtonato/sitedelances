<?php

ob_start();

require_once "../../../../system/conecta.php";
require_once "../../../../system/mysql.php";
require_once "../../../../app/Classes/Email.php";
require_once "../../../../app/Classes/Input.php";
require_once "../../../../app/Funcoes/funcoes.php";
require_once "../../../../app/Funcoes/funcoesAdmin.php";

$mysql = new Mysql();
$mysql->ini();

$arr = array();


if (isset($_POST['id']) AND $_POST['id'] AND LUGAR == 'admin') {

    $mysql->logs = 0;
    $mysql->campo['situacao'] = 2;
    $mysql->filtro = " where id = '" . $_POST['id'] . "' ";
    $ult_id = $mysql->update('lotes');
} else {
    $arr['violacao_de_regras'] = 1;
    violacao_de_regras($arr);
}

$mysql->fim();
echo json_encode($arr);
?>