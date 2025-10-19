<?php

//ob_start();

require_once "../../../system/conecta.php";
require_once "../../../system/mysql.php";
include_once '../../../app/Funcoes/funcoes.php';
include_once '../../../app/Classes/Email.php';
include_once '../../../app/Classes/Upload.php';

$mysql = new Mysql();


$arr = array();
$arr['alert'] = 'z';
$arr['status'] = false;


if (isset($_POST['gravar']) or isset($_POST['update'])) {


    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);

    $table = 'cadastro';

    $x = 0;
    $modulos_site['abas'][0]['check'] = '1';
    $modulos_site['campos'] = array();

    // Verificar validacoes
    $x++;
    $modulos_site['campos'][0]['outros_' . $x]['check'] = 1;
    $modulos_site['campos'][0]['outros_' . $x]['nome'] = 'CPF';
    $modulos_site['campos'][0]['outros_' . $x]['input']['nome'] = 'cpf';
    $modulos_site['campos'][0]['outros_' . $x]['input']['tags'] = 'required validar="cpf"';

    $x++;
    $modulos_site['campos'][0]['outros_' . $x]['check'] = 1;
    $modulos_site['campos'][0]['outros_' . $x]['nome'] = 'CNPJ';
    $modulos_site['campos'][0]['outros_' . $x]['input']['nome'] = 'cnpj';
    $modulos_site['campos'][0]['outros_' . $x]['input']['tags'] = 'required validar="cnpj"';

    $x++;
    $modulos_site['campos'][0]['outros_' . $x]['check'] = 1;
    $modulos_site['campos'][0]['outros_' . $x]['nome'] = 'Email';
    $modulos_site['campos'][0]['outros_' . $x]['input']['nome'] = 'email';
    $modulos_site['campos'][0]['outros_' . $x]['input']['tags'] = 'required validar';

    $x++;
    $modulos_site['campos'][0]['outros_' . $x]['check'] = 1;
    $modulos_site['campos'][0]['outros_' . $x]['nome'] = 'Senha';
    $modulos_site['campos'][0]['outros_' . $x]['input']['nome'] = 'senha';
    $modulos_site['campos'][0]['outros_' . $x]['input']['tags'] = 'required comparar="c_senha"';

    $x++;
    $modulos_site['campos'][0]['outros_' . $x]['check'] = 1;
    $modulos_site['campos'][0]['outros_' . $x]['nome'] = 'Confirmar Senha';
    $modulos_site['campos'][0]['outros_' . $x]['input']['nome'] = 'c_senha';
    $modulos_site['campos'][0]['outros_' . $x]['input']['tags'] = 'required';


    // VALIDACOES
    $validades = array('nome', 'telefone', 'cep', 'rua', 'numero', 'bairro', 'estados', 'cidades', 'login');
    if (!isset($_POST['update'])) {
        $validades[] = 'senha';
        $validades[] = 'termos';
    }
    foreach ($validades as $key => $value) {
        if (!(isset($_POST[$value]) and $_POST[$value])) {
            $arr['erro'][] = 'Preencha o campo: ' . ucfirst($value);
        }
    }
    if (isset($arr['erro'])) {
        echo json_encode($arr);
        exit();
    }

    $id = 0;
    if (isset($_POST['update']))
        $id = $_SESSION['x_site']->id;
    validacoes($table, array(), $_POST, $id, $modulos_site);
    // VALIDACOES


    $arr['html'] = '';
    include '../../../admin/views/Individual/variaveis.php';


    /* / Datas
      $_POST = firefox_calendar($_POST);

      if (isset($_POST['dia']) and isset($_POST['mes']) and isset($_POST['ano'])) {
      $_POST['nascimento'] = $_POST['ano'] . '-' . $_POST['mes'] . '-' . $_POST['dia'];
      unset($_POST['dia']);
      unset($_POST['mes']);
      unset($_POST['ano']);
      }
      / */

    $mysql->campo['status'] = 0;
    $mysql->campo['dataup'] = date('c');

    $mysql->campo = gravar_campos($table, $mysql->campo);

    // Gravando no Banco
    if (isset($_POST['update'])) {
        $mysql->prepare = array($_SESSION['x_site']->id);
        $mysql->filtro = " WHERE `id` = ? ";
        $arr['ult_id'] = $mysql->update($table);
    } else {
        $arr['ult_id'] = $mysql->insert($table);
    }


    // Fotos
    $upload = new Upload();
    if (isset($_FILES))
        $upload->fileUpload($arr['ult_id'], '../../../');


    if (!isset($_POST['update'])) {

        // Newsletter
        //if(isset($_POST['newsletter']) and $_POST['newsletter']){
        unset($mysql->campo);
        $mysql->campo['nome'] = $_POST['nome'];
        $mysql->campo['email'] = $_POST['email'];
        $mysql->campo['categorias'] = 1;
        $mysql->insert('newsletter');
        //}
        // Enviar Email
        $mysql->filtro = " WHERE `id` = 50 ";
        $textos = $mysql->read_unico('textos');
        $var_email = 'nome->' . $_POST['nome'] . '&email->' . $_POST['email'] . '&senha->' . $_POST['senha'];

        $email = new Email();
        $email->to = $_POST['email'];
        $email->remetente = nome_site();
        $email->assunto = var_email($textos->nome, $var_email);
        $email->txt = var_email(txt($textos), $var_email);
        $email->enviar();

        // Fazendo Login
        fazer_login($arr['ult_id']);

        $arr['evento'] = DIR . '/minha_conta/documentos/';
        $arr['status'] = true;
    } else {
        $arr['alert'] = lang('CADASTRO EDITADO COM SUCESSO!');
        $arr['evento'] = 'fechar_all();';
        $arr['evento'] .= 'setTimeout(function() {
            window . location . reload();
        }, 200);';
    }
}


echo json_encode($arr);
