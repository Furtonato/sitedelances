<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['resposta'] = 0;

	if(isset($_POST['id']) and $_POST['id'] and isset($_POST['banco']) and $_POST['banco'] and isset($_POST['comprimento']) and $_POST['comprimento'] and isset($_POST['qtd']) and $_POST['qtd']){

		$_SESSION['cotacao']['id'][$_POST['banco']][$_POST['id']] = $_POST['id'];

		foreach ($_POST['comprimento'] as $key => $value) {
			if($value){
				$_SESSION['cotacao']['qtd'][$_POST['banco']][$_POST['id']]['comprimentos'][] = $value;
				$_SESSION['cotacao']['qtd'][$_POST['banco']][$_POST['id']]['qtds'][] = $_POST['qtd'][$key] ? $_POST['qtd'][$key] : 1;
			}
		}

		$arr['resposta'] = 1;
	}

	echo json_encode($arr);

?>
