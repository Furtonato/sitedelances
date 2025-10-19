<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";
	require_once "../../../app/Classes/Email.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;


		$arr['title'] = lang('Fazer Login');
		$arr['html']  = '<form id="form_login" method="post" action="" enctype="multipart/form-data" >

							<div class="linha mb10">
								<b class="db mb5">'.lang('Email').':</b>
								<input type="text" name="email" class="w300 design" required >
							</div>
							<div class="linha mb10">
								<b class="db mb5">'.lang('Senha').':</b>
								<input type="password" name="senha" class="w300 design" required >
							</div>
							<input type="hidden" name="fazer_login" value="1">
							<input type="hidden" name="direcionar" value="refresh">
							<a class="fll mt10 fz11 cor_00f link" onclick="boxs('.A.'esqueci_senha'.A.');"> '.lang('Esqueci minha senha?').' </a>
							<button class="botao h30 flr pl10 pr10"> <i class="mr2 fa fa-check c_verde"></i> '.lang('Enviar').'</button>
							<div class="clear"></div>
						</form>
						<script>ajaxForm('.A.'form_login'.A.', '.A.'login.php'.A.');</script> ';


	echo json_encode($arr); 

?>