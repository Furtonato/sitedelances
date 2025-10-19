<?php

ob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';

$mysql = new Mysql();

$arr = array();
$arr['alert'] = 'z';
$arr['evento'] = '';
$arr['count_lances'] = 0;

// Arrumando formato do lance (input)
if (isset($_POST['lance'])) {
    $_POST['lance'] = str_replace('.', '', $_POST['lance']);
    $_POST['lance'] = str_replace(',', '.', $_POST['lance']);
}

// Arrumando formato do lance (+)
if (isset($_POST['id']) AND $_POST['id']) {
    $mysql->colunas = 'id, situacao, leiloes, lances, acrescimo, lances_cadastro, lances_plaquetas, lances_data, sucata, lance_ini, lance_min, data_acrescimo, data_acrescimo_ok, data_ini';
    $mysql->filtro = " where " . STATUS . " AND id = '" . $_POST['id'] . "' ";
    $lotes = $mysql->read_unico('lotes');

    if (isset($_POST['lance_mais']) AND $_POST['lance_mais']) {
        $_POST['lance'] = $lotes->lances + $_POST['lance_mais'];
    }
}

// Verificar login
if (!( (isset($_SESSION['x_site']->id) AND $_SESSION['x_site']->id) OR ( isset($_POST['plaqueta']) AND isset($_SESSION['x_admin']->id) AND $_SESSION['x_admin']->id) )) {
    $arr['erro'][] = lang('Você precisa estar logado para participar!');
    $arr['evento'] = 'setTimeout(function(){ window.parent.location="' . DIR . '/login/lote/-/' . $_POST['id'] . '"; }, 1000);';


    // Outras Verificacoes
} elseif (isset($lotes->id) AND isset($_POST['lance']) AND $_POST['lance']) {
    $_POST['lance'] = (float) $_POST['lance'];

    // VERIFICAR ERROS
    if (isset($_POST['plaqueta']) AND isset($_SESSION['x_admin']->id) AND $_SESSION['x_admin']->id) {
        $leiloes_habilitacoes = array(1, 2);
        $lotes_habilitacoes_sucata = array(1, 2);
    } else {
        $mysql->prepare = array($lotes->leiloes);
        $mysql->filtro = " WHERE `cadastro` = '" . $_SESSION['x_site']->id . "' AND `leiloes` = ? ";
        $leiloes_habilitacoes = $mysql->read('leiloes_habilitacoes');

        $mysql->prepare = array($lotes->id);
        $mysql->filtro = " WHERE `cadastro` = '" . $_SESSION['x_site']->id . "' AND `lotes` = ? ";
        $lotes_habilitacoes_sucata = $mysql->read('lotes_habilitacoes_sucata');

        $_POST['cadastro'] = $_SESSION['x_site']->id;
    }

    // Verificar habilitacao
    if (count($leiloes_habilitacoes) == 0) {
        $arr['erro'][] = lang('Você ainda não está Habilitado para Participar desse Leilão!');

        // Verificar habilitacao
    } elseif (count($lotes_habilitacoes_sucata) == 0 AND $lotes->sucata) {
        $arr['erro'][] = lang('Você ainda não está Habilitado para Participar desse Lote de Sucata!');

        // Verificar se Leilao ja Começou
    } elseif (!$lotes->situacao AND $lotes->data_ini > date('Y-m-d H:i:s')) {
        $arr['erro'][] = lang('O Leilão Ainda Não Começou!');

        // Verificar se Leilao ja Finalizou
    } elseif ($lotes->situacao AND $lotes->situacao != 20) {
        $arr['erro'][] = lang('O Leilão Já foi Finalizado!');
    } elseif ($lotes->situacao == 20 AND $_POST['lance'] < $lotes->lance_min) {
        $arr['erro'][] = lang('O Lance Minimo:') . ' ' . preco($lotes->lance_min, 1);
    } elseif ($_POST['lance'] <= $lotes->lances) {
        $arr['erro'][] = lang('Seu Lance tem que ser maior que o lance atual!');
    } elseif ($_POST['lance'] < $lotes->lance_ini) {
        $arr['erro'][] = lang('O Lance ínicial é de') . ' ' . preco($lotes->lance_ini, 1) . '!';
    }
    // VERIFICAR ERROS


    if (!isset($arr['erro'])) {
        $arr['lance'] = $_POST['lance'];
        $arr['cadastro'] = isset($_POST['cadastro']) ? $_POST['cadastro'] : 0;
        $arr['plaquetas'] = isset($_POST['plaqueta']) ? $_POST['plaqueta'] : 0;

        // ATUALIZANDO ACRESCIMO
        if ($lotes->data_acrescimo_ok) {
            unset($mysql->campo);
            $mysql->logs = 0;
            $acrescimo = $lotes->acrescimo ? $lotes->acrescimo : rel('leiloes', $lotes->leiloes, 'acrescimo');
            $mysql->campo['data_acrescimo'] = date('c', mktime(date('H'), date('i'), date('s') + $acrescimo, date('m'), date('d'), date('Y')));
            ;
            $mysql->filtro = " where " . STATUS . " AND id = '" . $lotes->id . "' ";
            $ult_id = $mysql->update('lotes');
        }
        // ATUALIZANDO ACRESCIMO
        // MUDAR LANCE DA TABLE LOTES PARA LOTES_LANCES Mudar lance da table lote para lotes_lances
        if (isset($lotes->lances) AND $lotes->lances) {
            unset($mysql->campo);
            $mysql->logs = 0;
            $mysql->campo['data'] = $lotes->lances_data;
            $mysql->campo['lances'] = $lotes->lances;
            $mysql->campo['cadastro'] = $lotes->lances_cadastro;
            $mysql->campo['plaquetas'] = $lotes->lances_plaquetas;
            $mysql->campo['lotes'] = $lotes->id;
            $ult_id = $mysql->insert('lotes_lances');
        }

        unset($mysql->campo);
        $mysql->logs = 0;
        $mysql->campo['lances_data'] = date('c');
        $mysql->campo['lances'] = $arr['lance'];
        $mysql->campo['lances_cadastro'] = $arr['cadastro'];
        $mysql->campo['lances_plaquetas'] = $arr['plaquetas'];
        $mysql->filtro = " where " . STATUS . " AND id = '" . $lotes->id . "' ";
        $ult_id = $mysql->update('lotes');
        // MUDAR LANCE DA TABLE LOTES PARA LOTES_LANCES Mudar lance da table lote para lotes_lances
        // VENDA DIRETA
        if ($lotes->situacao == 20) {
            unset($mysql->campo);
            $mysql->logs = 0;
            $mysql->campo['situacao'] = 10;
            $mysql->filtro = " where " . STATUS . " AND id = '" . $lotes->id . "' ";
            $mysql->update('lotes');
        }
        // VENDA DIRETA
    }
}

if (!isset($arr['erro']) AND !(isset($ult_id) AND $ult_id)) {
    $arr['erro'][] = lang('Ocorreu Algum erro!');
}


echo json_encode($arr);
