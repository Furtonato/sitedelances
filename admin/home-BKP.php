<?php
	require_once "../system/conecta.php";
	if(!isset($pagina_login)){ // nao mostrar na pagina de login
	    require_once '../system/mysql.php';
	    require_once '../app/Funcoes/funcoes.php';
		//require_once "../plugins/Tng/tng/tNG.inc.php";
	    require_once '../app/Funcoes/funcoesAdmin.php';

	    verificar_sessao();
	    require_once "views/Htmls/index.php";
	}

	$version = '2.0';

    echo
	'<!DOCTYPE html PUBLIC "-//W3C	//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> '.
	'<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br"> '.
	'<head> '.
		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> '.
		'<title>'.iff(LUGAR=='admin', 'Administração do Site', 'Área de '.ucfirst(LUGAR)).'</title> '.
		'<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"> '.
		'<link rel="shortcut icon" href="'.DIR.'/web/img/ico.ico" type="image/x-icon" /> '.

		'<link rel="stylesheet" type="text/css" href="'.DIR.'/plugins/Fonts/Fonts_Fa/css/font-awesome.min.css" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/plugins/Fonts/Fonts_Icon/simple-line-icons.css" /> '.

		'<link rel="stylesheet" type="text/css" href="'.DIR.'/plugins/Jquery/Datatables/css/dataTable.css" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/plugins/Jquery/Select2/css/select2.css" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/plugins/Jquery/UI/css/ui.css" /> '.

		'<link rel="stylesheet" type="text/css" media="screen" href="'.DIR.'/css/css.php" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/css/efeitos.css?version='.$version.'" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/css/resp.css?version='.$version.'" /> '.
		'<link rel="stylesheet" type="text/css" href="'.DIR.'/css/css.css?version='.$version.'" /> '.
		'<link rel="stylesheet/less" type="text/css" href="'.DIR.'/'.ADMIN.'/css/style.css?version='.$version.'" /> '.

		//boxs = pirobox
		//boxxs = sortablebox (box moveis)
		//boxx = menu hover ou inserir_box

		'<script> var HOST = "'.$_SERVER["HTTP_HOST"].'"; var DIR = "'.DIR.'"; var ADMIN = "'.ADMIN.'"; var LUGAR = "'.LUGAR.'";  var $_GET = new Array();  var $_SESSION = new Array(); </script> '.
	'</head> '.

	'<body id="admin'.iff(LUGAR!='admin', '_'.LUGAR).'"> '.
 
		'<section class="admin"> '.

			'<header> '.
				$html_header.
			'</header> '.

			'<article class="principal"> '.

				'<aside class="menu dn_700"> '.
					$html_menu_left.
				'</aside> '.

		        '<aside class="views m0_700"> '.
	    			'<section class="conteudo"> '.
	    				'<article class="lista"> ';
							if(isset($_GET['pg']) and $_GET['pg']){
								$mysql->prepare = array($_GET['pg']);
								$mysql->filtro = " WHERE `id` = ? AND `id` ";
								$modulos = $mysql->read_unico('menu_admin');
								if(isset($modulos->id)){
									$gets = '';
									foreach ($_GET as $key => $value) {
										if($key!='lang')
											$gets[] = $key.'='.$value;
									}
									$views = "<script> ".VIEWS."('".$modulos->id."', ".$modulos->tipo_modulo.", '".$modulos->gets.implode('&', $gets)."'); </script> ";
								}
							}
							if(!isset($views))
    							include '../'.ADMIN.'/views/default.php';
    					echo
	    				'</article> '.
	    				'<article class="events"></article> '.
	    			'</section> '.
				'</aside> '.
		        '<div class="clear"></div> '.

			'</article> '.

			'<footer> '.
				$html_footer.
			'</footer> '.
	    
		'</section> '.

	// Javascript
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/jquery-1.11.3.min.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/jquery.form.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/jquery-ui.min.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/less-1.7.5.min.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/bootstrap(popover-tooltip).min.js"></script> '.

	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Datatables/js/jquery.dataTables.min.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Plugins/ImageLightBox/js/imagelightbox.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Mascara/js/jquery.price_format.1.3.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Mascara/js/jquery.mask.min.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Mascara/js/mascara_events.js"></script> '.
	'<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Select2/js/select2.full.js"></script> '.

	'<script type="text/javascript" src="'.DIR.'/'.ADMIN.'/js/eventos.js?version='.$version.'"></script> '.
	'<script type="text/javascript" src="'.DIR.'/js/eventos_all.js?version='.$version.'"></script> '.

	'<script type="text/javascript" src="'.DIR.'/plugins/Ckeditor/ckeditor/ckeditor.js"></script> '.

	'<script> iniciar_events_admin('.A.A.'); </script> '.

	'<script> '.
		// Temas
		'if(lerCookie('.A.'temas'.A.')==undefined) gravarCookie('.A.'temas'.A.', '.A.'azul'.A.', 365); '.
		'document.write('.A.'<link id="style_color" href="'.DIR.'/'.ADMIN.'/css/cores/'.A.'+lerCookie('.A.'temas'.A.')+'.A.'.css" rel="stylesheet" type="text/css">'.A.'); '.
	'</script> ';

		// Pagina Atual
		echo isset($views) ? $views : '';
		if(BANCOOOO!=1) header("Location: /erro.php");


		// Passando Get por Javascript
		$gets = '?';
		foreach ($_GET as $key => $value)
			$gets .= '&'.$key.'='.$value;
		echo "<script> var GETS = '".$gets."'; </script>";

		// Fazer Backup do banco da dados
		/*
		if( (!isset($_SESSION['backup']) or $_SESSION['backup'] != date('W')) and $_SERVER['HTTP_HOST'] != 'localhost:4000'){
			$mysql->colunas = 'foto';
			$mysql->filtro = " WHERE `tipo` = 'backup' ";
			$backup = $mysql->read_unico("configs");				

			if($backup->foto != date('W')){
	            include '../plugins/Sql/backup_all.php';
				unset($mysql->campo);
				$mysql->filtro = " WHERE `tipo` = 'backup' ";
				$mysql->campo['foto'] = date('W');
				$mysql->update('configs');
			}
			$_SESSION['backup'] = date('W');
		}
		*/


		// STYLES PARA O SISTEMA
			if( !(isset($_SESSION['x_admin']->id) AND ($_SESSION['x_admin']->id == 1 OR $_SESSION['x_admin']->id == 2)) ){
				echo ' <style> .lista_2 .acoes .botao.novo, .ui-dialog.pg_2 .acoes .botao.salvar, .ui-dialog.pg_2 .acoes .botao.salvar_novo { display: none; } </style> ';
			}


			// NOVO
			// NOVO

		// STYLES PARA O SISTEMA

	echo
	'</body> '.
	'</html> ';

?>