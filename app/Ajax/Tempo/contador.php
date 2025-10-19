<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();

		$mysql->prepare = array($_POST['id']);
		$mysql->filtro = " WHERE `id` = ? ";
		$consulta = $mysql->read_unico($_POST['table']);

		$tempo = sub_data(data($consulta->$_POST['coluna'], 'd-m-Y-H-i-s'), date('d-m-Y-H-i-s'));

		$arr['tempo'] = iff($tempo['dias'], $tempo['dias'].' dias').' '.$tempo['hora'].':'.$tempo['min'].':'.$tempo['seg'];

	echo json_encode($arr); 
?>