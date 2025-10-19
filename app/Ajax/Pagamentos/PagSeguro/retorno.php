<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Classes/Email.php";

	ini_set('display_errors', 1);

	$mysql = new Mysql();

	//$_POST['notificationType'] = 'transaction';
    //$_POST['notificationCode'] = '70FDA4-3741DC41DC73-244443DFA51F-1F2058';

	if(isset($_GET['code']) AND $_GET['code']){
		$_POST['notificationCode'] = $_GET['code'];
	}

	if(isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction'){

		$mysql->filtro = " where tipo = 'pagamentos' ";
		$conta = $mysql->read_unico("configs");

	    $email = $conta->pagseguro_email;
	    $token = $conta->pagseguro_token;

	    $url = "https://ws.pagseguro.uol.com.br/v2/transactions/notifications/".$_POST['notificationCode']."?email=".$email."&token=".$token;
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $transaction = curl_exec($curl);
	    curl_close($curl);


	    if($transaction and $transaction != 'Unauthorized'){
		    $xml = simplexml_load_string($transaction);

		    // Status
			$status = 0;
		    if($xml->status == 3) // Pago
		    	$status = 1;
		    elseif($xml->status == 7) // Cancelado
		    	$status = 2;


		    // Retorno
			$referencia = explode('-', $xml->reference);
			if($referencia[0] == 'pedidos'){
	            for ($i=0; $i <= 0; $i++) { 
	                $table = !$i ? 'pedidos' : 'pedidos'.$i;
	                $mysql->nao_existe = 1;
	                $mysql->prepare = array($referencia[1]);
					$mysql->filtro = " WHERE `id` = ? AND `ja_foi_pago` = 0 ";
					$pedidos = $mysql->read_unico($table);
					if(isset($pedidos->valor_total) and $xml->grossAmount == $pedidos->valor_total){
						$retorno_pagseguro = 1;
						include '../retorno_pedidos.php';
					}
				}
			}
			//echo '<pre>';
			//print_r($xml);


	    } else {
			//header("Location: ".DIR_C."/minha_conta/");
	        exit;
	    }

	} else {
		//header("Location: ".DIR_C."/minha_conta/");
    	exit;
	}


	$mysql->filtro = " where tipo = 'google_analytics' ";
	$google_analytics = $mysql->read_unico("configs");
	echo $google_analytics->valor;


?>