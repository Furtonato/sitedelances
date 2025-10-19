<?php


//if(extension_loaded('zlib')){ob_start('ob_gzhandler');}

require_once "../head.php";



// AS VARIAVEIS DO AJAX ESTAO NO HEAD.PHP
// GRAVAÇÃO
if ($_GET['acao'] == 'gravar' and $_POST) {

    /* ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL); */

    include 'Individual/verificacoes.php';

    // Datatable Boxs
    if (isset($_POST['datable_boxs_pai']) and $_POST['datable_boxs_pai'] and isset($_POST['datable_boxs_item']) and $_POST['datable_boxs_item']) {
        $_POST[$_POST['datable_boxs_pai']] = $_POST['datable_boxs_item'];
        unset($_POST['datable_boxs_pai']);
        unset($_POST['datable_boxs_item']);
    }

    // Verificar validacoes
    validacoes($table, $modulos, $_POST, (isset($_GET['id']) ? $_GET['id'] : 0));

    // Retirar Pots q devem ser Files
    $_POST = remover_posts_files($_POST);

    // Datas Firefox
    data_firefox();

    // Criando Colunas
    $criarMysql = new criarMysql();
    $criarMysql->criarColunasArray($table, $_POST);
    $criarMysql->criarColunasArray($table, $_FILES);

    unset($mysql->campo);
    $mysql->campo['dataup'] = date('c');
    $mysql->campo = gravar_campos($table, $mysql->campo);

    // Gravando no Banco
    if (isset($_GET['id']) and $_GET['id']) {
        include 'Individual/update.php';
        if (!isset($arr['ult_id'])) {
            $coluna = $table == 'configs' ? 'tipo' : 'id';
            $mysql->prepare = array($_GET['id']);
            $mysql->filtro = " WHERE `" . $coluna . "` = ? ";
            $arr['ult_id'] = $mysql->update($table);
            $arr['dataup'] = date('d/m/Y H:i');
        }
    } else {
        include 'Individual/insert.php';
        if (!isset($arr['ult_id'])) {
            $arr['ult_id'] = $mysql->insert($table);
        }
    }
    $arr['acao'] = $_POST['acao_button'];

    include 'Individual/fim.php';


    // OUTRAS GRAVACOES
    // Inserir_box (Fieldset)
    $_POST = inserir_box_gravar($_POST, $arr['ult_id'], $table);

    // Gravando do Editor
    editor_gravar($table, $arr['ult_id'], $_POST);

    // Fotos
    $upload = new Upload();
    $caminho = LUGAR == 'admin' ? '../' : '../../';
    if (isset($_FILES)) {
        $files = $upload->fileUpload($arr['ult_id'], $caminho);

        /* if ($files) {

            // Coloca marca D'Agua
            include 'canvas.php';
            $canvas = new Canvas();

            $path = preg_match('(admin)', LUGAR) ? $caminho . "../web/fotos/" : $caminho . "web/fotos/";

            $dir = preg_match('(admin)', LUGAR) ? $caminho . "../web/img/" : $caminho . "web/img/";

            $new_path = preg_match('(admin)', LUGAR) ? $caminho . "../web/fotos/backup/" : $caminho . "web/fotos/backup/";

            foreach ($files as $key => $value) {
                if ($value == 'erro') {
                    $arr['evento'] = 'alert("Tamanho Máximo da Imagem Permitido: 2MB \nAlguma imagem cadastrada é maior que o tamanho máximo permitido e não foi cadastrada!");';
                }

                //Verifica se é imagem e se não está no pasta backup
                if (!file_exists($new_path . $value)) {
                    if (is_file($path . $value)) {
                        //copy($path . $value, $new_path . $value);
                    }
                }
                $db = $mysql->getDB();
                $sql = $db->prepare("SELECT valor FROM configs WHERE id = 23");
                $executa = $sql->execute();
                $reg = $sql->fetch(PDO::FETCH_ASSOC);

                $marca_dagua = $reg['valor'];

                $file = $path . $value;

                $canvas->carrega($file);
                $canvas->marca($dir . $marca_dagua, 'meio', 'centro', 50);
                $canvas->grava($path . $value, 80);
            }
        } */
    }

    // Varias Categorias - Categorias (Niveis) 
    vcategorias_categorias_nivels_gravar($table);
    // OUTRAS GRAVACOES
    // Datatable Boxs
    if (isset($_POST['datatable_boxs'])) {
        $arr['table'] = $table;
        $arr['rand'] = $_POST['rand'];
        $arr['datatable_boxs'] = 1;
        if ($table == 'mais_comentarios') {
            $mysql->colunas = 'item, tabelas';
            $mysql->prepare = array($arr['ult_id']);
            $mysql->filtro = " WHERE `id` = ? ";
            $item = $mysql->read_unico('mais_comentarios');
            $mysql->colunas = 'id';
            $mysql->prepare = array($item->item, $item->tabelas);
            $mysql->filtro = " WHERE `lang` = " . LANG . " AND `item` = ? AND `tabelas` = ? ";
            $consulta = $mysql->read('mais_comentarios');
            $arr['evento'] = "$('table.datatable').find('.td_datatable[dir=" . A2 . $item->item . A2 . "]').parent().parent().find('.n_mais_comentarios span').html(" . count($consulta) . "); ";
        }
    }



    // VIEWS Lista
} elseif ($_GET['acao'] == 'lista' and $_POST) {
    $arr['html'] .= '<div class="lista_' . $modulos->modulo . ' lista_' . $modulos->id . '">
                                <div class="mapa_url">
                                    <h1> ' . datatable_title($modulos) . ' </h1> ';

    if ($modulos->modulo == 'leiloes' or $modulos->modulo == 'lotes') {
        $leiloes_status = isset($_GET['get']['leiloes_status']) ? $_GET['get']['leiloes_status'] : 'zz';
        $arr['html'] .= '   <div class="">
                                        <b>Status:</b>
                                        <select class="designx" onchange="location=options[selectedIndex].value">
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '">Todos</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=0" ' . iff($leiloes_status == '0', 'selected') . '>Em Loteamento</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=1" ' . iff($leiloes_status == '1', 'selected') . '>Aberto</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=2" ' . iff($leiloes_status == '2', 'selected') . '>Arrematado</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=3" ' . iff($leiloes_status == '3', 'selected') . '>Não Arrematado</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=10" ' . iff($leiloes_status == '10', 'selected') . '>Em Condicional</option>
                                            <option value="' . DIR . '/admin/?pg=' . $modulos->id . '&mod=' . $modulos->modulo . '&leiloes_status=20" ' . iff($leiloes_status == '20', 'selected') . '>Venda Direta</option>
                                        </select>
                                    </div>
                                </div> ';
    }

    $arr['html'] .= '   <div class="box_table">
                                    ' . datatable_calendar($modulos) . ' <!-- Financeiro -->
                                    ' . datatable_script($modulos, $passar_para_ajax, $datatables_center, $table_ordem) . '
                                    ' . datatable_acoes($modulos, $datatables_top, $datatables_center) . '
                                    <form onSubmit="datatable_ordenar(' . A . $modulos->id . A . ', this)" method="post" action="javascript:void(0)" enctype="multipart/form-data">
                                        <div class="clear"></div>
                                        <table cellpadding="0" cellspacing="0" border="0" class="calc_1 datatable">
                                            ' . datatable_top($modulos, $datatables_top) . '
                                            </tbody>
                                        </table>
                                        <button class="dni"></button>
                                        <div class="clear"></div>
                                    </form>
                                </div>
                                ' . datatable_saldo_estatisticas($modulos) . ' <!-- Financeiro -->
                                <div class="resultado_extra"></div>
                             </div> ';




    // VIEWS Boxxs
} elseif ($_GET['acao'] == 'boxxs' and $_POST) {
    $arr['html'] .= '  <div class="mapa_url">
                                    <h1> <i class="' . $modulos->foto . '"></i> ' . $modulos->nome . ' </h1>
                                </div>
                                <ul class="boxxs">
                                    ' . boxxs_admin($modulos) . '
                                </ul>
                                <div class="clear"></div> ';

    // VIEWS Novo e Edição
} elseif (($_GET['acao'] == 'novo' or $_GET['acao'] == 'edit') and $_POST) {

    $modulos_abas = $modulos->abas ? unserialize(base64_decode($modulos->abas)) : array();

    $modulos_campos = $modulos->campos ? unserialize(base64_decode($modulos->campos)) : array();

    //print_r($modulos_abas);

    $arr['title'] = 'Cadastro de ' . $modulos->nome . ' ' . iff($ids[0], '#' . $ids[0]);

    include 'Individual/campos.php';
    $arr['html'] .= '<div class="campos_do_modulo">';
    $arr['html'] .= conteudo_da_pagina($conteudo, $modulos, $ids, $modulos_abas, $modulos_campos, $linhas);
    $arr['html'] .= '</div>';

    include 'Individual/script.php';

    // DELETAR
} elseif ($_GET['acao'] == 'delete' and $_POST) {
    foreach ($ids as $k => $v) {
        $mysql->filtro = " WHERE id = '" . $v . "' " . verificar_permissoes_itens($table) . " ";
        $mysql->delete($table);
    }
}



// VERIFICAR SE O PLANO ESTA PAGO
$arr['plano_ativo'] = 1;
if (LUGAR == 'clientes') {
    if (isset($_SESSION['x_clientes']->id)) {
        $mysql->filtro = " WHERE cadastro = '" . $_SESSION['x_clientes']->id . "' AND " . VERIFICACAO_ADMIN_CLIENTES . " ";
        $pedidos = $mysql->read_unico('pedidos_status');
    }
    if (!isset($pedidos->id) or LUGAR == 'admin' or $table == 'pedidos' or $table == 'cadastro') {
        $mysql->filtro = " where cadastro = '" . $_SESSION['x_clientes']->id . "' ";
        $pedidos_all = $mysql->read_unico('pedidos');
        $arr['plano_ativo'] = isset($pedidos_all->id) ? 2 : 0;
    }
}
// VERIFICAR SE O PLANO ESTA PAGO




echo json_encode(limpa_espacoes($arr));

//if(extension_loaded('zlib')){ob_end_flush();}