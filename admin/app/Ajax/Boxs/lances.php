<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Classes/Email.php";
	require_once "../../../../app/Classes/Input.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();


		if(isset($_POST['id']) AND $_POST['id'] AND isset($_POST['excluir_lance']) AND $_POST['excluir_lance'] AND LUGAR == 'admin'){

			if(isset($_POST['lances']) AND $_POST['lances']){
				$mysql->logs = 0;
				$mysql->filtro = " WHERE lotes = '".$_POST['id']."' AND FORMAT(`lances`,2) = FORMAT(".$_POST['lances'].",2) ";
				$lotes_lances = $mysql->delete('lotes_lances');

			} else {
				$mysql->filtro = " WHERE lotes = '".$_POST['id']."' ORDER BY lances desc ";
				$lotes_lances = $mysql->read_unico('lotes_lances');

				unset($mysql->campo);
				$mysql->logs = 0;
				$mysql->campo['lances_data'] = $lotes_lances->data;
				$mysql->campo['lances'] = $lotes_lances->lances;
				$mysql->campo['lances_cadastro'] = $lotes_lances->cadastro;
				$mysql->campo['lances_plaquetas'] = $lotes_lances->plaquetas;
				$mysql->filtro = " where id = '".$_POST['id']."' ";
				$ult_id = $mysql->update('lotes');

				$mysql->logs = 0;
				$mysql->filtro = " WHERE lotes = '".$_POST['id']."' ORDER BY lances desc LIMIT 1 ";
				$mysql->delete('lotes_lances');
			}

			$arr['alert'] = 1;
			$arr['evento'] = 'boxs('.A.'lances'.A.', '.A.'id='.$_POST['id'].A.') ';



		} elseif(isset($_POST['id']) AND $_POST['id'] AND LUGAR == 'admin') {

			$mysql->colunas = 'id, lances, lances_cadastro, lances_plaquetas, lances_data';
			$mysql->filtro = " WHERE id = '".$_POST['id']."' ";
			$lotes = $mysql->read_unico('lotes');

			$mysql->filtro = " where lotes = '".$_POST['id']."' ORDER BY lances desc ";
			$lotes_lances = $mysql->read('lotes_lances');

			$arr['title'] = 'Lances Efetuados';
			$arr['html']  = '<div class="w800 w100p_800"> ';
				if( (isset($lotes->lances) AND $lotes->lances) OR $lotes_lances ){
					$arr['html'] .= '<table class="w100p">
                                        <tr>
                                            <th class="table_top tac">Tipo</th>
                                            <th class="table_top">Usúario / Plaqueta</th>
                                            <th class="table_top tac">Data</th>
                                            <th class="table_top tac">Valor</th>
                                            <th class="table_top tac">Ações</th>
                                        </tr> ';
										if(isset($lotes->lances) AND $lotes->lances){
											$arr['html'] .= '<tr class="back_hover_F7F7F7"> ';
												$arr['html'] .= '<td class="p10 tac table_center back0">'.tipo_lance($lotes->lances_plaquetas).'</td> ';
												$arr['html'] .= '<td class="p10 tac table_center back0">'.lances_cadastro($lotes->lances_cadastro, $lotes->lances_plaquetas).'</td> ';
												$arr['html'] .= '<td class="p10 tac table_center back0">'.data($lotes->lances_data, 'd/m/Y H:i:s').'</td> ';
												$arr['html'] .= '<td class="p10 tac table_center back0">'.preco($lotes->lances, 1).'</td> ';
												$arr['html'] .= '<td class="p10 tac table_center back0"> ';
													$arr['html'] .= '<form id="form_lances" method="post" action="'.$_SERVER['SCRIPT_NAME'].'"> ';
														$arr['html'] .= '<input type="hidden" name="excluir_lance" value="1"> ';
														$arr['html'] .= '<input type="hidden" name="id" value="'.$_POST['id'].'"> ';
														$arr['html'] .= '<button class="back0 bd0"><i class="fa fa-times fz16 cor_f00"></i></button> ';
											 		$arr['html'] .= '</form> ';
													$arr['html'] .= '<script>ajaxForm('.A.'form_lances'.A.');</script> ';
												$arr['html'] .= '</td>';
											$arr['html'] .= '</tr>';
											}
											foreach ($lotes_lances as $key => $value) {
												$arr['html'] .= '<tr class="back_hover_F7F7F7"> ';
													$arr['html'] .= '<td class="p10 tac table_center back0">'.tipo_lance($value->plaquetas).'</td> ';
													$arr['html'] .= '<td class="p10 tac table_center back0">'.lances_cadastro($value->cadastro, $value->plaquetas).'</td> ';
													$arr['html'] .= '<td class="p10 tac table_center back0">'.data($value->data, 'd/m/Y H:i:s').'</td> ';
													$arr['html'] .= '<td class="p10 tac table_center back0">'.preco($value->lances, 1).'</td> ';
													$arr['html'] .= '<td class="p10 tac table_center back0"> ';
														$arr['html'] .= '<form id="form_lances" method="post" action="'.$_SERVER['SCRIPT_NAME'].'"> ';
															$arr['html'] .= '<input type="hidden" name="excluir_lance" value="1"> ';
															$arr['html'] .= '<input type="hidden" name="lances" value="'.$value->lances.'"> ';
															$arr['html'] .= '<input type="hidden" name="id" value="'.$_POST['id'].'"> ';
															$arr['html'] .= '<button class="back0 bd0"><i class="fa fa-times fz16 cor_f00"></i></button> ';
												 		$arr['html'] .= '</form> ';
														$arr['html'] .= '<script>ajaxForm('.A.'form_lances'.A.');</script> ';
													$arr['html'] .= '</td>';
												$arr['html'] .= '</tr>';
											}
					$arr['html'] .= '	</table>';
				} else {
					$arr['html'] .= '<div>Nenhum Lance Encontrado...</div>';
				}
			$arr['html'] .= '</div> ';


		} else {
			$arr['violacao_de_regras'] = 1;
			violacao_de_regras($arr);
		}

	$mysql->fim();
	echo json_encode($arr); 

?>