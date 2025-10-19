<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['id']) and $_POST['id']){

			$table = isset($_POST['table']) ? $_POST['table']  : 'textos';

			$mysql->filtro = " where id = '".$_POST['id']."' ";
			$item = $mysql->read_unico($table);
			if(isset($item->id)){

				$arr['title'] = $item->nome;
				$arr['html']  = '<div class="w800">
									'.txt($item).'
							    </div> ';

			}

		}

	echo json_encode($arr); 

?>