<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['html'] = '';


		if(isset($_POST['id']) and $_POST['id']){

			$mysql->filtro = " where status = '1' and tipo = 'pagamentos' ";
			$pagamentos = $mysql->read_unico('configs');

			$data['email'] = $pagamentos->pagseguro_email;
			$data['token'] = $pagamentos->pagseguro_token;

			$curl = curl_init(PAGSEGURO_URL.'/v2/sessions/');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));				 
			$xml = curl_exec($curl);
			curl_close($curl);

			if($xml == 'Unauthorized'){
				$arr['alert'] = 'Email do PagSeguro Não Autorizado!';

			} else {
				$xml= simplexml_load_string($xml);
				if(isset($xml->id))
					$arr['session'] = (string)$xml->id;
			}

		}

	echo json_encode($arr); 

?>