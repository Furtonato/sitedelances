<?

		$arr['form'] = '<div class="dni">';
			$arr['form'] .= '<form name="form_pagamento" id="form_pagamento" method="post" action="https://pagseguro.uol.com.br/checkout/checkout.jhtml">';
	
				$arr['form'] .= '<input type="hidden" name="email_cobranca" value="'.$conta->pagseguro_email.'">';
				$arr['form'] .= '<input type="hidden" name="tipo" value="CP">';
				$arr['form'] .= '<input type="hidden" name="moeda" value="BRL">';
		
				$arr['form'] .= '<input type="hidden" name="item_id_1" value="'.$pedidos->id.'">';
				$arr['form'] .= '<input type="hidden" name="item_descr_1" value="Pedido numero '.$pedidos->id.'" />';
				$arr['form'] .= '<input type="hidden" name="item_quant_1" value="1">';
				$arr['form'] .= '<input type="hidden" name="item_valor_1" value="'.numero($pedidos->valor_subtotal-$pedidos->desconto).'">';
				$arr['form'] .= '<input type="hidden" name="item_frete_1" value="'.numero($pedidos->frete).'">';
				$arr['form'] .= '<input type="hidden" name="item_peso_1" value="0">';
				$arr['form'] .= '<input type="hidden" name="ref_transacao" value="pedidos-'.$pedidos->id.'">';

			$arr['form'] .= '</form>';
		$arr['form'] .= '</div>';

?>