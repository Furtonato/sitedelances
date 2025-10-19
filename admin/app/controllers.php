<?php

// CONFIGURACOES
// Convertando GETS para GET
if (isset($_GET['gets'])) {
    $gets = explode(';;z;;', $_GET['gets']);
    foreach ($gets as $key => $value) {
        $ex = explode('=', $value);
        if (isset($ex[1])) {
            $_GET['get'][$ex[0]] = $ex[1];
        }
    }
}

// Modulos
if (LUGAR == 'admin') {
    $verificar_permissoes_usuario = verificar_permissoes_usuario();
    $mysql->prepare = array($_GET['modulo']);
    $mysql->filtro = " WHERE `id` = ? " . $verificar_permissoes_usuario . " ";
    $modulos = $mysql->read_unico('menu_admin');

    if ($_GET['modulo'] >= 69 and $_GET['modulo'] <= 73) {
        $mysql->filtro = " WHERE `id` = 63 ";
        $modulos_outros = $mysql->read_unico('menu_admin');

        $modulos->informacoes = $modulos_outros->informacoes;
        $modulos->campos = $modulos_outros->campos;
    }
} else {
    $mysql->prepare = array($_GET['modulo']);
    $mysql->filtro = " WHERE `id` = ? ";
    $modulos = $mysql->read_unico('menu_admin');
}


if (isset($modulos->modulo)) {
    $_GET['pg'] = $table = $modulos->modulo;
} elseif ($_GET['modulo'] == 'boxs') {
    $table = $_POST['table'];
    unset($_POST['table']);
} else {
    $arr['title'] = 'Gerenciar Itens';
    $arr['violacao_de_regras'] = 1;
    violacao_de_regras($arr);
}


// Logs de Acoes
define('LOGS_ACOES', 'usuarios');
define('LOGS_ACOES_ID', $_SESSION['x_' . LUGAR]->id);


// Filtro Lang (inicando o where)
$filtro = isset($filtro) ? $filtro : '';

// CONFIGURACOES
// DATATABLES
if (isset($_GET['pg'])) {

    $passar_para_ajax = '"filtro": "' . $filtro . '", "modulo": "' . $modulos->id . '", "pg": "' . $_GET['pg'] . '",';

    // Passando itens das colunas
    if ($modulos->id == 1) {
        $datatables_top = array('Status', 'Nome', 'Ordem', 'Icon', 'Categorias');
        $datatables_center = array('status', 'nome', 'ordem', 'foto->icon', 'categorias');
    } else {
        $modulos->colunas = datatable_usuarios_config($modulos);
        $colunas = $modulos->colunas;
        $campos = unserialize(base64_decode($modulos->campos));


        // Colunas
        $col_foto = 0;
        foreach ($campos[0] as $key => $value) {
            if (isset($value['check']) and $value['check']) {
                if ($value['input']['nome'] == 'foto')
                    $col_foto = 1;
            }
        }
        $datatables_top = array();
        $datatables_center = array();
        foreach ($colunas as $key => $value) {
            if (isset($value['check']) and $value['check']) {
                $ex = explode('->', $value['value']);
                if ($ex[0] == 'foto') {
                    if ($col_foto) {
                        $datatables_top[] = $value['nome'];
                        $datatables_center[] = $value['value'];
                    }
                } elseif ($ex[0] == 'relacionamento_categoria_automatico') {
                    if (preg_match('(-categorias-)', $modulos->informacoes)) {
                        $datatables_top[] = $value['nome'];
                        $datatables_center[] = 'categorias';
                    } elseif (preg_match('(-vcategorias-)', $modulos->informacoes)) {
                        $datatables_top[] = $value['nome'];
                        $datatables_center[] = 'vcategorias';
                    } elseif (preg_match('(-subcategorias-)', $modulos->informacoes)) {
                        $datatables_top[] = $value['nome'];
                        $datatables_center[] = 'subcategorias';
                    }
                } else {
                    $datatables_top[] = $value['nome'];
                    $datatables_center[] = $value['value'];
                }
            }
        }
        if ((isset($_GET['gets']) and (preg_match('(modulo=63)', $_GET['gets']) or preg_match('(modulo=69)', $_GET['gets']))) or (!isset($gerenciar_itens) and (preg_match('(-star-)', $modulos->informacoes) or preg_match('(-lancamentos-)', $modulos->informacoes) or preg_match('(-promocao-)', $modulos->informacoes) or preg_match('(-mapa-)', $modulos->informacoes)))) {
            $datatables_top[] = 'Ações';
            $datatables_center[] = 'id->acoes';
        }
    }

    // Ordenacao
    $table_ordem = (isset($modulos->table_ordem) and $modulos->table_ordem) ? $modulos->table_ordem : '';

    // Colunas (Value)
    foreach ($datatables_center as $key => $value) {
        $passar_para_ajax .= '"col[' . $key . ']": "' . $value . '",';
    }

    // Datatable Filtro
    if (isset($_POST['datatable_filtro'])) {
        foreach ($_POST['datatable_filtro'] as $k => $v) {
            foreach ($v as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k1 => $v1) {
                        if (is_array($v1)) {
                            foreach ($v1 as $k2 => $v2) {
                                $passar_para_ajax .= '"datatable_filtro[' . $k . '][' . $key . '][' . $k1 . '][' . $k2 . ']": "' . $v2 . '",';
                            }
                        } else {
                            $passar_para_ajax .= '"datatable_filtro[' . $k . '][' . $key . '][' . $k1 . ']": "' . $v1 . '",';
                        }
                    }
                } else {
                    $passar_para_ajax .= '"datatable_filtro[' . $k . '][' . $key . ']": "' . $value . '",';
                }
            }
        }
    }
}
// DATATABLES