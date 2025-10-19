<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../app/Classes/criarMysql.php";
	require_once "../../../app/Classes/publicMysql.php";

	$mysql = new Mysql();
	$mysql->ini();

	$criarMysql = new criarMysql();

	$arr = array();

		$gerenciar_itens = 1; // Variavel Informando q estou no gereciamento de itens
		$arr['title'] = 'Gerenciar Itens';
		$_POST['rand'] = isset($_POST['rand'])? $_POST['rand'] : 0;


		// CONFIGS
			if(isset($_POST['id'])){
				$mysql->prepare = array($_POST['id']);
				$mysql->filtro = " WHERE `id` = ? ";
			} else {
				$mysql->prepare = array($_POST['table']);
				$mysql->filtro = " WHERE `modulo` = ? ";
			}
			$modulos = $mysql->read_unico('menu_admin');

			// MENU ADMIN
			if(isset($modulos->id)){
				$_GET['modulo']  = $modulos->id;
				$_GET['modulo'] .= (isset($_POST['pai']) and $_POST['pai']) ? '&pai='.$_POST['pai'].'&item='.$_POST['item'] : '';
				$filtro = (isset($_POST['pai']) and $_POST['pai']) ? " and ".$_POST['pai']." = '".$_POST['item']."' " : "";

				$linhas = verificar_permissoes_all($modulos, 0, 'lista');
				require_once "../../../app/controllers.php";

				$datatables_top[] = 'Ações';
				$passar_para_ajax .= '"col['.count($datatables_center).']": "id->acoes_boxs->class=tac",';
				$passar_para_ajax .= '"rand": "'.$_POST['rand'].'", ';
		        $modulos->order = '10';
				$arr['script'] = datatable_script($modulos, $passar_para_ajax, $datatables_center, '', '_boxs');


			// MODULOS (MAIS COMENTARIOS)
			} elseif(LUGAR=='admin' and isset($_POST['table']) and $_POST['table']=='mais_comentarios'){
				$mysql->prepare = array($_POST['modulos']);
				$mysql->filtro = " WHERE `id` = ? ";
				$modulos = $mysql->read_unico('menu_admin');
				verificar_permissoes_all($modulos, 0, 'lista', $_POST['table']);

				$novo_item = 1; // Se quiser sem Star coloque 0;
				$star = 1; // Se quiser sem Star coloque 0;
				$_GET['modulo'] = $modulos->id.'&table='.$_POST['table'].'&item='.$_POST['item'];
				$datatables_top = (isset($star) and $star) ? array('Nome', 'Texto', 'Estrelas', 'Ação') : array('Nome', 'Texto', 'Ação');
				$data = '"pg": "'.$_POST['table'].'", "filtro": " and item = '.A.$_POST['item'].A.' and tabelas = '.A.$modulos->modulo.A.' ", "modulo":"'.$modulos->id.'", "rand": "'.$_POST['rand'].'", "col[0]": "nome", ';
				$data .= (isset($star) and $star) ? '"col[1]": "txt", "col[2]": "star->star", "col[3]": "id->acoes_boxs1->class=tac",' : '"col[1]": "txt", "col[2]": "id->acoes_boxs->class=tac",';

				unset($modulos);
				$modulos->informacoes = 'novo';
				$modulos->order = 10;
				$arr['script'] = datatable_script($modulos, $data, array('nome'), '', '_boxs');


			// NAO AUTORIZADO
            } else {
            	$arr['violacao_de_regras'] = 1;
            	violacao_de_regras($arr);
			}
		// CONFIGS




		// HTML
			$arr['html'] = '<div class="w900 w100p_900 posr gerenciar_itens mb10 boxs_'.$_POST['rand'].'"> ';
				if(preg_match('(novo)', $modulos->informacoes) AND !(isset($novo_item) AND !$novo_item) AND $_POST['table']!='mais_comentarios' ){
					$arr['html'] .= '<div class="posa z2 mb10"> ';
						$arr['html'] .= '<button class="botao" onclick="datatable_campos_boxs('.A.$_GET['modulo'].A.', '.A.$_POST['rand'].A.', '.A.A.');"> <i class="icon mr5 fa fa-plus-circle c_verde"></i> Novo </button> ';
					$arr['html'] .= '</div> ';
				}
				$arr['html'] .= '<div class="datatable_campos_boxs ml10 mr10 posa t0 l0 z5"></div> ';

				// Datatable
				$arr['html'] .= '<table class="datatable_boxs"> ';
					$arr['html'] .= datatable_top($modulos, $datatables_top);
					$arr['html'] .= '</tbody> ';
				$arr['html'] .= '</table> ';
				$arr['html'] .= '<div class="clear"></div> ';

			$arr['html'] .= '</div> ';
		// HTML


		// SCRIPT
			$arr['evento']  = 'setTimeout(function(){ ';
				$arr['evento'] .= '$(".gerenciar_itens .dataTables_filter").after('.A.' <div class="clear"></div> '.A.'); ';
				$arr['evento'] .= '$(".gerenciar_itens table.dataTable").wrap('.A.'<div class="table_mobile"></div>'.A.'); ';
				//$arr['evento'] .= '$(".gerenciar_itens table.dataTable").sortable({ cursor: "move" }); ';
			$arr['evento'] .= '}, 0.5); ';
		// SCRIPT


	$mysql->fim();
	echo json_encode($arr); 

?>