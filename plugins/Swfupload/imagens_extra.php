<?php
// Nas versões do PHP anteriores a 4.1.0, deve ser usado $HTTP_POST_FILES
// ao invés de $_FILES.

$uploaddir = '../../../../fotos/fotos/';
$uploadfile = $uploaddir . $_FILES['arquivo']['name'];

if($_FILES['arquivo']['name'] != 'Array' ){
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploaddir . $_FILES['arquivo']['name'])) {
	
		$arquivo =  $_FILES['arquivo']['name'];
		$tamanho =  $_FILES['arquivo']['size'];
		$erro    =  $_FILES['arquivo']['error'];
	} else {
		$arquivo =  $_FILES['arquivo']['name'];
		$tamanho =  $_FILES['arquivo']['size'];
		$erro    =  $_FILES['arquivo']['error'];
	}
}
?> 