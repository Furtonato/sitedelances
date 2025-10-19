<?

		// Retorno do Admin
		if(isset($retorno_admin)){
			$table = $_POST['table'];
			$retorno = '';
			$data_aprovacao = date('c');
			$situacao = $_POST['pedidos_situacoes'];
			$usuarios = $_SESSION['x_admin']->id;
		}


		// Retorno do Pagseguro
		if(isset($retorno_pagseguro)){
			$table = $table;
			$retorno = $transaction;
			$data_aprovacao = $xml->lastEventDate;
			$situacao = $status;
			$usuarios = 0;
		}


		// Atualizando Status
		if(isset($retorno_admin) or isset($retorno_pagseguro)){
			// Atualizando Histrico de Status do Pedido
			unset($mysql->campo);
			$mysql->campo['pedidos_situacoes'] = $pedidos->situacao;
			$mysql->campo['pedidos'] = $pedidos->id;
			$mysql->campo['usuarios'] = $pedidos->situacao_usuarios;
			$mysql->campo['data'] = $pedidos->situacao_data!='0000-00-00 00:00:00' ? $pedidos->situacao_data : $pedidos->data;
			$mysql->campo['txt'] = $pedidos->situacao_txt;
			$ult_id = $mysql->insert('pedidos_status');

			// Atualizando Status do Pedido
			unset($mysql->campo);
			$mysql->prepare = array($pedidos->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['situacao'] = $situacao;
			$mysql->campo['situacao_data'] = date('c');
			$mysql->campo['situacao_usuarios'] = $usuarios;
			$mysql->campo['situacao_txt'] = isset($_POST['txt']) ? cod('html->asc', $_POST['txt']) : '';
			$mysql->update($table);
		}


		// ------------------------------------------------------------------------


		// Cadastro
		$mysql->colunas = 'id, nome, email';
		$mysql->prepare = array($pedidos->cadastro);
		$mysql->filtro = " WHERE `id` = ? ";
		$cadastro = $mysql->read_unico('cadastro');


		// Verificar se ja veio algum retorno de pagamento
		if($situacao == 1 and !$pedidos->ja_foi_pago){

			// Acoes para todos os produtos do carrinho
			$produtos = explode('-', $pedidos->produtos);
			$qtds = explode('-', $pedidos->qtds);
			$refs = explode('-', $pedidos->refs);
			foreach ($produtos as $k => $v) {

				// Diminuir no Estoque e Aumentar ++ Vendido
				$mysql->colunas = 'id, estoque, preco, vendidos, vendidos_valor';
				$mysql->prepare = array($v);
				$mysql->filtro = " WHERE `id` = ? ";
				$consulta = $mysql->read('produtos');
				foreach ($consulta as $key => $value){
					$estoque = $value->estoque;
					$preco = $value->preco;

					$estoque_table = 'produtos';
					$estoque_id = $value->id;
					// VERIFICANDO CORES, TAMANHOS E OPCOES (NOME, ESTOQUES E PRECOS)
						$ref = explode('_', $refs[$k]);
						// Estoque de Cores e Tamanhos
						if($ref[1] and $ref[2]){
							$return = verificar_estoque_cores_tamanhos($value->id, $ref[1], $ref[2]);
							if(isset($return['id'])){
								$estoque_id = $return['id'];
								$estoque_table = 'produtos_cores_tamanhos';
								$estoque = $return['estoque'];
								$preco = $return['preco'] ? $return['preco'] : $preco;
							} else {
								$estoque_id = 0;
								$preco = 0;
							}
						}
						// Estoque de Opcoes
						for ($i=1; $i<=5; $i++) { 
							$opcao = 'opcoes'.$i;
							if($ref[$i+2]){;
								$return = verificar_estoque_opcoes($value->id, $ref[$i+2], $i);
								if(isset($return['id'])){
									$estoque_id = $return['id'];
									$estoque_table = 'produtos_opcoes'.$i;
									$estoque = $return['estoque'];
									$preco = $return['preco'] ? $return['preco'] : $preco;
								} else {
									$estoque_id = 0;
									$preco = 0;
								}
							}
						}
					// VERIFICANDO CORES, TAMANHOS E OPCOES (NOME, ESTOQUES E PRECOS)

					// Diminuindo estoque
					unset($mysql->campo);
					$mysql->campo['estoque'] = $estoque - $qtds[$k];
					$mysql->prepare = array($estoque_id);
					$mysql->filtro = " WHERE `id` = ? ";
					$mysql->update($estoque_table);

					// Estatisticas
					if($preco>0){
						unset($mysql->campo);
						$mysql->campo['vendidos'] = $value->vendidos + $qtds[$k];
						$mysql->campo['vendidos_valor'] = $value->vendidos_valor + ($preco*$qtds[$k]);
						$mysql->prepare = array($value->id);
						$mysql->filtro = " WHERE `id` = ? ";
						$mysql->update('produtos');
					}
				}

			}

			// Cupons
			if($pedidos->cupons){
				$mysql->colunas = 'id, usar, usado';
				$mysql->prepare = array($pedidos->cupons);
				$mysql->filtro = " WHERE `id` = ? ";
				$cupons = $mysql->read_unico('cupons');
				if(isset($cupons->id)){
					unset($mysql->campo);
					if(!$cupons->usar)
						$mysql->campo['status'] = 0;

					$mysql->campo['usado'] = $cupons->usado ? $cupons->usado.$cadastro->id.'-' : '-'.$cadastro->id.'-';
					$mysql->filtro = " where id = '".$cupons->id."' ";
					$mysql->update('cupons');
				}
			}

			// Gravando informando q ja foi pago uma vez
			unset($mysql->campo);
			$mysql->campo['ja_foi_pago'] = 1;
			$mysql->prepare = array($pedidos->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->update($table);
		}


		// ------------------------------------------------------------------------


		// Enviando Email
		$mysql->colunas = 'id, situacao';
		$mysql->prepare = array($_POST['id']);
		$mysql->filtro = " WHERE `id` = ? ";
		$pedidos = $mysql->read_unico($table);

        $nome = $pedidos->situacao ? rel('pedidos_situacoes', $pedidos->situacao) : SITUACAO_PD;
        $cor = $pedidos->situacao ? rel('pedidos_situacoes', $pedidos->situacao, 'cor') : '';
		$situacao = '<span style="color:'.$cor.'">'.$nome.'</span>';

		$mysql->filtro = " WHERE `id` = 52 ";
		$textos = $mysql->read_unico('textos');
		$var_email = 'DIR->'.DIR.'&nome->'.$cadastro->nome.'&email->'.$cadastro->email.'&id->'.$pedidos->id.'&status->'.$situacao;

		$email = new Email();
		$email->to			= $cadastro->email;
		$email->remetente	= nome_site();
		$email->assunto		= var_email($textos->nome, $var_email);
		$email->txt 		= var_email(txt($textos), $var_email);
		$email->enviar();


?>