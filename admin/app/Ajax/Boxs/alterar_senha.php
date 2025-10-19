<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();

	$verificar_senha_atual = 0;

		$_POST['id'] = LUGAR=='admin' ? $_POST['id'] : $_SESSION['x_'.LUGAR]->id;

		if(isset($_POST['gravar']) and $_POST['gravar'] and isset($_POST['modulos']) and $_POST['modulos'] and isset($_POST['id']) and $_POST['id']){

			$mysql->prepare = array($_POST['modulos']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			verificar_permissoes_all($modulos, $_POST['id']);

			$_POST['senha_atual'] = isset($_POST['senha_atual']) ? $_POST['senha_atual'] : '';
			$mysql->colunas = 'id';
			$mysql->prepare = array($_POST['id'], md5($_POST['senha_atual']));
			$mysql->filtro = " WHERE `id` = ? AND `senha` = ? ";
			$cadastro = $mysql->read_unico($modulos->modulo);

			if(!isset($cadastro->id) and $verificar_senha_atual){
				$arr['erro'][] = 'Senha Atual informada não confere!';
			} elseif(!isset($_POST['senha']) or !$_POST['senha']){
				$arr['erro'][] = 'Preencha o campo: Senha';
			} elseif($_POST['senha'] != $_POST['c_senha']){
				$arr['erro'][] = 'O campo Senha não está conferindo com o campo de confirmação!';
			}

			if(!isset($arr['erro'])){
				$mysql->logs_caminho = '../../';
				$mysql->prepare = array($_POST['id']);
				$mysql->filtro = " WHERE `id` = ? ";
				$mysql->campo['senha'] = md5($_POST['senha']);
				$mysql->update($modulos->modulo);
				$arr['alert'] = 1;
				$arr['evento'] = '$(".fundoo").trigger("click");';
			}


		} else {

			$arr['title'] = 'Alterar Senha';
			$arr['html']  = '<form id="alterarSenha" method="post" action="'.$_SERVER['SCRIPT_NAME'].'"> ';
				if($verificar_senha_atual){
					$arr['html'] .= '<div class="linha mb10">
										<b class="db mb5">Senha Atual:</b>
										<input type="password" name="senha_atual" class="w200 design" required >
									</div> ';
				}
				$arr['html'] .= '<div class="linha mb10">
									<b class="db mb5">Nova Senha:</b>
									<input type="password" name="senha" class="w200 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">Confirmar Senha:</b>
									<input type="password" name="c_senha" class="w200 design" required >
								</div>
								<input type="hidden" name="id" value="'.$_POST['id'].'">
								<input type="hidden" name="modulos" value="'.$_POST['modulos'].'">
								<input type="hidden" name="gravar" value="1">
								<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Salvar</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('.A.'alterarSenha'.A.');</script> ';

		}

	$mysql->fim();
	echo json_encode($arr); 

?>