<?

	// Verificando CNPJ
	if(isset($_POST[$value['input']['nome']])){

		$RecebeCNPJ = minusculo($_POST[$value['input']['nome']]);

		// Verificacao se existe
		$mysql = new Mysql();
		$mysql->colunas = "id";
		$mysql->prepare = array($id, $_POST[$value['input']['nome']]);
		$mysql->filtro = " WHERE `id` != ? AND `".$value['input']['nome']."` = ? ";
		$item = $mysql->read_unico($table);
		if(isset($item->id))
			$arr['erro'][] = 'Este '.$value['nome'].' inserido já está cadastrado, insira outro '.$value['nome'].'!';


		// Vericicas cnpf valido
		if(!isset($item->id)){
			//Retirar todos os caracteres que nao sejam 0-9
			$s="";
			for ($x=1; $x<=strlen($RecebeCNPJ); $x=$x+1){
				$ch=substr($RecebeCNPJ,$x-1,1);
				if (ord($ch)>=48 && ord($ch)<=57){
					$s=$s.$ch;
				}
			}
			
			$RecebeCNPJ=$s;

			if($RecebeCNPJ == '' or $RecebeCNPJ=="00000000000000" or $RecebeCNPJ=="11111111111111" or $RecebeCNPJ=="22222222222222" or $RecebeCNPJ=="33333333333333" or $RecebeCNPJ=="44444444444444" or $RecebeCNPJ=="55555555555555" or $RecebeCNPJ=="66666666666666" or $RecebeCNPJ=="77777777777777" or $RecebeCNPJ=="88888888888888" or $RecebeCNPJ=="99999999999999"){
				$arr['erro'][] = $value['nome'].' inválido, insira outro '.$value['nome'].'!';
			}else{
				
				if(strlen($RecebeCNPJ) <> 14){
					$arr['erro'][] = $value['nome'].' inválido, insira outro '.$value['nome'].'!';
		
				} else {
					$soma = 0;
					
					$soma += ($RecebeCNPJ[0] * 5);
					$soma += ($RecebeCNPJ[1] * 4);
					$soma += ($RecebeCNPJ[2] * 3);
					$soma += ($RecebeCNPJ[3] * 2);
					$soma += ($RecebeCNPJ[4] * 9); 
					$soma += ($RecebeCNPJ[5] * 8);
					$soma += ($RecebeCNPJ[6] * 7);
					$soma += ($RecebeCNPJ[7] * 6);
					$soma += ($RecebeCNPJ[8] * 5);
					$soma += ($RecebeCNPJ[9] * 4);
					$soma += ($RecebeCNPJ[10] * 3);
					$soma += ($RecebeCNPJ[11] * 2); 
					
					$d1 = $soma % 11; 
					$d1 = $d1 < 2 ? 0 : 11 - $d1; 
					
					$soma = 0;
					$soma += ($RecebeCNPJ[0] * 6); 
					$soma += ($RecebeCNPJ[1] * 5);
					$soma += ($RecebeCNPJ[2] * 4);
					$soma += ($RecebeCNPJ[3] * 3);
					$soma += ($RecebeCNPJ[4] * 2);
					$soma += ($RecebeCNPJ[5] * 9);
					$soma += ($RecebeCNPJ[6] * 8);
					$soma += ($RecebeCNPJ[7] * 7);
					$soma += ($RecebeCNPJ[8] * 6);
					$soma += ($RecebeCNPJ[9] * 5);
					$soma += ($RecebeCNPJ[10] * 4);
					$soma += ($RecebeCNPJ[11] * 3);
					$soma += ($RecebeCNPJ[12] * 2); 
					
					
					$d2 = $soma % 11; 
					$d2 = $d2 < 2 ? 0 : 11 - $d2;
					
					
					if($RecebeCNPJ[12] == $d1 && $RecebeCNPJ[13] == $d2){
					} else {
						$arr['erro'][] = $value['nome'].' inválido, insira outro '.$value['nome'].'!';
					}
				}
			}
		}

	}	
	// Verificando CNPJ

?>