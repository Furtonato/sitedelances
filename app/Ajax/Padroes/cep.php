<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";

	$mysql = new Mysql();
	$arr = array('erro'=>1, 'rua'=>'', 'bairros'=>'', 'cidades'=>'', 'estados'=>'');

		$cep = isset($_GET['cep']) ? $_GET['cep'] : $_POST['cep'];

		$json = json_decode(file_get_contents('../../../plugins/Json/localidades/cep/'.$cep[0].'0000000.json'));
		foreach ($json as $key => $value) {
			if(isset($value->$cep)){
				$arr = $value->$cep;
			}
		}

	echo json_encode($arr);

?>