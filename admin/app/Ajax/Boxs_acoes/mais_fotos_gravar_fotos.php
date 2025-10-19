<?php

ob_start();

require_once "../../../../system/conecta.php";
require_once "../../../../system/mysql.php";
require_once "../../../../app/Funcoes/funcoes.php";
require_once "../../../../app/Classes/Upload.php";
require_once "../../../../app/Funcoes/funcoesAdmin.php";

$mysql = new Mysql();
$mysql->ini();

$arr = array();
$arr['id'] = array();
$arr['alert'] = 1;

$mysql->prepare = array($_GET['modulos']);
$mysql->filtro = " WHERE `id` = ? ";
$modulos = $mysql->read_unico('menu_admin');
verificar_permissoes_all($modulos, 0, 'lista', 'mais_fotos');

// Fotos
$itens = array();
$table = 'mais_fotos';
$upload = new Upload();
$caminho = LUGAR == 'admin' ? '../../../' : '../../../../';
if (isset($_FILES))
    $itens = $upload->fileUpload(0, $caminho, 1);

if ($itens) {
    foreach ($itens as $key => $value) {
        $mysql->campo['foto'] = $value;
        $mysql->campo['tabelas'] = $modulos->modulo;
        $mysql->campo['item'] = $_GET['item'];
        $arr['id'][] = $mysql->insert('mais_fotos');
    }
}


$arr['evento'] = " $('#'+id).find('.input.file input').val(''); ";
$arr['evento'] .= " $('#'+id).find('.input.file span>span').html('Selecionar Fotos'); ";
$arr['evento'] .= " $('.carregando').show(); ";
$arr['evento'] .= " setTimeout(function(){ mais_fotos_update('" . $modulos->modulo . "', '" . $_GET['item'] . "', '" . $modulos->id . "'); }, 1000); ";


$mysql->fim();
echo json_encode($arr);
?>