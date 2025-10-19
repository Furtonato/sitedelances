<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../../app/Classes/Email.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['email']) and $_POST['email']){

			$mysql->prepare = array($_POST['email']);
			$mysql->filtro = " WHERE email = ? ";
			$cadastro = $mysql->read_unico(table_admin());
			if(isset($cadastro->id)){

				$email = new Email();
				$email->to = $_POST['email'];
				$email->assunto = 'Alteração de Senha no site '.nome_site();
				// <div><img src="'.DIR_C.'/web/img/logo.png" /></div><br>
				$email->txt = '	<div style="color:#333">
									<table border="0" cellPadding="0" width="550">
										<tr><td>Olá <b>'.$cadastro->nome.'</b>,</td></tr>
										<tr><td>Clique no link abaixo para criar uma nova senha</td></tr>
										<tr><td><a href="'.DIR_C.'/'.LUGAR.'/login.php?q='.base64_encode($cadastro->data.'574839'.$cadastro->id.'847382').'" style="color:#00F">Criar nova senha</a></td></tr>
									</table>
								</div>';
				$email->enviar();

				$arr['alert'] = "Sua senha foi enviado para seu email!";
				$arr['evento'] = '$(".fundoo").trigger("click");';

			} else {
				$arr['erro'][] = "Email não cadastrado!";
			}


		} else {

			$arr['title'] = 'Esqueceu a senha';
			$arr['html']  = '<form id="EsqueciSenha" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">
								<div class="linha mb10">
									<b class="db mb5">Seu Email:</b>
									<input type="email" name="email" class="w300 design" required >
								</div>
								<input type="hidden" name="gravar" value="1">
								<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Email</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('.A.'EsqueciSenha'.A.');</script> ';

		}

	$mysql->fim();
	echo json_encode($arr); 

?>