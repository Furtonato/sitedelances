<?php ob_start();

	require_once "../../../system/conecta.php";
	require_once "../../../system/mysql.php";
	include_once '../../../app/Funcoes/funcoes.php';

	$mysql = new Mysql();

	$arr = array();
	$arr['alert'] = 'z';

		function important($ex){
			$return = '';
			for ($i=0; $i < 10; $i++) { 
				$return .= (isset($ex[$i]) and $ex[$i]=='i') ? ' !important' : '';
			}
			return $return;
		}


		$style = array();
		$ex = explode(' ', $_POST['css']);
		foreach ($ex as $key => $value){

			$css = '';
			$attr = '';
			$val = '';
			$outros = '';
			$outros1 = preg_match('(hover_)', $value) ? ':hover' : '';

			// Background
			if(preg_match('(back_)', $value)){
				$attr = 'background';
				$ex = explode('_', $value);
				if(isset($ex[1])){
					if($ex[1] == 'hover' AND isset($ex[2])){
						$val = '#'.$ex[2].important($ex);
					} elseif(isset($ex[2]) AND $ex[2] != 'i') {
					    $val = 'filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="'."#".$ex[1].'", endColorstr="'."#".$ex[2].'");  background:-moz-linear-gradient(top, '."#".$ex[1].', '."#".$ex[2].'); background:-webkit-gradient(linear, left top, left bottom, from('."#".$ex[1].'), to('."#".$ex[2].'));'; 
					} else {
						$val = '#'.$ex[1].important($ex);
					}
				}


			// Border
			} elseif(preg_match('(bd_)', $value) or preg_match('(bdt_)', $value) or preg_match('(bdb_)', $value) or preg_match('(bdr_)', $value) or preg_match('(bdl_)', $value)){
				$attr = 'border';
				if(preg_match('(bdt_)', $value))		$attr = 'border-top';
				elseif(preg_match('(bdb_)', $value))	$attr = 'border-bottom';
				elseif(preg_match('(bdl_)', $value))	$attr = 'border-left';
				elseif(preg_match('(bdr_)', $value))	$attr = 'border-right';
				$ex = explode('_', $value);
				if(isset($ex[1])){
					if($ex[1] == 'hover' AND isset($ex[2])){
						$val = '1px solid #'.$ex[2].important($ex);
					} else {
						$val = '1px solid #'.$ex[1].important($ex);
					}
				}


			// Color
			} elseif(preg_match('(color_)', $value) or preg_match('(cor_)', $value)){
				$attr = 'color';
				$outros = 'a.'.$value.$outros1.', ';
				$ex = explode('_', $value);
				if(isset($ex[1])){
					if($ex[1] == 'hover' AND isset($ex[2])){
						$val = '#'.$ex[2].important($ex);
					} else {
						$val = '#'.$ex[1].important($ex);
					}
				}
			}

			if($attr and $val){
				$css = $outros.'.'.$value.$outros1.'{'.$attr.':'.$val.'}';
				$style[$outros.'.'.$value.$outros1] = $css;
			}

		}

		$arr['evento'] = "$('.events_externos .style').html('<style>".implode('', $style)."</style>'); ";


	echo json_encode($arr);

?>