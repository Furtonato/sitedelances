<?php

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require_once "../../../../system/conecta.php";
require_once "../../../../system/mysql.php";
require_once "../../../../app/Funcoes/funcoes.php";
require_once "../../../../app/Funcoes/funcoesAdmin.php";
require_once "../../../../app/Classes/Email.php";

$mysql = new Mysql();
$mysql->ini();

$arr = array();
$arr['id'] = 0;
$arr['acao'] = $_POST['acao'];


$ids = isset($_POST['ids']) ? explode('-', $_POST['ids']) : array($_POST['id']);

$mysql->prepare = array($_POST['modulos']);
$mysql->filtro = " WHERE `id` = ? ";
$modulos = $mysql->read_unico('menu_admin');

if (isset($_POST['table']) and $_POST['table'] == 'mais_comentarios') {
    verificar_permissoes_all($modulos, array($ids[0] ? $ids[0] : $ids[1]), 'extras', 1);
    $arr['table'] = $_POST['table'];
    $filtro = " and tabelas = '" . $modulos->modulo . "' and item = '" . $_POST['item'] . "' ";
} elseif (isset($_POST['table']) and $_POST['table'] == 'mais_fotos') {
    verificar_permissoes_all($modulos, array($ids[0] ? $ids[0] : $ids[1]), 'extras', 1);
    $arr['table'] = $_POST['table'];
    $arr['tabelas'] = $modulos->modulo;
    $filtro = " and tabelas = '" . $modulos->modulo . "' and item = '" . $_POST['item'] . "' ";
} else {
    verificar_permissoes_all($modulos, array($ids[0] ? $ids[0] : $ids[1]), 'extras');
    $arr['table'] = $modulos->modulo;
    $filtro = verificar_permissoes_itens($arr['table']);
}

// atualizar_acoes_filho
function atualizar_acoes_filho($arr, $item)
{
    $return = $arr;
    if ($arr['table'] == 'mais_comentarios') {
        $mysql = new Mysql();
        $mysql->prepare = array($item->item, $item->tabelas);
        $mysql->filtro = " WHERE `lang` = " . LANG . " AND `item` = ? AND `tabelas` = ? ";
        $consulta = $mysql->read('mais_comentarios');
        $arr['evento'] = "$('table.datatable').find('.td_datatable[dir=" . A2 . $item->item . A2 . "]').parent().parent().find('.n_mais_comentarios span').html(" . count($consulta) . "); ";
        $return = $arr;
    }
    return $return;
}

