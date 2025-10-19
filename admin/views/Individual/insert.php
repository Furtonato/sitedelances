<?php

if (isset($arr['ult_id']))
    unset($arr['ult_id']);


// Financeiro
if ($table == 'lotes') {

    $mysql->colunas = 'ordem';
    $mysql->filtro = " WHERE leiloes = '" . $_POST['leiloes'] . "' ";
    $lotes_all = $mysql->read('lotes');

    $n = count($lotes_all) + 1;

    foreach ($lotes_all as $key => $value) {
        if ($n <= $value->ordem) {
            $n = $value->ordem + 1;
        }
    }

    $mysql->campo['ordem'] = $n;
}

if (isset($_GET['tipo']) && $_GET['tipo'] == 'leilaoseguro') {
    
}
?>