<?
	require "../../../../../system/conecta.php";
	require "../../../../../system/mysql.php";
	require "../../../../../app/Funcoes/funcoes.php";
	require "sessao.php";

	$mysql = new Mysql();
	$tables_all = $mysql->tables();
	foreach ($tables_all as $k => $v){
		if(isset($v[0]) and $v[0]){
			$mysql->delete_table($v[0]);
		}
	}
	
	header("Location: index.php");

?>