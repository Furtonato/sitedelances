<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	$mysql = new Mysql();
	$mysql->ini();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['id']) and $_POST['id']){

			if(isset($_POST['box'])){
				$mysql->prepare = array($_POST['id']);
				if(LUGAR == 'admin'){
					$mysql->filtro = " WHERE `id` = ? ";
				} else {
					$mysql->filtro = " WHERE `id` = ? ";
				}
				$item = $mysql->read_unico($_POST['box']);

				// Txt
				if($_POST['box'] == 'dicas'){
					if(isset($item->id)){
						$arr['title'] = 'Dicas';
						$arr['html']  = '<div class="w700 w-a_700">
											<b class="db pb30 fz30">'.$item->nome.'</b>
												<img src="'.DIR.'/web/fotos/'.$item->foto.'" class="max-w300 max-h300 fll mr10 mb10">
												'.txt($item).'
												<div class="clear"></div>
									    </div> ';
					}

				// Videos
				} elseif($_POST['box'] == 'videos'){
					if(isset($item->id)){
						$arr['title'] = 'Video Aula';
						$arr['html']  = '<div class="w700 w-a_700">
											'.player($item->foto, '100%', 360).'
									    </div> ';
					}
	            }

            }

		}

	$mysql->fim();
	echo json_encode($arr); 

?>