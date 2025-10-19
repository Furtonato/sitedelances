<?

	// Verificando
	if(isset($_POST[$value['input']['nome']])){

		// Verificacao se existe
		$mysql = new Mysql();
		$mysql->colunas = "id";
		$mysql->prepare = array($id, $_POST[$value['input']['nome']]);
		$mysql->filtro = " WHERE `id` != ? AND `".$value['input']['nome']."` = ? ";
		$item = $mysql->read_unico($table);
		if(isset($item->id))
			$arr['erro'][] = 'Este '.$value['nome'].' inserido já está cadastrado, insira outro '.$value['nome'].'!';


	}
	// Verificando

?>