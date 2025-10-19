<?php ob_start();
	require_once "../../system/conecta.php";
	require_once "../../system/mysql.php";
	require_once "../../app/Funcoes/funcoes.php";
?>

<?php
	$mysql = new Mysql();

		$table = 'cadastro';

		$ex = explode('574839', base64_decode($_GET['q']));
		$ex = explode('847382', $ex[1]);
		$mysql->colunas = 'id, senha';
		$mysql->prepare = array($ex[0]);
		$mysql->filtro = " where `id` = ? ";
		$consulta = $mysql->read_unico($table);

		if(!isset($consulta->id)){
			echo '<script language="javascript" type="text/javascript">
					alert("Este úsuario não existe!");
				  </script> ';

		


		} elseif(isset($_POST['gravar']) and $_POST['gravar']){
			// Verificando erros
			if(!$_POST['senha']){
				$erro = 'Preencha o campo: Senha';
			} elseif($_POST['senha'] != $_POST['c_senha']){
				$erro = 'O campo Senha não está conferindo com o campo de Confirmação de Senha!';
			} elseif(md5($_POST['senha']) == $consulta->senha){
				$erro = 'Você não pode Cadastrar a mesma Senha Atual!';
			} else if(!isset($consulta->id)){
				$erro = 'Este Usuário não Existe!';
			}

			if(!isset($erro)){
				$mysql->prepare = array($consulta->id);
				$mysql->filtro = " where `id` = ? ";
				$mysql->campo['senha'] = md5($_POST['senha']);
				$mysql->update($table);
				echo '<script>alert("Operação Realizada com Sucesso!");</script> ';
				echo '<script>window.location.href="'.DIR.'/home/";</script> ';
				exit();

			} else {
				echo '<script>alert("'.$erro.'");</script> ';
				echo '<script>window.location.href="'.$_SERVER['SCRIPT_NAME'].'?q='.$_GET['q'].'";</script> ';
			}	
		


		} else { ?>
			<link rel="stylesheet" type="text/css" href="<?=DIR?>/css/css.php" />
			<link rel="stylesheet" type="text/css" href="<?=DIR?>/css/css.css" />
			<link rel="stylesheet" type="text/css" href="<?=DIR?>/css/style.css" />
			<script type="text/javascript" src="<?=DIR?>/plugins/Jquery/jquery-1.11.3.min.js"></script>
			<script type="text/javascript" src="<?=DIR?>/plugins/Jquery/jquery.form.js"></script>
			<script type="text/javascript" src="<?=DIR?>/js/eventos_all.js"></script>
			<script type="text/javascript" src="<?=DIR?>/js/eventos.js"></script>
			<script> var HOST = '<?=$_SERVER["HTTP_HOST"]?>'; var DIR = '<?=DIR?>'; var ADMIN = '<?=ADMIN?>'; var LUGAR = '<?=LUGAR?>'; var $_SESSION = new Array(); </script>

			<div class="events_externos">
				<div class="boxs">
					<div class="mt10" style="top: 0; margin-left: -115px;">
						<h3> Alterar Senha </h3>

						<div class="content">
							<form id="alterarSenha" method="post" action="<?=$_SERVER['SCRIPT_NAME']?>?q=<?=$_GET['q']?>">
								<div class="linha mb10">
									<b class="db mb5">Nova Senha:</b>
									<input type="password" name="senha" class="w200 h30 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">Confirmar Senha:</b>
									<input type="password" name="c_senha" class="w200 h30 design" required >
								</div>
								<input type="hidden" name="gravar" value="1">
								<button class="botao flr fwb"> Salvar</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('alterarSenha');</script>
						</div>

						<div class="clear"></div>
					</div>
				</div>
			</div>

		<?php  } ?>
