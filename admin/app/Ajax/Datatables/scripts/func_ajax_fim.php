<?php

// FINANCEIRO
if ($table == 'financeiro') {

    // FUNCAO GERAR SALDO
    function gerar_saldo($filtro) {
        $return = 0;
        $mysql = new Mysql();
        $mysql->filtro = $filtro;
        $consulta = $mysql->read('financeiro');
        foreach ($consulta as $key => $value) {
            $return += $value->preco;
        }
        return $return;
    }

    // TOP -> SALDO ATUAL (BOX CONTA ATUAL)
    $filtro_conta_atual = isset($filtro_conta_atual) ? $filtro_conta_atual : '';

    $saldo = 0;
    $saldo += gerar_saldo(" where lang = " . LANG . " and pago = 1 " . $filtro_conta_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 1 and status = 1 and lang = " . LANG . " ) ");
    $saldo -= gerar_saldo(" where lang = " . LANG . " and pago = 1 " . $filtro_conta_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 0 and status = 1 and lang = " . LANG . " ) ");
    if ($saldo < 0) {
        $saida['financeiro_saldo']['cor'] = 'c_vermelho3';
        $saida['financeiro_saldo']['valor'] = '(-' . preco($saldo * -1, 1) . ')';
    } else {
        $saida['financeiro_saldo']['cor'] = 'c_verde3';
        $saida['financeiro_saldo']['valor'] = '(' . preco($saldo, 1) . ')';
    }









    // FIM DA PAGINA -> SALDO ESTATISTICAS
    $filtro_mes_atual = isset($filtro_mes_atual) ? $filtro_mes_atual : '';
    $filtro_mes_passado = isset($filtro_mes_passado) ? $filtro_mes_passado : '';


    // SALDO LEFT
    // MES ATUAL
    if (isset($_SESSION['financeiro_mes_atual']) and isset($_SESSION['financeiro_ano_atual']))
        $saida['financeiro_mes_atual'] = mes($_SESSION['financeiro_mes_atual']) . ' / ' . $_SESSION['financeiro_ano_atual'];
    else
        $saida['financeiro_mes_atual'] = '';

    // Saldo Mes Passado
    $saldo_mes_passado = 0;
    $saldo_mes_passado += gerar_saldo(" where lang = " . LANG . " " . $filtro_conta_atual . " " . $filtro_mes_passado . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 1 and status = 1 and lang = " . LANG . " ) ");
    $saldo_mes_passado -= gerar_saldo(" where lang = " . LANG . " " . $filtro_conta_atual . " " . $filtro_mes_passado . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 0 and status = 1 and lang = " . LANG . " ) ");
    if ($saldo_mes_passado < 0)
        $saida['financeiro_saldo_mes_passado']['back'] = 'negativo';
    else
        $saida['financeiro_saldo_mes_passado']['back'] = 'positivo';
    $saida['financeiro_saldo_mes_passado']['valor'] = preco(abs($saldo_mes_passado), 1);

    // Saldo Mes Atual
    $saldo_mes_atual = 0;
    $saldo_mes_atual += gerar_saldo(" where lang = " . LANG . " " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 1 and status = 1 and lang = " . LANG . " ) ");
    $saldo_mes_atual -= gerar_saldo(" where lang = " . LANG . " " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 0 and status = 1 and lang = " . LANG . " ) ");
    if ($saldo_mes_atual < 0)
        $saida['financeiro_saldo_mes_atual']['back'] = 'negativo';
    else
        $saida['financeiro_saldo_mes_atual']['back'] = 'positivo';
    $saida['financeiro_saldo_mes_atual']['valor'] = preco(abs($saldo_mes_atual), 1);



    // SALDO CENTER
    // Recebimentos
    $saldo = 0;
    $saldo_pago = gerar_saldo(" where lang = " . LANG . " and pago = 1 " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 1 and status = 1 and lang = " . LANG . " ) ");
    $saldo_todos = gerar_saldo(" where lang = " . LANG . "              " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 1 and status = 1 and lang = " . LANG . " ) ");
    $saida['saldo_tipos_pago'][1] = preco($saldo_pago, 1);
    $saida['saldo_tipos_todos'][1] = preco($saldo_todos, 1);
    $saida['saldo_tipos_porc'][1] = 0;
    if ($saldo_pago * $saldo_todos)
        $saida['saldo_tipos_porc'][1] = ($saldo_pago * 100) / $saldo_todos;

    // Saidas
    $saldo = 0;
    $saldo_pago = gerar_saldo(" where lang = " . LANG . " and pago = 1 " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 0 and status = 1 and lang = " . LANG . " ) ");
    $saldo_todos = gerar_saldo(" where lang = " . LANG . "              " . $filtro_conta_atual . " " . $filtro_mes_atual . " and financeiro_tipos IN ( SELECT id FROM financeiro_tipos where saldo = 0 and status = 1 and lang = " . LANG . " ) ");
    $saida['saldo_tipos_pago'][0] = preco($saldo_pago, 1);
    $saida['saldo_tipos_todos'][0] = preco($saldo_todos, 1);
    $saida['saldo_tipos_porc'][0] = 0;
    if ($saldo_pago * $saldo_todos)
        $saida['saldo_tipos_porc'][0] = ($saldo_pago * 100) / $saldo_todos;

    for ($i = 2; $i <= 5; $i++) {
        $saldo = 0;
        $saldo_pago = gerar_saldo(" where lang = " . LANG . " and pago = 1 and financeiro_tipos = " . $i . " " . $filtro_conta_atual . " " . $filtro_mes_atual);
        $saldo_todos = gerar_saldo(" where lang = " . LANG . "              and financeiro_tipos = " . $i . " " . $filtro_conta_atual . " " . $filtro_mes_atual);
        $saida['saldo_tipos_pago'][$i] = preco($saldo_pago, 1);
        $saida['saldo_tipos_todos'][$i] = preco($saldo_todos, 1);
        $saida['saldo_tipos_porc'][$i] = 0;
        if ($saldo_pago * $saldo_todos)
            $saida['saldo_tipos_porc'][$i] = ($saldo_pago * 100) / $saldo_todos;
    }


    // SALDO RIGTH
    $saida['saldo_pago'] = preco($financeiro['saldo_pago'], 1);
    $saida['saldo_nao_pago'] = preco($financeiro['saldo_nao_pago'], 1);
    $saida['saldo'] = preco($financeiro['saldo'], 1);
}
?>