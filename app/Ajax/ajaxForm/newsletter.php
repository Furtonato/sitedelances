<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';
	include_once '../../../app/Classes/Email.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 'z';
	$arr['form'] = '';
	$arr['dir'] = DIR_C;


		if(isset($_POST['email'])){

        	$mysql->campo['nome'] = isset($_POST['nome']) ? $_POST['nome'] : $_POST['email'];
        	$mysql->campo['email'] = $_POST['email'];
            $mysql->insert('newsletter');


			// Email
			$arr['email'] = $_POST['email'];


			$arr['alert'] = 1;
			$arr['evento'] = ' ';

		}


	echo json_encode($arr); 
?>