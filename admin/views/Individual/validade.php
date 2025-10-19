<?
	

		// Cadastro
		if($table == 'cadastro'){

			$x = 0;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Nome';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'nome';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'CPF';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'cpf';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required validar="cpf"';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Data de nascimento';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'nascimento';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'CNPJ';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'cnpj';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required validar="cnpj"';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Senha';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'senha';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required comparar="c_senha"';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Confirmar Senha';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'c_senha';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required';




		// Empresas
		} elseif($table == 'empresas'){

			$x = 0;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'CNPJ';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'cnpj';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required validar="cnpj"';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Senha';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'senha';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required comparar="c_senha"';

			$x++;
			$modulos_campos[0]['outros_'.$x]['check'] = 1;
			$modulos_campos[0]['outros_'.$x]['nome'] = 'Confirmar Senha';
			$modulos_campos[0]['outros_'.$x]['input']['nome'] = 'c_senha';
			$modulos_campos[0]['outros_'.$x]['input']['tags'] = 'required';



		}


?>