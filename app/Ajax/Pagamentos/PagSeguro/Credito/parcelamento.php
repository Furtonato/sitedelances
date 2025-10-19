<?php ob_start();

	require_once "../../../../../system/conecta.php";
	require_once "../../../../../system/mysql.php";
	require_once "../../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['html'] = '';


		$parcelas = json_decode($_POST['parcelamento']);
		foreach ($parcelas->installments as $k => $v) {
			foreach ($v as $key => $value){
				$arr['html'] .= '<option value="'.$value->quantity.'">'.$value->quantity.'x de '.preco($value->installmentAmount, 1).' '.iff($value->interestFree, 'sem juros', 'com juros').'</option> ';
			}
		}


	echo json_encode($arr); 

?>