<?php

// FINANCEIRO
// FUNCAO PARCELAMENTO INFINITO
function parcelamento_infinito($modulos) {
    $mysql = new Mysql();
    if (isset($_SESSION['financeiro_mes_atual']) and $_SESSION['financeiro_mes_atual'] and isset($_SESSION['financeiro_ano_atual']) and $_SESSION['financeiro_ano_atual']) {
        $datas = array();

        $data_futura = data($_SESSION['financeiro_ano_atual'] . "-" . ($_SESSION['financeiro_mes_atual'] + 2) . "-01", 'Y-m-d');
        $mysql->prepare = array($data_futura);
        $mysql->filtro = " WHERE `se_repete` != 0 AND `data_acabar` = 0 AND `data_gravada` < ? ";
        $financeiro_parcelamentos = $mysql->read('financeiro_parcelamentos');
        foreach ($financeiro_parcelamentos as $key => $value) {
            // Tempo do Parcelamento
            $dias = tipo_parcelamento($value->se_repete, 1);
            $meses = tipo_parcelamento($value->se_repete, 0, 1);
            $anos = tipo_parcelamento($value->se_repete, 0, 0, 1);

            for ($i = $value->data_gravada; $i < $data_futura;) {
                $ult_data_gravada[$value->id] = $i = somar_datas($i, $anos, $meses, $dias);
                $datas[$value->id][] = $i;
            }
            $info[$value->id] = unserialize($value->info);
        }
    }

    foreach ($datas as $k => $v) {
        foreach ($v as $key => $value) {
            $mysql->prepare = array($k, $value);
            $mysql->filtro = " WHERE `financeiro_parcelamentos` = ? AND `data_data` = ? ";
            $financeiro = $mysql->read_unico('financeiro');
            if (!isset($financeiro->id)) {
                $campo = $info[$k];
                $mysql->campo = array();
                $mysql->campo = $campo;
                $mysql->campo['data_data'] = $value;
                $mysql->campo['financeiro_parcelamentos'] = $k;
                $mysql->insert('financeiro');
            }
        }
    }

    // Gravando data_gravada
    if (isset($ult_data_gravada)) {
        foreach ($ult_data_gravada as $key => $value) {
            $mysql->campo = array();
            $mysql->logs = 0;
            $mysql->filtro = " where id = '" . $key . "' ";
            $mysql->campo['data_gravada'] = $value;
            $mysql->update('financeiro_parcelamentos');
        }
    }
}

// TIPO DE PARCELAMENTO
function tipo_parcelamento($se_repete, $dia = 0, $mes = 0, $ano = 0) {
    $dias = 0;
    $meses = 0;
    $anos = 0;
    switch ($se_repete) {
        case 1: $dias = 7;
            break; // Semanalmente
        case 2: $dias = 15;
            break; // Quinzenalmente
        case 3: $meses = 1;
            break; // Mensalmente
        case 4: $meses = 2;
            break; // Bimestralmente
        case 5: $meses = 3;
            break; // Trimestralmente
        case 6: $meses = 6;
            break; // Semestralmente
        case 7: $anos = 1;
            break; // Anualmente
    }
    if ($dia)
        return $dias;
    if ($mes)
        return $meses;
    if ($ano)
        return $anos;
}

?>