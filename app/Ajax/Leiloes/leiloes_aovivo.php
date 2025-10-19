<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();
	$arr = array();


	if(isset($_POST['id'])){
		$arr['lote_atual'] = 0;

        $mysql->colunas = 'id, nome, situacao, lances, lances_cadastro, lances_plaquetas, lances_data, lance_min1, data_ini, data_fim, data_ini1, data_fim1, ordem';
        $mysql->filtro = " WHERE ".STATUS." AND `leiloes` = '".$_POST['id']."' ORDER BY ".ORDER." ";
        $lotes = $mysql->read('lotes');
        foreach ($lotes as $key => $value) {

            $arr['item'][$value->id]['ordem'] = (int)$value->ordem;
            $arr['item'][$value->id]['nome'] = $value->nome;

            $arr['item'][$value->id]['data']  = '<div class="fz11">1ª Praça: '.data($value->data_ini, 'd/m/Y H:i').'</div>';
			$arr['item'][$value->id]['data'] .= '<div class="h4"></div> ';
			$arr['item'][$value->id]['data'] .= $value->lance_min1>0 ? '<div class="fz11">2ª Praça: '.data($value->data_ini1, 'd/m/Y H:i').'</div>' : '';

			$arr['item'][$value->id]['lances'] = preco($value->lances, 1);

			$arr['item'][$value->id]['cadastro']  = '<div>'.lances_cadastro($value->lances_cadastro, $value->lances_plaquetas).'</div>';
			$arr['item'][$value->id]['cadastro'] .= '<div class="fz10">('.data($value->lances_data, 'd/m/Y H:i').')</div>';

			$arr['item'][$value->id]['situacao'] = $value->situacao;

			// Back
			if($value->situacao==2){
				$arr['item'][$value->id]['back'] = ARREMATADO;
				$arr['item'][$value->id]['cor'] = '#fff';
			} elseif($value->situacao==3){
				$arr['item'][$value->id]['back'] = NAO_ARREMATADO;
				$arr['item'][$value->id]['cor'] = '#666';
			} elseif($value->situacao==10){
				$arr['item'][$value->id]['back'] = CONDICIONAL;
				$arr['item'][$value->id]['cor'] = '#fff';
			} elseif($value->situacao==20){
				$arr['item'][$value->id]['back'] = VENDA_DIRETA;
				$arr['item'][$value->id]['cor'] = '#fff';
			} else {
				if(status_leiloes_aberto($value)){
					$arr['item'][$value->id]['back'] = ABERTO;
					$arr['item'][$value->id]['cor'] = '#fff';
				} else {
					$arr['item'][$value->id]['back'] = '#fff';					
					$arr['item'][$value->id]['cor'] = '#666';
				}

				if($arr['lote_atual']==0){
					$arr['lote_atual'] = $value->id;
				}
			}	

		}

		// Lote Atual Leilao Ao Vivo		
		unset($mysql->campo);
		$mysql->logs = 0;
		$mysql->campo['lote_atual'] = $arr['lote_atual'];
		$mysql->filtro = " where ".STATUS." AND id = '".$_POST['id']."' ";
		$ult_id = $mysql->update('leiloes');

	}

	echo json_encode($arr);

?>