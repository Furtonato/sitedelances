<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['id'] = 0;

		if(isset($_POST['id']) and $_POST['id'] and isset($_POST['modelos']) and $_POST['modelos'] and isset($_POST['boxxs'])){
			$mysql->prepare = array($_POST['modulos']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			verificar_permissoes_all($modulos, 0, 'lista');

			$mysql->prepare = array($_POST['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['boxxs'] = $_POST['boxxs'];
			$arr['id'] = $mysql->update($modulos->modulo);

		}

	$mysql->fim();
	echo json_encode($arr); 

?>