<?php ob_start();

	require_once "../../../../system/conecta.php";
	require_once "../../../../system/mysql.php";
	require_once "../../../../app/Funcoes/funcoes.php";
	require_once "../../../../app/Funcoes/funcoesAdmin.php";

	require_once "../../../../admin/app/Classes/criarMysql.php";

	$mysql = new Mysql();

	$arr = array();


		function mapa_admin($tabelas, $item){
			$return = '';
			$mysql1 = new Mysql();
			$mysql1->prepare = array($item);
			$mysql1->filtro = " WHERE `id` = ? ";
			$item = $mysql1->read_unico($tabelas);

			if(isset($item->maps_lat) and $item->maps_lat and isset($item->maps_lng) and $item->maps_lng){
				$return .= '<iframe src="'.DIR.'/plugins/Google/Maps/maps.php?lat='.$item->maps_lat.'&lon='.$item->maps_lng.'&zoom='.$item->maps_zoom.'&nome='.$item->nome.'" width="100%" height="380" background="no" scrolling="No" marginwidth="0" marginheight="0" frameborder="0"></iframe> ';
			} else {
				$return .= '<div style="background:#DDD; padding:10px 20px"> ';
					$return .= 'Endereço não encontrado... Digite apenas o nome da rua, cidade, estado e cep do local desejado (Não coloque o bairro e não digite o número assim "<u>Nº 95</u>"; digite assim "<u>95</u>" ou n digite o número).<br /> ';
					$return .= 'Caso ainda não encontre, procure no site do <a href="http://maps.google.com.br/maps" target="_blank" class="azul">Google Maps</a> e depois escreva aqui o endereço encontrado lá. ';
				$return .= '</div> ';
			}
			return $return;
		}



		if(isset($_POST['gravar']) and $_POST['gravar']){
			if($_POST['modulos'] == 'all'){
				$modulos->modulo = 'all';
			} else {
				$mysql->prepare = array($_POST['modulos']);
				$mysql->filtro = " WHERE `id` = ? ";
				$modulos = $mysql->read_unico('menu_admin');
				verificar_permissoes_all($modulos, 0, 'lista', 'mais_mapas');
			}

			// Gravar long e Lat
			if($_POST['tipo']){
				$dados['lat'] = $_POST['lat'];
				$dados['lon'] = $_POST['lon'];
				$_POST['endereco'] = '';
			} else
				$dados = mapa_google($_POST['endereco']);

			$mysql->prepare = array($_POST['item']);
			$mysql->filtro = " WHERE `id` = ? ";
			$mysql->campo['maps_lat'] = $dados['lat'];
			$mysql->campo['maps_lng'] = $dados['lon'];
			$mysql->campo['maps_tipo'] = $_POST['tipo'];
			$mysql->campo['maps_zoom'] = $_POST['zoom'];
			$mysql->campo['maps'] = $_POST['endereco'];
			$ult_id = $mysql->update($modulos->modulo);


			$html = mapa_admin($modulos->modulo, $_POST['item']);

			$arr['alert'] = 1;
			$arr['evento'] = "$('div.mapa_google').html('".$html."'); ";



		} else {
			if($_POST['modulos'] == 'all'){
				$modulos->modulo = 'all';
			} else {
				$mysql->prepare = array($_POST['modulos']);
				$mysql->filtro = " WHERE `id` = ? ";
				$modulos = $mysql->read_unico('menu_admin');
				verificar_permissoes_all($modulos, 0, 'lista', 'mais_mapas');
			}

            $criarMysql = new criarMysql();
            $criarMysql->criarColunas($modulos->modulo, 'maps_tipo', 'int');
            $criarMysql->criarColunas($modulos->modulo, 'maps_zoom', 'int');
            $criarMysql->criarColunas($modulos->modulo, 'maps_lat', 'varchar(50)');
            $criarMysql->criarColunas($modulos->modulo, 'maps_lng', 'varchar(50)');
            $criarMysql->criarColunas($modulos->modulo, 'maps', 'text');

			$mysql->prepare = array($_POST['item']);
			$mysql->filtro = " WHERE `id` = ? ";
			$value = $mysql->read_unico($modulos->modulo);

			$tipo 	  = isset($value->maps_tipo) ? $value->maps_tipo : 0;
			$zoom 	  = (isset($value->maps_zoom) and $value->maps_zoom) ? $value->maps_zoom : 18;
			$lat 	  = isset($value->maps_lat) ? $value->maps_lat : '';
			$lon 	  = isset($value->maps_lng) ? $value->maps_lng : '';
			$endereco = isset($value->maps) ? $value->maps : '';


			$arr['title'] = 'Mapas';
			$_POST['rand'] = isset($_POST['rand'])? $_POST['rand'] : 0;

			$arr['html'] = '<div class="w700 itens mb10">
								<form id="GoogleMaps" method="post" action="'.$_SERVER['SCRIPT_NAME'].'">

									<b>Escolha o metodo de cadastro que você irá usar</b><br>
									<label> <input type="radio" name="tipo" value="0" '.iff(!$tipo, 'checked').' onclick="show('.A.'.endereco'.A.');hide('.A.'.lat_long'.A.');"> Localização </label> &nbsp &nbsp
									<label> <input type="radio" name="tipo" value="1" '.iff($tipo, 'checked').'  onclick="show('.A.'.lat_long'.A.');hide('.A.'.endereco'.A.');"> Latitude e longitude </label>
									<div class="clear h10"></div>

									<div class="wr12">
										<b>Zoom:</b>
										<input type="text" name="zoom" class="w200 design" placeholder="Zoom" value="'.$zoom.'" >
									</div>
									<div class="clear h5"></div>

									<div class="lat_long '.iff(!$tipo, 'dn').'">
										<div class="wr12">
											<b>Latitude:</b>
											<input type="text" name="lat" class="w200 design" value="'.$lat.'" >
										</div>
										<div class="clear h5"></div>
										<div class="wr12">
											<b>longitude:</b>
											<input type="text" name="lon" class="w200 design" value="'.$lon.'" >
										</div>
										<div class="clear h5"></div>
									</div>

									<div class="endereco '.iff($tipo, 'dn').'">
										<b>Endereço Completo:</b>
										<input type="text" name="endereco" class="w700 design" placeholder="Localização" value="'.$endereco.'" >
										<span class="fz11 c_999"> Obs.: Forneça o Endereço ou Cep da Empresa; Exemplo: <i><u>Avenida Afonso Pena, 3761, Belo Horizonte - Minas Gerais 30130-008</u></i> ou <i><u>Rua Barata Ribeiro, Copacabaca, RJ</u></i> - (Caso não tenho um endereço válido, ache o endereço que deseja em <a href="http://maps.google.com.br/maps" target="_blank" class="c_azul">Google Maps</a> </span>
										<div class="h5"></div>
									</div>

									<input type="hidden" name="modulos" value="'.$_POST['modulos'].'">
									<input type="hidden" name="item" value="'.$_POST['item'].'">
									<input type="hidden" name="gravar" value="1">

									<button class="botao" ><i class="mr5 fa fa-check c_verde"></i> Gravar</button>
									<div class="clear h10"></div>
									<div class="mapa_google">'.mapa_admin($modulos->modulo, $_POST['item']).'</div>
								</form>
								<script>ajaxForm('.A.'GoogleMaps'.A.');</script>

							 </div> ';

		}



	echo json_encode($arr); 

?>