foreach ($ids as $key => $value) {
    if ($value) {
        $mysql->prepare = array($value);
        $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
        $item = $mysql->read_unico($arr['table']);
        if (isset($item->id)) {

            $mysql->logs_caminho = '../../';

            switch ($_POST['acao']) {

                    // Block
                case 'block':
                    verificar_permissoes_acoes($modulos, 'block', $arr['table']);
                    unset($mysql->campo);
                    if ($item->status)
                        $mysql->logs = 'Desbloqueio (Status)';
                    else
                        $mysql->logs = 'Bloqueio (Status)';
                    $mysql->campo['status'] = (int) !$item->status;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);

                    if ($arr['table'] == 'cadastro' and !$item->status) {
                        $email = new Email();
                        $email->to = $item->email;
                        $email->assunto = 'Você foi habilitado no site ' . nome_site();
                        $email->txt = 'Agora você está habilitado a participar dos leilões no site <a href="' . DIR_C . '">' . nome_site() . '</a>';
                        $email->enviar();
                    }
                    break;


                    // Block1
                case 'block1':
                    verificar_permissoes_acoes($modulos, 'block', $arr['table']);
                    unset($mysql->campo);
                    if ($item->status1)
                        $mysql->logs = 'Desbloqueio (Status1)';
                    else
                        $mysql->logs = 'Bloqueio (Status1)';
                    $mysql->campo['status'] = (int) !$item->status;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);
                    break;


                    // Clonar
                case 'clonar':
                    verificar_permissoes_acoes($modulos, 'clonar', $arr['table']);
                    if ($item->table == 'menu_admin') {
                        $mysql->json = 1;
                        $mysql->prepare = array($value);
                        $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                        $item = $mysql->read_unico($arr['table']);

                        $mysql->logs = 'Item Clonado';
                        foreach ($item as $key1 => $value1) {
                            if ($key1 != 'id' and $key1 != 'table') {
                                if ($key1 == 'data' or $key1 == 'dataup')
                                    $mysql->campo[$key1] = date('c');
                                else
                                    $mysql->campo[$key1] = $value1;
                            }
                        }
                        if ($modulos->id == 1)
                            $mysql->logs = 0;
                        $arr['id'] = $mysql->insert($arr['table']);

                        copy("../../../../app/Json/menu_admin/" . $value . ".json", "../../../../app/Json/menu_admin/" . $arr['id'] . ".json");

                        $mysql->json = 0;
                    } else {
                        unset($mysql->campo);
                        $mysql->logs = 'Item Clonado';
                        foreach ($item as $key1 => $value1) {
                            if ($key1 != 'id' and $key1 != 'table') {
                                if ($key1 == 'data' or $key1 == 'dataup')
                                    $mysql->campo[$key1] = date('c');
                                else
                                    $mysql->campo[$key1] = $value1;
                            }
                        }
                        if ($modulos->id == 1)
                            $mysql->logs = 0;
                        $arr['id'] = $mysql->insert($arr['table']);

                        $mysql->prepare = array($item->id, $item->table);
                        $mysql->filtro = " WHERE `item` = ? AND `tabelas` = ? ";
                        $z_txt = $mysql->read('z_txt');
                        foreach ($z_txt as $k => $v) {
                            unset($mysql->campo);
                            $mysql->campo['tipo'] = $v->tipo;
                            $mysql->campo['item'] = $arr['id'];
                            $mysql->campo['tabelas'] = $v->tabelas;
                            $mysql->campo['txt'] = $v->txt;
                            $mysql->insert('z_txt');
                        }
                    }
                    $arr = atualizar_acoes_filho($arr, $item);
                    break;


                    // Star
                case 'star':
                    verificar_permissoes_acoes($modulos, 'star', $arr['table']);
                    unset($mysql->campo);
                    if ($item->star)
                        $mysql->logs = 'Destaque Desativado';
                    else
                        $mysql->logs = 'Destaque Ativado';
                    $mysql->campo['star'] = !$item->star;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);
                    break;


                    // Lancamentos
                case 'lancamentos':
                    verificar_permissoes_acoes($modulos, 'lancamentos', $arr['table']);
                    unset($mysql->campo);
                    if ($item->lancamentos)
                        $mysql->logs = 'Lançamentos Desativado';
                    else
                        $mysql->logs = 'Lançamentos Ativado';
                    $mysql->campo['lancamentos'] = !$item->lancamentos;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);
                    break;


                    // Promocao
                case 'promocao':
                    verificar_permissoes_acoes($modulos, 'promocao', $arr['table']);
                    unset($mysql->campo);
                    if ($item->promocao)
                        $mysql->logs = 'Promoção Desativado';
                    else
                        $mysql->logs = 'Promoção Ativado';
                    $mysql->campo['promocao'] = !$item->promocao;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);
                    break;

                    // Delete
                case 'delete':
                    verificar_permissoes_all($modulos, array($value), 'delete', $arr['table']);
                    unset($mysql->campo);
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->delete($arr['table']);

                    $arr = atualizar_acoes_filho($arr, $item);
                    break;


                case 'abrir_lance':
                    //verificar_permissoes_acoes($modulos, 'block', $arr['table']);
                    unset($mysql->campo);

                    $mysql->logs = 'Liberado para Lance!';

                    $mysql->campo['situacao'] = 0;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);

                    break;

                case 'apagar_lances':
                    /* $sql = $db->prepare("DELETE FROM lotes_lances WHERE id > 0");
                    $executa = $sql->execute(); */

                    unset($mysql->campo);

                    $mysql->logs = 'Lances apagados!';

                    $mysql->campo['lances'] = 0;
                    $mysql->campo['lances_cadastro'] = 0;
                    $mysql->campo['lance_cadastro'] = 0;
                    if ($modulos->id == 1)
                        $mysql->logs = 0;

                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
                    $arr['id'] = $mysql->update($arr['table']);
                    
                    $mysql->prepare = array($value);
                    $mysql->filtro = " WHERE lotes = ? ";
                    $mysql->delete('lotes_lances');


                    break;
            }
        }
    }
}

$mysql->fim();


/* if ($_POST['acao'] == 'apagar_lances') {
    $db = $mysql->getDB();
    $sql = $db->prepare("DELETE FROM lotes_lances WHERE lotes = $value");
    $sql->execute();
} */

echo json_encode($arr);
