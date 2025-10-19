<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;

		$video = '';
		if(isset($_POST['id'])){
			$mysql->prepare = array($_POST['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$item = $mysql->read_unico( isset($_POST['table']) ? $_POST['table']  : 'videos' );
			$video = $item->link;

		} elseif(isset($_POST['link'])){
			$video = $_POST['link'];
		}

		$auto = '';
		if(isset($_POST['auto'])){
			$auto = '?autoplay=1"';
		}

		if($video){
			$arr['title'] = 'Video';
			$arr['html']  = '<div>
								<div class="w800 w100p_800 br10">
									'.video($video.$auto, '100%', 460).'
								</div>
							 </div>
							 <div class="carregando1"> <img src="'.DIR.'/web/img/outros/carregando/loader.gif" /> <span> Carregando... </span> </div> ';
		}

	echo json_encode($arr); 

?>