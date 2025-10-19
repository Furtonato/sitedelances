<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();
	$arr = array();


		if( !(isset($_SESSION['x_site']->id) AND $_SESSION['x_site']->id) ){
			$arr['erro'][] = lang('Você precisa estar logado para participar!');
			$arr['evento'] = 'setTimeout(function(){ window.parent.location="'.DIR.'/login/lote/-/'.$_POST['id'].'"; }, 1000);';


		// Habilitar para Lote sucata
		}  elseif(isset($_POST['sucata']) AND $_POST['sucata']) {
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$cadastro = $mysql->read_unico('cadastro');
			if(!$cadastro->status){
				$arr['erro'][] = lang('Você ainda não está Habilitado para Participar desse Leilão! Regularize seus Documentos!');
			} elseif(!$cadastro->tipo){
				$arr['erro'][] = lang('Você não Pode Participar de um Leilão de um Lote de Sucata!');
			} else {
				$mysql->campo['data'] = date('c');
				$mysql->campo['lotes'] = $_POST['id'];
				$mysql->campo['cadastro'] = $_SESSION['x_site']->id;
				$ult_id = $mysql->insert('lotes_habilitacoes_sucata');
			}



		// Habilitar para Leilao
		} else {
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$cadastro = $mysql->read_unico('cadastro');
			if(!$cadastro->status){
				$arr['erro'][] = lang('Você ainda não está Habilitado para Participar desse Leilão! Regularize seus Documentos!');

			} else {
				$mysql->campo['data'] = date('c');
				$mysql->campo['leiloes'] = $_POST['id'];
				$mysql->campo['cadastro'] = $_SESSION['x_site']->id;
				$ult_id = $mysql->insert('leiloes_habilitacoes');
			}
		}


	echo json_encode($arr);

?>