<?
	;

	$ini = array();
	$fim = array();
	$x=0;

	// filtro, resp, input-tipo, input-tipo1, input-opcoes, input-tags, input-extra, 
	if($modulos->modulo == 'cadastro'){

		$ini['z1'][$x]['nome'] = 'Nome';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'nome';
		$ini['z1'][$x]['check'] = 1;
		$x++;

		//$ini['z1'][$x]['nome'] = 'Sobrenome';
		//$ini['z1'][$x]['resp'] = 'wr6';
		//$ini['z1'][$x]['input']['nome'] = FF.'sobrenome';
		//$ini['z1'][$x]['check'] = 1;
		//$x++;

		/*
		$ini['z1'][$x]['nome'] = 'CPF';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'cpf';
		$ini['z1'][$x]['check'] = 1;
		$x++;

		$ini['z1'][$x]['nome'] = 'RG';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'rg';
		$ini['z1'][$x]['check'] = 1;
		$x++;
		*/

		$ini['z1'][$x]['nome'] = 'Data de nascimento';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'nascimento';
		$ini['z1'][$x]['input']['tipo1'] = 'date';
		$ini['z1'][$x]['check'] = 1;
		$x++;

		/*
		$ini['z1'][$x]['nome'] = 'Sexo';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'sexo';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '0->Feminino; 1->Masculino';
		$ini['z1'][$x]['check'] = 1;
		$x++;
		*/

		//$ini['z1'][$x]['nome'] = 'CNPJ';
		//$ini['z1'][$x]['resp'] = 'wr6';
		//$ini['z1'][$x]['input']['nome'] = FF.'cnpj';
		//$ini['z1'][$x]['check'] = 1;
		//$x++;


		$fim['z2'][$x]['nome'] = 'Nº de Compras';
		$fim['z2'][$x]['resp'] = 'wr6';
		$fim['z2'][$x]['input']['nome'] = FF.'qtd_comprado';
		$fim['z2'][$x]['input']['tipo1'] = 'number';
		$fim['z2'][$x]['check'] = 1;
		$x++;

		$fim['z2'][$x]['nome'] = 'Valor Comprado';
		$fim['z2'][$x]['resp'] = 'wr6';
		$fim['z2'][$x]['input']['nome'] = FF.'valor_comprado';
		$fim['z2'][$x]['input']['tags'] = ' class="preco" ';
		$fim['z2'][$x]['check'] = 1;
		$x++;







	} elseif($modulos->modulo == 'pedidos'){


		$ini['z1'][$x]['nome'] = 'Data da Compra';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'data';
		$ini['z1'][$x]['input']['tipo1'] = 'date';
		$ini['z1'][$x]['check'] = 1;
		$x++;


		$ini['z1'][$x]['nome'] = 'Situação';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'situacao';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '0->'.SITUACAO_PD.'; '.option_banco('pedidos_situacoes');
		$ini['z1'][$x]['check'] = 1;
		$x++;


		$ini['z1'][$x]['filtro'] = ' and produtos '.like('$value');
		$ini['z1'][$x]['nome'] = 'Produtos';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'produtos';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '(banco)->produtos';
		$ini['z1'][$x]['check'] = 1;
		$x++;

		$ini['z1'][$x]['nome'] = 'Clientes';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'cadastro';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '(banco)->cadastro';
		$ini['z1'][$x]['check'] = 1;
		$x++;


		$ini['z1'][$x]['filtro'] = ' and produtos IN ( SELECT id FROM produtos where (categorias = $value  or categorias IN (SELECT id FROM produtos1_cate where subcategorias = $value ) ) ) ';
		$ini['z1'][$x]['nome'] = 'Categorias';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'produtos1_cate';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '(banco)->produtos1_cate';
		$ini['z1'][$x]['check'] = 1;
		$x++;

		$ini['z1'][$x]['filtro'] = ' and produtos IN ( SELECT id FROM produtos where cidades = $value ) ';
		$ini['z1'][$x]['nome'] = 'Cidades';
		$ini['z1'][$x]['resp'] = 'wr6';
		$ini['z1'][$x]['input']['nome'] = FF.'cidades';
		$ini['z1'][$x]['input']['tipo'] = 'select';
		$ini['z1'][$x]['input']['opcoes'] = '(banco)->cidades';
		$ini['z1'][$x]['check'] = 1;
		$x++;


	}

	$campos = array_merge($ini, $modulos_campos, $fim);


?>