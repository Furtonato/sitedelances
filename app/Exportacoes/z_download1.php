<?phpob_start();

      $arquivo = 'web/fotos/'.$_GET['arquivo'];

      header("Content-Type: application/save");
      header("Content-Length:".filesize($arquivo)); 
      header('Content-Disposition: attachment; filename="'.$_GET['arquivo'].'"'); 
      header("Content-Transfer-Encoding: binary");
      header('Expires: 0'); 
      header('Pragma: no-cache'); 

      $dw = fopen($arquivo, "r"); 
      fpassthru($dw); 
      fclose($dw);

?>