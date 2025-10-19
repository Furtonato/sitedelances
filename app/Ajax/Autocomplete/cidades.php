<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();
	$array = array();

	if(isset($_POST['pesq'])){
		$mysql->colunas = 'id, cidades';
		$mysql->filtro = " where `estados` = '".$_POST['estados']."' AND `cidades` REGEXP '".cod('busca', $_POST['pesq'])."' GROUP BY `cidades` ORDER BY `cidades` ASC ";
		$consulta = $mysql->read('vendas');

		foreach ($consulta as $key => $value) {
			$row_array['id'] = $value->id;
			$row_array['value'] = $value->cidades;
			//$row_array['value'] .= ' [id='.$value->id.']';
			array_push($array, $row_array);  
		}
	}

	echo json_encode($array);

?>