<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['resposta'] = '';

	if(isset($_POST['id']) and $_POST['id'] and isset($_POST['banco']) and $_POST['banco']){

		$_SESSION['cotacao']['id'][$_POST['banco']][$_POST['id']] = $_POST['id'];
		$_SESSION['cotacao']['qtd'][$_POST['banco']][$_POST['id']] = isset($_POST['qtd']) ? $_POST['qtd'] : 1;

	}

	echo json_encode($arr);

?>
