<?php

ob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';
include_once '../../../app/Classes/Email.php';
include_once '../../../app/Classes/Upload.php';

$mysql = new Mysql();

$arr = array();
$arr['alert'] = 0;


// Log Gravacoes (Formularios)
if (isset($_POST['log_gravacoes'])) {

    // Produtos da cotacao
    if (isset($_POST['produtos'])) {
        $x = 0;
        foreach ($_POST['produtos'] as $key => $value) {
            $_POST['mail']['campo'][(50 + $x)] = $value . '<br> <b>Medidas e Quantidades</b>:' . $_POST['qtds'][$key] . '<br>';
            $_POST['mail']['nome'][(50 + $x)] = 'Produto';
            $x++;
        }
        $_POST['mail']['nome'][50] = '<br>-------------------------------------------------<br><br>PRODUTOS PARA COTAÇÃO<br><br>' . $_POST['mail']['nome'][50];
    }

    $email = new Email();
    $enviado = $email->enviar();

    $mysql->campo['pagina'] = 'fale';
    $mysql->campo['nome'] = $_POST['to'];
    $mysql->campo['assunto'] = $_POST['assunto'];

    $mysql->campo['txt'] = '';
    if (isset($_FILES["anexo"]["name"])) {
        $_FILES['foto'] = $_FILES["anexo"];
        $upload = new Upload();
        $upload->fileUpload(0, '../');
        $mysql->campo['txt'] .= '<b>Axeno:</b>&nbsp;<a href="' . DIR . '/web/fotos/' . $_GET['imagem_nome'] . '" target="_blank" class="azul">' . $_GET['imagem_nome'] . '</a> <br>';
    }

    $mysql->campo['txt'] .= '<b>Para:</b>&nbsp;' . $_POST['to'] . ' <br>';
    $mysql->campo['txt'] .= '<b>Remetente:</b>&nbsp;' . $_POST['remetente'] . ' <br>';
    $mysql->campo['txt'] .= '<b>Assunto:</b>&nbsp;' . $_POST['assunto'] . ' <br>';
    $mysql->campo['txt'] .= $_POST['corpo'];
    $mysql->insert('log_gravacoes');

    if ($enviado == 1 or $enviado == 2) {
        $arr['alert'] = "Enviado com sucesso!";
        //unset($_SESSION['cotacao']);
    } else {
        $arr['erro'][] = "Erro ao enviar...";
    }


    // Email
    foreach ($_POST['mail']['campo'] as $key => $value) {
        if (preg_match('(@)', $value)) {
            $arr['email'] = $value;
        }
    }
}


echo json_encode($arr);
?>