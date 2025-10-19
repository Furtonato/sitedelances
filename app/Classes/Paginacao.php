<?php

	class Paginacao extends Mysql {

		public $pags;

		public function pag($table){
			
			global $dados;
			$colunas = $this->colunas;
			$filtro = $this->filtro;
			$prepare = $this->prepare;

			$_GET['pag'] = isset($_GET['pag']) ? $_GET['pag'] : 0;
			$item_atual = $_GET['pag'] ? $_GET['pag']*$this->pags : 0;

			// Busca
			if(isset($_GET['gets1']) and !$_GET['gets1'] and isset($_POST['pesq']) and !$_POST['pesq'])
				unset($_SESSION['pesq_session_filtro']);

			// Itens Por Pagina
			$this->colunas = $colunas;
			$this->prepare = $prepare;
			$this->filtro = $filtro." LIMIT ".$item_atual.", ".$this->pags." ";
			$itens_pag = count($this->read($table));

			// Numero de Paginas e Limite de Paginas Vizualizadas
			$this->colunas = 'COUNT(id)';
			$this->prepare = $prepare;
			$this->filtro = $filtro;
			$itens_pag = $this->read($table);
			$itens_pag = current($itens_pag[0]);

			$num_final_pag = ($itens_pag/$this->pags);
			$num_final_pag = explode('.', $num_final_pag);
			if(!isset($num_final_pag[1])) $num_final_pag[0]--;
			if($_GET['pag'] > 12 and $num_final_pag[0] > 9) $y=$_GET['pag']-12; else $y=0;
			$n_paginas = 0;

			// URL q será passada para proxima pagina
			$url_01 = explode('?pag=', $_SERVER ['REQUEST_URI']);
			$url_02 = explode('&pag=', $url_01[0]);
			$url_03 = explode('?', $url_02[0]);
			$url_atual = isset($url_03[1]) ? $url_02[0].'&' : $url_02[0].'?';
			

			// PAGINACAO
				$dados['pagg'] = '<div class="clear"></div>';
				if(!$itens_pag){
					$dados['pagg'] .= '<div class="p40 tac fz16">Nenhum item encontrado...</div>';

				} else {
					$dados['pagg'] .= '<div class="pagg">';
						$dados['pagg'] .= '<a '.iff($_GET['pag'], 'href="'.$url_atual.'pag=0" class="', 'class="ativo').' hover1 hoverr3"  ><<</a>';
						for($y; $y<=$num_final_pag[0]; $y++){
							if($n_paginas <= 9){ $n_paginas++;
								$dados['pagg'] .= '<a '.iff($_GET['pag']!=$y, 'href="'.$url_atual.'pag='.$y.'" class="', 'class="ativo').' hover1 hoverr3"  >'.($y+1).'</a>';
							}
						}
						$dados['pagg'] .= '<a '.iff($_GET['pag']!=($y-1), 'href="'.$url_atual.'pag='.($y-1).'" class="', 'class="ativo').' hover1 hoverr3" >>></a>';
					$dados['pagg'] .= '</div>';
				}

			$this->colunas = $colunas;
			$this->prepare = $prepare;
			$this->filtro = $filtro." LIMIT ".$item_atual.", ".$this->pags." ";
			return($this->read($table));
		}

	}