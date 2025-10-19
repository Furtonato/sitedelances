<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();


		$ex = explode('_', $_POST['ref']);
		$id = $arr['id'] = $ex[0];
		$ref = $arr['ref'] = $_POST['ref'];

		unset($_SESSION['carrinho']['itens'][$id][$ref]);


	echo json_encode($arr);

?>