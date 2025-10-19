<?php

	class criarMysql extends Mysql {

		public $consulta = array();

		protected function conecta1(){

			global $localhost_config, $nome_config, $senha_config, $banco_config;
			$this->conecta($localhost_config, $nome_config, $senha_config, $banco_config);

		}


		// Criar Colunas
		public function criarTabelas($table, $itens=1){

			if($table != 'senha' and $table != 'fundo' and $table != 'mapa' and $table != 'mais_senhas'){

				$publicMysql = new publicMysql();
				$publicMysql->nao_existe = 1;
				$publicMysql->colunas = 'id';
				$publicMysql->filtro = " LIMIT 1 ";
				if($publicMysql->read($table) == "Tabela ".$table." nao existe"){
	
					$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
							  `id` int NOT NULL AUTO_INCREMENT,
							  `status` int NOT NULL DEFAULT '1',
							  `lang` int NOT NULL DEFAULT '1',
							  `nome` text NOT NULL,
							  `foto` text NOT NULL,
							  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  `dataup` datetime NOT NULL,
							  `ordem` int(3) unsigned zerofill NOT NULL DEFAULT '999',
							  PRIMARY KEY (`id`)	) ENGINE=InnoDB; ";
					$publicMysql->db->query($sql);

					if(preg_match('(1_cate)', $table)){
						$sql  = " ALTER TABLE  `{$table}` ADD  `tipo` INT NOT NULL; ";
						$sql .= " ALTER TABLE  `{$table}` ADD  `subcategorias` INT NOT NULL; ";
						$sql .= " ALTER TABLE  `{$table}` ADD  `vcategorias` text NOT NULL; ";
						$sql .= " ALTER TABLE  `{$table}` ADD  `star` INT NOT NULL; ";
						$publicMysql->db->query($sql);

						if($itens){
							$sql = "INSERT INTO `{$table}` (`id`, `nome`, `foto`, `dataup`) VALUES
										(1, 'Categorias 01', '01.jpg', '".date("c")."'),
										(2, 'Categorias 02', '02.jpg', '".date("c")."'),
										(3, 'Categorias 03', '03.jpg', '".date("c")."'); ";
							$publicMysql->db->query($sql);
						}

					} elseif($itens) {
						$sql = "INSERT INTO `{$table}` (`id`, `nome`, `foto`, `dataup`) VALUES
									(1, 'Item 01', '01.jpg', '".date("c")."'),
									(2, 'Item 02', '02.jpg', '".date("c")."'),
									(3, 'Item 03', '03.jpg', '".date("c")."'),
									(4, 'Item 04', '04.jpg', '".date("c")."'),
									(5, 'Item 05', '05.jpg', '".date("c")."'),
									(6, 'Item 06', '06.jpg', '".date("c")."'); ";
						$publicMysql->db->query($sql);
					}
	
				}

			}
		}




		// Criar Colunas
		public function criarColunas($table, $coluna, $tipo='text'){

			$variaveis = 'nome, select_text, clonar, c_senha, txt_editor, txt_editor1, txt_editor2, txt_editor3, txt_editor4, txt_editor5, txt_editor6, sem_foto, sem_multifotos, mapa, mais_fotos, mais_comentarios';
			$variaveis = str_replace(' ', '', $variaveis);
			$array_ex = explode(',', $variaveis);

			if(preg_match('(_hidden)', $coluna) or preg_match('(_button)', $coluna))
				$array_ex[] = $coluna;

			if(!in_array($coluna, $array_ex) and !preg_match('(_temp_)', $coluna)){

				$consulta = $this->read_unico($table);
				if($coluna and !array_key_exists($coluna, $this->read_unico($table)) AND isset($consulta->id)){
					$this->conecta1();
					$colunas = array('pago', 'categorias', 'subcategorias', 'star', 'lancamentos', 'promocao', 'mapa');
					$tipos = array('int');
					if(in_array($tipo, $tipos) or in_array($coluna, $colunas)){
						$tipo = 'int(11)';
					} elseif(preg_match('(data_)', $coluna)){
						$tipo = 'date';
					}
					$array = $this->db->query(" ALTER TABLE `".$table."` ADD `".$coluna."` ".$tipo." NOT NULL ");
				}

			}

		}


		// Criar colunas (Array)
		public function criarColunasArray($table, $post, $variaveis=''){
			foreach($post as $nome_get => $valor_get){
				if(!is_object($valor_get) or !is_array($valor_get)){
					if(!array_key_exists($nome_get, $this->consulta)){
						if($nome_get == 'cidades')
							$this->criarColunas($table, 'estados');	
						$this->criarColunas($table, $nome_get);
					}
				}
			}
		}

	}

?>