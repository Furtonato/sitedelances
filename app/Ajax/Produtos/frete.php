<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';
	include_once '../../../app/Classes/Frete.php';

	$mysql = new Mysql();
	$frete = new Frete();

	$arr = array();


		if(isset($_POST['cep']) and isset($_GET['id'])){
			$mysql->prepare = array($_GET['id']);
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' AND `id` = ? ";
			$produtos = $mysql->read_unico('produtos');

			$frete->endereco = 1;
			$frete->frete_gratis = isset($produtos->frete) ? $produtos->frete : 0;
			$frete->peso = isset($produtos->peso) ? $produtos->peso : 0;
			$frete->altura = isset($produtos->altura) ? $produtos->altura : 0;
			$frete->largura = isset($produtos->largura) ? $produtos->largura : 0;
			$frete->comprimento = isset($produtos->comprimento) ? $produtos->comprimento : 0;
			$frete->valor_total = $produtos->preco;
			$arr['frete'] = $frete->calcula_frete($_POST['cep'], 1);
		}


	echo json_encode($arr);

?>