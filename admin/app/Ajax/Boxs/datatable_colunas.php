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
	$arr['html'] = '';


		if(isset($_POST['gravar']) and $_POST['gravar'] and LUGAR == 'admin'){
			$arr['alert'] = 1;

			$mysql->prepare = array($_SESSION['x_admin']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['itens_pagina'] = $_POST['itens_pagina'];
			$mysql->update('usuarios');		




		} elseif(LUGAR == 'admin') {
			$mysql->prepare = array($_POST['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$modulos = $mysql->read_unico('menu_admin');
			verificar_permissoes_all($modulos, 0, 'lista');

			// Colunas Gravadas em Usuarios_config
			$mysql->prepare = array($modulos->id, $_SESSION['x_admin']->id);
			$mysql->filtro = " WHERE `modulos` = ? AND `usuarios` = ? ";
			$usuarios_config = $mysql->read_unico('usuarios_config');
			if(!isset($usuarios_config->modulos)){
				$x=0;
				$colunas = array();
				foreach (unserialize(base64_decode($modulos->colunas)) as $key => $value) { $x++;
					if(isset($value['check']) and $value['check']){
						$colunas[$x] = 'coluna_'.$key;
					}
				}
				$mysql->campo['colunas'] = base64_encode(serialize($colunas));
				$mysql->campo['modulos'] = $modulos->id;
				$mysql->campo['usuarios'] = $_SESSION['x_admin']->id;
				$mysql->insert('usuarios_config');

				$mysql->prepare = array($modulos->id, $_SESSION['x_admin']->id);
				$mysql->filtro = " WHERE `modulos` = ? AND `usuarios` = ? ";
				$usuarios_config = $mysql->read_unico('usuarios_config');
			}
			$usuarios_config_colunas = unserialize(base64_decode($usuarios_config->colunas));
			// Colunas Gravadas em Usuarios_config

			// Excluindo campos q ja existem em colunas
			$colunas_menu_admin = array();
			foreach (unserialize(base64_decode($modulos->colunas)) as $key => $value) {
				if(isset($value['check']) and $value['check']){
					$ex = explode('->', $value['value']);
					$colunas_menu_admin[] = $ex[0];
				}
			}
			// Excluindo campos q ja existem em colunas


			// Pegando dados as colunas para formar o html
				$boxxs = array(1=>'', 2=>'');

				// Colunas
				foreach (unserialize(base64_decode($modulos->colunas)) as $key => $value) {
					if(isset($value['check']) and $value['check'] and $value['nome']){
						// Foto
							$foto = 0;
							if($value['value'] == 'foto'){
								foreach (unserialize(base64_decode($modulos->campos)) as $ke => $val) {
									foreach ($val as $k => $v) {
										if(isset($v['check']) and $v['check'] and  $v['input']['nome'] == 'foto')
										$foto = 1;
									}
								}
							} else $foto = 1;

						// Categorias
							$categorias = 0;
							if($value['value'] == 'relacionamento_categoria_automatico'){
								if(preg_match('(-categorias-)', $modulos->informacoes) or preg_match('(-varias_categorias-)', $modulos->informacoes) or preg_match('(-subcategorias-)', $modulos->informacoes) )
										$categorias = 1;
							} else $categorias = 1;

						if($foto and $categorias){
							$value['nome'] = $value['nome']=='NOME DA CATEGORIA' ? 'Categorias' : $value['nome'];
							$ex = explode('->', $value['nome']);
							$temp  = '<li class="wr3" dir="coluna_'.$key.'"> ';
								$temp .= '<span class="limit"> ';
									$temp .= $ex[0];
								$temp .= '</span> ';
							$temp .= '</li> ';
							if(in_array('coluna_'.$key, $usuarios_config_colunas)){
								$n = 0;
								foreach ($usuarios_config_colunas as $k1 => $v1) {
									if($v1 == 'coluna_'.$key)
										$n = $k1;
								}
								$boxxs[1][$n] = $temp;
							} else {
								$boxxs[2] .= $temp;
							}
						}
					}
				}

				// Campos
				foreach (unserialize(base64_decode($modulos->campos)) as $k => $v) {
					foreach ($v as $key => $value) {
						if(isset($value['check']) and $value['check'] and $value['nome'] and !preg_match('(categorias)', $value['input']['opcoes']) and !preg_match('(_hidden)', $value['input']['nome']) and !preg_match('(inserir_box)', $value['input']['nome']) and !preg_match('(multifotos)', $value['input']['nome']) and $value['input']['tipo'] != 'hidden' and $value['input']['tipo'] != 'textarea' and $value['input']['tipo'] != 'button' and $value['input']['tipo'] != 'editor' and $value['input']['tipo'] != 'file_editor' ){
							if(!in_array($value['input']['nome'], $colunas_menu_admin)){
								$temp  = '<li class="wr3" dir="campo_'.$k.'_'.$key.'"> ';
									$temp .= '<span class="limit"> ';
										$temp .= $value['nome'];
									$temp .= '</span> ';
								$temp .= '</li> ';
								if(in_array('campo_'.$k.'_'.$key, $usuarios_config_colunas)){
									$n = 0;
									foreach ($usuarios_config_colunas as $k1 => $v1) {
										if($v1 == 'campo_'.$k.'_'.$key)
											$n = $k1;
									}
									$boxxs[1][$n] = $temp;
								} else {
									$boxxs[2] .= $temp;
								}
							}
						}
					}
				}
			// Pegando dados as colunas para formar o html



			// Ordenando cols no boxxs 1
				ksort($boxxs[1]);
				$boxxs_1 = '';
				foreach ($boxxs[1] as $key => $value) {
					$boxxs_1 .= $value;
				}



			$arr['title'] = 'Configurações';

			$arr['html']  = '<div class="max-w900 linha mb10">
								<b class="db mb5">Quais colunas irão aparecer para este módulo:</b>
								<ul class="boxxs">
									<li class="w100p">
										<div class="colunas">
											<ul class="sortable boxxs_verde" boxxs="1">
												'.$boxxs_1.'
												<div class="clear"></div>
											</ul> 
											<hr>
											<ul class="sortable boxxs_amarelo">
												'.$boxxs[2].'
												<div class="clear"></div>
											</ul>
											<hr>
										</div>
									</li>
								</ul>
								<div class="clear"></div>
							</div>
							<button type="button" class="botao flr" onclick="datatable_colunas_delete('.A.$modulos->id.A.');'.VIEWS.'('.A.$modulos->id.A.', '.A.$modulos->tipo_modulo.A.', '.A.$modulos->gets.A.');"> <i class="mr2 fa fa-check-circle c_verde"></i> Resetar</button>
							<button class="botao" onclick="views('.A.$modulos->id.A.', '.A.$modulos->tipo_modulo.A.', '.A.$modulos->gets.A.');"> <i class="mr2 fa fa-check c_verde"></i> Atualizar</button>
							<script>boxxs('.A.'datatable_colunas_gravar("'.$modulos->id.'")'.A.');</script> ';

		} else {
			$arr['violacao_de_regras'] = 1;
			violacao_de_regras($arr);
		}

	$mysql->fim();
	echo json_encode($arr); 

?>