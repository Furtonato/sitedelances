<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array(); 
	$arr['value'] = array();

		$mysql->prepare = array($_GET['modulos']);
		$mysql->filtro = " WHERE `id` = ? ";
		$modulos = $mysql->read_unico('menu_admin');
		verificar_permissoes_all($modulos, 0, 'lista', ((isset($_GET['rand']) and $_GET['rand']) ? $_GET['rand'] : ''));

		// Ordem
		if(isset($_POST['ordem'])){
			foreach ($_POST['ordem'] as $key => $value) {
				$mysql->logs = 'Item Ordenando';
		        $mysql->campo['ordem'] = $value;

				if($modulos->id == 1) $mysql->logs = 0;
				$mysql->logs_caminho = '../../';

				$mysql->prepare = array($key);
				$mysql->filtro = " WHERE `id` = ? ";
				$ult_id = $mysql->update($modulos->modulo);
				$arr['value'][$key] = $value;
			}
		}

		// Input
		if(isset($_POST['input'])){
			foreach ($_POST['input'] as $k => $v) {
				foreach ($v as $key => $value) {
					$mysql->logs = ucfirst($k).': '.$value;
			        $mysql->campo[$k] = $value;

					if($modulos->id == 1) $mysql->logs = 0;
					$mysql->logs_caminho = '../../';

					$mysql->prepare = array($key);
					$mysql->filtro = " WHERE `id` = ? ";
					$ult_id = $mysql->update($modulos->modulo);
					$arr['value'][$key] = $value;
				}
			}
		}


	$mysql->fim();
	echo json_encode($arr); 
?>