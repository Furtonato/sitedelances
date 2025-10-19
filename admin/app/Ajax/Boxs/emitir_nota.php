<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";


	$arr = array();
	$arr['alert'] = 0;


		$arr['title'] = 'Emitir Nota';
		$arr['html']  = '<form method="get" action="'.DIR.'/imprimir/nota1/" target="_blank" enctype="multipart/form-data" >
							<input type="hidden" name="ids" value="'.$_POST['ids'].'">
							<div class="linha mb15">
								<div class="linha mb15">
									<b class="db mb5">Observações:</b>
									<textarea name="emitir_nota_txt" class="w500 h300 design"></textarea>
								</div>
								<button class="botao flr h30 pl10 pr10"> <i class="mr2 fa fa-check c_verde"></i> Emitir Nota</button>
								<div class="clear"></div>
							</div>
						</form>';


	echo json_encode($arr); 

?>