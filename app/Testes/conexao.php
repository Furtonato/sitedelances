<?php ob_start(); session_start();

	$localhost_config	= 'localhost';
	$nome_config	 	= 'root';
	$senha_config		= 'Seique741598';
	$banco_config		= 'leilao_bd';


	try {
		$db = new PDO('mysql:host='.$localhost_config.';dbname='.$banco_config, $nome_config, $senha_config);
	} catch (PDOException $e) {
		die("Ooooooo servidor esta fora do ar! Aguarde em alguns instantes o site voltara a funcionar!");
	}		
	$db->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );


	$conectando = mysql_connect($localhost_config ,$nome_config ,$senha_config); 
	if(!$conectando){
		echo "No";
	}else{
		echo 'Ok';
	}

?>