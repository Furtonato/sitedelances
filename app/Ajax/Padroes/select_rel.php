<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";

	$mysql = new Mysql();

	echo isset($_GET['selecione']) ? '<option value="">'.$_GET['selecione'].'</option> ' : '<option value="">- - -</option> ';

	if(isset($_GET['estados']) and $_GET['estados']){
		$json = json_decode(file_get_contents('../../../plugins/Json/localidades/cidades/'.$_GET['estados'].'.json'));
		foreach($json as $value){
			foreach($value->cidades as $v){
				echo '<option value="'.$v.'" ';
				echo (isset($_GET['val']) and $_GET['val']==$v) ? 'selected' : '';
				echo '>'.$v.'</option>';
			}
		}
	
	} elseif(isset($_GET['cidades']) and $_GET['cidades']){

		$selected = (isset($_GET['val']) and $_GET['val']=='Centro') ? 'selected' : '';
		echo '<option value="Centro" '.$selected.'>Centro</option> ';

		$json = json_decode(file_get_contents('../../../plugins/Json/localidades/bairros/'.$_GET['uf'].'.json'));
		foreach($json as $value){
			if($value->cidade == $_GET['cidades']){
				foreach($value->bairros as $v){
					echo '<option value="'.$v.'" ';
					echo (isset($_GET['val']) and $_GET['val']==$v) ? 'selected' : '';
					echo '>'.$v.'</option>';
				}
			}
		}

	}

?>