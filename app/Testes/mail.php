<?

	require_once('../../system/conecta.php');
	require_once('../../plugins/PHPMailer/class.phpmailer.php');

	require_once('../../system/mysql.php');
	require_once('../Classes/Email.php');

	$email = new Email();
	$email->to = isset($_GET['to']) ? $_GET['to'] : 'fmmatos@hotmail.com';
	$email->assunto = $_SERVER['HTTP_HOST'];

	$email->txt = 'texto (normal)';
	$enviado = $email->enviar();

	echo 'Enviado: '.$enviado.'<br>';
	echo 'To: '.$_POST['to'].'<br>';
	echo 'Remetente: '.$_POST['remetente'].'<br>';
	echo 'Assunto: '.$_POST['assunto'].'<br>';
	echo 'HTML: '.$_POST['html'].'<br><br><br>';


	$email->remetente = 'contato@hotmail.com';
	$email->txt = 'texto (hotmail)';
	$enviado = $email->enviar();

	echo 'Enviado: '.$enviado.'<br>';
	echo 'To: '.$_POST['to'].'<br>';
	echo 'Remetente: '.$_POST['remetente'].'<br>';
	echo 'Assunto: '.$_POST['assunto'].'<br>';
	echo 'HTML: '.$_POST['html'].'<br><br><br>';



	$email->remetente = 'contato@'.$_SERVER['HTTP_HOST'];
	$email->txt = 'texto ('.$_SERVER['HTTP_HOST'].')';
	$enviado = $email->enviar();

	echo 'Enviado: '.$enviado.'<br>';
	echo 'To: '.$_POST['to'].'<br>';
	echo 'Remetente: '.$_POST['remetente'].'<br>';
	echo 'Assunto: '.$_POST['assunto'].'<br>';
	echo 'HTML: '.$_POST['html'].'<br><br><br>';
	exit();

?>