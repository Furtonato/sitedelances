<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../../app/Classes/Imagem.php";
	require_once "../../../../app/Classes/Input.php";

	$mysql = new Mysql();
	$mysql->ini();

	$input = new Input();

	$arr = array();
	$arr['html'] = '';

		$ids[0] = $_POST['id'];
		$arr['acao'] = $_POST['id'] ? 'edit' : 'novo';

		$mysql->prepare = array($_POST['modulos']);
		$mysql->filtro = " WHERE `id` = ? ";
		$modulos = $mysql->read_unico('menu_admin');

		if(isset($modulos->id)){

			$table = $modulos->modulo;
			if(isset($_POST['table']) and $_POST['table'] == 'mais_comentarios'){
				$table = 'mais_comentarios';
				verificar_permissoes_all($modulos, $ids, $arr['acao'], $_POST['table']);
				if($_POST['id']){
					$filtro = " and tabelas = '".$modulos->modulo."' and item = '".$_POST['item']."' ";
					$linhas = consulta_no_banco('mais_comentarios', $ids, $filtro, $arr['acao']);
            		if(!$linhas) violacao_de_regras();
            	} else {
					$linhas = '';
            	}

				$outros_inputs   = '<input type="hidden" name="table" value="'.$_POST['table'].'" >';
				$outros_inputs  .= $arr['acao'] != 'edit' ? '<input type="hidden" name="tabelas" value="'.$modulos->modulo.'" >' : '';

				$modulos->id 	 = 'boxs';
	            $modulos->abas   = base64_encode( serialize( array( array('nome'=>'', 'check'=>1, 'disabled'=>0 ) )) );
	            $modulos->campos = base64_encode( serialize( array( array( 1 => array('check'=>1, 'temp'=>'text', 'fields'=>1, 'resp'=>'wr12', 'nome'=>'Nome', 'input' => array( 'nome'=>'nome', 'tags'=>'', 'opcoes'=>'', 'extra'=>'', 'tipo'=>'text', 'tipo1'=>'text', 'design'=>1, 'disabled'=>0, 'executar_funcao'=>''), 'dois_pontos'=>':', 'nome_classe'=>'', 'pai_fields_classe'=>'', 'fields_classe'=>'', 'legend'=>'' ),          'txt' => array('check'=>1, 'temp'=>'textarea', 'fields'=>1, 'resp'=>'wr12', 'nome'=>'Descrição curta', 'input' => array( 'nome'=>'txt', 'tags'=>'', 'opcoes'=>0, 'extra'=>'', 'tipo'=>'textarea', 'tipo1'=>'text', 'design'=>1, 'disabled'=>0, 'executar_funcao'=>''), 'dois_pontos'=>':', 'nome_classe'=>'', 'pai_fields_classe'=>'', 'fields_classe'=>'', 'legend'=>'' ) ) ) ));
			} else {
				$linhas = verificar_permissoes_all($modulos, $ids, $arr['acao']);
				if($arr['acao'] != 'edit' and isset($_POST['pai']) and isset($_POST['pai_value']))
					$outros_inputs =  '<input type="hidden" name="'.$_POST['pai'].'" value="'.$_POST['pai_value'].'" >';
			}


			// HTML
		        $modulos_abas = $modulos->abas ? unserialize(base64_decode($modulos->abas)) : array();
		        $modulos_campos = $modulos->campos ? unserialize(base64_decode($modulos->campos)) : array();

				$arr['html'] = '<div class="w900 w100p_900 posr gerenciar_itens mb10"> ';
			    	$form = "form_campos_boxs_".$_POST['rand'];
			    	$arr['html'] .= '<form class="'.$form.'" action="javascript:void(0)" method="post" enctype="multipart/form-data"> ';

		    		$arr['html'] .= '<a href="javascript:datatable_campos_boxs_fechar()" class="fechar"><i class="fa fa-times"></i></a> ';
			    	$arr['html'] .= '<h3> Cadastro de '.str_replace('_', ' ', $modulos->nome).' </h3> ';

						$arr['html'] .= '<button class="botao mt7 ml25 c_verde"> <i class="mr5 fa fa-check"></i> <span>Salvar</span> </button> ';

			            foreach ($modulos_abas as $kabas => $value_abas){
				    		$arr['html'] .= '<ul class="itens"> ';
				    			$arr['html'] .= campos_das_paginas($modulos_campos[$kabas], $kabas, $linhas, $modulos->modulo);
					    		$arr['html'] .= '<div class="clear"></div> ';
				    		$arr['html'] .= '</ul> ';
				    		$arr['html'] .= '<div class="clear"></div> ';
				    	}

			        	$arr['html'] .= '<input type="hidden" name="acao_button" value="1"> ';
			        	$arr['html'] .= '<input type="hidden" name="datatable_boxs" value="1"> ';

			        	$arr['html'] .= '<input type="hidden" name="rand" value="'.$_POST['rand'].'"> ';
						$arr['html'] .= (isset($_POST['item']) and $_POST['item'] and $arr['acao'] != 'edit') ? '<input type="hidden" name="datable_boxs_item" value="'.$_POST['item'].'" >' : '';
						$arr['html'] .= (isset($_POST['pai']) and $_POST['pai'] and $arr['acao'] != 'edit') ? '<input type="hidden" name="datable_boxs_pai" value="'.$_POST['pai'].'" >' : '';
			        	$arr['html'] .= isset($outros_inputs) ? $outros_inputs : '';

			        $arr['html'] .= '</form> ';
			        $arr['html'] .= '<script> gravar_item('.A.$modulos->id.A.', '.A.$ids[0].A.', '.A.'.boxs .datatable_campos_boxs form.'.$form.A.') </script> ';
		        $arr['html'] .= '</div> ';
			// HTML

		}


	$mysql->fim();
	echo json_encode($arr); 
?>