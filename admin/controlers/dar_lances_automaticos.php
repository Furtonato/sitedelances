<?php

include('init.php');




$_ids = array_filter(explode('-', $_POST['_ids']));

$usuarios = $_POST['usuario'];
$valores_lances =  $_POST['valor_lance'];

$count = 0;
$max = count($usuarios);

foreach ($_ids as $_id) {
    $sql = $db->prepare("SELECT * FROM `lotes` WHERE id = $_id");
    $sql->execute();
    $lote = $sql->fetch(PDO::FETCH_OBJ);

    if ($lote->lances == 0) {
        $valor_lance = $lote->lance_ini + $valores_lances[$count];
    } else {
        $valor_lance = $lote->lances + $valores_lances[$count];
    }

    $cadastro = $usuarios[$count];
    $plaquetas = 0;

    $data_acrescimo = date('Y-m-d H:i:s');

    //Atualiza dados na tabela lotes
    $sql = $db->prepare("UPDATE lotes SET lances = $valor_lance, lances_cadastro = $cadastro, data_acrescimo = '$data_acrescimo', lances_data = '$data_acrescimo' WHERE  id = $_id");
    $sql->execute();

    //Add ao historico de lances $sql = $db->prepare("INSERT INTO lotes_lances (data, lances, cadastro, plaquetas, lotes) VALUES ('$data_acrescimo', '$valor_lance', $cadastro, 0, $_id)");
    if ($lote->lances_cadastro > 0) {
        $sql = $db->prepare("INSERT INTO lotes_lances (data, lances, cadastro, plaquetas, lotes) VALUES ('$lote->lances_data', $lote->lances, $lote->lances_cadastro, 0, $_id)");
        $sql->execute();
    }

    $count++;
    if ($count == $max) {
        $count = 0;
    }
}

?>
<h3>Lances salvos com sucesso!</h3>
<button onclick="window.history.back()">Voltar</button>