<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['produtoss_opcoes'] = '';


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


		/* OPCOES */
		$banco = 'produtos_'.$_POST['name'];
		$opcoes = isset($item->$banco) ? ex($item->$banco) : array();
		$mysql->coluna = 'id';
		$existe = $mysql->existe($banco);
		if($existe and $opcoes){

			$mysql->coluna = 'id, nome';
			$mysql->prepare = array($item->id);
			$mysql->filtro = " WHERE ".STATUS." AND `produtos` = ? AND `id` IN (".implode(',', $opcoes).") ORDER BY ".ORDER." ";
			$produtos_opcoes = $mysql->read($banco);
			foreach ($produtos_opcoes as $key => $value) {
				if(!isset($opcao_atual)) $opcao_atual = $_POST['opcao'] ? $_POST['opcao'] : $value->id;
				$arr['produtoss_opcoes'] .= '<option value="'.$value->id.'" '.iff(!$value->estoque, 'class="cor_ccc"').' '.iff($opcao_atual==$value->id, 'selected').'>'.$value->nome.'</option>';

				if($opcao_atual==$value->id){
					$estoque = $value->estoque;
					$codigo = $value->codigo ? $value->codigo : $codigo;
					$foto = $value->foto ? $value->foto : $foto;
					$preco = $value->preco>0 ? $value->preco : $preco;
					$preco1 = $value->preco1>0 ? $value->preco1 : $preco1;
					$preco2 = ($value->parcelas>0 and $value->preco2>0) ? $value->preco2 : 0;
					$parcelas = ($value->parcelas>0 and $value->preco2>0) ? $value->parcelas : 0;
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
		/* OPCOES */


	echo json_encode($arr);

?>