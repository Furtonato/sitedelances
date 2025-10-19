<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['id'] = array();

		$mysql->prepare = array($_GET['modulos']);
		$mysql->filtro = " WHERE `id` = ? ";
		$modulos = $mysql->read_unico('menu_admin');
		verificar_permissoes_all($modulos, 0, 'lista', 'mais_fotos');
		$arr['tabelas'] = $modulos->modulo;


		// Nome
		if(isset($_POST['nome'])){
			foreach ($_POST['nome'] as $key => $value) {
				$mysql->prepare = array($key);
				$mysql->filtro = " WHERE `id` = ? ";
	        	$mysql->campo['nome'] = $value;
				$arr['id']['nome'][] = $mysql->update('mais_fotos');
			}
		}

		// Ordem
		if(isset($_POST['ordem'])){
			unset($mysql->campo);
			foreach ($_POST['ordem'] as $key => $value) {
				$mysql->prepare = array($key);
				$mysql->filtro = " WHERE `id` = ? ";
	        	$mysql->campo['ordem'] = $value;
				$arr['id']['ordem'][] = $mysql->update('mais_fotos');
			}
		}

		// Delete
		if(isset($_POST['delete'])){
			foreach ($_POST['delete'] as $key => $value) {
				$mysql->prepare = array($key);
				$mysql->filtro = " WHERE `id` = ? ";
				$arr['id']['delete'][] = $mysql->delete('mais_fotos');
			}
		}


	$mysql->fim();
	echo json_encode($arr); 
?>