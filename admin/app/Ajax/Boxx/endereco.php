<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";
	require_once "../../../../app/Classes/Input.php";

	$mysql = new Mysql();
	$input = new Input();

	$tipo = $_GET['tipo'];


		if(isset($_GET['id']) and $_GET['id']){
			$mysql->prepare = array($_GET['id']);
			$mysql->filtro = " WHERE `cadastro` = ? ";
			$enderecos = $mysql->read($_GET['table'].'_'.str_replace('inserir_box_', '', $tipo));
		} else {

			$mysql->logs = 0;
			$mysql->campo['lang'] = LANG;
			$_GET['id'] = $mysql->insert('cadastro_'.str_replace('inserir_box_', '', $tipo));

			$mysql->prepare = array($_GET['id']);
			$mysql->filtro = " WHERE `id` = ? ";
			$enderecos = $mysql->read('cadastro_'.str_replace('inserir_box_', '', $tipo));
		}


		$x=0;
		foreach ($enderecos as $key => $value){

			// Values
			$val = (object)array();
			$v = $tipo.'['.$value->id.'][cep]';			$val->$v = isset($value->cep) ? $value->cep : '';
			$v = $tipo.'['.$value->id.'][rua]';			$val->$v = isset($value->rua) ? $value->rua : '';
			$v = $tipo.'['.$value->id.'][numero]';		$val->$v = isset($value->numero) ? $value->numero : '';
			$v = $tipo.'['.$value->id.'][complemento]';	$val->$v = isset($value->complemento) ? $value->complemento : '';
			$v = $tipo.'['.$value->id.'][bairro]';		$val->$v = isset($value->bairro) ? $value->bairro : '';
			$v = $tipo.'['.$value->id.'][estados]';		$val->$v = isset($value->estados) ? $value->estados : '';
			$v = $tipo.'['.$value->id.'][cidades]';		$val->$v = isset($value->cidades) ? $value->cidades : '';
			$input->value = $val;
			// Values


			echo !isset($_GET['novo']) ? '<div class="boxx ml10 '.iff(!$x, 'boxx_pinc').'"> ' : '';

				echo '<div class="topo"> ';
					echo '<div class="fll"> <div class="input"> <label class="pb5"> <input type="radio" name="'.$tipo.'[principal]" '.iff($value->principal, 'checked').' value="'.$value->id.'" class="design "> <p>&nbspPrincipal</p> </label> </div> </div> ';
					echo ($x OR isset($_GET['novo'])) ? '<span class="flr m5 c-p" onclick="fieldset_fechar(this)"> <i class="fa fa-times fz16 m0 c_vermelho"></i> </span> ' : '';
					echo '<div class="clear"></div> ';
				echo '</div> ';

				echo '<div class="item"> ';

			        $input->tags = ' class="cep design" id="cep_'.$value->id.'" onBlur="cepp_fields(this)"  required ';
			        echo '<li class="wr3"> '.$input->text('CEP', $tipo.'['.$value->id.'][cep]').'</li> ';
					echo '<div class="clear"></div> ';

			        $input->tags = ' class="design" id="rua_'.$value->id.'" required ';
			        echo '<li class="wr6"> '.$input->text('Rua', $tipo.'['.$value->id.'][rua]').'</li> ';

			        $input->tags = ' class="design" required ';
			        echo '<li class="wr3"> '.$input->text('Número', $tipo.'['.$value->id.'][numero]').'</li> ';

			        $input->tags = ' class="design" ';
			        echo '<li class="wr3"> '.$input->text('Complemento', $tipo.'['.$value->id.'][complemento]').'</li> ';

			        $input->tags = ' class="design" id="bairro_'.$value->id.'" required ';
			        echo '<li class="wr4"> '.$input->text('Bairro', $tipo.'['.$value->id.'][bairro]').'</li> ';

			        $input->tags = ' readonly class="design" id="cidades_'.$value->id.'" required ';
			        $input->opcoes = '(cidades)';
			        echo '<li class="wr4"> '.$input->text('Cidade', $tipo.'['.$value->id.'][cidades]').'</li> ';

			        $input->tags = ' readonly class="design" id="estados_'.$value->id.'" rel_estados="'.$tipo.'_'.$value->id.'_cidades" required ';
			        $input->opcoes = '(estados)';
			        echo '<li class="wr4"> '.$input->text('Estado', $tipo.'['.$value->id.'][estados]').'</li> ';

					echo '<div class="clear"></div> ';
				echo '</div> ';

			echo !isset($_GET['novo']) ? '</div> ' : '';

			$x++;

		}


?>

