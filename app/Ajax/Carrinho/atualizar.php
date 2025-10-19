<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';
	include_once '../../../app/Classes/Frete.php';

	$mysql = new Mysql();
	$frete = new Frete();

	$arr = array();
	$arr['tipos_frete'] = array();

		$count = 0;
		$subtotal = 0;

		// ALTERAR QTD
		if($_POST['tipo'] == 'qtd' OR $_POST['tipo'] == 'qtd-1' OR $_POST['tipo'] == 'qtd1'){
			$qtd_anterior = $_SESSION['carrinho']['itens'][$_POST['id']][$_POST['ref']]->qtd;
			if($_POST['tipo'] == 'qtd'){
				$qtd_anterior = $_POST['val']>0 ? $_POST['val'] : 1;
			} elseif($_POST['tipo'] == 'qtd-1'){
				$qtd_anterior = $qtd_anterior>1 ? $qtd_anterior-1 : 1;
			} elseif($_POST['tipo'] == 'qtd1'){
				$qtd_anterior = $qtd_anterior+1;;
			}
			$_SESSION['carrinho']['itens'][$_POST['id']][$_POST['ref']]->qtd = $qtd_anterior;
		}


		// ITENS NO CARRINHO
		if(isset($_SESSION['carrinho']['itens'])){
			foreach($_SESSION['carrinho']['itens'] as $key => $array){
				foreach($array as $ref => $value){
					$mysql->colunas = 'id, nome, preco';
					$mysql->prepare = array($key);
					$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' AND `id` = ? ";
					$produtos = $mysql->read_unico('produtos');

					if(isset($produtos->id)){
						$preco = $produtos->preco;

						$descricao = array();
						if($value->cores) $descricao[] = rel('produtos_cores', $value->cores);
						if($value->tamanhos) $descricao[] = rel('produtos_tamanhos', $value->tamanhos);
						if($value->opcoes1) $descricao[] = rel('produtos_opcoes1', $value->opcoes1);
						if($value->opcoes2) $descricao[] = rel('produtos_opcoes2', $value->opcoes2);
						if($value->opcoes3) $descricao[] = rel('produtos_opcoes3', $value->opcoes3);
						if($value->opcoes4) $descricao[] = rel('produtos_opcoes4', $value->opcoes4);
						if($value->opcoes5) $descricao[] = rel('produtos_opcoes5', $value->opcoes5);

						// Estoque de Cores e Tamanhos
						if($value->cores and $value->tamanhos){
							$return = verificar_estoque_cores_tamanhos($produtos->id, $value->cores, $value->tamanhos);
							if(isset($return['id'])){
								$preco = $return['preco'] ? $return['preco'] : $preco;
							} elseif(isset($return['null'])){
								$value->cores = '';
								$value->tamanhos = '';
							}
						}

						// Estoque de Opcoes
						for ($i=1; $i<=5; $i++) { 
							$opcao = 'opcoes'.$i;
							if($value->$opcao){;
								$return = verificar_estoque_opcoes($produtos->id, $value->$opcao, $i);
								if(isset($return['id'])){
									$preco = $return['preco'] ? $return['preco'] : $preco;
								} elseif(isset($return['null'])){
									$value->$opcao = '';
								}
							}
						}

						$count++;
						$subtotal += $value->qtd*$preco;
						$arr['itens'][$ref]['nome'] = $produtos->nome;
						$arr['itens'][$ref]['descricao'] = (isset($descricao) and $descricao) ? '<br><i class="fz14 fwn">('.implode(' / ', $descricao).')</i>' : '';
						$arr['itens'][$ref]['preco'] = preco($preco, 1);
						$arr['itens'][$ref]['qtd'] = $value->qtd;
						$arr['itens'][$ref]['subtotal'] = preco($value->qtd*$preco, 1);
					}
				}
			}
		}



		// ----------------------------------------------------------------------



		// ENDERECO
		if(isset($_SESSION['x_site']->id)){
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `cadastro` = ? AND `principal` = 1 ";
			$enderecos_principal = $mysql->read_unico('cadastro_enderecos');

			if(isset($enderecos_principal->id)){
				$arr['endereco_atual']  = '<div>'.$enderecos_principal->rua.', '.$enderecos_principal->numero.' '.$enderecos_principal->complemento.'</div>';
				$arr['endereco_atual'] .= '<div>'.$enderecos_principal->bairro.', '.$enderecos_principal->cidades.' / '.$enderecos_principal->estados.'</div>';
				$arr['endereco_atual'] .= '<div>CEP: '.$enderecos_principal->cep.'</div>';
				$arr['cep'] = $enderecos_principal->cep;

				$_SESSION['carrinho']['frete']['cep'] = $enderecos_principal->cep;
				$_SESSION['carrinho']['frete']['rua'] = $enderecos_principal->rua;
				$_SESSION['carrinho']['frete']['numero'] = $enderecos_principal->numero;
				$_SESSION['carrinho']['frete']['complemento'] = $enderecos_principal->complemento;
				$_SESSION['carrinho']['frete']['bairro'] = $enderecos_principal->bairro;
				$_SESSION['carrinho']['frete']['cidades'] = $enderecos_principal->cidades;
				$_SESSION['carrinho']['frete']['estados'] = $enderecos_principal->estados;

			} else {
				$mysql->colunas = 'id';
				$mysql->prepare = array($_SESSION['x_site']->id);
				$mysql->filtro = " WHERE `cadastro` = ? ";
				$enderecos_principal = $mysql->read_unico('cadastro_enderecos');
				if($enderecos_principal) {
					$arr['evento'] = "boxs('endereco_edit', 'ini=1'); ";
				} else {
					$arr['evento'] = "boxs('endereco_add'); ";
				}
			}
		}


		// FRETE
		if(isset($arr['cep']) AND $arr['cep']){
			$frete->valor_total = $subtotal;
			$arr['tipos_frete'] = $frete->calcula_frete($arr['cep']);
		}

		// SELECIONAR FRETE
		if($_POST['tipo'] == 'frete'){
			$_SESSION['carrinho']['frete']['valor'] = $arr['tipos_frete']['valor'][$_POST['val']];
			$_SESSION['carrinho']['frete']['tipo'] = $_POST['val'];

			if($_SESSION['carrinho']['frete']['valor']==''){
				unset($_SESSION['carrinho']['frete']['valor']);
			}
		}
		$arr['tipo_frete_atual'] = isset($_SESSION['carrinho']['frete']['tipo']) ? $_SESSION['carrinho']['frete']['tipo'] : '';

		// PRECO DO FRETE
		$frete = isset($_SESSION['carrinho']['frete']['valor']) ? $_SESSION['carrinho']['frete']['valor'] : '';



		// ----------------------------------------------------------------------



		// DESCONTO
		$desconto = isset($_SESSION['creditos']) ? $_SESSION['creditos'] : 0;
		$desconto_info = array();

		if(isset($_SESSION['desconto'])){
			foreach ($_SESSION['desconto'] as $key => $value) {
				if($key == 'cupons'){
					$mysql->colunas = 'id, nome, preco, preco1, frete';
					$mysql->prepare = array($value['id']);
					$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' AND `id` = ? ";
					$cupons = $mysql->read_unico('cupons');
					if(isset($cupons->id)){
						$info = '<div class=c_cuponss>>> Cupom: (#'.$cupons->id.') '.$cupons->nome.' (';
						$info1 = ')</div>';
						if($cupons->preco>0){ // Preco
							$desconto += $cupons->preco;
							$desconto_info[] = $info.preco($cupons->preco, 1).$info1;
						}
						if($cupons->preco1>0){ // Procentagem
							$desconto += $subtotal*($cupons->preco1/100);
							$desconto_info[] = $info.preco($cupons->preco1, 0, 2, ',', '.', 1).'%'.$info1;
						}
						if($cupons->frete AND isset($_POST['tipo_frete_atual']) and $_POST['tipo_frete_atual']=='pac' ){ // Frete
							$frete = '0.00';
							$_SESSION['carrinho']['frete']['valor'] = $frete;
							$_SESSION['carrinho']['frete']['tipo'] = 'pac';
							$desconto_info[] = $info.'Frete grátis!'.$info1;
						}
					}
				}
			}
		}

		// VERIFICANDO SE O CREDITO UTILIZADO EH MAIOR QUE O TOTAL
		$preco_final = $subtotal + numero($frete);
		if($preco_final<$desconto AND isset($_SESSION['creditos'])){
			$nao_creditos = $desconto - $_SESSION['creditos'];
			$_SESSION['creditos'] = $preco_final - $nao_creditos;
		}



		// ----------------------------------------------------------------------



		// VALORES FINAIS E TOPO
		$arr['count'] = $count;
		$arr['desconto'] = preco($desconto, 1);
		$arr['desconto_n'] = $desconto;
		$arr['frete'] = $frete!='' ? preco($frete, 1) : '';
		$arr['frete_n'] = $frete ? $frete : 0;
		$arr['subtotal'] = preco($subtotal, 1);
		$total = $subtotal + numero($frete) - $desconto;
		$arr['total'] = $total>0 ? preco($total, 1) : '0,00';


		// SESSION
		$_SESSION['carrinho']['desconto']['valor'] = $desconto;
		$_SESSION['carrinho']['desconto']['info'] = implode('<br>', $desconto_info);


	echo json_encode($arr);
?>