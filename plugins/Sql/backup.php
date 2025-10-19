<?
	if($_SERVER['HTTP_HOST'] != 'localhost:4000'){

		$back = fopen('../../plugins/Sql/menu_admin/'.$table.' '.date('Y-m-d-H-i-s').'.sql','w');

		$sql = '';
		$table = 'menu_admin';
		$mysql->json = 1;

			// Gravando Colunas
			$colunas = $mysql->colunas($table);
			foreach ($colunas as $k1 => $v1){
				$criar = 'Create Table'; // Nome o elemento da array (obj) de exportacao do banco;
				$sql .= $v1->$criar.";\n\n";

				// Gravando Dados
				$mysql->filtro = "";
				$tables = $mysql->read($table);
				if($tables){
					$mysql->filtro = " limit 0,1 ";
					$tables_colunas = $mysql->read($table);
					foreach ($tables_colunas as $key => $value) {
						$sql .= "INSERT INTO `$table` (`";
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
							if($table!='menu_admin' and $table!='z_txt'){
								$itens = str_replace("'", '&#39;', $itens);
							} else {
								$itens = str_replace("'", "''", $itens);
							}
							$sql .= implode("', '", $itens);
						}
						$sql .= ($x!=count($tables)) ? "'),\n" : '';
					}
					$sql .= "');\n";
				}

			}
			$sql .= "\n";
			$sql .= "-- --------------------------------------------------------";
			$sql .= "\n\n";


			$mysql->json = 0;

		fwrite($back, utf8_encode($sql));
		fclose($back); 

	}

?>