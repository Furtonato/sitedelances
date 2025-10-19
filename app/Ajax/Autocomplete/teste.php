<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();
	$array = array();

	if(isset($_GET['term'])){
		$mysql->filtro .= " where nome REGEXP '".cod('busca', $_GET['term'])."' ORDER BY `nome` ASC ";
		$consulta = $mysql->read('produtos');

		foreach ($consulta as $key => $value) {
			$row_array['id'] = $value->id;
			$row_array['value'] = $value->nome;
			//$row_array['value'] .= ' [id='.$value->id.']';
			array_push($array, $row_array);  
		}
	}

	echo json_encode($array);

?>