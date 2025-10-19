<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Classes/Upload.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['html'] = '<span class="bd_ccc dn"></span>';

		$mysql->prepare = array($_POST['tabelas'], $_POST['item']);
		$mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? ORDER BY `ordem` ASC, `id` ASC ";
		$mais_fotos = $mysql->read('mais_fotos');
		foreach ($mais_fotos as $key => $value) {

			$arr['html'] .= '<div class="wf4 p10"> ';
				$arr['html'] .= '<div class="p10 br5" style="border: 1px solid #ccc"> ';

					$arr['html'] .= '<div class="wr6 fll pr10"> <label for="imgFoto_'.$value->id.'"> <img src="'.DIR.'/web/fotos/'.$value->foto.'" class="w100p max-w80"> </label> </div> ';
					$arr['html'] .= '<div class="wr6 fll"> ';
						$arr['html'] .= '<input name="nome['.$value->id.']" type="text" class="design w100p h24" value="'.$value->nome.'" placeholder="Nome" > ';
						$arr['html'] .= '<div class="h5"></div> ';
						$arr['html'] .= '<input name="ordem['.$value->id.']" type="text" class="design w50p h24 tac" value="'.$value->ordem.'" > ';
						$arr['html'] .= '<div class="h5"></div> ';
						$arr['html'] .= '<label class="fll p5" title="Selecione os itens que deseja excluir!" > <input type="checkbox" name="delete['.$value->id.']" id="imgFoto_'.$value->id.'" value="1" class="design vm"> </label> ';
						$arr['html'] .= '<a class="fll p5" onclick="datatable_acoes('.A.'block'.A.', '.A.$_POST['modulos'].A.', '.A.$value->id.A.', '.A.'mais_fotos'.A.', '.A.$_POST['item'].A.')">    <i class="di fz16  fa fa-check '.iff($value->status, 'c_verde', 'n_ativo').'"></i> </a> ';
						$arr['html'] .= '<a class="fll p5" onclick="datatable_acoes('.A.'delete'.A.', '.A.$_POST['modulos'].A.', '.A.$value->id.A.', '.A.'mais_fotos'.A.', '.A.$_POST['item'].A.')">    <i class="di fz16 fa fa-times c_vermelho"></i> </a> ';
						$arr['html'] .= '<div class="clear"></div> ';
					$arr['html'] .= '</div> ';
					$arr['html'] .= '<div class="clear"></div> ';

				$arr['html'] .= '</div> ';
			$arr['html'] .= '</div> ';

		}
		$arr['html'] .= '<div class="clear"></div> ';

		$arr['n'] = count($mais_fotos);

	$mysql->fim();
	echo json_encode($arr); 

?>