<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = '';
	$arr['alert_boxs'] = 0;
	$arr['topo'] = '';

		if($_POST['id']){

			$id = $_POST['id'];
			$qtd = $_POST['qtd']*1;

			$mysql->prepare = array($id);
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' AND `id` = ? ORDER BY `id` DESC ";
			$produtos = $mysql->read_unico('produtos');
			$estoque = $produtos->estoque;

			// Estoque de Cores e Tamanhos
			if($_POST['cores'] and $_POST['tamanhos']){
				$return = verificar_estoque_cores_tamanhos($id, $_POST['cores'], $_POST['tamanhos']);
				if(isset($return['estoque'])){
					$estoque = $return['estoque'];
				} elseif(isset($return['null'])){
					$_POST['cores'] = '';
					$_POST['tamanhos'] = '';
				}
			}

			// Estoque de Opcoes
			for ($i=1; $i<=5; $i++) { 
				$opcao = 'opcoes'.$i;
				if($_POST[$opcao]){
					$return = verificar_estoque_opcoes($id, $_POST[$opcao], $i);
					if(isset($return['estoque'])){
						$estoque = $return['estoque'];
					} elseif(isset($return['null'])){
						$_POST[$opcao] = '';
					}
				}
			}


			$arr['estoque'] = $estoque;
			if($qtd and $estoque >= $qtd){

				$ref = $_POST['id'].'_'.$_POST['cores'].'_'.$_POST['tamanhos'].'_'.$_POST['opcoes1'].'_'.$_POST['opcoes2'].'_'.$_POST['opcoes3'].'_'.$_POST['opcoes4'].'_'.$_POST['opcoes5'];
				$arr['ref'] = $ref;

				$arr['item_all'] = '';
				if(!isset($_SESSION['carrinho']['itens'][$id][$ref]->qtd)){
					$mysql->filtro = " where id = '".$id."' ";
					$item = $mysql->read_unico('produtos');

					/*
					$arr['item_all'] .= '<li class="posr mb10 carrinho_all_item_'.$ref.'">';
						$arr['item_all'] .= '<a onclick="carrinho_deletar_item('.A.$ref.A.')" class="delete-item"><i class="fa fa-times"></i></a> ';
						$arr['item_all'] .= '<div class="fll mr10"><a href="'.url('produto', $item).'"><img src="'.DIR.'/web/fotos/'.$item->foto.'" widht="50" height="50"></a></div> ';
						$arr['item_all'] .= '<div class="fwb"><a href="'.url('produto', $item).'">'.$item->nome.'</a></div> ';
						$arr['item_all'] .= '<div class=""><a href="'.url('produto', $item).'">'.preco($item->preco, 1).'</div> ';
						$arr['item_all'] .= '<div class="clear"></div> ';
					$arr['item_all'] .= '</li>';
					*/
				}


				$_SESSION['carrinho']['itens'][$id][$ref]->qtd = $qtd;
				$_SESSION['carrinho']['itens'][$id][$ref]->cores = $_POST['cores'];
				$_SESSION['carrinho']['itens'][$id][$ref]->tamanhos = $_POST['tamanhos'];
				$_SESSION['carrinho']['itens'][$id][$ref]->opcoes1 = $_POST['opcoes1'];
				$_SESSION['carrinho']['itens'][$id][$ref]->opcoes2 = $_POST['opcoes2'];
				$_SESSION['carrinho']['itens'][$id][$ref]->opcoes3 = $_POST['opcoes3'];
				$_SESSION['carrinho']['itens'][$id][$ref]->opcoes4 = $_POST['opcoes4'];
				$_SESSION['carrinho']['itens'][$id][$ref]->opcoes5 = $_POST['opcoes5'];
				$arr['alert'] = 'Produto enviado para o carrinho!';
				$arr['alert_boxs'] = 1;

			} else {

				$arr['erro'][] = 'Produto indísponivel para a Quantidade Solicidada!';

			}

		}

	if(!isset($include))
		echo json_encode($arr);

?>