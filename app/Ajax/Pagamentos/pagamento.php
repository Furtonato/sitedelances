<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';
	include_once '../../../app/Classes/Email.php';

	$mysql = new Mysql();
	$mysql->ini();

	$dados = array();
	$arr = array();
	$arr['alert'] = 0;


		if(!isset($_SESSION['carrinho']['frete']['cep']) or !isset($_SESSION['carrinho']['frete']['cep'])){
			$arr['alert'] = lang('Selecione o Endereço de Entrega');
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `cadastro` = ? ";
			$cadastro_enderecos = $mysql->read('cadastro_enderecos');
			if(isset($cadastro_enderecos->id))
				$arr['evento'] = "boxs('endereco_edit'); ";
			else
				$arr['evento'] = "boxs('endereco_add'); ";

		} elseif(!isset($_SESSION['carrinho']['frete']['tipo']))
			$arr['alert'] = lang('Selecione o Tipo de Entrega');

		elseif(!(isset($_SESSION['carrinho']['frete']['valor']) and $_SESSION['carrinho']['frete']['valor']!=''))
			$arr['alert'] = lang('O Frete não foi Calculado!');

		elseif(!isset($_SESSION['x_site']->id))
			$arr['alert'] = lang('Você Precisa esta Logado para Efetuar uma Compra!');

		elseif(!(isset($_SESSION['carrinho']['itens']) and $_SESSION['carrinho']))
			$arr['alert'] = lang('Seu Carrinho está Vazio!');





		// LIBERADOOOO
		elseif($_POST['id']==0) {

			$CARRINHO = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : array();

			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$cadastro = $mysql->read_unico("cadastro");


			// Itens
			$dados['nome'] = array();
			$dados['ids'] = array();
			$dados['qtds'] = array();
			$dados['refs'] = array();
			$dados['precos'] = array();
			$dados['valor_subtotal'] = 0;
			$dados['credito'] = 0;
			$dados['desconto'] = 0;
			$dados['desconto_info'] = '';

			// Pegando id dos Produtos
			$itens = array();
			foreach($CARRINHO['itens'] as $key => $array){
				foreach($array as $ref => $value){
					if(isset($key)){

						// Pegando Info dos Produtos
						$mysql->prepare = array($key);
						$mysql->filtro = " WHERE `status` = 1 and `lang` = '".LANG."' AND `id` = ? ";
						$produtos = $mysql->read_unico("produtos");
						if(isset($produtos->id)){
							$estoque = $produtos->estoque;
							$preco = $produtos->preco;
							$nome = '';

							$dados['ids'][]	 = $produtos->id;
							$dados['qtds'][] = $value->qtd;
							$dados['refs'][] = $ref;

							// VERIFICANDO CORES, TAMANHOS E OPCOES (NOME, ESTOQUES E PRECOS)
								// Estoque de Cores e Tamanhos
								if($value->cores and $value->tamanhos){
									$return = verificar_estoque_cores_tamanhos($produtos->id, $value->cores, $value->tamanhos);
									if(isset($return['id'])){
										$estoque = $return['estoque'];
										$preco = $return['preco'] ? $return['preco'] : $preco;
										$nome .= rel('produtos_cores', $value->cores).' / '.rel('produtos_tamanhos', $value->tamanhos);
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
											$estoque = $return['estoque'];
											$preco = $return['preco'] ? $return['preco'] : $preco;
											$nome .= $nome ? ' / ' : '';
											$nome .= rel('produtos_opcoes'.$i, $value->$opcao);
										} elseif(isset($return['null'])){
											$value->$opcao = '';
										}
									}
								}
							// VERIFICANDO CORES, TAMANHOS E OPCOES (NOME, ESTOQUES E PRECOS)


							$nome 					 = $nome ? '('.$nome.')' : '';
							$dados['nome'][]	 	 = '<div class=c_descc>>> (#'.iff($produtos->codigo, $produtos->codigo, $produtos->id).') '.$produtos->nome.' '.$nome.'</div>';
							$dados['precos'][] 		 = $preco;
							$dados['valor_subtotal'] += $value->qtd*$preco;


							// VERIFICANDO ESTOQUE E ERROS
								// Conferindo Credito
								if(isset($_SESSION['creditos']) and $_SESSION['creditos']){
									if($cadastro->creditos<$_SESSION['creditos']){
										$arr['erro'] = 'Você Não Possui a Quantidade de Créditos que está querendo usar na Compra!';
									} else {
										$dados['credito'] += $_SESSION['creditos'];
										$mysql->campo['creditos'] = $cadastro->creditos - $_SESSION['creditos'];
										$mysql->prepare = array($cadastro->id);
										$mysql->filtro = " WHERE `id` = ? ";
										$mysql->update('cadastro');
									}
								}

								// Estoque
								if($value->qtd>1 and $estoque){
									if($estoque<$value->qtd){
										if(!isset($no_estoque)) $arr['erro'] = 'Estoque Excedido para os Itens Abaixo!';
										$arr['erro'] .= '<div>>> '.$produtos->nome.' '.iff($nome, '('.$nome.')').' (Estoque: '.$estoque.')</div>';
										$no_estoque = 1;
									}
								} elseif(!$estoque) {
									if(!isset($no_estoque)) $arr['erro'] = 'Sem Estoque Disponível para os Itens Abaixo!';
									$arr['erro'] .= '<div>>> '.$produtos->nome.' '.iff($nome, '('.$nome.')').' (Estoque: '.$estoque.')</div>';
									$no_estoque = 1;
								}

								/*/ Tempo (COMPRA COLETIVA)
								$tempo = sub_data(data($produtos->data_fim, 'd/m/Y/H/i/s'), date('d/m/Y/H/i/s'));
								if($tempo['hora_total']=='00' and $tempo['min']=='00' and $tempo['seg']=='00'){
									$arr['erro'] = 'O tempo para Compra desse Produto Acabou!';
								}

								// Minimo de Compra
								if($produtos->qtd_min>$value->qtd)
									$arr['erro'] = 'Você pode Comprar no Mínimo '.$produtos->qtd_min.' Produtos!';

								// Maximo de Compra
								if($produtos->qtd_max<$value->qtd)
									$arr['erro'] = 'Você pode Comprar no Máximo '.$produtos->qtd_max.' Produtos!';
								// (COMPRA COLETIVA) */

							// VERIFICANDO ESTOQUE E ERROS

						}
						// Pegando Info dos Produtos
					}
				}
			}


			// ERROS
			if(isset($arr['erro'])){
				$arr['alert'] = $arr['erro'];
				echo json_encode($arr); exit();
			}


			// DADOS FINAIS
			$dados['metodo']		= isset($_POST['metodo']) ? $_POST['metodo'] : '';
			$dados['cupons']		= isset($_SESSION['desconto']['cupons']['id']) ? $_SESSION['desconto']['cupons']['id'] : 0;
			$dados['tipo_frete']    = isset($CARRINHO['frete']['tipo'])	 ? $CARRINHO['frete']['tipo'] : '';
			$dados['frete'] 		= isset($CARRINHO['frete']['valor']) ? $CARRINHO['frete']['valor'] : 0;
			$dados['desconto'] 	 	+= isset($CARRINHO['desconto']['valor']) ? $CARRINHO['desconto']['valor'] : 0;
			$dados['desconto_info'] .= isset($CARRINHO['desconto']['info']) ? $CARRINHO['desconto']['info'] : '';


			// GRAVANDO O PEDIDO
			unset($mysql->campo);
			$mysql->campo['nome']			= implode('<z></z>', $dados['nome']);
			$mysql->campo['cadastro']		= $_SESSION['x_site']->id;

			$mysql->campo['produtos']		= '-'.implode('-', $dados['ids']).'-';
			$mysql->campo['qtds']			= '-'.implode('-', $dados['qtds']).'-';
			$mysql->campo['refs']			= '-'.implode('-', $dados['refs']).'-';
			$mysql->campo['precos']			= '-'.implode('-', $dados['precos']).'-';

			$mysql->campo['metodo']			= $dados['metodo'];
			$mysql->campo['cupons']			= $dados['cupons'];
			$mysql->campo['tipo_frete']		= $dados['tipo_frete'];
			$mysql->campo['frete']			= numero($dados['frete']);

			$mysql->campo['credito']		= $dados['credito'];
			$mysql->campo['desconto']		= numero($dados['desconto']);
			$mysql->campo['desconto_info']	= $dados['desconto_info'];
			$mysql->campo['valor_subtotal']	= numero($dados['valor_subtotal']);
			$mysql->campo['valor_total']	= numero($dados['frete'] + $dados['valor_subtotal'] - $dados['desconto']);

			$mysql->campo['voucher']		= voucher();
			$arr['itens'] = $mysql->campo;
			$arr['id'] = $mysql->insert("pedidos");


			// GRAVANDO JSON DO CARRINHO
            $file = fopen("../../../plugins/Json/pedidos/".$arr['id'].".json", 'w');
            fwrite($file, json_encode($CARRINHO));
            fclose($file);




			// ENVIANDO DADOS POR EMAIL
				$email = new Email();
	
				// Pegando Dados do pedido
				$mysql->prepare = array($arr['id']);
				$mysql->filtro = " WHERE `id` = ? ";
				$pedidos = $mysql->read_unico("pedidos");

				// Escobo do Email
				$mysql->filtro = " WHERE `id` = 51 ";
				$textos = $mysql->read_unico('textos');
				$enderecos = $CARRINHO['frete'];

				// Variaveis do Escobo
				$var_email = 'nome->'.$cadastro->nome.'&email->'.$cadastro->email;
				$var_email .= '&id->'.$pedidos->id.'&data->'.data($pedidos->data, 'd/m/Y').'&metodo_pagamento->'.$dados['metodo'].'&tipo_frete->'.ucfirst($dados['tipo_frete']);
				$var_email .= '&endereco->'.$enderecos['rua'].', '.$enderecos['numero'].' '.$enderecos['complemento'].'&bairro->'.$enderecos['bairro'].'&cep->'.$enderecos['cep'].'&cidade->'.$enderecos['cidades'].'&estado->'.$enderecos['estados'];
				$var_email .= '&tabela_produtos->'.str_replace('&', 'z;z-z;z', emails_tabela_de_pedidos($pedidos));

				// Enviando para o Cliente
				$email->to			= $cadastro->email;
				$email->remetente	= nome_site();
				$email->assunto		= var_email($textos->nome, $var_email);
				$email->txt 		= str_replace('z;z-z;z', '&', str_replace('src="/web/', 'src="'.DIR_C.'/web/', var_email(txt($textos), $var_email) ) );
				$email->enviar();

				// Enviando para o Dono do Site
				$mysql->filtro = " WHERE `status` = '1' and tipo = 'emails' ";
				$email_remetente = $mysql->read_unico('configs');
				$email->to = $email_remetente->email;
				$email->enviar();

			// ENVIANDO DADOS POR EMAIL


			// REDIRECIONANDO PARA O PLATAFORMA DE PAGAMENTO
			if($pedidos->valor_total<=0){ // VALOR TOTAL DA COMPRA ZERADO

				$_POST['id'] = $pedidos->id;
				$table = 'pedidos';
				$transaction = '';
				$xml->lastEventDate = '';
				$status = 1;
				$retorno_pagseguro = 1;
				include 'retorno_pedidos.php';
				$arr['evento']  = "alert('".lang('Sua Compra Foi Realizada Com Sucesso!')."'); ";
				$arr['evento'] .= 'window.location.href="'.DIR.'/minha_conta/"; ';


			} elseif($cadastro->id and $pedidos->id and $pedidos->valor_subtotal){
				$mysql->filtro = " WHERE `tipo` = 'pagamentos' ";
				$conta = $mysql->read_unico("configs");
				include $dados['metodo'].'/form.php';
			}
			//unset($_SESSION['carrinho']);
			// REDIRECIONANDO PARA O PLATAFORMA DE PAGAMENTO








		// SEGUNDA VIA DO PAGAMENTO
		} elseif($_POST['id']) {

			$mysql->prepare = array($_POST['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$pedidos = $mysql->read_unico("pedidos");

			// Direcionar para pagina de pagamento
			if($pedidos->id and $pedidos->valor_subtotal){
				$mysql->filtro = " WHERE `tipo` = 'pagamentos' ";
				$conta = $mysql->read_unico("configs");
				include $_POST['metodo'].'/form.php';
			}

		}


	$mysql->fim();
	echo json_encode($arr);

?>