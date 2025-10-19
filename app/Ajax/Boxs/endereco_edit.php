<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['gravar']) and $_POST['gravar']){

			if(isset($_POST['endereco'])){
				$mysql->campo['principal'] = 0;
				$mysql->prepare = array($_SESSION['x_site']->id);
				$mysql->filtro = " WHERE `cadastro` = ? ";
				$ult_id = $mysql->update('cadastro_enderecos');

				$mysql->campo['principal'] = 1;
				$mysql->prepare = array($_POST['endereco']);
				$mysql->filtro = " WHERE `id` = ? ";
				$ult_id = $mysql->update('cadastro_enderecos');

				// Resetar Frete
				unset($_SESSION['carrinho']['frete']['valor']);

				$arr['alert'] = 'z';
				$arr['evento']  = '$(".fundoo").trigger("click"); ';
				$arr['evento'] .= 'carrinhoo_atualizar(); ';
			}



		} elseif(isset($_POST['delete']) and $_POST['delete']){

			$mysql->prepare = array($_POST['delete']);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->delete('cadastro_enderecos');

			if(isset($_SESSION['carrinho']['frete']['endereco_atual']) and $_POST['delete'] == $_SESSION['carrinho']['frete']['endereco_atual']){
				unset($_SESSION['carrinho']['frete']['endereco_atual']);
				unset($_SESSION['carrinho']['frete']['cep']);
				unset($_SESSION['carrinho']['frete']['rua']);
				unset($_SESSION['carrinho']['frete']['numero']);
				unset($_SESSION['carrinho']['frete']['complemento']);
				unset($_SESSION['carrinho']['frete']['bairro']);
				unset($_SESSION['carrinho']['frete']['estados']);
				unset($_SESSION['carrinho']['frete']['cidades']);
			}

			$arr['alert'] = 1;
			$arr['evento'] = '$(".linha_'.$_POST['delete'].'").hide();';
			$arr['evento'] .= 'carrinhoo_atualizar(); ';


		} else {

			$arr['title'] = 'Selecionar Endereço';
			$arr['html']  = '<form id="EditEndereco" class="fz12" method="post" action="'.$_SERVER['SCRIPT_NAME'].'"> ';

			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `cadastro` = ? ";
			$cadastro_enderecos = $mysql->read('cadastro_enderecos');
			if($cadastro_enderecos){
				foreach ($cadastro_enderecos as $key => $value) {
					$arr['html'] .= '<div class="linha linha_'.$value->id.' p5 pl10 pr10 mb5 br10"> ';
						$arr['html'] .= '<a onclick="ajaxNormal('.A.'Boxs/endereco_edit.php'.A.', '.A.'delete='.$value->id.A.')" class="mr4" title="Deletar Enredeço"> <i class="fa fa-times fz14 c_vermelho"></i> </a> ';
						$arr['html'] .= '<label class="m0"> ';
							$arr['html'] .= '<input type="radio" name="endereco" value="'.$value->id.'" '.iff($value->principal==1, 'checked').' onclick="$('.A.'#EditEndereco'.A.').submit();" > ';
							$arr['html'] .= $value->rua.', '.$value->numero.' '.$value->complemento.' - '.$value->bairro.' - '.$value->cidades.' / '.$value->estados.' - '.$value->cep;
						$arr['html'] .= '</label> ';
					$arr['html'] .= '</div> ';
				}

				$arr['html'] .= '	<input type="hidden" name="gravar" value="1">
									<div class="clear"></div>
									<style>
										#EditEndereco .linha:hover { background: #eee; }
									</style>
								</form>
								<script>ajaxForm('.A.'EditEndereco'.A.');</script> ';
			} else {
				$arr['evento'] = "boxs('endereco_add'); ";				
			}


		}

	echo json_encode($arr); 

?>