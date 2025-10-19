<?
	require "../../../../../system/conecta.php";
	require "../../../../../system/mysql.php";
	require "../../../../../app/Funcoes/funcoes.php";
	require "sessao.php";

	$back = fopen("back.sql","w"); 

	$sql = '';
	$mysql = new Mysql();
	$tables_all = $mysql->tables();
	foreach ($tables_all as $k => $v){
		if(isset($v[0]) and $v[0]){

			// Gravando Colunas
			$colunas = $mysql->colunas($v[0]);
			foreach ($colunas as $k1 => $v1){
				$criar = 'Create Table'; // Nome o elemento da array (obj) de exportacao do banco;
				$sql .= $v1->$criar.";\n\n";

				// Gravando Dados
				$mysql->filtro = "";
				$tables = $mysql->read($v[0]);
				if($tables){
					$mysql->filtro = " limit 0,1 ";
					$tables_colunas = $mysql->read($v[0]);
					foreach ($tables_colunas as $key => $value) {
						$sql .= "INSERT INTO `$v[0]` (`";
						$cols = array();
						foreach ($value as $key1 => $value1) {
							if($key1 != 'table')
								$cols[] = $key1;
						}
						$sql .= implode("`, `",$cols);
						$sql .= "`) VALUES \n";
					}
					$x=0;
					foreach ($tables as $key => $value) { $x++;
						$sql .= "('";
						if(isset($value) and $value){
							$itens = array();
							foreach ($value as $key1 => $value1) {
								if($key1 != 'table')
									$itens[] = $value1;
							}
							$sql .= implode("', '",str_replace("'", '&#39;', $itens)); 
						}
						$sql .= ($x!=count($tables)) ? "'),\n" : '';
					}
					$sql .= "');\n";
				}

			}
			$sql .= "\n";
			$sql .= "-- --------------------------------------------------------";
			$sql .= "\n\n";
		}
	}
	fwrite($back,$sql);
	fclose($back); 
	
	header("Location: index.php");

?>