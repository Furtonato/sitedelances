<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['id'] = $_POST['id'];

		$filtro_tipo = preg_match('(1_cate)', $_POST['table']) ? ' AND tipo = 0' : '';

		if(isset($_POST['pai']) and $_POST['pai'] and isset($_POST['item']) and $_POST['item']){
			$mysql->prepare = array($_POST['item']);
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' ".$filtro_tipo." AND `".$_POST['pai']."` = ? ORDER BY `ordem` ASC, `nome` ASC ";
		} else {
			$mysql->filtro = " WHERE `status` = 1 AND `lang` = '".LANG."' ".$filtro_tipo." ORDER BY `nome` ASC ";
		}
		$table = $mysql->read($_POST['table']);

		$arr['html'] = $_POST['multiple']!='true' ? '<option value="">- - -</option>' : '';
		$arr['html'] .= '<optgroup label="Ações"> ';
			$arr['html'] .= preg_match('(1_cate)', $_POST['table']) ? '<option value="(cn)">Cadastrar Novo</option> ' : '';
			$arr['html'] .= LUGAR == 'admin' ? '<option value="(gi)">Gerenciar Itens</option> ' : '';
		$arr['html'] .= '</optgroup> ';
		$arr['html'] .= '<optgroup label="Itens"> ';

			foreach ($table as $key => $value) {
				$arr['html'] .= '<option value="'.$value->id.'" ';
				if($_POST['multiple']!='true'){
					$arr['html'] .= $_POST['id']==$value->id ? 'selected' : '';
				} else {
					$ex = explode(',', $_POST['value']);
					foreach ($ex as $k => $v) {
						$arr['html'] .= $v==$value->id ? 'selected' : '';
					}
				}
				$arr['html'] .= ' > ';
					$arr['html'] .= preg_match('(1_cate)', $_POST['table']) ? tracos_nivels($value->tipo).' ' : '';
					if($_POST['table'] == 'produtos_cores_tamanhos' OR $_POST['table'] == 'produtos_opcoes1' OR $_POST['table'] == 'produtos_opcoes2' OR $_POST['table'] == 'produtos_opcoes3' OR $_POST['table'] == 'produtos_opcoes4' OR $_POST['table'] == 'produtos_opcoes5')
						$arr['html'] .= $value->nome.' (Estoque: '.$value->estoque.') ('.preco($value->preco, 1).')';
					else
						$arr['html'] .= $value->nome;
				$arr['html'] .= '</option> ';
				$arr['html'] .= preg_match('(1_cate)', $_POST['table']) ? categorias_nivels($_POST['table'], $value->id, 1000, '') : '';
			}

		$arr['html'] .= '</optgroup> ';

	echo json_encode($arr); 

?>