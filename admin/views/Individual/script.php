<?php

// Cadastro
if ($modulos->modulo == 'cadastro') {

    $arr['html'] .= '<script> ';
    $arr['html'] .= '$("#tipo_0").on("click", function() { requireds(".pg_' . $modulos->modulo . ' ul.campos.box .conteudo_ini", ".req_tipo_0", ".req_tipo_1") }); ';
    $arr['html'] .= '$("#tipo_1").on("click", function() { requireds(".pg_' . $modulos->modulo . ' ul.campos.box .conteudo_ini", ".req_tipo_1", ".req_tipo_0") }); ';
    $tipo = isset($linhas->tipo) ? $linhas->tipo : 0;
    $arr['html'] .= 'requireds_ini(".pg_' . $modulos->modulo . ' ul.campos.box .conteudo_ini", parseInt(' . $tipo . ')); ';
    $arr['html'] .= '</script> ';






    // Financeiro
} elseif ($modulos->modulo == 'financeiro') {

    if ($_GET['acao'] == 'novo')
        $arr['html'] .= '<script> setTime(function(){ $(' . A . '.finput.finput_financeiro_tipos #financeiro_tipos' . A . ').val(' . $_SESSION['financeiro_tipos'] . ').trigger(' . A . 'change' . A . '); $(' . A . '.finput.finput_financeiro_contas #financeiro_contas' . A . ').val(' . $_SESSION['financeiro_conta_atual'] . ').trigger(' . A . 'change' . A . '); }, "0.5"); </script>';
} elseif ($modulos->modulo == '') {

    $arr['html'] .= ' ';
}
?>