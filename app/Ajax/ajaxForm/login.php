<?php

ob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';
include_once '../../../app/Classes/Email.php';

$mysql = new Mysql();

$arr = array();
$arr['alert'] = 'z';

$arr['status'] = false;


// Login
if (isset($_POST['fazer_login'])) {

    $mysql->prepare = array($_POST['email']);
    $mysql->filtro = "  WHERE `email` = ? ";
    $cadastro = $mysql->read_unico('cadastro');
    if (!isset($cadastro->id)) {
        $mysql->prepare = array($_POST['email']);
        $mysql->filtro = "  WHERE `login` = ? ";
        $cadastro = $mysql->read_unico('cadastro');
    }

    if (isset($cadastro->id)) {
        $login_senha = $cadastro->senha;
        $login_status = $cadastro->status;

        //if($login_status){
        if (strcmp($cadastro->senha, md5($_POST['senha'])) == 0) {
            fazer_login($cadastro->id);

            // Enviar Email
            $mysql->filtro = " WHERE `id` = 51 ";
            $textos = $mysql->read_unico('textos');
            $var_email = 'nome->' . $cadastro->nome . '&email->' . $cadastro->email . '&data->' . date('d/m/Y H:i') . '&ip->' . $_SERVER['REMOTE_ADDR'];

            $email = new Email();
            $email->to = $cadastro->email;
            $email->remetente = nome_site();
            $email->assunto = var_email($textos->nome, $var_email);
            $email->txt = var_email(txt($textos), $var_email);
            $email->enviar();


            $arr['status'] = true;

            $_POST['direcionar'] = (isset($_POST['direcionar']) AND $_POST['direcionar'] != '-') ? $_POST['direcionar'] : 'minha_conta';
            if ($_POST['direcionar'] == 'refresh') {
                $arr['evento'] = DIR . '/';
            } else {
                $arr['evento'] = DIR . '/' . $_POST['direcionar'];
            }
        } else
            $arr['erro'][] = lang('Senha Inválida!');
        //} else $arr['erro'][] = lang('Conta Bloqueada!');
    } else
        $arr['erro'][] = lang('Email ou Login Não Cadastrado!');




    echo json_encode($arr);
} elseif (isset($_GET['sair'])) {

    // Log
    if (isset($_SESSION['x_site']->log)) {
        $mysql->prepare = array($_SESSION['x_site']->log);
        $mysql->filtro = " WHERE `id` = ? ";
        $mysql->campo['data_saida'] = date('c');
        $ult_id = $mysql->update('log');
    }

    // Unset
    unset($_SESSION['x_site']);
    unset($_SESSION['carrinho']);

    header("Location: //" . $_SERVER['HTTP_HOST']);
}


