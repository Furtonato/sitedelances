<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();


		if(isset($_POST['gravar']) and $_POST['gravar']){

			$mysql->prepare = array($_SESSION['x_site']->id, md5($_POST['senha_atual']));
			$mysql->filtro = " where `id` = ? AND `senha` = ? ";
			$cadastro = $mysql->read_unico('cadastro');

			if(!isset($cadastro->id)){
				$arr['erro'][] = 'Senha Atual informada não confere!';
			} elseif(!isset($_POST['senha']) or !$_POST['senha']){
				$arr['erro'][] = 'Preencha o campo: Senha';
			} elseif($_POST['senha'] != $_POST['c_senha']){
				$arr['erro'][] = 'O campo Senha não está conferindo com o campo de confirmação!';
			}

			if(!isset($arr['erro'])){
				$mysql->prepare = array($_SESSION['x_site']->id);
				$mysql->filtro = " where `id` = ? ";
				$mysql->campo['senha'] = md5($_POST['senha']);
				$mysql->update('cadastro');
				$arr['alert'] = 1;
				$arr['evento'] = '$(".fundoo").trigger("click");';
			}


		} else {

			$arr['title'] = lang('Alterar Senha');
			$arr['html']  = '<form id="alterarSenha" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">
								<div class="linha mb10">
									<b class="db mb5">'.lang('Senha Atual').':</b>
									<input type="password" name="senha_atual" class="w200 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">'.lang('Nova Senha').':</b>
									<input type="password" name="senha" class="w200 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">'.lang('Confirmar Senha').':</b>
									<input type="password" name="c_senha" class="w200 design" required >
								</div>
								<input type="hidden" name="gravar" value="1">
								<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> '.lang('Salvar').'</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('.A.'alterarSenha'.A.');</script> ';

		}

	echo json_encode($arr); 

?>