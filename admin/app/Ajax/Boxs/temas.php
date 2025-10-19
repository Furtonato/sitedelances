<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();


		$arr['title'] = 'Temas';

		$arr['html']  = '<ul class="style_color">
							<li><a onclick="temas(this)" class="azul"></a></li>
							<li><a onclick="temas(this)" class="azul_escuro"></a></li>
							<li><a onclick="temas(this)" class="azul_escuro1"></a></li>
							<li><a onclick="temas(this)" class="cinza"></a></li>
							<li><a onclick="temas(this)" class="branco"></a></li>
							<li><a onclick="temas(this)" class="branco1"></a></li>
							<div class="clear"></div>
						</ul>';


	$mysql->fim();
	echo json_encode($arr); 

?>