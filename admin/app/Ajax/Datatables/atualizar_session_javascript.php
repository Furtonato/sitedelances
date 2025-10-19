<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";

	$mysql = new Mysql();

	$arr = array(); 


		// Dar valor para os session q n existem
		if( $mysql->existe('financeiro_tipos') and !(isset($_POST['financeiro_tipos']) and $_POST['financeiro_tipos']) ){
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = ".LANG." ORDER BY `ordem` ASC, `id` ASC ";
			$tipo = $mysql->read_unico('financeiro_tipos');
			if(isset($tipo->id))
				$_SESSION['financeiro_tipos'] = $tipo->id;
		}


		// Passando Session Javascript para Session Php
		foreach ($_POST as $key => $value) {
			$_SESSION[$key] = $value;
			$arr['json'][$key] = $value;
		}


		// Return Json Das Session (so informacoes)
		//foreach ($_SESSION as $key => $value) {
			//$arr['session'][$key] = $value;
		//}



	echo json_encode($arr); 
?>