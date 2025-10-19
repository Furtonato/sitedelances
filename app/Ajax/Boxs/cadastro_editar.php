<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	require_once "../../../app/Funcoes/funcoes.php";

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 0;

		$pg_cadastro = get_include_contents('../../../views/cadastro.phtml');

		$mysql->prepare = array($_SESSION['x_site']->id);
		$mysql->filtro = " WHERE `id` = ? ";
		$cadastro = $mysql->read_unico('cadastro');

		$arr['title'] = lang('Editando Cadastro');
		$arr['html']  = '<style>
							.cadastro_editar .centerr { width: auto !important; padding: 0 !important; margin: 0 !important; }
							.cadastro_editar .dados { padding: 0 !important; margin: 0 !important; border: none !important; }
							.cadastro_editar .botao_edit { width: auto !important; }
							.cadastro_editar .add_edit { display: block; }
						 </style>
						'.$pg_cadastro;

		$arr['html'] .= '<script> var $dados = new Array(); </script> ';
						foreach ($cadastro as $key => $value){
							if($key == 'tipo'){
								if($value){
									$arr['html'] .= '<script> setTimeout(function(){ $(".tipo_pf").remove(); }, 0.5);; </script> ';
								} else {
									$arr['html'] .= '<script> setTimeout(function(){ $(".tipo_pj").remove(); }, 0.5);; </script> ';
								}
							}
							if(browser()!='chrome' and $key == 'nascimento'){
								$value = data($value, 'd/m/Y');
							}
							if($key == 'nascimento'){
								$ex = explode('-', $value);
								$arr['html'] .= '<script> $dados["dia"] =  "'.$ex[2].'"; </script> ';
								$arr['html'] .= '<script> $dados["mes"] =  "'.$ex[1].'"; </script> ';
								$arr['html'] .= '<script> $dados["ano"] =  "'.$ex[0].'"; </script> ';
							}
							$arr['html'] .= '<script> $dados["'.$key.'"] =  "'.$value.'"; </script> ';
						}
		$arr['html'] .= '<script>
							setTimeout(function(){ 
							 	$(".cadastro_editar .remove_edit").remove();
							 	$(".cadastro_editar .termos_uso").html("<input type=hidden name=update value=1 >&nbsp;");
								$(".cadastro_editar #form_cadastro input").each(function() {
									$(this).val( $dados[$(this).attr("name")] );
								});
								$(".cadastro_editar #form_cadastro select").each(function() {
									$(this).val( $dados[$(this).attr("name")] ).trigger("change");
								});
								setTimeout(function(){ $(".cadastro_editar #form_cadastro select#cidades").val( $dados["cidades"] ).trigger("change"); }, 800);
								setTimeout(function(){ $(".cadastro_editar #form_cadastro select#cidades").val( $dados["cidades"] ).trigger("change"); }, 1500);
							}, 0.5);
						 </script> ';


	echo json_encode($arr); 

?>