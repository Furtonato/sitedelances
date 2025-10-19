<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);


ob_start();
require_once '../system/conecta.php';
require_once '../system/mysql.php';
require_once '../app/Funcoes/funcoes.php';
require_once '../' . ADMIN . '/app/Classes/Login.php';
require_once '../app/Classes/Email.php';

$mysql = new Mysql();
$Login = new Login();
$Login->Esqueci_senha();
$Login->Logout();

// Eliminar Bug de LUGAR == site
if (LUGAR == 'site') {
	echo '<script>window.parent.location="' . DIR . '/admin/";</script>';
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= LUGAR == 'admin' ? 'Administração do Site' : 'Área de ' . ucfirst(LUGAR) ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<link rel="shortcut icon" href="<?= DIR ?>/web/img/ico.ico" type="image/x-icon" />

	<link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Fonts/Fonts_Fa/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Fonts/Fonts_Icon/simple-line-icons.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="<?= DIR ?>/css/css.php" />
	<link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/css.css" />
	<link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/resp.css" />
	<link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/efeitos.css" />
	<link rel="stylesheet/less" type="text/css" href="<?= DIR ?>/<?= ADMIN ?>/css/style.css" />
</head>

<body class="login_pd back_000">

	<?php if (!(isset($_GET['q']) and $_GET['q'])) { ?>
		<div class="w520 w-a_500 pt100 pb50 m-a">
			<div class="h50 dn_700"></div>
			<h1 class="pt25 pb18 back_2FACD8">
				<p class="cor_fff tac fz28 ttu ts"><?= LUGAR == 'admin' ? 'Administração do Site' : 'Área de ' . ucfirst(LUGAR) ?></p>
			</h1>

			<form action="" method="post">
				<div class="pt30 pl25 pr25 pb10 back_F8F8F8">
					<label class="db mb16 bd_E6E6E6">
						<i class="fa fa-user w43 h47 posa pt15 pl13 pr13 fz18 bdr_E6E6E6"></i>
						<div class="calc43">
							<input type="text" name="login" id="login" class="design w100p h47 pl10 pr10 ml42 bdt0 bdb0 bdr0 bdl_E6E6E6 back_fff" placeholder="<?= LUGAR == 'admin' ? 'Login' : 'Email' ?>" />
						</div>
					</label>

					<label class="db mb16 bd_E6E6E6">
						<i class="fa fa-key w43 h47 posa pt15 pl13 pr13 fz18 bdr_E6E6E6"></i>
						<div class="calc42">
							<input type="password" name="senha" id="senha" class="design w100p h47 pl10 pr10 ml42 bdt0 bdb0 bdr0 back_fff" placeholder="Senha" />
						</div>
					</label>
				</div>

				<div class="p20 tar back_F1F1F1">
					<button type="button" class="botao p15 pl20 pr20 mr10 fz11 ttu cor_fff hoverr br5 back_9E9E9E" onclick="boxs('esqueci_senha');"> Esqueci minha senha? </button>
					<button type="submit" class="botao p15 pl20 pr20 fz11 ttu cor_fff hoverr br5 back_2EADD4"> Entrar </button>
					<div class="clear"></div>
				</div>
			</form>
		</div>

	<?php } else { ?>
		<div class="w420 w-a_400 pt100 pb50 m-a">
			<div class="h50 dn_700"></div>
			<h1 class="pt25 pb18 back_2FACD8">
				<p class="cor_fff tac fz28 ttu ts">Alterar Senha</p>
			</h1>

			<form action="" method="post">
				<div class="pt30 pl25 pr25 pb10 back_F8F8F8">
					<label class="db mb16">
						<input type="password" name="senha" id="senha" class="design w100p h47 pl10 pr10 bd_E6E6E6 back_fff" placeholder="Nova Senha*" />
					</label>

					<label class="db mb16">
						<input type="password" name="c_senha" id="c_senha" class="design w100p h47 pl10 pr10 bd_E6E6E6 back_fff" placeholder="Confirmar Senha*" />
					</label>
				</div>

				<div class="p20 tar back_F1F1F1">
					<button type="submit" class="botao fz11 ttu cor_fff hoverr br5 back_2EADD4"> Salvar </button>
					<div class="clear"></div>
				</div>
			</form>
		</div>
	<?php  } ?>

	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery.form.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/less-1.7.5.min.js"></script>

	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/jquery.price_format.1.3.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/mascara_events.js"></script>
	<script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Select2/js/select2.full.js"></script>

	<script type="text/javascript" src="<?= DIR ?>/js/eventos_all.js"></script>
	<script>
		var HOST = '<?= $_SERVER["HTTP_HOST"] ?>';
		var DIR = '<?= DIR ?>';
		var ADMIN = '<?= ADMIN ?>';
		var LUGAR = '<?= LUGAR ?>';
		var $_SESSION = new Array();
	</script>
	<script>
		criar_css();
	</script>

	<?php  if (isset($_GET['error']) and $_GET['error']) { ?>
		<div class="events_externos">
			<div class="alerts">
				<div class="acao_0 alert dbi">
					<p> <?= $_GET['error'] ?> </p>
				</div>
			</div>
		</div>
	<?php  } ?>
</body>

</html>