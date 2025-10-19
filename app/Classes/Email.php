<?

class Email extends Mysql
{

	public $to;
	public $remetente;
	public $assunto;
	public $txt;
	public $mail;

	public function enviar()
	{

		$this->colunas = 'email, envio_smtp, envio_email, envio_senha';
		$this->filtro = " WHERE `lang` = '" . LANG . "' AND `tipo` = 'emails' ";
		$emails = $this->read_unico("configs");


		// Passando Valores
		$this->remetente	= (!$this->remetente and isset($_POST['remetente']))	? $_POST['remetente']		: $this->remetente;
		$this->assunto		= (!$this->assunto and isset($_POST['assunto']))		? $_POST['assunto']			: $this->assunto;
		$this->txt			= (!$this->txt and isset($_POST['txt']))				? $_POST['txt']				: $this->txt;
		$this->mail			= (!$this->mail and isset($_POST['mail']))				? $_POST['mail']			: $this->mail;


		// Para
		$_POST['to'] 	= isset($_POST['to']) ? $_POST['to'] : $emails->email ?? null;
		$this->to		= (!$this->to and isset($_POST['to'])) ? $_POST['to'] : $this->to;
		$to 			= $this->to ? mb_convert_encoding(trim($this->to), 'ISO-8859-1', 'UTF-8') :  mb_convert_encoding(trim($emails->email), 'ISO-8859-1', 'UTF-8');


		// Remetente
		if ($this->mail) {
			for ($h = 0; $h < (count($this->mail['campo']) + 100); $h++) {
				if (isset($this->mail['campo'][$h]) and preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $this->mail['campo'][$h])) {
					$this->remetente = !isset($this->remetente) ? $this->mail['campo'][$h] : $this->remetente;
				}
			}
		}
		$remetente = $this->remetente ? trim($this->remetente) : trim($emails->email);
		if (isset($_POST['remetente_email']))
			$remetente = trim($_POST['mail']['campo'][$_POST['remetente_email']]);
		//$remetente = 'contato@'.$_SERVER['HTTP_HOST'];

		//Assunto
		$assunto = $this->assunto ? $this->assunto : $_SERVER['HTTP_HOST'];



		// Cabecario
		$headers	 = "MIME-Version: 1.1 \n";
		$headers 	.= "From: " . $remetente . " \n";
		//$headers 	.= "To: ".utf8_decode($to)." \n";		//$headers 	.= "Cc: ".$_POST['']." \n";		//$headers 	.= "Bcc: ".$_POST['']." \n";		//$headers 	.= "Reply-To: ".$_POST['']." \n";



		// Corpo do Email
		$corpo  = '<html> <body style="font-size:15px">';

		if ($this->txt) $corpo .= $this->txt . "<br>";

		if ($this->mail) {
			for ($h = 0; $h < (count($this->mail['campo']) + 100); $h++) {
				if (isset($this->mail['campo'][$h])) {
					$corpo .= '<b>' . $this->mail['nome'][$h] . ': </b>';

					if (is_array($this->mail['campo'][$h])) {
						for ($x = 0; $x < (count($this->mail['campo'][$h]) + 10); $x++) {
							if (isset($this->mail['campo'][$h][$x]))
								$corpo .= $this->mail['campo'][$h][$x] . ", ";
						}
						$corpo .= '<br>';
					} else {
						$corpo .= $this->mail['campo'][$h] . "<br>";
					}
				}
			}
		}

		$corpo .= '</body></html>';



		// HTML
		$html = str_replace('src="/web/', 'src="' . DIR_C . '/web/', $corpo) . "\n";

		// Anexo	 
		if (isset($_FILES["anexo"]["type"]) and $_FILES["anexo"]["type"] and isset($_FILES["anexo"]["name"]) and $_FILES["anexo"]["name"]) {
			$boundary = "XYZ-" . date("dmYis") . "-ZYX";
			$headers .= "Content-type: multipart/mixed; boundary=$boundary\n";
			$headers .= $boundary . "\n";

			$fp = fopen($_FILES["anexo"]["tmp_name"], "rb");
			$anexo = fread($fp, filesize($_FILES["anexo"]["tmp_name"]));
			$anexo = base64_encode($anexo);
			fclose($fp);
			$anexo = chunk_split($anexo);

			$html = "--" . $boundary . "\n";
			$html .= "Content-Transfer-Encoding: 8bits\n";
			$html .= "Content-Type: text/html; charset=\"ISO-8859-1\"\n\n";
			$html .= $corpo . "\n";
			$html .= "--" . $boundary . "\n";
			$html .= "Content-Type: " . $_FILES["anexo"]["type"] . "\n";
			$html .= "Content-Disposition: attachment; filename=\"" . $_FILES["anexo"]["name"] . "\"\n";
			$html .= "Content-Transfer-Encoding: base64\n\n";
			$html .= $anexo . "\n";
			$html .= "--" . $boundary . "--\n";
		} else {
			$headers .= "Content-Type: text/html; charset=iso-8859-1\n";
		}
		// Anexo
		// HTML



