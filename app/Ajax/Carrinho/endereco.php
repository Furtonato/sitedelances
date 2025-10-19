<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();


		// Endereco (Frete)
		$mysql->prepare = array($_SESSION['x_site']->id);
		$mysql->filtro = " WHERE `cadastro` = ? AND `principal` = 1 ";
		$enderecos_principal = $mysql->read_unico('cadastro_enderecos');

		if(isset($enderecos_principal->id)){
			$arr['endereco_atual_all']  = '<div>'.$enderecos_principal->rua.', '.$enderecos_principal->numero.' '.$enderecos_principal->complemento.'</div>';
			$arr['endereco_atual_all'] .= '<div>'.$enderecos_principal->bairro.', '.estado($enderecos_principal->estados).' - '.cidade($enderecos_principal->cidades).'</div>';
			$arr['endereco_atual_all'] .= '<div>CEP: '.$enderecos_principal->cep.'</div>';
			$arr['cep'] = $enderecos_principal->cep;

			$_SESSION['carrinho']['frete']['cep'] = $enderecos_principal->cep;
			$_SESSION['carrinho']['frete']['rua'] = $enderecos_principal->rua;
			$_SESSION['carrinho']['frete']['numero'] = $enderecos_principal->numero;
			$_SESSION['carrinho']['frete']['complemento'] = $enderecos_principal->complemento;
			$_SESSION['carrinho']['frete']['bairro'] = $enderecos_principal->bairro;
			$_SESSION['carrinho']['frete']['cidades'] = $enderecos_principal->cidades;
			$_SESSION['carrinho']['frete']['estados'] = $enderecos_principal->estados;

		} else {
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `cadastro` = ? ";
			$enderecos_principal = $mysql->read_unico('cadastro_enderecos');
			if($enderecos_principal) {
				$arr['evento'] = "boxs('endereco_edit', 'ini=1'); ";
			} else {
				$arr['evento'] = "boxs('endereco_add'); ";
			}
		}



	echo json_encode($arr);

?>