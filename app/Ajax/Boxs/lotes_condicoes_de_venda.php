<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['id']) and $_POST['id']){

			$mysql->filtro = " where id = '".$_POST['id']."' ";
			$item = $mysql->read_unico('lotes');
			if(isset($item->id)){

				$arr['title'] = lang('Visualizar Condições de Venda');
				$arr['html']  = '<div class="w800 w100p_800">
									'.txt($item).'
								</div> ';

			}

		}


	echo json_encode($arr); 

?>