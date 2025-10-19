<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Classes/Email.php";
	require_once "../../../../app/Classes/Input.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();

	$verificar_senha_atual = 0;


		if(isset($_POST['email']) and $_POST['email'] and LUGAR == 'admin'){

			$_POST['txt'] = stripslashes($_POST['txt']);

			$email = new Email();
			$email->assunto = $_POST['assunto'];
			$email->titulo = str_replace('src="', 'src="http://'.$_SERVER['HTTP_HOST'], $_POST['txt']);

			$email->to = $_POST['email'];
			$email->enviar();

			$arr['alert'] = 1;
			$arr['evento'] = '$(".fundoo").trigger("click");';


		} elseif(LUGAR == 'admin') {
			$input = new Input();

			$assunto = '';
			if(isset($_POST['lote']) AND $_POST['lote']){
				$mysql->filtro = " WHERE `id` = 55 ";
				$textos = $mysql->read_unico('textos');

        		$mysql->colunas = 'id, nome, pago, ordem, leiloes, lances, lances_cadastro, lances_data, cidades, estados';
                $mysql->filtro = " WHERE `id` = '".$_POST['lote']."' ";
				$lotes = $mysql->read_unico('lotes');

				$mysql->colunas = 'id, nome, email';
				$mysql->filtro = " WHERE id = '".$lotes->lances_cadastro."' ";
				$cadastro = $mysql->read_unico('cadastro');

				$var_email  = email_55($cadastro, $lotes);
				$assunto = var_email($textos->nome, $var_email, 1);
				$input->value = $textos;
				$input->enviar_termo_nota = 1;
			}

			$arr['title'] = 'Newsletter';
			$arr['html']  = '<div class="w940">
								<form id="formNewsletter" method="post" action="'.$_SERVER['SCRIPT_NAME'].'"> ';

					$mysql->colunas = 'id, email';
					$mysql->filtro = " WHERE id = '".$_POST['cadastro']."' ";
					$cadastro = $mysql->read_unico('cadastro');

					$arr['html'] .= '<div class="w940 linha mb10">
										<b class="db mb5">Email do Cliente:</b>
										<input type="text" name="email" class="w940 design" value="'.$cadastro->email.'" required >
									</div> ';

					$arr['html'] .= '<div class="w940 linha mb10">
										<b class="db mb5">Assunto:</b>
										<input type="text" name="assunto" class="w940 design" value="'.$assunto.'" required >
									</div> ';

					$arr['html'] .= '<style> #formNewsletter .finput.finput_editor { padding: 0 !important; }</style> ';
					$arr['html'] .= '<div class="w940 linha mb10">
										'.$input->editor('Texto', 'txt').'
									</div>
									<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Enviar</button>
									<div class="clear"></div>
								</form>
								<script>ajaxForm('.A.'formNewsletter'.A.');</script>
							</div> ';

		} else {
			$arr['violacao_de_regras'] = 1;
			violacao_de_regras($arr);
		}

	$mysql->fim();
	echo json_encode($arr); 

?>