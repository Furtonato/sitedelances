<?php

require_once '../../system/conecta.php';
require_once '../../plugins/PHPMailer/class.phpmailer.php';

require_once '../../system/mysql.php';
require_once '../../app/Funcoes/funcoes.php';
//   require_once '../../plugins/Tng/tng/tNG.inc.php';

include_once '../../app/Funcoes/funcoesAdmin.php';


// Verificar Sessao
verificar_sessao();

// Iniciando Classes
$mysql = new Mysql();
$input = new Input();
$img = new Imagem();


// Acoes
require_once "../app/controllers.php";
require_once "../app/verificacao.php";

// Auto Load Class
function __autoload($class_name)
{
    // Classes
    if (file_exists("../../app/Classes/" . $class_name . ".php")) {
        require_once("../../app/Classes/" . $class_name . ".php");
    } elseif (file_exists("../app/Classes/" . $class_name . ".php")) {
        require_once("../app/Classes/" . $class_name . ".php");
        // TNG
    } else if (file_exists("../../plugins/Tng/tng/triggers/" . $class_name . ".class.php")) {
        //require_once("../../plugins/Tng/tng/triggers/".$class_name.".class.php");
    }
}

// VARIAVEIS DO AJAX
if (isset($modulos->id)) {

    $urll['pg'] = 'pg=' . $modulos->id;
    $urll['mod'] = 'mod=' . $modulos->modulo;
    if (isset($_GET['gets'])) {
        $ex = explode(';;z;;', $_GET['gets']);
        foreach ($ex as $k => $v) {
            $ex1 = explode('=', $v);
            if ($ex1[0] != 'pg')
                $urll[$ex1[0]] = $v;
            if ($ex1[0] == 'table')
                $_POST['tables'] = '-' . $ex1[1] . '-';
        }
    }

    $arr = array();
    $arr['html'] = "<script> GETS = '?" . implode('&', $urll) . "'; </script>";
    $arr['title'] = '';
    $arr['modulo'] = $modulos->modulo;
    $arr['url'] = DIR . '/' . LUGAR . '/?' . implode('&', $urll);


    // IDS PARA EDIT E DELETE
    $ids = array();
    if ($_GET['acao'] == 'edit' or $_GET['acao'] == 'delete') {
        $table = $modulos->modulo;
        $ex = isset($_POST['ids']) ? explode('-', $_POST['ids']) : array();
        foreach ($ex as $v) {
            if ($v)
                $ids[] = $v;
        }

        $ex = isset($_POST['tables']) ? explode('-', $_POST['tables']) : array();
        $tables = array();
        foreach ($ex as $v) {
            if ($v)
                $tables[] = $v;
        }
        $table = isset($tables[0]) ? $tables[0] : $table;
    }
    $ids[0] = isset($ids[0]) ? $ids[0] : 0;

    $_GET['acao'] = $_GET['acao'] == 'filtro' ? 'lista' : $_GET['acao'];
    // IDS PARA EDIT E DELETE
    // ACOES DE PERMISSAO (VIOLACAO DE REGRAS)
    $linhas = verificar_permissoes_all($modulos, $ids);
    // ACOES DE PERMISSAO (VIOLACAO DE REGRAS)
}
