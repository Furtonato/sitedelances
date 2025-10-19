<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";
	require_once "../../../app/Classes/Email.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['cadastro']) and $_POST['cadastro']){

        	$mysql->campo['status'] = 0;
        	$mysql->campo['cadastro'] = $_POST['cadastro'];
        	$mysql->campo['item'] = $_POST['item'];
        	$mysql->campo['tabelas'] = $_POST['tabelas'];
        	$mysql->campo['star'] = $_POST['star'];
        	$mysql->campo['nome'] = $_POST['nome'];
        	$mysql->campo['txt'] = $_POST['txt'];
            $mysql->insert('mais_comentarios');

			$email = new Email();
			$email->remetente = nome_site();
			$email->assunto = 'Novo comentário no site '.nome_site();
			$email->txt = 'Um novo comentário foi feito pelo Usuário: '.$_POST['nome'].' no Módulo: '.ucfirst($_POST['tabelas']).' - id: #'.$_POST['item'];
			$email->enviar();

			$arr['alert'] = 'Comentário Enviado com Sucesso! Aguarde a Liberação do Administrador...';
			$arr['evento'] = ' fechar_all(); ';


		} elseif(isset($_POST['add']) and $_POST['add']){

			if(!isset($_SESSION['x_site']->id)){
				$arr['title'] = 'Escrever Comentários';
				$arr['html']  = ' <div class="pb10"> Você precisa se logar para poder fazer um comentário! </div> ';

			} else {

				$mysql->prepare = array($_SESSION['x_site']->id, $_POST['tabelas'], $_POST['item']);
				$mysql->filtro = " WHERE `cadastro` = ? AND `tabelas` = ? AND `item` = ? ";
				$comentarios = $mysql->read_unico('mais_comentarios');
				if(isset($comentarios->id)){
					$arr['title'] = 'Escrever Comentários';
					$arr['html']  = ' <div class="pb10"> Você já comentou este produto! </div> ';

				} else {

					$mysql->prepare = array($_SESSION['x_site']->id);
					$mysql->filtro = " WHERE `id` = ? ";
					$cadastro = $mysql->read_unico('cadastro');
					$nome = isset($cadastro->nome) ? $cadastro->nome : '';

					$arr['title'] = 'Escrever Comentários';
					$arr['html']  = '<form id="comentarios" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">
										<input type="hidden" name="cadastro" value="'.$_SESSION['x_site']->id.'" >
										<input type="hidden" name="item" value="'.$_POST['item'].'" >
										<input type="hidden" name="tabelas" value="'.$_POST['tabelas'].'" >

										<div class="linha mb10 fz16">
											<b class="fll p2">Avaliar: &nbsp;</b>
											<i class="fa fa-star-o fll p2 mt5 c-p c_amarelo votar_star" dir="1"></i>
											<i class="fa fa-star-o fll p2 mt5 c-p c_amarelo votar_star" dir="2"></i>
											<i class="fa fa-star-o fll p2 mt5 c-p c_amarelo votar_star" dir="3"></i>
											<i class="fa fa-star-o fll p2 mt5 c-p c_amarelo votar_star" dir="4"></i>
											<i class="fa fa-star-o fll p2 mt5 c-p c_amarelo votar_star" dir="5"></i>
											<div class="clear"></div>
											<input type="hidden" name="star" value="0">
										</div>
										<div class="linha mb10">
											<b class="db mb5">Nome:</b>
											<input type="text" name="nome" value="'.$nome.'" class="w300 design" required >
										</div>
										<div class="linha mb15">
											<b class="db mb5">Comentário:</b>
											<textarea name="txt" class="w300 h150 design"></textarea>
										</div>
										<input type="hidden" name="gravar" value="1">
										<button class="botao h30 pl10 pr10"> <i class="mr2 fa fa-check c_verde"></i> Enviar</button>
										<div class="clear"></div>
									</form>
									<script>ajaxForm('.A.'comentarios'.A.'); votar_star();</script> ';
				}

			}

		} else {


			$mysql->prepare = array($_POST['tabelas'], $_POST['item']);
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' AND `tabelas` = ? AND `item` = ? ORDER BY `id` DESC ";
			$comentarios = $mysql->read('mais_comentarios');

			$arr['title'] = 'Comentários';
			$arr['html']  = '<div class="w700"> ';
								foreach ($comentarios as $key => $value) {
									$arr['html'] .= '<div class="mb20">
														<b>Nome: </b> '.$value->nome.'
														<div class="hh3"></div>
														<b>Avaliação:</b> '.star_icon($value->star, 5).'
														<div class="hh3"></div>
														<b>Texto: </b> '.$value->txt.'
													 </div>';
								}
								if(!$comentarios){									
									$arr['html'] .= 'Ninguem comentou este produto ainda!';
								}

			$arr['html'] .= '</div> ';


		}


	$mysql->fim();
	echo json_encode($arr); 

?>