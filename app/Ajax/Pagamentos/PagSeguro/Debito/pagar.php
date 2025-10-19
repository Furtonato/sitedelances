<?php ob_start();

	require_once "../../../../../system/conecta.php";
	require_once "../../../../../system/mysql.php";
	require_once "../../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['xml'] = '';
		

		if(isset($_POST['id']) and $_POST['id'] and isset($_POST['SenderHash']) and $_POST['SenderHash']){

			$bancos = array('bancodobrasil', 'bradesco', 'itau');

			foreach ($bancos as $k => $banco) {

				$mysql->filtro = " where id = '".$_POST['id']."' ";
				$pedidos = $mysql->read_unico('pedidos');

				$mysql->filtro = " where id = '".$pedidos->cadastro."' ";
				$cadastro = $mysql->read_unico("cadastro");

				$mysql->filtro = " where status = '1' and tipo = 'pagamentos' ";
				$pagamentos = $mysql->read_unico('configs');
				$data['email'] = $pagamentos->pagseguro_email;
				$data['token'] = $pagamentos->pagseguro_token;

				// Dados importantes do Pagamento
				$data['bankName'] = $banco;
				$data['itemId1'] = $pedidos->id;
				$data['itemDescription1'] = 'Pedido numero '.$pedidos->id;
				$data['itemQuantity1'] = 1;
				$data['itemAmount1'] = $pedidos->valor_subtotal;
				// Dados importantes do Pagamento


				$data['paymentMode'] = 'default';
				$data['paymentMethod'] = 'eft';
				$data['receiverEmail'] = $pagamentos->pagseguro_email;
				$data['currency'] = 'BRL';
				//$data['extraAmount'] = 0;
				$data['notificationURL'] = DIR_C.'/app/Ajax/Pagamentos/PagSeguro/retorno.php';
				$data['reference'] = 'pedidos-'.$pedidos->id;

				$celular = explode(' ', $cadastro->celular);
				$ddd = str_replace('(', '', str_replace(')', '', $celular[0]));
				$data['senderHash'] = $_POST['SenderHash'];
				$data['senderName'] = cod('html->iso', $cadastro->nome.' '.$cadastro->sobrenome);
				$data['senderCPF'] = $cadastro->cpf ? str_replace('-', '', str_replace('.', '', $cadastro->cpf) ) : '35804790838';;
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

				$data['shippingType'] = $pedidos->tipo_frete=='pac' ? 1 : ( $pedidos->tipo_frete=='sedex' ? 2 : 3 );
				$data['shippingCost'] = $pedidos->frete;


				$curl = curl_init(PAGSEGURO_URL.'/v2/transactions/');
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));				 
				$xml = curl_exec($curl);
				curl_close($curl);

				$arr['data'][$banco] = $data;
				$arr['xml'][$banco] = simplexml_load_string($xml);

				if(isset($arr['xml'][$banco]->error) && $arr['xml'][$banco]->error){
					//foreach ($arr['xml'][$banco]->error as $key => $value) {
						//$arr['erro'][] = ucfirst($banco).': '.$value->message.'<span class="dn">'.$value->code.'</span>';
					//}
					$arr['xml'][$banco]['paymentLink'] = '';
				}
			}

			$mysql->filtro = " where id = '".$pedidos->id."' ";
			$mysql->campo['forma_pagamento'] = 2;
			$mysql->campo['forma_pagamento_pago'] = 1;
			$mysql->campo['forma_pagamento_qtd_parcelas'] = 1;
			$mysql->campo['forma_pagamento_valor_parcelas'] = $pedidos->valor_subtotal;
			//$mysql->campo['forma_pagamento_banco'] = $_POST['banco'];
			$mysql->update('pedidos');


		} else if(isset($_POST['id']) and $_POST['id'] and isset($_POST['banco']) and $_POST['banco']){
			$mysql->filtro = " where id = '".$pedidos->id."' ";
			$mysql->campo['forma_pagamento_banco'] = $_POST['banco'];
			$mysql->update('pedidos');
		}

	echo json_encode($arr); 

?>