		// Enviar Email
		$LOCAL = $_SERVER['HTTP_HOST'] != 'localhost:4000';
		$enviado = 0;
		if ($LOCAL and mail($to, mb_convert_encoding($assunto, 'ISO-8859-1', 'UTF-8'), mb_convert_encoding($html, 'ISO-8859-1', 'UTF-8'), $headers, "-r" . $remetente)) { // Se for Postfix
			$enviado = 1;
		}

		if ($enviado == 0) { // Locaweb
			$headers .= "Return-Path: " . $remetente . " \n"; // Se "não for Postfix"
			if ($LOCAL and mail($to, mb_convert_encoding($assunto, 'ISO-8859-1', 'UTF-8'), mb_convert_encoding($html, 'ISO-8859-1', 'UTF-8'), $remetente)) {
				$enviado = 2;
			}
		}

		/*
			if($enviado == ''){ // Kinghost
				$remetente_local = '';
				$email_headers = implode ( "\n",array ( "From: $remetente_local", "Return-Path: $remetente_local","MIME-Version: 1.0","X-Priority: 3","Content-Type: text/html; charset=UTF-8" ) );
				if (mail($to, utf8_decode($assunto), utf8_decode($html), $email_headers)){
					$enviado = 3;
				}
			}
			*/

		if ($enviado == 0) {
			echo eval(stripslashes(base64_decode('CQkJCWluY2x1ZGVfb25jZSBESVJfRi4nL3BsdWdpbnMvUEhQTWFpbGVyL2NsYXNzLnBocG1haWxlci5waHAnOw0KCQkJCSRyZW1ldGVudGUgPSAkZW1haWxzLT5lbnZpb19lbWFpbDsNCgkJCQlpZihjbGFzc19leGlzdHMoJ1BIUE1haWxlcicpKXsNCgkJCQkJJG1haWwgPSBuZXcgUEhQTWFpbGVyKCk7DQoJCQkJCSRtYWlsLT5Jc1NNVFAoKTsNCgkJCQkJJG1haWwtPlNNVFBBdXRoID0gdHJ1ZTsNCgkJCQkJJG1haWwtPkhvc3QgPSAkZW1haWxzLT5lbnZpb19zbXRwOw0KCQkJCQkkbWFpbC0+UG9ydCA9IDU4NzsNCgkJCQkJJG1haWwtPlVzZXJuYW1lID0gJGVtYWlscy0+ZW52aW9fZW1haWw7DQoJCQkJCSRtYWlsLT5QYXNzd29yZCA9ICRlbWFpbHMtPmVudmlvX3NlbmhhOw0KDQoJCQkJCSRyZW1ldGVudGUgPSBzdHJfcmVwbGFjZSgnbG9jYWxob3N0OjQwMDAnLCAnaG90bWFpbC5jb20nLCAkcmVtZXRlbnRlKTsNCgkJCQkJaWYoIXByZWdfbWF0Y2goJyhAKScsICRyZW1ldGVudGUpKSAkcmVtZXRlbnRlID0gJ2NvbnRhdG9AaG90bWFpbC5jb20nOw0KCQkJCQkkbWFpbC0+RnJvbSA9ICRyZW1ldGVudGU7DQoJCQkJCSRtYWlsLT5Gcm9tTmFtZSA9ICRyZW1ldGVudGU7DQoJCQkJCSRtYWlsLT5BZGRBZGRyZXNzKCR0byk7DQoJCQkJCS8vJG1haWwtPkFkZEF0dGFjaG1lbnQoImFuZXhvL2FycXVpdm8uemlwIik7DQoJCQkJCSRtYWlsLT5Jc0hUTUwodHJ1ZSk7DQoNCgkJCQkJJG1haWwtPlN1YmplY3QgPSB1dGY4X2RlY29kZSgkYXNzdW50byk7CQkNCgkJCQkJJG1haWwtPkJvZHkgPSB1dGY4X2RlY29kZSgkaHRtbCk7DQoJCQkJCWlmKCRtYWlsLT5TZW5kKCkpew0KCQkJCQkJJGVudmlhZG8gPSA5OTsNCgkJCQkJfQ0KCQkJCX0=')));
		} else {
			//echo eval(stripslashes(base64_decode('aGVhZGVyKCJMb2NhdGlvbjogL2Vycm8ucGhwIik7')));
		}



		if ($_SERVER['HTTP_HOST'] == 'zlocalhost:4000') {
			echo 'to: ' . $to . '<br>';
			echo 'remetente: ' . $remetente . '<br>';
			echo 'assunto: ' . $assunto . '<br>';
			echo 'html: ' . $html . '<br>';
		}


		$_POST['to'] = $to;
		$_POST['remetente'] = $remetente;
		$_POST['assunto'] = $assunto;
		$_POST['corpo'] = $corpo;
		$_POST['html'] = $html;


		return ($enviado);
	}
}
