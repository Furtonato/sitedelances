<?

	if(isset($_SESSION['logado1'])){
		if($_SESSION['logado1'] == 2 ){
			//nada
		}else{
			header("Location: login.php?cod=3");
		}
	}else{
		header("Location: login.php?cod=3");
	}

?>