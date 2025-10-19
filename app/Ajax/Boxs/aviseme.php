<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";
	require_once "../../../app/Classes/Email.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;


		if(isset($_POST['email']) and $_POST['email']){

        	$mysql->campo['produtos'] = $_POST['produtos'];
        	$mysql->campo['nome'] = $_POST['nome'];
        	$mysql->campo['email'] = $_POST['email'];
        	$mysql->campo['celular'] = $_POST['celular'];
        	$mysql->campo['telefone'] = $_POST['telefone'];
        	$mysql->campo['ref'] = $_POST['ref'];
            $mysql->insert('aviseme');

			$arr['alert'] = 'Enviado com Sucesso!';
			$arr['evento'] = ' fechar_all(); ';


		} else {

			if(isset($_SESSION['x_site']->id)){
				$mysql->filtro = " where id = '".$_SESSION['x_site']->id."' ";
				$cadastro = $mysql->read_unico('cadastro');
			}
			$nome = isset($cadastro->nome) ? $cadastro->nome : '';
			$email = isset($cadastro->email) ? $cadastro->email : '';
			$telefone = isset($cadastro->telefone) ? $cadastro->telefone : '';
			$celular = isset($cadastro->celular) ? $cadastro->celular : '';
			$whatsapp = isset($cadastro->whatsapp) ? $cadastro->whatsapp : '';

			$mysql->filtro = " where status = 1 and lang = '".LANG."' and id = '".$_POST['id']."' order by ordem asc, id desc ";
			$produtos = $mysql->read_unico('produtos');

			$ref = array();
			if(isset($_POST['cor']) and $_POST['cor']) 			$ref[] = 'Cor: '.rel('produtos_cores', $_POST['cor']);
			if(isset($_POST['tamanho']) and $_POST['tamanho'])	$ref[] = 'Tamanho: '.rel('produtos_tamanhos', $_POST['tamanho']);
			if(isset($_POST['opcoes1']) and $_POST['opcoes1'])	$ref[] = 'Opçoes 1: '.rel('produtos_opcoes1', $_POST['opcoes1']);
			if(isset($_POST['opcoes2']) and $_POST['opcoes2'])	$ref[] = 'Opçoes 2: '.rel('produtos_opcoes2', $_POST['opcoes2']);
			if(isset($_POST['opcoes3']) and $_POST['opcoes3'])	$ref[] = 'Opçoes 3: '.rel('produtos_opcoes3', $_POST['opcoes3']);
			if(isset($_POST['opcoes4']) and $_POST['opcoes4'])	$ref[] = 'Opçoes 4: '.rel('produtos_opcoes4', $_POST['opcoes4']);
			if(isset($_POST['opcoes5']) and $_POST['opcoes5'])	$ref[] = 'Opçoes 5: '.rel('produtos_opcoes5', $_POST['opcoes5']);

			$arr['title'] = 'Avise-me Quando Chegar';
			$arr['html']  = '<form id="Aviseme" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">

								<input type="hidden" name="produtos" value="'.$produtos->id.'" >
								<div class="linha mb10">
									<b class="db mb5">Nome:</b>
									<input type="text" name="nome" value="'.$nome.'" class="w300 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">Email:</b>
									<input type="email" name="email" value="'.$email.'" class="w300 design" required >
								</div>
								<div class="linha mb10">
									<b class="db mb5">Telefone:</b>
									<input type="tel" name="telefone" value="'.$telefone.'" class="w300 design" >
								</div>
								<div class="linha mb10">
									<b class="db mb5">Celular:</b>
									<input type="tel" name="celular" value="'.$celular.'" class="w300 design">
								</div>
								<div class="linha mb15">
									<b class="db mb5">Whatsapp:</b>
									<input type="tel" name="whatsapp" value="'.$whatsapp.'" class="w300 design">
								</div>
								<input type="hidden" name="ref" value="'.implode('<br>', $ref).'">
								<input type="hidden" name="gravar" value="1">								
								<button class="botao"> <i class="mr2 fa fa-check c_verde"></i> Enviar</button>
								<div class="clear"></div>
							</form>
							<script>ajaxForm('.A.'Aviseme'.A.');</script> ';

		}

	echo json_encode($arr); 

?>