<?php ob_start(); if(!isset($no_session_start)) session_start();

	echo 'Sessao da Pagina Anterior: '.$_SESSION['atual'];

	echo '<br>';

	echo 'Sessao da Pagina Atual: '.$_SESSION['atual'] = date('d/m/Y - H:i:s');

?>