<?phpob_start();

	$link 	= $_GET["end"].$_GET["arquivo"];
	header ("Content-Disposition: attachment; filename=".$_GET["arquivo"]."");
	header ("Content-Type: application/octet-stream");
	header ("Content-Length: ".filesize($link));
	readfile($link);

?>