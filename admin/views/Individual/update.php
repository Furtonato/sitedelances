<?php

if (isset($arr['ult_id']))
    unset($arr['ult_id']);


if ($table == 'lotes') {

    if (isset($_POST['situacao']) AND $_POST['situacao']) {
        $mysql->colunas = 'id, situacao, lances_cadastro';
        $mysql->filtro = " WHERE id = '" . $_GET['id'] . "' ";
        $lotes = $mysql->read_unico('lotes');

        if ($lotes->situacao != $_POST['situacao']) {

            if ($_POST['situacao'] == 2) {
                email_leilao_arrematado($lotes->id, $lotes->lances_cadastro);
            } elseif ($_POST['situacao'] == 3) {
                email_leilao_nao_arrematado($lotes->id, $lotes->lances_cadastro);
            }
        }
    }
}
