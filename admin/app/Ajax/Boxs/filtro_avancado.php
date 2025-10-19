<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../../app/Classes/Input.php";

	$mysql = new Mysql();
	$mysql->ini();

	$input = new Input();

	$arr = array();

		if(isset($_POST['acao']) and $_POST['acao'] == 'itens'){

			if(isset($_POST['datatable_filtro']['value'])){
				foreach ($_POST['datatable_filtro']['value'] as $key => $value){
					$filtro[] = datatable_filtro($key, $value);
				}
			}

			if(isset($filtro)){
				$arr['html']  = '<ul class="verde"> ';
					$arr['html'] .= '<b>Filtros: </b> ';
					foreach ($filtro as $key => $value){
						if($value['nome']){
							$arr['html'] .= '<li> ';
								$arr['html'] .= '<a onclick="datatable_filtro_delete_item('.A.$_POST['modulo'].A.', '.A.$value['name'].A.', this)"><i class="fa fa-times mr5 fz14 c_verde"></i></a>';
								$arr['html'] .= $value['nome'].': '.$value['item'];
							$arr['html'] .= '</li> ';
						}
					}
				$arr['html'] .= '</ul> ';
			}



		} elseif(isset($_POST['acao']) and $_POST['acao'] == 'filtro_inicial'){
			$arr['post']  = '';
			$mysql->prepare = array($_POST['modulo']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			if(isset($modulos->modulo) and isset($_SESSION['filtro_inicial'][$modulos->modulo])){
				foreach ($_SESSION['filtro_inicial'][$modulos->modulo] as $key => $value) {
					foreach ($value as $k => $v) {
						$arr['post'] .= '&datatable_filtro['.$k.']['.$key.']='.$v;
					}
				}
			}

			$arr['boxs'] = $_POST['boxs'];



		} elseif(isset($_POST['acao']) and $_POST['acao'] == 'novo'){
			$arr['post']  = '';
			$arr['post'] .= '&datatable_filtro[nome]['.$_POST['name'].']='.$_POST['nome'];
			$arr['post'] .= '&datatable_filtro[tipo]['.$_POST['name'].']='.$_POST['tipo'];
			$arr['post'] .= '&datatable_filtro[value]['.$_POST['name'].']='.$_POST['value'];
			$arr['post'] .= '&datatable_filtro[opcoes]['.$_POST['name'].']='.$_POST['opcoes'];

			$mysql->prepare = array($_POST['modulo']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			if(isset($modulos->modulo)){
				$_SESSION['filtro_inicial'][$modulos->modulo][$_POST['name']]['nome'] = $_POST['nome'];
				$_SESSION['filtro_inicial'][$modulos->modulo][$_POST['name']]['tipo'] = $_POST['tipo'];
				$_SESSION['filtro_inicial'][$modulos->modulo][$_POST['name']]['value'] = $_POST['value'];
				$_SESSION['filtro_inicial'][$modulos->modulo][$_POST['name']]['opcoes'] = $_POST['opcoes'];
			}



		} elseif(isset($_POST['acao']) and $_POST['acao'] == 'delete'){
			$arr['post'] = '';
			$arr['item'] = $_POST['name'];

			$mysql->prepare = array($_POST['modulo']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			$modulo = isset($modulos->modulo) ? $modulos->modulo : '';

			foreach ($_POST['datatable_filtro'] as $k => $v){
				foreach ($v as $key => $value){
					 $arr['post'] .= datatable_filtro_del($modulo, $k, $key, $value);
				}
			}



		} else {

			$mysql->prepare = array($_POST['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');

			$arr['title'] = 'Filtro Avançado';

			$arr['html']  = '<div class="max-w900 linha mb10">
								<form method="post" action="javascript:void(0)" onsubmit="datatable_filtro('.A.$modulos->id.A.', this)" autocomplete="off">
									<ul class="filtro_avancado"> ';

										$linhas = '';
										$filtro_avancado = '';
										$modulos_campos = unserialize(base64_decode($modulos->campos));
										require_once "../../../views/Individual/filtros.php";

						                foreach ($campos as $k => $v) {
							                foreach ($v as $key => $value) {

												$input_nome = $value['input']['nome'];
												$tipo = isset($value['input']['tipo']) ? $value['input']['tipo'] : 'text';
												$tipo1 = (isset($value['input']['tipo1']) and $value['input']['tipo1']) ? $value['input']['tipo1'] : 'text';

												if(isset($value['check']) and $value['check'] and !preg_match('(_hidden)', $input_nome) and !preg_match('(inserir_box)', $input_nome) and $tipo1!='color' ){

													if($tipo != 'hidden' and $tipo != 'file' and $tipo != 'textarea' and $tipo != 'button' and $tipo != 'editor' and $tipo != 'file_editor' and (!isset($value['input']['disabled']) or !$value['input']['disabled']) ){

														$table = $modulos->modulo;
														$tags = isset($value['input']['tags']) ? $value['input']['tags'] : '';
														$tags_real = $tags;
														$value['input']['design'] = isset($value['input']['design']) ? $value['input']['design'] : 1;
									                    if($value['input']['design']!=2){
									                        $desgin = 'class="design ';
									                        $tags = !preg_match('(designx)', $tags) ? str_replace('class="', $desgin, $tags) : $tags;
									                        $tags = !(preg_match('(class=")', $tags)) ? $desgin.'" '.$tags : $tags;
									                    }
									                    $tags = str_replace('required', '', $tags);
									                    $tags = str_replace('multiple', '', $tags);
									                    $tags = str_replace('onblur=', '', $tags);
									                    $tags = str_replace('onclick=', '', $tags);

								                    	$input->value = isset($_POST['datatable_filtro']['value'][$input_nome]) ? $_POST['datatable_filtro']['value'][$input_nome] : '';

									                    $input->opcoes = isset($value['input']['opcoes']) ? $value['input']['opcoes'] : '';

									                    $input->check_ini = 0;
									                    $input->filtro_avancado = 1;
									                    $input->gerenciar_item = 0;
									                    $input->tags = $tags;
									                    $input->table = $table;
									                    $input->extra = isset($value['input']['extra']) ? $value['input']['extra'] : '';
									                    $input->p = isset($value['nome_classe']) ? $value['nome_classe'] : '';
									                    $input->dois_pontos = isset($value['dois_pontos']) ? $value['dois_pontos'] : ':';

														$tipo = $tipo=='radio' ? 'select' : $tipo;

														$tipo1 = (isset($value['temp']) and $value['temp']=='preco') ? 'search' : $tipo1;

														$value['resp'] = isset($value['resp']) ? $value['resp'] : 12;
										                $arr['html'] .= ' <li class="'.$value['resp'].' '.iff(preg_match('(multiple)', $input->tags), 'h-a').' "> ';

										                	// Inputs
										                	if($tipo1 == 'date' or $tipo1 == 'datetime-local'){
									                    		$arr['html'] .= $input->$tipo($value['nome'], 'datatable_filtro[value]['.$input_nome.']', 'date');
									                    	} else {
										                    	$arr['html'] .= $input->$tipo($value['nome'], 'datatable_filtro[value]['.$input_nome.']', $tipo1);
									                    	}

									                    	$arr['html'] .= '<input type="hidden" name="datatable_filtro[nome]['.$input_nome.']" value="'.$value['nome'].'"> ';

									                    	$tipo = $tipo1!='text' ? $tipo1 : $tipo;
									                    	$tipo = (isset($value['temp']) and $value['temp']=='preco') ? 'preco' : $tipo;
									                    	$tipo = preg_match('(multiple)', $tags_real) ? 'checkbox' : $tipo;
									                    	$arr['html'] .= '<input type="hidden" name="datatable_filtro[tipo]['.$input_nome.']" value="'.$tipo.'"> ';

									                    	if(isset($value['filtro']) and $value['filtro'])
									                    		$arr['html'] .= '<input type="hidden" name="datatable_filtro[filtro]['.$input_nome.']" value="'.$value['filtro'].'"> ';

									                    	if($input->filtro_avancado and ($tipo=='select' or $tipo=='checkbox' or $tipo=='radio') )
									                    		$arr['html'] .= '<input type="hidden" name="datatable_filtro[opcoes]['.$input_nome.']" value="'.$input->opcoes.'"> ';

											                $arr['html'] .= ' <div class="clear"></div> ';

										                $arr['html'] .= ' </li> ';

													}

									            }
								            }
								        }

	        $arr['html'] .= ' 			<div class="clear h10"></div>
									</ul>
									<button type="reset" class="botao flr" onclick="window.location.reload();"> <i class="mr2 fa fa-check-circle c_verde"></i> Reset</button>
									<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Pesquisar</button>
								</form>
							 </div> ';

		}


	$mysql->fim();
	echo json_encode($arr); 

?>