<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../app/Classes/criarMysql.php";
	require_once "../../../app/Classes/publicMysql.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['id'] = 0;


		if(isset($_POST['nome']) and $_POST['nome'] and isset($_POST['table']) and $_POST['table']){

			$criarMysql = new criarMysql();
            $criarMysql->criarTabelas($_POST['table'], 0);
            if(LUGAR != 'admin'){
            	$criarMysql->criarColunas($_POST['table'], table_admin());
            }

			$mysql->prepare = array($_POST['table']);
			$mysql->filtro = " WHERE `modulo` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			$linhas = verificar_permissoes_all($modulos, 0, 'novo');

			$mysql->campo['nome'] = $_POST['nome'];
			if(LUGAR != 'admin'){
				$mysql->campo[table_admin()] = $_SESSION['x_'.LUGAR]->id;
			}
			$arr['id'] = $mysql->insert($_POST['table']);

			if(preg_match('(1_cate)', $_POST['table'])){
				$mysql->prepare = array($arr['id']);
				$mysql->filtro = " WHERE `id` = ? ";
				$mysql->campo['vcategorias'] = '-'.$arr['id'].'-';
				$mysql->update($_POST['table']);
			}

		}

	$mysql->fim();
	echo json_encode($arr); 

?>