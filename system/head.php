<?php

require_once '../system/conecta.php';
require_once '../plugins/PHPMailer/class.phpmailer.php';

require_once '../system/mysql.php';
require_once '../app/Funcoes/funcoes.php';
require_once '../system/system.php';

require_once '../app/controllers.php';
require_once '../admin/app/Classes/criarMysql.php';
require_once '../admin/app/Classes/publicMysql.php';
//require_once '../plugins/Tng/tng/tNG.inc.php';

require_once '../app/Funcoes/funcoesAdmin.php';
require_once('../app/verificacoes.php');

if (isset($_SESSION['x_site']->id)) {
    $mysql->prepare = array($_SESSION['x_site']->id);
    $mysql->filtro = " WHERE `id` = ? ";
    $mysql->campo['ult_acesso'] = date('c');
    $mysql->update('cadastro');
}

if (!isset($nao_rodar_system)) {
    $star = new System;
}
