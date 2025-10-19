<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();

		if(isset($_POST['gravar']) and $_POST['gravar'] and LUGAR == 'admin'){
			$arr['alert'] = 1;

			$mysql->prepare = array($_SESSION['x_admin']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['itens_pagina'] = $_POST['itens_pagina'];
			$mysql->update('usuarios');		

			$arr['evento'] = 'window.location.reload(); ';


		} elseif(isset($_POST['reset']) and $_POST['reset']){
			$arr['alert'] = 1;

			$mysql->prepare = array($_SESSION['x_admin']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['itens_pagina'] = '';
			$mysql->update('usuarios');		

			$arr['evento']  = '$("select[name=itens_pagina]").val(25); ';
			$arr['evento'] .= 'window.location.reload(); ';


		} elseif(LUGAR == 'admin') {
			$mysql->prepare = array($_SESSION['x_admin']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$usuarios = $mysql->read_unico('usuarios');
			$itens_pagina = (isset($usuarios->itens_pagina) and $usuarios->itens_pagina) ? $usuarios->itens_pagina : 25;

			$arr['title'] = 'Configurações';

			$arr['html']  = '<form id="configForm" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">
								<div class="linha mb10">
									<b class="db mb5">Configurar o número de itens por pagina nas tabelas:</b>
									<select name="itens_pagina" class="design">
										<option value="10" '.iff($itens_pagina==10, 'selected').'>10</option>
										<option value="25" '.iff($itens_pagina==25, 'selected').'>25</option>
										<option value="50" '.iff($itens_pagina==50, 'selected').'>50</option>
										<option value="100" '.iff($itens_pagina==100, 'selected').'>100</option>
										<option value="500" '.iff($itens_pagina==500, 'selected').'>500</option>
										<option value="1000" '.iff($itens_pagina==1000, 'selected').'>1000</option>
										<option value="10000" '.iff($itens_pagina==10000, 'selected').'>10000</option>
										<option value="1000000" '.iff($itens_pagina==1000000, 'selected').'>1000000</option>
									</select>
									<input type="hidden" name="gravar" value="1">
								</div>
								<button type="button" class="botao flr" onclick="ajaxNormalAdmin('.A.'Boxs/itens_por_pagina.php'.A.', '.A.'reset=1'.A.')"> <i class="mr2 fa fa-check-circle c_verde"></i> Resetar</button>
								<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Salvar</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('.A.'configForm'.A.');</script> ';

		} else {
			$arr['violacao_de_regras'] = 1;
			violacao_de_regras($arr);
		}

	$mysql->fim();
	echo json_encode($arr); 

?>