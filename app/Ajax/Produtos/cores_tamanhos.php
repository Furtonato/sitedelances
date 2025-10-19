<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['produtoss_cores'] = '';
	$arr['produtoss_tamanhos'] = '';


		$mysql->prepare = array($_POST['id']);
		$mysql->filtro = " WHERE ".STATUS." AND `id` = ? ORDER BY ".ORDER." ";
		$item = $mysql->read_unico('produtos');
		$estoque = $item->estoque;
		$codigo = $item->codigo;
		$foto = $item->foto;
		$preco = $item->preco;
		$preco1 = $item->preco1;
		$preco2 = $item->preco2;
		$parcelas = $item->parcelas;



		/* CORES E TAMANHOS */
		$cores_tamanhos = ex($item->produtos_cores_tamanhos);
		$mysql->coluna = 'id';
		$existe = $mysql->existe('produtos_cores_tamanhos');
		if($existe AND $cores_tamanhos){

			$mysql->coluna = 'id';
			$mysql->prepare = array($item->id);
			$mysql->filtro = " WHERE ".STATUS." AND `produtos` = ? AND `id` IN (".implode(',', $cores_tamanhos).") ORDER BY ".ORDER." ";
			$produtos_cores_tamanhos = $mysql->read('produtos_cores_tamanhos');
			if($produtos_cores_tamanhos){

				// Cores
				$mysql->coluna = 'id';
				$mysql->prepare = array($item->id);
				$mysql->filtro = " WHERE ".STATUS." AND `produtos` = ? AND `id` IN (".implode(',', $cores_tamanhos).") GROUP BY `produtos_cores` ORDER BY ".ORDER." ";
				$produtos_cores_tamanhos = $mysql->read('produtos_cores_tamanhos');

				$arr['produtoss_cores'] .= '<b class="db pb5">Cores Disponíveis:</b> <ul> ';
				foreach ($produtos_cores_tamanhos as $k => $v) {

					$mysql->coluna = 'id, nome, fotos, color';
					$mysql->prepare = array($v->produtos_cores);
					$mysql->filtro = " WHERE ".STATUS." AND `id` = ? ORDER BY ".ORDER." ";
					$produtos_cores = $mysql->read('produtos_cores');
					if(!isset($cor_atual)) $cor_atual = $_POST['cor'] ? $_POST['cor'] : $produtos_cores[0]->id;

					foreach ($produtos_cores as $key => $value) {
						$arr['produtoss_cores'] .= '<li> ';
							$arr['produtoss_cores'] .= '<a onclick="produtoss_cores_tamanhos('.$item->id.', '.$value->id.')" class="fll mr5 mb5 '.iff($cor_atual==$value->id, 'bd_000', 'bd_aaa bd_hover_666 op5 op7_hover').'"> ';
								$arr['produtoss_cores'] .= '<div class="w20 h20 m1" style="background:'.iff($value->foto, 'url('.DIR.'/web/fotos/'.$value->foto.')', $value->color).'" title="'.$value->nome.'"></div> ';
							$arr['produtoss_cores'] .= '</a> ';
						$arr['produtoss_cores'] .= '</li> ';
					}
				}
				$arr['produtoss_cores'] .= '<div class="clear"></div> </ul> <div class="clear"></div> ';
				$arr['produtoss_tamanhos'] .= '<input type="hidden" name="produtoss_cor" id="produtoss_cor" value="'.$cor_atual.'"> ';


				// Tamanhos
				$mysql->coluna = 'id';
				$mysql->prepare = array($item->id, $cor_atual);
				$mysql->filtro = " WHERE ".STATUS." AND `produtos` = ? AND `produtos_cores` = ? AND `id` IN (".implode(',', $cores_tamanhos).") GROUP BY `produtos_tamanhos` ORDER BY ".ORDER." ";
				$produtos_cores_tamanhos = $mysql->read('produtos_cores_tamanhos');

				$arr['produtoss_tamanhos'] .= '<b class="db pb5">Tamanhos Disponíveis:</b> <ul> ';
				foreach ($produtos_cores_tamanhos as $k => $v) {

					$mysql->coluna = 'id, nome';
					$mysql->prepare = array($v->produtos_tamanhos);
					$mysql->filtro = " WHERE ".STATUS." AND `id` = ? ORDER BY ".ORDER." ";
					$produtos_tamanhos = $mysql->read('produtos_tamanhos');
					if(!isset($tamanho_atual)) $tamanho_atual = $_POST['tamanho'] ? $_POST['tamanho'] : $produtos_tamanhos[0]->id;

					foreach ($produtos_tamanhos as $key => $value) {
						$arr['produtoss_tamanhos'] .= '<li> ';
							$arr['produtoss_tamanhos'] .= '<a onclick="produtoss_cores_tamanhos('.$item->id.', '.$cor_atual.', '.$value->id.')" class="posr fll mr5 mb5 pt2 pb2 pl5 pr5 fwb '.iff($tamanho_atual==$value->id, 'bd_000', 'bd_aaa bd_hover_666 op5 op7_hover back_fff').'"> ';
								$arr['produtoss_tamanhos'] .= $value->nome;
								if(!($v->estoque>0))
									$arr['produtoss_tamanhos'] .= '<div class="posa t0 r0 mt-7 mr-5 c_flex jc"><i class="fz14 fa fa-times c_vermelho"></i></div>';
							$arr['produtoss_tamanhos'] .= '</a> ';
						$arr['produtoss_tamanhos'] .= '</li> ';
						$arr['produtoss_tamanhos'] .= '</li> ';
					}
				}
				$arr['produtoss_tamanhos'] .= '<div class="clear"></div> </ul> <div class="clear"></div> ';
				$arr['produtoss_tamanhos'] .= '<input type="hidden" name="produtoss_tamanho" id="produtoss_tamanho" value="'.$tamanho_atual.'"> ';


				// Pegando Valores
				$mysql->prepare = array($item->id, $cor_atual, $tamanho_atual);
				$mysql->filtro = " WHERE ".STATUS." AND `produtos` = ? AND `produtos_cores` = ? AND `produtos_tamanhos` = ? ORDER BY ".ORDER." ";
				$produtos_cores_tamanhos = $mysql->read('produtos_cores_tamanhos');
				foreach ($produtos_cores_tamanhos as $key => $value) {
					$estoque = $value->estoque;
					$codigo = $value->codigo ? $value->codigo : $codigo;
					$foto = $value->foto ? $value->foto : $foto;
					$preco = $value->preco>0 ? $value->preco : $preco;
					$preco1 = $value->preco1>0 ? $value->preco1 : $preco1;
					$preco2 = ($value->parcelas>0 and $value->preco2>0) ? $value->preco2 : 0;
					$parcelas = ($value->parcelas>0 and $value->preco2>0) ? $value->parcelas : 0;

					$arr['cor_atual'] = $cor_atual;
					$arr['tamanho_atual'] = $tamanho_atual;
				}

			}

			// Dados
			$arr['produtoss_estoque'] = $estoque;
			$arr['produtoss_foto'] = DIR.'/web/fotos/'.$foto;
			$arr['produtoss_codigo'] = $codigo ? $codigo : '';
			$arr['produtoss_preco'] = $preco>0 ? preco($preco, 1) : '';
			$arr['produtoss_preco1'] = $preco1>0 ? preco($preco1, 1) : '';
			$arr['produtoss_parcelas'] = ($parcelas>0 and $preco2>0) ? $parcelas.' x '.preco($preco2, 1) : '';

		}
		/* CORES E TAMANHOS */


	echo json_encode($arr);

?>