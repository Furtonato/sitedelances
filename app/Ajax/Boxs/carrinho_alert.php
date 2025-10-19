<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;

		if(isset($_POST['id']) and $_POST['id']){

			$mysql->filtro = " where id = '".$_POST['id']."' ";
			$produtos = $mysql->read_unico('produtos');

			$arr['title'] = 'Produdo Adicionado com Sucesso!';
			$arr['html']  = '<div class="w300">
								<div class="wf6"> <a href="'.url('produto', $produtos).'"><img src="'.DIR.'/web/fotos/'.$produtos->foto.'" class="w100p"></a> </div>
								<div class="wf6 pl10 fwb"> <a href="'.url('produto', $produtos).'">'.$produtos->nome.'</a> </div>
								<div class="clear h15"></div>
								<div class="wf7 tac pr5"> <a href="javascript:fechar_all()" class="db botao pl10 pr10"> <i class="cor_666 fa fa-reply"></i> Continuar Comprando</a> </div>
								<div class="wf5 tac"> <a href="'.DIR.'/carrinho/" class="db botao pl10 pr10"> <i class="cor_35AA47 fa fa-shopping-cart"></i> Ir pro carrinho</a> </div>
								<div class="clear h5"></div>
						    </div> ';

		}

	echo json_encode($arr); 

?>