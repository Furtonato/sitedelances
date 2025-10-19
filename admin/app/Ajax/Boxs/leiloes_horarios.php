<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Classes/Email.php";
	require_once "../../../../app/Classes/Input.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$input = new Input();

	$arr = array();

		if(isset($_POST['id']) AND $_POST['id'] AND isset($_POST['horarios_lotes']) AND $_POST['horarios_lotes'] AND LUGAR == 'admin'){

            // Datas Firefox
			data_firefox();

			// DATAS DOS LOTES
				$lotes = array(0);
				foreach ($_POST['lote_data_ini'] as $key => $value) {
					$lotes[] = $key;

					unset($mysql->campo);
					$mysql->campo['data_ini'] = $_POST['lote_data_ini'][$key];
					$mysql->campo['data_fim'] = $_POST['lote_data_fim'][$key];
					$mysql->filtro = " where id = '".$key."' ";
					$mysql->update('lotes');
				}

				// Data Ini
				$mysql->filtro = " WHERE leiloes = '".$_POST['id']."' AND id IN (".implode(',', $lotes).") ORDER BY data_ini ASC ";
				$lotes_data_ini = $mysql->read_unico('lotes');

				// Data Fim
				$mysql->filtro = " WHERE leiloes = '".$_POST['id']."' AND id IN (".implode(',', $lotes).") ORDER BY data_fim DESC ";
				$lotes_data_fim = $mysql->read_unico('lotes');

				// GravANDo Datas dos Leiloes
				unset($mysql->campo);
				$mysql->campo['data_ini'] = $lotes_data_ini->data_ini;
				$mysql->campo['data_fim'] = $lotes_data_fim->data_fim;
				$mysql->filtro = " where id = '".$_POST['id']."' ";
				$mysql->update('leiloes');
			// DATAS DOS LOTES

			// DATAS PARA TODOS OS LOTES
				if(isset($_POST['leilao_data_ini_check']) AND $_POST['leilao_data_ini_check'] == 1){
					unset($mysql->campo);
					$mysql->campo['data_ini'] = $_POST['leilao_data_ini'];
					$mysql->filtro = " where leiloes = '".$_POST['id']."' ";
					$mysql->update('lotes');

					$mysql->campo['data_ini'] = $_POST['leilao_data_ini'];
					$mysql->filtro = " where id = '".$_POST['id']."' ";
					$mysql->update('leiloes');					
				}
				if(isset($_POST['leilao_data_fim_check']) AND $_POST['leilao_data_fim_check'] == 1){
					unset($mysql->campo);
					$mysql->campo['data_fim'] = $_POST['leilao_data_fim'];
					$mysql->filtro = " where leiloes = '".$_POST['id']."' ";
					$mysql->update('lotes');

					$mysql->campo['data_fim'] = $_POST['leilao_data_fim'];
					$mysql->filtro = " where id = '".$_POST['id']."' ";
					$mysql->update('leiloes');					
				}
			// DATAS PARA TODOS OS LOTES


			$arr['alert'] = 1;
			$arr['evento'] = '$(".fundoo").trigger("click");';
			$arr['evento'] .= 'datatable_update(); ';






		} elseif(isset($_POST['id']) AND $_POST['id'] AND LUGAR == 'admin'){

			$mysql->filtro = " WHERE leiloes = '".$_POST['id']."' ORDER BY ordem ASC, nome ASC, id DESC ";
			$lotes = $mysql->read('lotes');

			$arr['title'] = 'Tabela de Horários do Leilão de Lotes';
			$arr['html']  = '<div class="w800 w100p_800"> ';
				if(count($lotes)){
					$arr['html'] .= '<form id="formHorarios" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">
										<input type="hidden" name="horarios_lotes" value="1">
										<input type="hidden" name="id" value="'.$_POST['id'].'">
										<div class="">
											<div><b>Mudar Todos os Horários</b></div>
											<div class="wr6 pt5">
												<label>
													<input type="checkbox" name="leilao_data_ini_check" value="1" class="mr2 vam">
													Data inicial:
												</label>
												<input type="datetime-local" name="leilao_data_ini" class="design">
												'.iff(browser()!='chrome', '<input type="hidden" name="datatime_firefox[leilao_data_ini]" />').'
											</div>
											<div class="wr6 pt5">
												<label>
													<input type="checkbox" name="leilao_data_fim_check" value="1" class="mr2 vam">
													Data Fim: 
												</label>
												<input type="datetime-local" name="leilao_data_fim" class="design">
												'.iff(browser()!='chrome', '<input type="hidden" name="datatime_firefox[leilao_data_fim]" />').'
											</div>
											<div class="clear"></div>
										</div>
										<div class="pt10">
											<div class="pb5"><b>Horários dos Lotes</b></div> ';
											foreach ($lotes as $key => $value) {
												$data_ini = 'lote_data_ini['.$value->id.']';
												$data_fim = 'lote_data_fim['.$value->id.']';

												$input->value = (object)array();
												$input->value->$data_ini = $value->data_ini;
												$input->value->$data_fim = $value->data_fim;
												
					$arr['html'] .= '			<div class="pt10 pr10 mb10 bd_ccc">
													<div class="pl10 fwb">Nº do Lote: '.$value->ordem.'</div>
													<div class="wr6 pt5">
														'.$input->text('Data Ini', $data_ini, 'datetime-local').'
													</div>
													<div class="wr6 pt5">
														'.$input->text('Data Fim', $data_fim, 'datetime-local').'
													</div>
													<div class="h10 clear"></div>
												</div>';
											}
					$arr['html'] .= '		<div class="">
												<button class="botao"> <i class="mr5 fa fa-check c_verde"></i> Salvar </button>
											</div>
										</div>
									</form>
									<script>ajaxForm('.A.'formHorarios'.A.');</script> ';
				} else {
					$arr['html'] .= '<div>Nenhum Lote Cadastrado...</div>';
				}
			$arr['html'] .= '</div> ';


		} else {
			$arr['violacao_de_regras'] = 1;
			violacao_de_regras($arr);
		}

	$mysql->fim();
	echo json_encode($arr); 

?>