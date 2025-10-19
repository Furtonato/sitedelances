<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['html'] = '';


		$mysql->prepare = array($_POST['modulos']);
		$mysql->filtro = " WHERE `id` = ? ";
		$modulos = $mysql->read_unico('menu_admin');
		verificar_permissoes_all($modulos, 0, 'lista', 'mais_fotos');


		$arr['title'] = 'Mais Fotos';
		$_POST['rand'] = isset($_POST['rand'])? $_POST['rand'] : 0;

		$arr['html'] .= '<div class="w700 w-a_700 itens mb10"> ';

			$form = "form_mais_fotos_".rand();
			$arr['html'] .= '<div form="'.$form.'"></div> ';
			$arr['html'] .= '<form id="'.$form.'" class="fll mr5" action="'.DIR.'/'.ADMIN.'/app/Ajax/Boxs_acoes/mais_fotos_gravar_fotos.php?modulos='.$_POST['modulos'].'&item='.$_POST['item'].'" method="post" enctype="multipart/form-data"> ';

				$arr['html'] .= '<label for="multifotos" class="botao dibi h34 pb8 ba0 br0 input file"> ';
					$arr['html'] .= '<span class="vm c-p limit"> <i class="fa fa-file-image-o ml2 mr3 c_azul"></i> <span>Selecionar Fotos</span> </span> ';
					$arr['html'] .= '<input type="file" name="multifotos[]" id="multifotos" class="design " onchange="input_file(this)" multiple=""> ';
				$arr['html'] .= '</label> ';
				$arr['html'] .= '<input type="submit" class="dni"> ';

			$arr['html'] .= '</form> ';

			$arr['html'] .= '<script> ajaxForm('.A.$form.A.') </script> ';
			$arr['html'] .= '<button form="form_mais_fotos" onclick="submitt('.A.'#'.$form.A.')" class="botao fll mr5"> <i class="icon mr5 fa fa-check c_verde"></i> Salvar </button> ';
			$arr['html'] .= '<button form="form_mais_fotos" class="botao fll mr5"> <i class="icon mr5 fa fa-times c_vermelho"></i> Deletar Selecionados </button> ';
			$arr['html'] .= '<div class="clear"></div> ';

			$arr['html'] .= '<form id="form_mais_fotos" action="javascript:void(0)" onsubmit="mais_fotos_gravar('.A.$_POST['modulos'].A.', '.A.$_POST['item'].A.', this)" method="post" enctype="multipart/form-data"> ';
				$arr['html'] .= ' <script> mais_fotos_update('.A.$modulos->modulo.A.', '.A.$_POST['item'].A.', '.A.$_POST['modulos'].A.'); </script> ';
				$arr['html'] .= ' <div class="mais_fotos_update"></div> ';
			$arr['html'] .= '</form> ';
		
		$arr['html'] .= '</div> ';


	$mysql->fim();
	echo json_encode($arr); 

?>