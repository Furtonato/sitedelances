<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

include 'canvas.php';

$path = "fotos/";
$diretorio = dir($path);

$new_path = "backup/";

$canvas = new Canvas();

if (!file_exists($new_path)) {
    mkdir($new_path, 0777);
}

echo "Lista de Arquivos do diretório '<strong>" . $path . "</strong>':<br />";
while ($arquivo = $diretorio->read()) {

    //Verifica se é imagem e se não está no pasta backup
    if (!file_exists($new_path . $arquivo)) {
        if (is_file($path . $arquivo)) {
            copy($path . $arquivo, $new_path . $arquivo);
        }
    }
    $file = $new_path . $arquivo;

    if (is_file($file)) {
        $canvas->carrega($file);
        $canvas->marca('marca.jpg', 'meio', 'centro', 50);
        $canvas->grava($path . $arquivo, 80);
    }
    echo "<a href='" . $path . $arquivo . "'>" . $path . $arquivo . "</a><br />";
}
$diretorio->close();

