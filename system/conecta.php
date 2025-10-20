<?php

// A CONFIGURAÇÃO DA SESSÃO FOI MOVIDA PARA CÁ (PARA ANTES DO SESSION_START)
// Garante que a pasta de sessão seja a 'tmp' dentro da raiz do site.
@ini_set('session.save_path', __DIR__ . '/../tmp');

if (!isset($no_session_start))
    session_start();

// DEFINES
// ADMIN
define('ADMIN', 'admin');


// LUGAR
$admins = array('clientes');
$lugar = 'site';
$url_fisica = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['SCRIPT_FILENAME'];
if (preg_match('(/' . ADMIN . ')', $url_fisica)) {
    $lugar = 'admin';
} else {
    foreach ($admins as $key => $value) {
        if (preg_match('(/' . $value . ')', $url_fisica)) {
            $lugar = $value;
        }
    }
}
define('LUGAR', $lugar);


// DIR
$dir_url = array($_SERVER['SCRIPT_NAME']);
$dir__ = array(__DIR__);
$pastas = array('admin', 'api', 'app', 'css', 'app', 'js', 'plugins', 'system', 'views', 'web', 'z_temp', 'tarefa_cron');
$pastas = array_merge($pastas, $admins);
foreach ($pastas as $key => $value) {
    $dir_url = explode('/' . $value, $dir_url[0]);
    $dir__ = explode(DIRECTORY_SEPARATOR . $value, $dir__[0]);
}
define('DIR', $dir_url[0]);
define('DIR_F', $dir__[0]);
define('DIR_C', 'https://' . $_SERVER['HTTP_HOST'] . DIR);
define('DIR_D', str_replace(DIR, '', $_SERVER['REQUEST_URI']));
define('DIR_ALL', $_SERVER['REQUEST_URI']);
define('DIR_ckfinder', '../..');


// LANG
if (!isset($_GET['lang'])) {
    $_GET['lang'] = isset($_SESSION['lang']) ? $_SESSION['lang'] : 1;
}
$_SESSION['lang'] = $_GET['lang'];
define('LANG', $_GET['lang']);


// CONFIGS
date_default_timezone_set("America/Sao_Paulo");
if (!isset($codificacao))
    ini_set("default_charset", "UTF-8");
ini_set("MAX_FILE_SIZE", "10M");
ini_set("allow_url_fopen", 1);
ini_set("allow_url_include", 1);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '-1');
// A LINHA @ini_set('session.save_path', 'tmp'); FOI REMOVIDA DAQUI
//setlocale(LC_TIME, NULL); // data BR smarty
// DEFINES
// PAGAMENTOS
// Situcoes de Pedidos
define('SITUACAO_PD', "Aguardando Pagamento");

// PagSeguro
define('PAGSEGURO', 1);
define('PAGSEGURO_TESTE', ''); // sandbox.
// PAGAMENTOS
// DEFINES MYSQL
define('STATUS', " `status` = 1 AND `lang` = '" . LANG . "' ");
define('ORDER', " `ordem` ASC, `nome` ASC, `id` DESC ");
// DEFINES MYSQL
// VERIFICACOES
define("SITUACAO", " AND (situacao = 0 OR situacao = 1 OR situacao = 20) ");
// VERIFICACOES
// CORES DAS SITUACOES
define('LOTEAMENTO', '#fff');
define('ABERTO', 'rgba(0, 194, 0, .5)');
define('ARREMATADO', 'rgba(229, 10, 25, .8)');
define('NAO_ARREMATADO', 'rgba(204, 204, 204, .5)');
define('CONDICIONAL', 'rgba(255, 128, 0, .8)');
define('VENDA_DIRETA', 'rgba(0, 0, 255, .5)');
// CORES DAS SITUACOES

require_once DIR_F . '/plugins/cn/config.php';
//		require_once '/home/admin/web/testeplataforma.com/public_html/plugins/ZZ/confg.php';


if (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1) {
    ini_set('display_errors', 0);
    define('TESTE', 0);
} else {
    ini_set('display_errors', 0);
    define('TESTE', 0);
}