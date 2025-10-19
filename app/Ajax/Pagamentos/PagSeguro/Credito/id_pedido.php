<?php ob_start();

	require_once "../../../../../system/conecta.php";
	require_once "../../../../../system/mysql.php";
	require_once "../../../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['id'] = 0;


		if(isset($_GET['id']) and $_GET['id']){

			$mysql->filtro = " where id = '".$_GET['id']."' ";
			$pedidos = $mysql->read_unico('pedidos');

			$arr['id'] = $_GET['id'];
			$arr['mobile'] = $_POST['mobile'];

			$arr['parcelas'] = $_POST['parcelas'];
			$arr['valor'] = $pedidos->valor_total;
			$arr['bandeira'] = (isset($_POST['bandeiras']) and $_POST['bandeiras']) ? $_POST['bandeiras'] : 'erro';

			$arr['nome'] = $_POST['nome'];
			$arr['numero'] = str_replace(' ', '', $_POST['numero']);
			$arr['cvv'] = $_POST['cvv'];
			$arr['cpf'] = $_POST['cpf'];

			$ex = explode('/', $_POST['validade']);
			$arr['vencimento_mes'] = isset($ex[0]) ? $ex[0] : '00';
			$arr['vencimento_ano'] = isset($ex[1]) ? '20'.$ex[1] : '2000';

			$arr['form'] = '';
			foreach ($arr as $key => $value) {
				$arr['form'] .= '&'.$key.'='.$value;
			}

		}

	echo json_encode($arr); 

?>