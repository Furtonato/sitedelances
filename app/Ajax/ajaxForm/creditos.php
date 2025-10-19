<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';
	include_once '../../../app/Classes/Email.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 'z';


		if(isset($_POST['creditos'])){
			$mysql->prepare = array($_SESSION['x_site']->id);
			$mysql->filtro = " WHERE `id` = ? ";
			$cadastro = $mysql->read_unico('cadastro');

			// Creditos
			$_POST['creditos'] = $cadastro->creditos>$_POST['creditos'] ? $_POST['creditos'] : $cadastro->creditos;
			$arr['creditos'] = $_SESSION['creditos'] = $_POST['creditos'];

			// Mostrar ou nao creditos
			if($arr['creditos'])
				$arr['evento']  = '$(".carrinhoo_box_credito").fadeIn(); ';
			else
				$arr['evento']  = '$(".carrinhoo_box_credito").fadeOut(); ';

			$arr['evento'] .= '$(".creditoo_atual").html("'.preco($cadastro->creditos, 1).'"); ';
			$arr['evento'] .= '$(".carrinhoo_credito").html("'.preco($arr['creditos'], 1).'"); ';
			$arr['evento'] .= 'carrinhoo_atualizar() ';
		}



	echo json_encode($arr); 
?>