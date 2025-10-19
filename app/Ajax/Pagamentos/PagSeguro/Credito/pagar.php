<?php ob_start();

	require_once "../../../../../system/conecta.php";
	require_once "../../../../../system/mysql.php";
	require_once "../../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['xml'] = '';
		

		if(isset($_POST['id']) and $_POST['id']){

			$mysql->filtro = " where id = '".$_POST['id']."' ";
			$pedidos = $mysql->read_unico('pedidos');

			$mysql->filtro = " where id = '".$pedidos->cadastro."' ";
			$cadastro = $mysql->read_unico("cadastro");

			$mysql->filtro = " where status = '1' and tipo = 'pagamentos' ";
			$pagamentos = $mysql->read_unico('configs');
			$data['email'] = $pagamentos->pagseguro_email;
			$data['token'] = $pagamentos->pagseguro_token;


			// Parcelas
				$parcelas_all = array();
				$qtd_parcelas = 0;
				$parcelas = json_decode($_POST['parcelamento']);
				foreach ($parcelas->installments as $k => $v) {
					foreach ($v as $key => $value){
						$parcelas_all[$value->quantity] = $value->installmentAmount;
						if($value->interestFree)
							$qtd_parcelas++;
					}
				}
			// Parcelas

			// Dados importantes do Pagamento
			$data['creditCardToken'] = $_POST['token']; // Token
			$data['installmentQuantity'] = $_POST['parcelas']; // Quantidade de parcelas sem juros
			$data['installmentValue'] = numero($parcelas_all[ $_POST['parcelas'] ]); // Valor das parcelas
			$data['noInterestInstallmentQuantity'] = $qtd_parcelas; // Quantidade de parcelas sem juros
			// Dados importantes do Pagamento


			$data['itemId1'] = $pedidos->id;
			$data['itemDescription1'] = 'Pedido numero '.$pedidos->id;
			$data['itemQuantity1'] = 1;
			$data['itemAmount1'] = $pedidos->valor_subtotal;


			$data['paymentMode'] = 'default';
			$data['paymentMethod'] = 'creditCard';
			$data['receiverEmail'] = $pagamentos->pagseguro_email;
			$data['currency'] = 'BRL';
			//$data['extraAmount'] = 0;
			$data['notificationURL'] = DIR_C.'/app/Ajax/Pagamentos/PagSeguro/retorno.php';
			$data['reference'] = 'pedidos-'.$pedidos->id;

			$celular = explode(' ', $cadastro->celular);
			$ddd = str_replace('(', '', str_replace(')', '', $celular[0]));
			$data['senderHash'] = $_POST['SenderHash'];
			$data['senderName'] = cod('html->iso', $cadastro->nome.' '.$cadastro->sobrenome);
			$data['senderCPF'] = $cadastro->cpf ? str_replace('-', '', str_replace('.', '', $cadastro->cpf) ) : '35804790838';
			$data['senderAreaCode'] = $ddd ? $ddd : '19';
			$data['senderPhone'] = (isset($celular[1]) and $celular[1]) ? str_replace('-', '', $celular[1]) : '99275440';
			$data['senderEmail'] = $cadastro->email;

            $carrinho = unserialize($pedidos->carrinho);
			$data['shippingAddressStreet'] = cod('html->iso', $carrinho['frete']['rua']);
			$data['shippingAddressNumber'] = cod('html->iso', $carrinho['frete']['numero']);
			$data['shippingAddressComplement'] = cod('html->iso', $carrinho['frete']['complemento']);
			$data['shippingAddressDistrict'] = cod('html->iso', $carrinho['frete']['bairro']);
			$data['shippingAddressPostalCode'] = str_replace('-', '', str_replace('.', '', $carrinho['frete']['cep']) );
			$data['shippingAddressCity'] = cod('html->iso', cidade($carrinho['frete']['cidades']));
			$data['shippingAddressState'] = estado($carrinho['frete']['estados'], 'ab');
			$data['shippingAddressCountry'] = 'BRA';

			$data['shippingType'] = $pedidos->tipo_frete=='PAC' ? 1 : ( $pedidos->tipo_frete=='SEDEX' ? 2 : 3 );
			$data['shippingCost'] = $pedidos->frete;

			$data['creditCardHolderName'] = $_POST['nome'];
			$data['creditCardHolderCPF'] = str_replace('-', '', str_replace('.', '', $_POST['cpf']) );
			$data['creditCardHolderBirthDate'] = data($cadastro->nascimento);

			$data['creditCardHolderAreaCode'] = str_replace('(', '', $celular[0]);
			$data['creditCardHolderPhone'] = isset($celular[1]) ? str_replace('-', '', $celular[1]) : '';

			$data['billingAddressStreet'] = $carrinho['frete']['rua'];
			$data['billingAddressNumber'] = $carrinho['frete']['numero'];
			$data['billingAddressComplement'] = $carrinho['frete']['complemento'];
			$data['billingAddressDistrict'] = $carrinho['frete']['bairro'];
			$data['billingAddressPostalCode'] = str_replace('-', '', str_replace('.', '', $carrinho['frete']['cep']) );
			$data['billingAddressCity'] = cidade($carrinho['frete']['cidades']);
			$data['billingAddressState'] = estado($carrinho['frete']['estados'], 'ab');
			$data['billingAddressCountry'] = 'BRA';

			$curl = curl_init(PAGSEGURO_URL.'/v2/transactions/');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));				 
			$xml = curl_exec($curl);
			curl_close($curl);

			$arr['data'] = $data;
			$arr['xml'] = simplexml_load_string($xml);

			if(isset($arr['xml']) and $arr['xml']){
				if($arr['xml']->error){
					foreach ($arr['xml']->error as $key => $value) {
						$arr['erro'][] = $value->message.'<span class="dn">'.$value->code.'</span>';
					}

				} elseif($arr['xml']->status == 5){
					$arr['erro'][] = 'O comprador, dentro do prazo de liberação da transação, abriu uma disputa.';

				} elseif($arr['xml']->status == 6){
					$arr['erro'][] = 'O valor da transação foi devolvido para o comprador';

				} elseif($arr['xml']->status == 7){
					$arr['erro'][] = 'A transação foi cancelada sem ter sido finalizada.';

				} elseif($arr['xml']->status == 1 or $arr['xml']->status == 2 or $arr['xml']->status == 3 or $arr['xml']->status == 4){
					$arr['pago'] = 1;

					$mysql->filtro = " where id = '".$pedidos->id."' ";
					$mysql->campo['forma_pagamento'] = 3;
					$mysql->campo['forma_pagamento_pago'] = 1;
					$mysql->campo['forma_pagamento_qtd_parcelas'] = $_POST['parcelas'];
					$mysql->campo['forma_pagamento_status'] = $arr['xml']->status;
					$mysql->campo['forma_pagamento_valor_parcelas'] = numero($parcelas_all[ $_POST['parcelas'] ]);
					$mysql->update('pedidos');
				}

			} else {
				$arr['erro'][] = 'Ocorreu algum problema no processo! Verifique se o com os dados do cartão estão corretos!';
			}

		}

	echo json_encode($arr); 

?>