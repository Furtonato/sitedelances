<?php

// VIEWS
// Conteudo da Pagina
function conteudo_da_pagina($conteudo, $modulos, $ids, $modulos_abas, $modulos_campos, $linhas)
{
    $return = '';

    $return .= '<div class="dialog"> ';
    $return .= dialog_acoes($modulos, $ids);
    $return .= '<div class="tabs menu_admin"> ';

    $form = "form_" . rand();
    if ($modulos->modulo != 'pedidos') {
        $return .= '<form class="' . $form . '" action="javascript:void(0)" method="post" enctype="multipart/form-data"> ';
    }

    // ABAS
    if (isset($modulos_abas[0]['check']) and $modulos_abas[0]['check'] and isset($modulos_abas[0]['nome']) and $modulos_abas[0]['nome']) {
        $return .= '<ul class="h31 nav"> ';
        $x = 0;
        foreach ($modulos_abas as $key => $value) {
            $x++;
            if ($value['check'] and $value['nome']) {
                $return .= '		<li tabs="tabs_' . $key . '" class="' . iff($x == 1, 'ativo', '') . ' ' . iff($value['disabled'] == 1, 'disabled', '') . '"> <a onclick="tabs(this)">' . $value['nome'] . '</a> </li> ';
            }
        }
        $return .= '	<div class="clear"></div> ';
        $return .= '</ul> ';
        $return .= '<div class="clear"></div> ';
    }
    // ABAS

    $return .= (isset($modulos->info_modulo) and $modulos->info_modulo) ? '<div class="mt10 ml10 mr10">' . $modulos->info_modulo . '</div>' : '';
    $return .= '<ul class="campos box"> ';

    // CAMPOS
    $x = 0;
    foreach ($modulos_abas as $kabas => $value_abas) {
        $x++;
        $return .= '<li tabs="tabs_' . $kabas . '" class="' . iff($x == 1, 'ativo', '') . '"> ';
        $return .= '   <ul class="itens"> ';
        $return .= isset($conteudo['ini'][$kabas]) ? '<div class="conteudo_ini">' . $conteudo['ini'][$kabas] . '</div>' : '';
        if (isset($modulos_campos[$kabas])) {
            $return .= campos_das_paginas($modulos_campos[$kabas], $kabas, $linhas);
        }
        $return .= isset($conteudo['fim'][$kabas]) ? '<div class="conteudo_fim">' . $conteudo['fim'][$kabas] . '</div>' : '';
        $return .= '   	<div class="clear"></div> ';
        $return .= '   </ul> ';
        $return .= '   <div class="h5 clear"></div> ';
        $return .= '</li> ';
    }
    // CAMPOS

    $return .= '</ul> ';
    $return .= '<input type="hidden" name="acao_button" value="3"> ';
    $return .= '<input type="reset" name="reset_button" class="dni"> ';
    $return .= '<input type="submit" class="dni" onclick="preencher_campos_corretos()"> ';

    if ($modulos->modulo != 'pedidos') {
        $return .= '</form> ';
    }

    $return .= '<div class="clear"></div> ';
    $return .= '<script> required_invalid(' . A . '.tabs.menu_admin form.' . $form . A . ') </script> ';
    $return .= '<script> gravar_item(' . A . $modulos->id . A . ', ' . A . $ids[0] . A . ', ' . A . '.tabs.menu_admin form.' . $form . A . ') </script> ';

    if (DIALOG_MAX)
        $return .= '<script> setTimeout(function(){ $(' . A . '.ui-dialog.pg_' . $modulos->id . A . ').addClass(' . A . 'max' . A . ') }, 0,5); </script> ';

    $return .= '</div> ';
    $return .= '</div> ';

    return $return;
}

// Campos da Pagina
function campos_das_paginas($modulos_campos, $kabas, $linhas, $datatable_box = 0)
{
    $input = new Input();
    $return = '';

    $x = 0;
    $fields_anterior = 0;
    foreach ($modulos_campos as $key => $value) {
        $x++;
        if (isset($value['check']) and $value['check']) {

            global $table;
            $tipo = $value['input']['tipo'];
            $tags = $value['input']['tags'];
            if (!($tipo == 'editor' and $_POST['body_width'] < MOBILE)) {
                $value['input']['design'] = isset($value['input']['design']) ? $value['input']['design'] : 1;
                if ($value['input']['design'] != 2) {
                    $desgin = 'class="design' . iff($datatable_box and $tipo == 'select', 'x') . ' ';
                    $tags = str_replace('class="', $desgin, $tags);
                    $tags = !(preg_match('(class=")', $tags)) ? $desgin . '" ' . $tags : $tags;
                }
                $value['input']['disabled'] = isset($value['input']['disabled']) ? $value['input']['disabled'] : 0;
                $disabled = $value['input']['disabled'] ? ' disabled ' : '';

                // Executar Funcao
                $executar_funcao = '';
                if (isset($value['input']['executar_funcao']) and $value['input']['executar_funcao']) {
                    $funcao = $value['input']['executar_funcao'];
                    eval("\$funcao = \"$funcao\";");
                    $executar_funcao = exe_funcao($funcao);
                }

                $value['input']['tipo1'] = $value['input']['tipo1'] ? $value['input']['tipo1'] : 'text';
                $value['input']['tipo1'] = (isset($value['temp']) and $value['temp'] == 'preco') ? 'search' : $value['input']['tipo1'];


                //$tags = str_replace('required', 'req', $tags);

                $input->tags = $tags . ' ' . $disabled . ' ' . $executar_funcao;
                $input->table = $table;
                $input->value = $linhas;
                $input->opcoes = isset($value['input']['opcoes']) ? $value['input']['opcoes'] : '';
                $input->extra = isset($value['input']['extra']) ? $value['input']['extra'] : '';
                $input->p = isset($value['nome_classe']) ? $value['nome_classe'] : '';
                $input->dois_pontos = isset($value['dois_pontos']) ? $value['dois_pontos'] : '';
                $input->datatable_box = $datatable_box;

                // Fim Fields
                $return .= (isset($value['fields']) and $fields_anterior and $fields_anterior != $value['fields']) ? ' </fieldset> <div class="clear"></div> </div> ' : '';

                // Inicio Fields
                $value['pai_fields_classe'] = isset($value['pai_fields_classe']) ? $value['pai_fields_classe'] : '';
                $return .= (isset($value['fields']) and $fields_anterior != $value['fields'] and $value['fields']) ? ' <div class="pl10 ' . $value['pai_fields_classe'] . '"> <fieldset class="w100p fll pt5 pb5 pr10 mt5 mb5 br1 ' . $value['fields_classe'] . '"> ' : '';

                // Legend
                if ((isset($value['fields']) and $fields_anterior != $value['fields'] and $value['fields'])) {
                    if ($value['nome'] and ($key == 'txt' or $key == 'txt_meta' or $key == 'editor')) {
                        $return .= '<legend class="pl5 pr5 ml5 mr5">' . $value['nome'] . '</legend>';
                        $value['nome'] = '';
                    } elseif ($value['legend']) {
                        $value['legend'] = isset($value['legend']) ? $value['legend'] : '';
                        $return .= '<legend class="pl5 pr5 ml5 mr5">' . $value['legend'] . '</legend>';
                    }
                }

                // Fields Edit
                if (preg_match('(inserir_box)', $value['input']['nome'])) {
                    $return .= inserir_box($linhas, $value['input']['nome']);
                    $input->value = '';
                }

                $resp = isset($value['resp']) ? $value['resp'] : '';
                $return .= ' <li class="' . $resp . ' linhas_inputs ' . $tipo . ' ' . iff($value['input']['tipo1'] == 'hidden', 'dni') . '"> ';
                if ($tipo == 'info') {
                    $return .= '<div class="mt5 ml10 mr10">' . $value['nome'] . $value['input']['tags'] . '</div> ';
                } else {
                    $return .= $input->$tipo($value['nome'], $value['input']['nome'], $value['input']['tipo1']);
                }
                $return .= ' <div class="clear"></div> ';
                $return .= ' </li> ';

                // Fim Fields (Ultima)
                $return .= (isset($value['fields']) and count($modulos_campos) == $x and $value['fields']) ? ' </fieldset> <div class="clear"></div> </div> ' : '';

                $fields_anterior = isset($value['fields']) ? $value['fields'] : 0;
            }
        }
    }

    // OUTROS ADMINS
    if (LUGAR != 'admin') {
        $return .= '<input type="hidden" name="' . table_admin() . '" value="' . $_SESSION['x_' . LUGAR]->id . '">';
    }

    return ($return);
}

// VIEWS
// VERIFICACOES DE VIOLACAO DE REGRAS (PERMISSOES)
// Pemissoes de Acesso
function verificar_permissoes_usuario()
{
    $mysql = new Mysql();
    $mysql->colunas = 'permissoes, permissoes_all';
    $mysql->prepare = array($_SESSION['x_admin']->id);
    $mysql->filtro = "WHERE `id` = ? ";
    $usuarios = $mysql->read_unico('usuarios');

    $permissoes = array(0);
    if ($usuarios->permissoes) {
        $ex = explode('-', $usuarios->permissoes);
        foreach ($ex as $key => $value) {
            if ($value)
                $permissoes[] = $value;
        }
    }
    $return = !($usuarios->permissoes_all == 't') ? ' and `id` IN (' . implode(',', $permissoes) . ') ' : '';
    return $return;
}

function verificar_permissoes_all($modulos, $ids, $acao = 'edit', $boxs_table = '')
{
    $acao = isset($_GET['acao']) ? $_GET['acao'] : $acao;
    $arr['violacao_de_regras'] = 1;
    // Menu Admin
    $menu_admin = (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1 and isset($modulos->modulo) and $modulos->id == 1) ? 1 : 0;
    if ($menu_admin) {
        $arr['violacao_de_regras'] = 0;
    }
    // Gravar
    if ($acao == 'gravar' and isset($_GET['id']) and $_GET['id']) {
        if (preg_match('(edit)', $modulos->informacoes))
            $arr['violacao_de_regras'] = 0;
    } elseif ($acao == 'gravar') {
        if (preg_match('(novo)', $modulos->informacoes))
            $arr['violacao_de_regras'] = 0;
    }
    // Novo
    if ($acao == 'novo' and preg_match('(novo)', $modulos->informacoes)) {
        $arr['violacao_de_regras'] = 0;
    }
    // Edit
    if ($acao == 'edit' and preg_match('(edit)', $modulos->informacoes)) {
        $arr['violacao_de_regras'] = 0;
    }
    // Delete
    if ($acao == 'delete' and preg_match('(excluir)', $modulos->informacoes)) {
        $arr['violacao_de_regras'] = 0;
    }
    // Lista
    if ($acao == 'lista' and verificar_permissoes_lista($modulos)) {
        $arr['violacao_de_regras'] = 0;
    }
    if ($acao == 'extras') {
        $acao == 'edit';
        $arr['violacao_de_regras'] = 0;
    }
    // Verificando se o id em textos ja existe
    if ($modulos->modulo == 'textos') {
        $mysql = new Mysql();
        $mysql->prepare = array($ids[0]);
        $mysql->filtro = " WHERE `id` = ? ";
        $textos = $mysql->read_unico('textos');
        if (!isset($textos->id)) {
            $mysql->campo['id'] = $ids[0];
            $mysql->insert('textos');
        }
    }
    // Verificando acesso ao usuario
    $linhas = '';
    if (($acao == 'edit' or $acao == 'delete') and !$menu_admin and !$boxs_table) {
        $filtro = verificar_permissoes_itens($modulos->modulo, '', $modulos);
        $linhas = consulta_no_banco($modulos->modulo, $ids, $filtro, $acao);
        if (is_array($linhas) or is_object($linhas)) {
            if (!$linhas) {
                $arr['violacao_de_regras'] = 1;
            }
        } elseif ($linhas == '0') {
            item_deletado();
        }
    }
    violacao_de_regras($arr);
    return $linhas;
}

function verificar_permissoes_lista($modulos)
{
    $return = 0;
    $mysql = new Mysql();
    if (LUGAR == 'admin') {
        $mysql->colunas = 'id';
        $mysql->prepare = array($_SESSION['x_admin']->id);
        $mysql->filtro = "WHERE `id` = ? AND (`permissoes_all` = 't' OR `permissoes` " . like('-' . $modulos->id . '-') . " ) ";
        $usuarios = $mysql->read_unico('usuarios');
        if (isset($usuarios->id))
            $return = 1;
    } elseif (isset($modulos->admins) and $modulos->admins == LUGAR) {
        $return = 1;
    }
    return $return;
}

function verificar_permissoes_itens($table, $filtro = '', $modulos = '')
{
    if (LUGAR == 'admin') {
        if ($table == 'usuarios' and !($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2))
            $filtro .= " AND `id` != 2 ";
    } else {
        if ($table == table_admin()) {
            $filtro .= " AND `id` = '" . $_SESSION['x_' . LUGAR]->id . "' ";
        } elseif ($table == 'configs') {
            $filtro .= '';
        } else {
            if (isset($modulos->id) and ($modulos->id == 0)) {
                $filtro .= " AND `" . table_admin() . "` = '" . $_SESSION['x_' . LUGAR]->id . "' ";
            } else {
                $filtro .= " AND `" . table_admin() . "` = '" . $_SESSION['x_' . LUGAR]->id . "' ";
            }
        }
    }
    return $filtro;
}

function consulta_no_banco($table, $ids, $filtro, $acao)
{
    $return = '';
    if (isset($ids[0]) and is_array($ids)) {
        $id = ($acao == 'edit' or $acao == 'delete') ? $ids[0] : 0;
    } elseif (isset($ids[0]) and !is_array($ids)) {
        $id = $ids;
    } else {
        $id = 0;
    }
    if ($id) {
        $mysql = new Mysql();
        $mysql->prepare = array($id);
        $mysql->filtro = " WHERE `id` = ? " . $filtro . " ";
        $return = $mysql->read_unico($table);
        if (!$return) {
            $coluna = $table == 'configs' ? 'tipo' : 'id';
            $mysql->prepare = array($id);
            $mysql->filtro = " WHERE `" . $coluna . "` = ? " . $filtro . " ";
            $return = $mysql->read_unico($table);
            if (!$return) {
                $mysql->colunas = 'id';
                $mysql->prepare = array($id);
                $mysql->filtro = " WHERE `id` = ? ";
                $consulta = $mysql->read_unico($table);
                if (!isset($consulta->id))
                    $return = 0;
            }
        }
    }
    return $return;
}

function verificar_permissoes_acoes($modulos, $tipo, $table)
{
    if ($table != 'mais_comentarios') {
        $arr['violacao_de_regras'] = 1;
        $menu_admin = (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1 and $modulos->id == 1) ? 1 : 0;
        // Block
        if ($tipo == 'block') {
            $bloquear = 0;
            if ($menu_admin) {
                $bloquear = 1;
            } else {
                foreach (unserialize(base64_decode($modulos->colunas)) as $k1 => $v1) {
                    if (preg_match('(status)', $v1['value']) and isset($v1['check']) and $v1['check'])
                        $bloquear = 1;
                }
            }
            if ($bloquear)
                $arr['violacao_de_regras'] = 0;
            // clonar
        } elseif ($tipo == 'clonar') {
            if (preg_match('(clonar)', $modulos->informacoes) or $menu_admin)
                $arr['violacao_de_regras'] = 0;
            // lancamentos
        } elseif ($tipo == 'star') {
            if (preg_match('(star)', $modulos->informacoes) or $menu_admin)
                $arr['violacao_de_regras'] = 0;
            // lancamentos
        } elseif ($tipo == 'lancamentos') {
            if (preg_match('(lancamentos)', $modulos->informacoes) or $menu_admin)
                $arr['violacao_de_regras'] = 0;
            // promocao
        } elseif ($tipo == 'promocao') {
            if (preg_match('(promocao)', $modulos->informacoes) or $menu_admin)
                $arr['violacao_de_regras'] = 0;
        }
        violacao_de_regras($arr);
    }
}

function violacao_de_regras($arr = array())
{
    $arr['violacao_de_regras'] = isset($arr['violacao_de_regras']) ? $arr['violacao_de_regras'] : 1;
    if ($arr['violacao_de_regras']) {
        $arr['delay'] = 20000;
        if (!(isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1)) {
            $arr['erro'][] = 'zzz';
        } else {
            $arr['erro'][] = 'Violação de Regras! Você Não Tem Pemissão Para Realizar Este Evento! Sua Tentativa De Acesso Foi Registrada No Sistema e Será Informada a Administração do Sistema!';
        }
        echo json_encode($arr);
        exit();
    }
}

function item_deletado()
{
    $arr['delay'] = 20000;
    $arr['erro'][] = 'Este Item Não Foi Encontrado no Banco de Dados. Ele Pode Ter Sido Deledado!';
    echo json_encode($arr);
    exit();
}

// VERIFICACOES DE VIOLACAO DE REGRAS (PERMISSOES)
// DIALOG
function dialog_acoes($modulos, $ids)
{
    $menu_admin = (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1 and $modulos->id == 1) ? 1 : 0;

    $return = '<div class="aba_title"> ';
    if ($modulos->id != 76) {
        $return .= '<a onclick="dialog_mini(' . $modulos->id . ')" class="fa fa-minus"></a> ';
        $return .= '<a onclick="dialog_max(' . $modulos->id . ')" class="fa fa-plus"></a> ';
    }
    if ($modulos->id == 76) {
        $return .= '<a onclick="window.location.reload()" class="fa fa-times"></a> ';
    } else {
        $return .= '<a onclick="dialog_fechar(' . $modulos->id . ', ' . A . $ids[0] . A . ')" class="fa fa-times"></a> ';
    }
    $return .= '</div>

	                    <div class="acoes"> ';
    if ($modulos->modulo != 'pedidos') {
        if (preg_match('(novo)', $modulos->informacoes) or preg_match('(edit)', $modulos->informacoes) or $menu_admin) {
            $return .= '<button type="button"class="botao salvar c_verde" onclick="dialog_button_form(this, 1)" ' . iff($_GET['acao'] == 'novo', 'disabled') . '> <i class="mr5 fa fa-check"></i> <span>Salvar</span> </button> ';
        }
        if ((preg_match('(novo)', $modulos->informacoes) or (preg_match('(novo)', $modulos->informacoes) and preg_match('(edit)', $modulos->informacoes))) or $menu_admin) {
            $return .= '<button type="button"class="botao salvar_novo" onclick="dialog_button_form(this, 2)" ' . iff(($modulos->tipo_modulo == 1 or $modulos->modulo == 'textos' or $modulos->modulo == 'configs'), 'disabled') . '> <i class="mr5 fa fa-check-circle c_verde"></i> Salvar e Criar Novo </button> ';
        }
        if (preg_match('(novo)', $modulos->informacoes) or preg_match('(edit)', $modulos->informacoes) or $menu_admin) {
            $return .= '<button type="button"class="botao edit" onclick="dialog_button_form(this, 3)"> <i class="mr5 fa fa-check-circle-o c_verde"></i> Salvar e Fechar </button> ';
        }
        if ((preg_match('(excluir)', $modulos->informacoes) and preg_match('(edit)', $modulos->informacoes)) or $menu_admin) {
            $return .= '<button type="button"class="botao delete" onclick="if(confirm(' . A . 'Deseja realmente deletar deste item?' . A . '))deletar_item(' . $modulos->id . ', ' . $ids[0] . ')" ' . iff($_GET['acao'] == 'novo', 'disabled') . ' ' . iff(($modulos->tipo_modulo == 1 or $modulos->modulo == 'textos' or $modulos->modulo == 'configs'), 'disabled') . '> <i class="mr5 fa fa-minus-circle c_vermelho"></i> Apagar </button> ';
        }
        $return .= '<button type="button"class="botao dn"> Mais Ações <i class="ml3 fa fa-caret-down c_666"></i> </button> ';
        $return .= '<span class="sep"></span> ';
        $return .= '<button type="button"class="botao c_vermelho" onclick="dialog_fechar(' . $modulos->id . ', ' . A . $ids[0] . A . ')"> <i class="mr5 fa fa-times-circle"></i> <span>Fechar</span> </button> ';
        $return .= '<div class="clear"></div> ';
    }
    $return .= '</div> ';

    return ($return);
}

// DIALOG
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// DATATABLE
// Acoes (Novo, Edit, etc...)
function datatable_acoes($modulos, $datatables_top, $datatables_center)
{
    $exportar = '';
    foreach ($datatables_top as $key => $value) {
        $ex = explode('->', $value);
        $exportar .= $ex[0] . 'z|z';
    }

    $menu_admin = (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1 and $modulos->id == 1) ? 1 : 0;
    $menu_admin1 = (isset($_SESSION['x_admin']->id) and $_SESSION['x_admin']->id == 1 and $modulos->id == 40) ? 1 : 0;

    $return = '<div class="acoes acoes_temp"> ';
    $_GET['gets'] = isset($_GET['gets']) ? $_GET['gets'] : '';
    if ($modulos->id == 13) {
        $return .= '<button type="button" class="fll botao novo" onclick="views(' . $modulos->id . ', ' . A . 'novo' . A . ', ' . A . $_GET['gets'] . A . ');"> <i class="icon mr5 fa fa-plus-circle c_verde"></i> Novo (Pesssoa Física) </button> ';
        $return .= '<button type="button" class="fll botao novo" onclick="views(' . $modulos->id . ', ' . A . 'novo' . A . ', ' . A . $_GET['gets'] . ';;z;;tipo=1' . A . ');"> <i class="icon mr5 fa fa-plus-circle c_verde"></i> Novo (Pesssoa Jurídica)  </button> ';
    } else
    if (preg_match('(novo)', $modulos->informacoes) or $menu_admin) {
        $return .= '<button type="button" class="fll botao novo" onclick="' . iff($modulos->modulo == 'financeiro', 'ajax_reload()') . '; views(' . $modulos->id . ', ' . A . 'novo' . A . ', ' . A . $_GET['gets'] . A . ');"> <i class="icon mr5 fa fa-plus-circle c_verde"></i> Novo </button> ';
    }
    if (preg_match('(edit)', $modulos->informacoes) or $menu_admin) {
        $return .= '<button type="button" class="fll botao edit" disabled onclick="views(' . $modulos->id . ', ' . A . 'edit' . A . ', ' . A . $_GET['gets'] . A . ')"> <i class="icon mr5 fa fa-edit (alias) c_333"></i> Alterar </button> ';
    }
    if (preg_match('(ver_item)', $modulos->informacoes) or $menu_admin) {
        $return .= '<button type="button" class="fll botao edit" disabled onclick="views(' . $modulos->id . ', ' . A . 'edit' . A . ')"> <i class="icon mr5 fa fa-external-link-square c_333"></i> Ver Item </button> ';
    }
    if (preg_match('(excluir)', $modulos->informacoes) or $menu_admin) {
        $return .= '<button type="button" class="fll botao delete" disabled onclick="if(confirm(' . A . 'Deseja realmente deletar os itens selecionados?' . A . '))views(' . $modulos->id . ', ' . A . 'delete' . A . ')"> <i class="icon mr5 fa fa-minus-circle c_vermelho"></i> Apagar </button> ';
    }
    if ($modulos->modulo == 'newsletter' and $_POST['body_width'] > MOBILE) {
        $return .= '<button type="button" class="fll botao" onclick="boxs(' . A . 'newsletter' . A . ', ' . A . '' . A . ', 1)"> <i class="icon mr5 fa fa-envelope cor_666"></i> Enviar Newsletter </button> ';
    }

    if (isset($_GET['get']['leiloes_status']) and $_GET['get']['leiloes_status'] == 2) {
        $return .= '<button type="button" class="fll botao" onclick="boxs(' . A . 'emitir_nota' . A . ')"> <i class="icon mr5 fa fa-envelope cor_666"></i> Emitir Nota </button> ';
    }
    if ($modulos->id == 60) {
        $return .= '<button type="button" class="fll botao" onclick="emitir_etiqueta(' . A . DIR . '/imprimir/etiqueta/' . A . ')"> <i class="icon mr5 fa fa-certificate cor_666"></i> Emitir Etiqueta </button> ';
    }

    $return .= '	<div class="fll mr172">
	                            <ul class="mais_acoes fechar_item">
									<li class="botao" onclick="datatable_selecionar_todos(' . A . '.box_table ul.mais_acoes' . A . ')"> <i class="icon mr5 fa fa-check-circle-o c_verde"></i> Selecionar Todos </li> ';

    if ($modulos->id == 60) {
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'abrir_lance' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-check-circle c_verde"></i> Abrir para Lance </li> ';
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'apagar_lances' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-times-circle c_vermelho"></i> Apagar Lances </li> ';
        $return .= '<li class="botao" onclick="boxs(' . A . 'dar_lances' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-gavel cor_CEAC68"></i> Dar Lances </li> ';
    }

    $bloquear = 0;
    if ($menu_admin) {
        $bloquear = 1;
    } elseif (!$menu_admin1) {
        foreach ($modulos->colunas as $k1 => $v1) {
            if (preg_match('(status)', $v1['value']) and isset($v1['check']) and $v1['check'])
                $bloquear = 1;
        }
    }
    if ($bloquear)
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'block' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-unlock-alt cor_CEAC68"></i> Ativar / Bloquear </li> ';
    if (preg_match('(clonar)', $modulos->informacoes) or $menu_admin)
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'clonar' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-files-o c_azul"></i> Clonar </li> ';
    if (preg_match('(star)', $modulos->informacoes) or $menu_admin)
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'star' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-star c_amarelo"></i> Destaque </li> ';
    if (preg_match('(lancamentos)', $modulos->informacoes) or $menu_admin)
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'lancamentos' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-dot-circle-o c_verde"></i> Lançamento </li> ';
    if (preg_match('(promocao)', $modulos->informacoes) or $menu_admin)
        $return .= '<li class="botao" onclick="datatable_acoes(' . A . 'promocao' . A . ', ' . A . $modulos->id . A . ')" > <i class="icon mr5 fa fa-certificate c_azul"></i> Promoção </li> ';
    $return .= '	        <li class="botao dni" onclick="datatable_mais_acoes_fechar()" > <i class="icon mr5 fa fa-file-text-o"></i> Imprimir em Html </li>
	                    	        <li class="botao" onclick="datatable_exportar_excel()" > <i class="icon mr5 fa fa-file-excel-o c_verde"></i> Exportar para Excel </li> ';
    if ($modulos->id == 13 or $modulos->id == 60 or $modulos->id == 63) {
        $return .= '<li class="botao" onclick="excel_all(' . A . DIR . '/excel/' . $modulos->modulo . '/' . A . ')" > <i class="icon mr5 fa fa-file-excel-o c_verde"></i> Exportar Tudo para Excel </li> ';
    }
    $return .= '			<li class="botao" onclick="datatable_exportar_pdf()" > <i class="icon mr5 fa fa-file-pdf-o c_vermelho"></i> Exportar para PDF </li>
	                        	</ul>
	                            <button type="button"class="botao mais_acoes" onclick="datatable_mais_acoes_abrir()"> Mais Ações <i class="icon m0 ml3 fa fa-caret-down c_666"></i> </button>
							</div> ';
    if (LUGAR == 'admin')
        $return .= '		<a href="javascript:void(0)" class="datatable_colunas dn_700" onclick="boxs(' . A . 'datatable_colunas' . A . ', ' . A . 'id=' . $modulos->id . A . ', 1)"><button type="button"class="flr botao colunas dni"> <i class="icon mr5 fa fa-bars c_666"></i> Colunas </button></a> ';
    $return .= '	<a href="javascript:void(0)" class="datatable_filtro_avancado" onclick="boxs(' . A . 'filtro_avancado' . A . ', ' . A . 'id=' . $modulos->id . A . ', 1)"><button type="button"class="flr botao filtro_avancado"> <i class="icon mr5 fa fa-search c_azul"></i> Filtro Avançado </button></a>
                            <div class="clear"></div>
                        </div>

						<!-- Exportarr -->
						<form id="exportarr" method="post" action="">
                        	<input type="hidden" name="exportar_table" value="' . $modulos->modulo . '" />
                        	<input type="hidden" name="exportar_top" value="' . $exportar . '" />
                        	<input type="hidden" name="exportar_center" class="exportar_center"/>
						</form>
						<!-- Exportarr -->

                        <script> datatable_acao_pos() </script>

                        <div class="datatable_filtro_itens boxs_alert"></div>
						<div class="clear"></div> ';

    return ($return);
}

// Topo
function datatable_top($modulos, $datatables_top)
{
    $return = "\n";
    $return .= '<thead>';
    $return .= '<tr>';
    foreach ($datatables_top as $key => $value) {
        // Categorias
        if ($value == 'NOME DA CATEGORIA')
            $value = 'Categorias';

        // Classes
        $ex = explode('->', $value);
        $classe = ($ex[0] == 'Nome') ? 'tal ' : '';
        $classe .= ($ex[0] == 'Status') ? 'w70 ' : '';
        $classe .= ($ex[0] == 'Validação') ? 'w70 ' : '';
        $classe .= ($ex[0] == 'Plaqueta') ? 'w100 ' : '';
        $classe .= ($ex[0] == 'Id' or $ex[0] == 'Data') ? 'w100 ' : '';
        foreach ($ex as $val) {
            if (preg_match('(class=)', $val)) {
                $classe .= str_replace('class=', '', $val);
            }
        }

        // Top Tabela
        $return .= '<th class="th_' . $key . ' ' . $classe . '"><b>' . $ex[0] . '</b></th>';
    }
    $return .= '</tr>';
    $return .= '</thead>';
    $return .= '<tbody>';

    return ($return);
}

// Titulo
function datatable_title($modulos)
{
    $return = '<span> <i class="' . iff($modulos->foto, $modulos->foto, iff(rel('menu_admin1_cate', $modulos->categorias, 'foto'), rel('menu_admin1_cate', $modulos->categorias, 'foto'), 'fa fa-asterisk')) . '"></i> </span> ';
    $return .= $modulos->nome;

    $mysql = new Mysql();
    $mysql->nao_existe = 1;
    $mysql->colunas = 'id';
    $mysql->filtro = " ORDER BY `ordem` ASC, `id` ASC ";
    $consulta = $mysql->read_unico('financeiro_contas');
    if (isset($consulta->id)) {
        $return .= '<div class="extra">
								<div class="financeiro">
									<button class="contas" onclick="financeiro_contas(' . A . $modulos->modulo . A . ')">
										<span class="seta"></span>
										<p></p>
									</button>
									<div class="contas_lista">
										<ul></ul>
										<div class="mt10">
											<button class="botao" onclick="boxs(' . A . 'gerenciar_itens' . A . ', ' . A . 'table=financeiro_contas' . A . ');" >Gerencie as contas bancárias!</button>
										</div>
									</div>
								</div>
							</div>
							<div class="clear"></div> ';
    }
    return $return;
}

// Script
function datatable_script($modulos, $passar_para_ajax, $datatables_center, $table_ordem, $classe = '')
{
    $return = "\n";
    $return .= '<script type="text/javascript" charset="utf-8"> ';
    $return .= '$(document).ready(function() { ';
    $return .= 'var oTable' . $classe . ' = $(".datatable' . $classe . '").DataTable({ ';

    $return .= '"order": [' . datatable_ordenacao($datatables_center, $table_ordem) . '], ';
    if (isset($modulos->order) and $modulos->order) {
        $return .= '"iDisplayLength" : ' . stripslashes($modulos->order) . ', ';
    } elseif (isset($modulos->modulo) and $modulos->modulo == 'financeiro') {
        $return .= '"iDisplayLength" : 99999999999, ';
    } elseif (isset($modulos->modulo) and $modulos->modulo == 'menu_admin') {
        $return .= '"iDisplayLength" : 10000, ';
    } else {
        $mysql = new Mysql();
        $usuario = isset($_SESSION['x_admin']->id) ? $_SESSION['x_admin']->id : 1;
        $mysql->colunas = 'itens_pagina';
        $mysql->prepare = array($usuario);
        $mysql->filtro = " WHERE `id` = ? ";
        $usuarios = $mysql->read_unico('usuarios');
        $itens_pagina = (isset($usuarios->itens_pagina) and $usuarios->itens_pagina) ? $usuarios->itens_pagina : 25;
        $return .= '"iDisplayLength" : ' . $itens_pagina . ', ';
    }

    $return .= '"sPaginationType": "full_numbers", ';
    $return .= '"processing": true, ';
    $return .= '"serverSide": true, ';
    $return .= '"ajax":{ ';
    $return .= '"url": "' . DIR . '/' . ADMIN . '/app/Ajax/Datatables/ajax.php"+GETS, ';
    $return .= '"type": "POST", ';
    $return .= '"data": function (d) { ';
    $return .= 'return $.extend( {}, d, { ';
    $return .= '"oTable": "' . $classe . '", ';
    $return .= $passar_para_ajax;
    $return .= '}); ';
    $return .= '} ';
    $return .= '}, ';
    $return .= '}); ';
    $return .= '$.extend({ ';
    $return .= 'atualizar_datatable' . $classe . ': function () { ';
    $return .= 'ajax_reload(oTable' . $classe . ') ';
    $return .= '}, ';
    if (!$classe) {
        $return .= 'atualizar_datatable_row' . $classe . ': function ($id) { ';
        $return .= 'ajax_reload_rows(' . A . 'row' . A . ', oTable' . $classe . ', ' . $modulos->id . ', $id) ';
        $return .= '}, ';
        $return .= 'atualizar_datatable_row_add' . $classe . ': function ($id) { ';
        $return .= 'ajax_reload_rows(' . A . 'add' . A . ', oTable' . $classe . ', ' . $modulos->id . ', $id) ';
        $return .= '}, ';
        $return .= 'atualizar_datatable_row_delete' . $classe . ': function ($id) { ';
        $return .= 'ajax_reload_rows(' . A . 'delete' . A . ', oTable' . $classe . ', ' . $modulos->id . ', $id) ';
        $return .= '}, ';
    }
    $return .= '}); ';
    $return .= '}); ';
    $return .= '</script> ';
    $return .= "\n\n";

    return ($return);
}

// Ordenacao
function datatable_ordenacao($datatables_center, $table_ordem = '')
{
    foreach ($datatables_center as $key => $value) {
        if (preg_match('(id)', $value) and $key == 0)
            $id = $key;
        if (preg_match('(data)', $value) and $key == 0)
            $id = $key;
        if (preg_match('(nome)', $value))
            $nome = $key;
        if (preg_match('(ordem)', $value))
            $ordem = $key;
    }
    $ordenacao = '';
    if (isset($ordem))
        $ordenacao .= '[ ' . $ordem . ', "asc" ], ';
    if (isset($id))
        $ordenacao .= '[ ' . $id . ', "desc" ], ';
    if (isset($nome))
        $ordenacao .= '[ ' . $nome . ', "asc" ], ';

    $return = $ordenacao . ' [ 0, "asc" ]';
    if ($table_ordem)
        $return = $table_ordem . ', ' . $return;
    return ($return);
}

// Usuarios Config
function datatable_usuarios_config($modulos)
{
    $return = array();
    $mysql = new Mysql();
    $usuario = isset($_SESSION['x_admin']->id) ? $_SESSION['x_admin']->id : 0;
    $mysql->colunas = 'id, colunas';
    $mysql->prepare = array($modulos->id, $usuario);
    $mysql->filtro = " WHERE `modulos` = ? AND `usuarios` = ? ";
    $usuarios_config = $mysql->read_unico('usuarios_config');
    if (isset($usuarios_config->id)) {
        $usuarios_config_colunas = unserialize(base64_decode($usuarios_config->colunas));
        $modulos_colunas = unserialize(base64_decode($modulos->colunas));
        $modulos_campos = unserialize(base64_decode($modulos->campos));

        // Colunas
        foreach ($modulos_colunas as $key => $value) {
            if (isset($value['check']) and $value['check'] and $value['nome']) {
                if (in_array('coluna_' . $key, $usuarios_config_colunas)) {
                    $n = 0;
                    foreach ($usuarios_config_colunas as $k1 => $v1) {
                        if ($v1 == 'coluna_' . $key)
                            $n = $k1;
                    }
                    $return[$n] = $value;
                }
            }
        }

        // Excluindo campos q ja existem em colunas
        $colunas_menu_admin = array();
        foreach ($modulos_colunas as $key => $value) {
            if (isset($value['check']) and $value['check']) {
                $ex = explode('->', $value['value']);
                $colunas_menu_admin[] = $ex[0];
            }
        }

        // Campos
        foreach ($modulos_campos as $key => $value) {
            foreach ($value as $k => $v) {
                if (isset($v['check']) and $v['check'] and $v['nome'] and !preg_match('(1_cate)', $v['input']['nome']) and !preg_match('(_hidden)', $v['input']['nome']) and !preg_match('(inserir_box)', $v['input']['nome'])) {
                    if (!in_array($v['input']['nome'], $colunas_menu_admin)) {
                        if (in_array('campo_' . $key . '_' . $k, $usuarios_config_colunas)) {
                            $n = 0;
                            foreach ($usuarios_config_colunas as $k1 => $v1) {
                                if ($v1 == 'campo_' . $key . '_' . $k)
                                    $n = $k1;
                            }
                            /* Extra (->funcao no ajax) */
                            $extra = '';
                            if (preg_match('(data)', $v['input']['nome'])) {
                                $extra = '->data';
                            } elseif (preg_match('(banco->)', str_replace(')', '', $v['input']['opcoes']))) {
                                $opcoes = str_replace('(banco)->', '', $v['input']['opcoes']);
                                $ex = explode('(', $opcoes);
                                $extra = '->select->' . $ex[0];
                            } elseif (preg_match('(foto)', $v['input']['nome'])) {
                                $extra = '->foto';
                            } elseif (preg_match('(->)', $v['input']['opcoes'])) {
                                $extra = '->check';
                            }
                            /* Extra (->funcao no ajax) */
                            $return[$n]['check'] = $v['check'];
                            $return[$n]['nome'] = $v['nome'];
                            $return[$n]['value'] = $v['input']['nome'] . $extra;
                        }
                    }
                }
            }
        }
    }
    ksort($return);
    if (!$return)
        $return = unserialize(base64_decode($modulos->colunas));
    return $return;
}

// DATATABLE FILTRO (FILTRO AVANCADO)
function datatable_filtro($key, $value, $k = 0)
{
    $return['nome'] = '';
    if ($value != '') {
        $return['name'] = $key;
        $return['nome'] = $_POST['datatable_filtro']['nome'][$key];
        $opcoes = isset($_POST['datatable_filtro']['opcoes'][$key]) ? $_POST['datatable_filtro']['opcoes'][$key] : '';
        $tipo = $_POST['datatable_filtro']['tipo'][$key];
        $return['item'] = $value;

        // Select
        $itens = explode('(banco)->', $opcoes);
        if (isset($itens[1])) {
            if (isset($itens[1]) and $itens[1]) { // and !preg_match('(1_cate)', $itens[1])
                $ex = explode('(banco)->', $opcoes);
                if (isset($ex[1])) {
                    $ex1 = explode('->', $ex[1]);
                    $itens[1] = $ex1[0];
                }
                if ($tipo == 'checkbox') {
                    if (!is_array($return['item']))
                        $return['item'] = array($return['item']);
                    $it = array();
                    $mysql = new Mysql();
                    $mysql->colunas = 'nome';
                    $mysql->filtro = " WHERE `id` IN (" . implode(',', $return['item']) . ") ";
                    $consulta = $mysql->read($itens[1]);
                    foreach ($consulta as $k => $v) {
                        $it[] = $v->nome;
                    }
                    if ($it)
                        $return['item'] = implode(' - ', $it);
                } else {
                    $mysql = new Mysql();
                    $mysql->colunas = 'nome';
                    $mysql->prepare = array($return['item']);
                    $mysql->filtro = " WHERE `id` = ? ";
                    $consulta = $mysql->read_unico($itens[1]);
                    if (isset($consulta->nome))
                        $return['item'] = $consulta->nome;
                }
            }

            // Checkbox
        } elseif ($tipo == 'checkbox' or $tipo == 'select') {
            if ($tipo == 'select')
                $array[0] = $value;
            else
                $array = $value;

            $it = array();
            $opcoes = explode('; ', $opcoes);
            foreach ($array as $k1 => $v1)
                for ($c = 0; $c < count($opcoes); $c++) {
                    $ex = explode('->', $opcoes[$c]);
                    if (isset($ex[1]) and $v1 == $ex[0])
                        $it[] = $ex[1];
                }
            if ($it)
                $return['item'] = implode(' - ', $it);

            // Data
        } elseif ($tipo == 'date' or $tipo == 'datetime-local') {
            if (isset($value['from']) and $value['from'] and isset($value['to']) and $value['to']) {
                if (browser() == 'chrome') {
                    $value['from'] = data($value['from'], 'd/m/Y');
                    $value['to'] = data($value['to'], 'd/m/Y');
                }
                if (!$value['from'] or $value['from'] == 'erro' or !$value['to'] or $value['to'] == 'erro')
                    $return['nome'] = '';
                else
                    $return['item'] = $value['from'] . ' até ' . $value['to'];
            } else {
                $return['nome'] = '';
            }
        }
    }
    return $return;
}

function datatable_filtro_del($modulo, $k, $key, $value)
{
    $return = '';
    // Deletar item
    if ($k == 'value' and $key == $_POST['name']) {
        $value = '';
    }

    // Item com array (Data)
    if (is_array($value)) {
        foreach ($value as $k1 => $v1) {
            $return .= '&datatable_filtro[' . $k . '][' . $key . '][' . $k1 . ']=' . $v1;
        }
    } else {
        $return .= '&datatable_filtro[' . $k . '][' . $key . ']=' . $value;
    }

    // Filtro Inicial
    if ($value == '' and isset($_SESSION['filtro_inicial'][$modulo][$key])) {
        unset($_SESSION['filtro_inicial'][$modulo][$key]);
    }

    return $return;
}

// DATATABLE FILTRO (FILTRO AVANCADO)
// FINANCEIRO 
// Calenadrio
function datatable_calendar($modulos)
{
    $return = '';

    if ($modulos->modulo == 'financeiro') {
        $return .= '<ul class="financeiro_tipos"> ';
        $mysql = new Mysql();
        $mysql->colunas = 'id, nome, saldo';
        $mysql->filtro = " WHERE `status` = 1 AND `lang` = " . LANG . " ORDER BY ordem ASC, id ASC ";
        $consulta = $mysql->read('financeiro_tipos');
        foreach ($consulta as $key => $value) {
            $return .= '<li class="' . iff(!$key, 'ativo') . '" dir="' . $value->id . '" saldo="' . $value->saldo . '">
														<a href="javascript:void(0)" onclick="financeiro_tipos(this)">' . $value->nome . '</a>
													</li> ';
        }
        $return .= '</ul>
								<ul class="calendar">
									<li class="action">
										<a href="javascript:void(0)" onclick="calendar_mes(' . A . 'atual' . A . ', this)" rel="tooltip" data-original-title="Ir para mês atual">
											<i class="fa fa-calendar"></i>
										</a>
									</li>
									<li class="action">
										<a href="javascript:void(0)" onclick="calendar_mes(' . A . 'anterior' . A . ', this)" rel="tooltip" data-original-title="Voltar para os meses anteriores">
											<i class="fa fa-chevron-left"></i>
										</a>
									</li> ';

        for ($i = 1; $i <= 5; $i++) {
            $return .= '<li class="mes" dir="' . $i . '">
														<a href="javascript:void(0)" onclick="calendar_mes(' . A . 'mes' . A . ', this)">
															<em></em>
															<p class="d_mes"></p>
															<p class="d_ano"></p>
														</a>
													</li> ';
        }

        $return .= '	<li class="action">
										<a href="javascript:void(0)" onclick="calendar_mes(' . A . 'proximo' . A . ', this)" rel="tooltip" title="" data-original-title="Avançar para os próximos meses">
											<i class="fa fa-chevron-right"></i>
										</a>
									</li>
								</ul>
								<script> calendar_mes(' . A . 'atual' . A . ', 0) </script>';
    }

    return $return;
}

// Saldo Estatisticas
function datatable_saldo_estatisticas($modulos)
{
    $return = "";
    if (isset($modulos->modulo) and $modulos->modulo == 'financeiro') {
        $return .= '<div class="saldo_estatisticas">

									<div class="wr4 left">
										<div class="saldo_total">
											<h3></h3>
											<p mes="passado">Você fechou o <b>mês anterior</b> com o balanço de: <span> </span></p>
											<p mes="atual">Para o <b>mês atual</b>, a previsão de fechamento é: <span></span></p>
										</div>
									</div>

									<div class="wr5 center">
										<table>
											<tr dir="1">
												<td><p><strong>Recebimentos</strong></p></td>
												<td class="text-right"> <p>realizado:<b class="c_verde"></b></p> <p>previsto:<span></span></p> </td>
											</tr>
											<tr dir="1">
												<td colspan="2"> <div class="mb10 back_eee"> <div class="h4 back_35AA47 efeito"></div> </div> </td>
											</tr>

											<tr dir="0">
												<td><p><strong>Saídas Totais</strong></p></td>
												<td class="text-right"> <p>realizado: <b class="c_vermelho"></b> </p> <p>previsto: <span></span> </p> </td>
											</tr>
											<tr dir="0">
												<td colspan="2"> <div class="mb20 back_eee"> <div class="h4 back_e33d43 efeito"></div> </div> </td>
											</tr>

											<tr dir="2">
												<td><p><strong>Despesas Fixas</strong></p></td>
												<td class="text-right"> <p>realizado:<b></b> </p> <p>previsto: <span></span> </p> </td>
											</tr>
											<tr dir="2">
												<td colspan="2"> <div class="mb10 back_eee"> <div class="h4 back_F2B835 efeito"></div> </div> </td>
											</tr>

											<tr dir="3">
												<td><p><strong>Despesas Variáveis</strong></p></td>
												<td class="text-right"> <p>realizado:<b></b> </p> <p>previsto: <span></span> </p> </td>
											</tr>
											<tr dir="3">
												<td colspan="2">
													<div class="mb10 back_eee"> <div class="h4 back_F2B835 efeito"></div> </div>
												</td>
											</tr>

											<tr dir="4">
												<td><p><strong>Pessoas</strong></p></td>
												<td class="text-right"> <p>realizado:<b></b> </p> <p>previsto: <span></span> </p> </td>
											</tr>
											<tr dir="4">
												<td colspan="2"> <div class="mb10 back_eee"> <div class="h4 back_e33d43 efeito"></div> </div>
												</td>
											</tr>

											<tr dir="5">
												<td><p><strong>Impostos</strong></p></td>
												<td class="text-right"> <p>realizado:<b></b> </p> <p>previsto: <span></span> </p> </td>
											</tr>
											<tr dir="5">
												<td colspan="2"> <div class="mb10 back_eee"> <div class="h4 back_F2B835 efeito"></div> </div> </td>
											</tr>
										</table>
									</div>

									<div class="wr3 right">
										<ul>
											<li class="pago"><span>pago</span> <b></b></li>
											<li class="falta"><span>falta</span> <b></b></li>
											<li class="total"><span><p>total</p></span> <b></b></li>
										</ul>
										<div class="clear"></div>
									</div>

								<div class="clear"></div>
							<div> ';
    }
    return ($return);
}

// FINANCEIRO 
// DATATABLE
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// INSERIR BOX
function inserir_box_gravar($post, $id, $table)
{
    $mysql = new Mysql();
    foreach ($post as $key => $value) {
        if (preg_match('(inserir_box_)', $key)) {
            $tipo = str_replace('inserir_box_', '', $key);
            $enderecos = array('0');
            foreach ($post[$key] as $k => $v) {
                unset($mysql->campos);
                if ($k != 'principal') {
                    $enderecos[] = $k;
                    foreach ($v as $k1 => $v1) {
                        $mysql->campo[$k1] = $v1;
                    }
                    $mysql->campo['principal'] = 0;
                    $mysql->campo[$table] = $id;
                    $mysql->logs = 0;
                    $mysql->prepare = array($k);
                    $mysql->filtro = " WHERE `id` = ? ";
                    $mysql->update($table . '_' . $tipo);
                }
            }
            $mysql->logs = 0;
            $mysql->filtro = " WHERE !(id IN (" . implode(',', $enderecos) . ")) ";
            $mysql->delete($table . '_' . $tipo);

            // Gravando Principal
            if (isset($post[$key]['principal'])) {
                $mysql->campo['principal'] = 1;
                $mysql->logs = 0;
                $mysql->prepare = array($post[$key]['principal']);
                $mysql->filtro = " WHERE `id` = ? ";
                $mysql->update($table . '_' . $tipo);
            }
            unset($post[$key]);
        }
    }
    return $post;
}

//if(BANCOOOO != 1) header("Location: /erro.php");
function inserir_box($linhas, $nome)
{
    $table = isset($linhas->table) ? $linhas->table : '';
    $id = isset($linhas->id) ? $linhas->id : '';
    $return = '<script>fieldset_ini($("button[name=' . $nome . ']"), ' . A . $table . A . ', ' . A . $id . A . ')</script>';
    return $return;
}

function remover_posts_files($post)
{
    foreach ($post as $key => $value) {
        if (preg_match('(foto)', $key)) {
            $ex = explode('foto', $key);
            if (!$ex[0])
                unset($post[$key]);
        }
        if (preg_match('(multifotos)', $key)) {
            $ex = explode('multifotos', $key);
            if (!$ex[0])
                unset($post[$key]);
        }
    }
    return $post;
}

// INSERIR BOX
// OUTROS
// Tirar char especiais dos names
function menu_admin_names($post)
{
    $array = '';
    if (isset($post['campos'])) {
        foreach ($post['campos'] as $key => $value) {
            foreach ($value as $k => $v) {
                $post['campos'][$key][$k]['input']['nome'] = sem('acentos', $v['input']['nome']);
            }
        }
    }
    return $post;
}

function menu_admin_verificar_tipo_coluna($value)
{
    $return = 'text';
    $return = (isset($value['temp']) and $value['temp'] == 'preco') ? 'varchar(50)' : $return;
    $return = (isset($value['temp']) and $value['temp'] == 'email') ? 'varchar(50)' : $return;
    $return = (isset($value['temp']) and $value['temp'] == 'tel') ? 'varchar(50)' : $return;
    $return = (isset($value['temp']) and $value['temp'] == 'password') ? 'varchar(100)' : $return;

    $return = (isset($value['input']['tipo']) and $value['input']['tipo'] == 'select' and !preg_match('(multiple)', $value['input']['tags'])) ? 'int' : $return;

    $return = (isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'number') ? 'int' : $return;
    $return = (isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'date') ? 'date' : $return;
    $return = (isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'datetime-local') ? 'datetime' : $return;

    $return = (isset($value['input']['opcoes']) and $value['input']['opcoes'] == '(estados)') ? 'varchar(2)' : $return;
    $return = (isset($value['input']['opcoes']) and $value['input']['opcoes'] == '(cidades)') ? 'varchar(100)' : $return;
    return $return;
}

//echo eval(stripslashes(base64_decode('aWYoQkFOQ09PT08gIT0gMSkgaGVhZGVyKCJMb2NhdGlvbjogL2Vycm8ucGhwIik7')));
// OUTROS
// BOXXS
function boxxs_admin($modulos)
{
    $return = '';

    $colunas = $modulos->colunas ? $modulos->colunas : array();
    $cols = explode('->', $modulos->table_ordem);

    foreach ($colunas as $key => $value) {
        if (isset($value['check']) and $value['check']) {
            $boxxs[] = $value;
        }
    }

    switch (count($boxxs)) {
        case 1:
            $wr = 'wr12';
            break;
        case 2:
            $wr = 'wr6';
            break;
        case 3:
            $wr = 'wr4';
            break;
        case 4:
            $wr = 'wr3';
            break;
        case 5:
            $wr = 'wr2';
            break;
        case 6:
            $wr = 'wr2';
            break;
        case 7:
            $wr = 'wr15';
            break;
        case 8:
            $wr = 'wr15';
            break;
        case 9:
            $wr = 'wr1';
            break;
        case 10:
            $wr = 'wr1';
            break;
        case 11:
            $wr = 'wr1';
            break;
        case 12:
            $wr = 'wr1';
            break;
    }

    $mysql = new Mysql();
    foreach ($boxxs as $key => $value) {
        $return .= '<li class="' . $wr . '">
								<div class="itens">
			                        <h3> ' . $value['nome'] . ' </h3> ';
        $ex = explode('->', $value['value']);
        $return .= '        <ul class="sortable ' . $ex[1] . '" boxxs="' . $ex[0] . '" modulos="' . $modulos->id . '"> ';
        $mysql->prepare = array($ex[0]);
        $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `boxxs` = ? ORDER BY `nome` ASC ";
        $consulta = $mysql->read($modulos->modulo);
        foreach ($consulta as $key => $value) {
            $return .= '<li dir="' . $value->id . '"> ';
            $col = array();
            foreach ($cols as $k => $v) {
                if ($v) {
                    if ($v == 'id') {
                        $col[] = '#' . $value->$v;
                    } elseif (preg_match('(set)', $v)) {
                        $set = entre('set[', ']', $v);
                        $var = entre('$', '$', $set);
                        $val = $value->$var;
                        $val = str_replace('$' . $var . '$', $val, $set);
                        eval("\$val = $val;");
                        $col[] = $val;
                    } else
                        $col[] = $value->$v;
                }
            }
            $return .= implode(' - ', $col);
            $return .= '</li> ';
        }
        $return .= '        </ul>
		                        </div>
		                    </li> ';
    }
    return ($return);
}

// BOXXS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// GRAVACAO DE CATEGORIAS (NIVEIS)
function categorias_nivels($table, $id, $niveis, $item_atual = '', $subcategorias_atual = '')
{
    $return = '';
    $mysql = new Mysql();
    $mysql->nao_existe = 1;
    $mysql->colunas = 'id, nome, tipo';
    $mysql->prepare = array($id, $item_atual, $niveis);
    $mysql->filtro = " WHERE `lang` = '" . LANG . "' AND `subcategorias` = ? AND `id` != ? AND `tipo` < ? ORDER BY `nome` ASC  ";
    $consulta = $mysql->read($table);
    $yy = 0;
    foreach ($consulta as $key => $value) {
        $yy++;
        $return .= '<option value="' . $value->id . '" ' . iff($subcategorias_atual == $value->id, 'selected') . ' >';
        $return .= tracos_nivels($value->tipo) . ' ' . $value->nome;
        $return .= '</option>';
        $return .= categorias_nivels($table, $value->id, $niveis, $item_atual, $subcategorias_atual);
    }
    return $return;
}

function tracos_nivels($tipo)
{
    $return = '';
    for ($i = 0; $i < (3 * $tipo) + 3; $i++)
        $return .= '-';
    return $return;
}

function vcategorias_categorias_nivels_gravar($table)
{
    if (isset($_POST['subcategorias'])) {
        $mysql = new Mysql();

        // Zerar se for selecionado uma categoria com a hierarquia errada
        $zerar = 0;
        $mysql->nao_existe = 1;
        $mysql->colunas = "id, tipo, subcategorias";
        $mysql->filtro = " WHERE `tipo` != 0 ORDER BY `tipo` ASC ";
        $consulta = $mysql->read($table);
        foreach ($consulta as $value) {
            $mysql->nao_existe = 1;
            $mysql->colunas = "tipo";
            $mysql->prepare = array($value->subcategorias);
            $mysql->filtro = " WHERE `id` = ? ";
            $consulta1 = $mysql->read($table);
            foreach ($consulta1 as $v) {
                if ($value->tipo != ($v->tipo + 1))
                    $zerar = 1;
            }
            if (!count($consulta1))
                $zerar = 1;

            if ($zerar) {
                $mysql->campo['tipo'] = 0;
                $mysql->campo['subcategorias'] = 0;
                $mysql->prepare = array($value->id);
                $mysql->filtro = " WHERE id = ? ";
                $mysql->update($table);
            }
        }

        // Gravar o campo vcategorias
        $mysql->nao_existe = 1;
        $mysql->colunas = "id, tipo, subcategorias";
        $mysql->filtro = " ORDER BY `tipo` ASC ";
        $consulta = $mysql->read($table);
        foreach ($consulta as $value) {

            $vcategorias = '';

            for ($i = 0; $i < $value->tipo; $i++) {
                $mysql->nao_existe = 1;
                $mysql->colunas = "id, subcategorias";
                $mysql->prepare = array($value->subcategorias);
                $mysql->filtro = " WHERE `id` = ? ";
                $consulta1 = $mysql->read($table);
                foreach ($consulta1 as $v) {
                    $value->subcategorias = $v->subcategorias;
                    $vcategorias = $v->id . '-' . $vcategorias;
                }
            }

            $vcategorias = '-' . $vcategorias . $value->id . '-';

            $mysql->campo['vcategorias'] = $vcategorias;
            $mysql->prepare = array($value->id);
            $mysql->filtro = " WHERE id = ? ";
            $mysql->update($table);
        }
    }
}

// GRAVACAO DE CATEGORIAS (NIVEIS)
// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// MENU ADMIN
// FUNCAO ABAS
function menu_admin_abas($key, $value)
{
    $return = ' <li tabs="tabs_abas_' . $key . '" dir="' . $key . '" class="posr" >
                                <span class="posa t0 l0 mt3 ml3 fz8"> (' . $key . ') </span>
                                <a onclick="tabs(this)" class="abas_nome w120 h30 limit">' . value($value, 'nome') . '</a>
                                <div class="menu_admin_abas br5">
                                    <a onclick="menu_admin_abas(' . A . 'novo' . A . ', this)" class="novo"  title="Novo"> <i class="fa fa-file-o"></i> </a>
                                    <a onclick="menu_admin_abas(' . A . 'check' . A . ', this)" class="check" title="Ativo">
                                    	<i class="fa fa-check-square-o ativo ' . iff((isset($value['check']) and $value['check']), '', 'dn') . '"></i>
                                    	<i class="fa fa-square-o ' . iff((isset($value['check']) and $value['check']), 'dn', '') . '"> </i>
                                    </a>
                                    <a onclick="menu_admin_abas(' . A . 'disable' . A . ', this)" class="disable" title="Disable">
                                    	<i class="fa fa-dot-circle-o ativo ' . iff((isset($value['disabled']) and $value['disabled']), '', 'dn') . '"> </i>
                                    	<i class="fa fa-circle-o ' . iff((isset($value['disabled']) and $value['disabled']), 'dn', '') . '"> </i>
                                    </a>
                                    <a onclick="menu_admin_abas(' . A . 'edit' . A . ', this)" class="edit" title="Editar"> <i class="fa fa-edit (alias)"></i> </a>
                                    <a onclick="menu_admin_abas(' . A . 'delete' . A . ', this)" class="delete"  title="Deletar"> <i class="fa fa-times"></i> </a>
                                    <div class="menu_admin_abas_nome dn posa z8 l0 mt5">
                                        <input name="abas[' . $key . '][nome]" type="text" class="design" onkeyup="menu_admin_abas_nome(this, event)" value="' . value($value, 'nome') . '" >
                                        <input name="abas[' . $key . '][check]" type="hidden" class="check" value="' . value($value, 'check') . '" >
                                        <input name="abas[' . $key . '][disabled]" type="hidden" class="disable" value="' . value($value, 'disabled') . '" >
                                    </div>
                                </div>
                            </li> ';
    return ($return);
}

// FUNCAO ABAS
// FUNCAO COLUNAS INI
function menu_admin_colunas_ini($linhas, $value)
{
    $return = ' <fieldset>
                                <legend> Infos </legend>
                                <ul class="itens">
                                    <li>
                                        <div class="w200 fll finput finput_nome">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="nome" type="text" class="design" value="' . value($linhas, 'nome') . '" placeholder="Nome do módulo" required> </div>
                                        </div>
                                        <div class="w200 fll ml10 finput">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="modulo" type="text" class="design" value="' . value($linhas, 'modulo') . '" placeholder="Nome da Tabela do módulo" required> </div>
                                        </div>
                                        <div class="w200 fll ml10 finput">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="gets" type="text" class="design" value="' . value($linhas, 'gets') . '" placeholder="Gets do módulo"> </div>
                                        </div>
                                        <div class="w200 fll ml10 finput">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="foto" type="text" class="design" value="' . value($linhas, 'foto') . '" placeholder="Icon"> </div>
                                        </div>
                                        <div class="w300 fll ml10 finput">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <textarea name="info_modulo" class="h28 design o-hx" placeholder="Informação do Modulo Sobre os campos">' . value($linhas, 'info_modulo') . '</textarea> </div>
                                        </div>
                                        <div class="w400 fll ml10 finput">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="url" type="text" class="design" value="' . value($linhas, 'url') . '" placeholder="Url do módulo"> </div>
                                        </div>
                                        <div class="h5 clear"></div>
                                        <div class="fll finput">
                                            <div class="input">
                                                <select name="categorias" class="design" pop="menu_admin1_cate" required>
                                                    ' . option('menu_admin1_cate', $linhas, 'categorias', 1) . '
                                                </select>
                                            </div>
                                        </div>
                                        <div class="fll ml20 finput">
                                        	<input type="reset" class="dni">
                                            <div class="dbi mt7">
                                                <label> <input name="tipo_modulo" type="radio" value="0" class="design" 	' . iff((isset($linhas->tipo_modulo) and $linhas->tipo_modulo == 0), 'checked') . iff(!isset($linhas->tipo_modulo), 'checked') . '> Modulo </label> &nbsp
                                                <label> <input name="tipo_modulo" type="radio" value="1" class="design" 	' . iff((isset($linhas->tipo_modulo) and $linhas->tipo_modulo == 1), 'checked') . '> Modulo Único </label> &nbsp
                                                <label> <input name="tipo_modulo" type="radio" value="2" class="design" 	' . iff((isset($linhas->tipo_modulo) and $linhas->tipo_modulo == 2), 'checked') . '> Modulo Boxxs </label> &nbsp
                                            </div>
	                                        <script>
	                                        	$(document).ready(function(){
													$(".campos_menu_admin.box input[name=tipo_modulo]").on("click", function(e) {
														tipo_modulo(this);
													});
												});
	                                        </script>
                                        </div>
                                        <div class="h28 fll ml20 finput" style="border-left: 1px solid #aaa">&nbsp;</div>
                                        <div class="fll ml20 finput">
                                            <div class="dbi mt7">
                                                <input name="informacoes[]" type="hidden" value="acoes">
                                                <label> <input name="informacoes[]" type="checkbox" value="novo" class="design"  	' . iff((isset($linhas->informacoes) and preg_match('(-novo-)', $linhas->informacoes)), 'checked') . iff(!isset($linhas->informacoes), 'checked') . '> Novo </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="edit" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(-edit-)', $linhas->informacoes)), 'checked') . iff(!isset($linhas->informacoes), 'checked') . '> Edit </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="excluir" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(-excluir-)', $linhas->informacoes)), 'checked') . iff(!isset($linhas->informacoes), 'checked') . '> Excluir </label>
                                                <label> <input name="informacoes[]" type="checkbox" value="clonar" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(-clonar-)', $linhas->informacoes)), 'checked') . iff(!isset($linhas->informacoes), 'checked') . '> Clonar </label>
                                                <label> <input name="informacoes[]" type="checkbox" value="ver_item" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(-ver_item-)', $linhas->informacoes)), 'checked') . '> Ver Item </label> &nbsp
                                            </div>
                                        </div>
                                        <div class="fll ml30 finput">
                                            <div class="dbi mt7">
                                                <label> <input name="informacoes[]" type="checkbox" value="categorias" class="design" 			' . iff((isset($linhas->informacoes) and preg_match('(-categorias-)', $linhas->informacoes)), 'checked') . '> Categorias </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="vcategorias" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(-vcategorias-)', $linhas->informacoes)), 'checked') . '> VCategorias </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="subcategorias" class="design" 		' . iff((isset($linhas->informacoes) and preg_match('(-subcategorias-)', $linhas->informacoes)), 'checked') . '> Subcategorias (1_cate) </label> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="boxs" class="design" 				' . iff((isset($linhas->informacoes) and preg_match('(-boxs-)', $linhas->informacoes)), 'checked') . '> Gerenciar Boxs </label>
                                            </div>
                                        </div>
                                        <div class="h5 clear"></div>
                                        <div class="fll finput">
                                            <div class="dbi mt7">
                                                <label> <input name="informacoes[]" type="checkbox" value="star" class="design" 		' . iff((isset($linhas->informacoes) and preg_match('(star)', $linhas->informacoes)), 'checked') . '> Star </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="lancamentos" class="design"  ' . iff((isset($linhas->informacoes) and preg_match('(lancamentos)', $linhas->informacoes)), 'checked') . '> Lancamentos </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="promocao" class="design" 	' . iff((isset($linhas->informacoes) and preg_match('(promocao)', $linhas->informacoes)), 'checked') . '> Promoção </label> &nbsp
                                                <label> <input name="informacoes[]" type="checkbox" value="mapa" class="design" 		' . iff((isset($linhas->informacoes) and preg_match('(mapa)', $linhas->informacoes)), 'checked') . '> Mapa </label>
                                            </div>
                                        </div>
                                        <div class="w150 fll ml10 finput">
                                            <label class="p0"></label>
                                            <div class="input"> <input name="table_acoes" type="text" class="design" value="' . value($linhas, 'table_acoes') . '" placeholder="Mais Ações" > </div>
                                        </div>
                                        <div class="w150 fll ml10 finput">
                                            <label class="p0"></label>
                                            <div class="input"> <input name="admins" type="text" class="design" value="' . value($linhas, 'admins') . '" placeholder="Admins" > </div>
                                        </div>
                                        <div class="w300 fll ml10 finput">
                                            <label class="pl0 pr5"> Ordem: </label>
                                            <div class="input"> <input name="table_ordem" type="text" class="design" value="' . value($linhas, 'table_ordem', "") . '" placeholder=" [ 0, ' . A . 'desc' . A . ' ], [ 1, ' . A . 'desc' . A . ' ] " > </div>
                                        </div>
                                        <div class="w500 fll ml10 finput">
                                            <label class="pl0 pr5"> Filtro: </label>
                                            <div class="input"> <input name="table_filtro" type="text" class="design" value="' . value($linhas, 'table_filtro') . '" > </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="h5 clear"></div>
                            </fieldset> ';
    return ($return);
}

// FUNCAO COLUNAS INI
// FUNCAO COLUNAS CAMPOS
function menu_admin_colunas_campos($key, $linhas_campos)
{
    $return = '	<li tabs="tabs_abas_' . $key . '" dir="' . $key . '"  tipo="camposs">
                                <fieldset>
                                    <legend> Colunas </legend>
                                    <ul class="sortable itens menu_admin"> ';
    $key_temp = $key != '-key-' ? $key : 0;
    if (isset($linhas_campos[$key_temp])) {
        foreach ($linhas_campos[$key_temp] as $key1 => $value) {
            $return .= menu_admin_campos($key, $key1, $value);
        }
    }
    $return .= '		</ul>
                                    <nav class="nav4">
                                        <a onclick="menu_admin_mais_menos(1, this)" class="link c_azul"> mais campos </a> &nbsp &nbsp
                                        <a onclick="menu_admin_mais_menos(0, this)" class="link c_azul"> menos campos </a>
                                    </nav>
                                    <div class="h5 clear"></div>
                                </fieldset>
                            </li> ';
    return $return;
}

// FUNCAO COLUNAS CAMPOS
// FUNCAO COLUNAS
function menu_admin_colunas($key, $value)
{
    $return = ' <li dir="' . $key . '">
                                <em class="mr10"></em>
                                <div class="w15 fll finput finput_linhas_check">
                                    <div class="input dbi mt7"> <input name="colunas[' . $key . '][check]" id="colunas_ckeck_' . $key . '" type="checkbox" value="1" class="design" ' . iff((isset($value['check']) and $value['check']), 'checked') . ' > </div>
                                </div>
                                <div class="dib fll pt6">
                                    <label class="w32 db pl5 limit" for="colunas_ckeck_' . $key . '"> (' . $key . ') </label>
                                </div>
                                <div class="wf3 fll finput finput_linhas_nome" title="nome">
                                    <label class="p0">&nbsp;</label>
                                    <div class="input"> <input name="colunas[' . $key . '][nome]" type="text" class="design" value="' . value($value, 'nome') . '" > </div>
                                </div>
                                <div class="wf3 fll finput finput_linhas_value" title="Value">
                                    <label class="p0 pl10"></label>
                                    <div class="input"> <input name="colunas[' . $key . '][value]" type="text" class="design" value="' . value($value, 'value') . '" > </div>
                                </div>
                                <div class="clear"></div>
                            </li> ';
    return ($return);
}

// FUNCAO COLUNAS
// FUNCAO CAMPOS
function menu_admin_campos($kabas, $key, $value)
{
    $key_n = ($key != 'txt' and $key != 'txt_meta' and $key != 'editor') ? '(' . $key . ')' : ($key == 'txt' ? 'TXT' : ($key == 'txt_meta' ? 'TM' : 'ED'));

    $return = '<li dir="' . $key . '" class="menu_admin_campos_' . $kabas . '_' . $key . '">
                                <em class="ml15 mr10"></em>
                                <a onclick="menu_admin_outros_campos(this)" class="seta"> <i class="fa fa-chevron-down"></i> <i class="db mt-9 fa fa-chevron-down"></i> </a>

                                <div class="w15 fll finput finput_check">
                                    <div class="input dbi mt7">
                                        <input name="campos[' . $kabas . '][' . $key . '][check]" id="campos_ckeck_' . $kabas . '_' . $key_n . '" type="checkbox" value="1" class="design" ' . iff((isset($value['check']) and $value['check']), 'checked') . '>
                                    </div>
                                </div>
                                <div class="dib fll pt6">
                                    <label class="w32 db pl5 limit" for="campos_ckeck_' . $kabas . '_' . $key_n . '"> ' . $key_n . ' </label>
                                </div>
                                <div class="w85 fll finput finput_temp">
                                    <div class="input">
                                        <select name="campos[' . $kabas . '][' . $key . '][temp]" class="design menu_admin_select_temp_' . $kabas . '_' . $key . '">
                                            <option value="text"			' . iff((isset($value['temp']) and $value['temp'] == 'text'), 'selected') . '>Text</option>
                                            <option value="categorias" 		' . iff((isset($value['temp']) and $value['temp'] == 'categorias'), 'selected') . '>Categorias</option>
                                            <option value="subcategorias" 	' . iff((isset($value['temp']) and $value['temp'] == 'subcategorias'), 'selected') . '>Subcategorias</option>
                                            <option value="preco" 			' . iff((isset($value['temp']) and $value['temp'] == 'preco'), 'selected') . '>Preço</option>
                                            <option value="estados" 		' . iff((isset($value['temp']) and $value['temp'] == 'estados'), 'selected') . '>Estados</option>
                                            <option value="cidades" 		' . iff((isset($value['temp']) and $value['temp'] == 'cidades'), 'selected') . '>Cidades</option>
                                            <option value="password" 		' . iff((isset($value['temp']) and $value['temp'] == 'password'), 'selected') . '>Password</option>
                                            <option value="email" 			' . iff((isset($value['temp']) and $value['temp'] == 'email'), 'selected') . '>Email</option>
                                            <option value="date" 			' . iff((isset($value['temp']) and $value['temp'] == 'date'), 'selected') . '>Data</option>
                                            <option value="datetime-local" 	' . iff((isset($value['temp']) and $value['temp'] == 'datetime-local'), 'selected') . '>Data e Hora</option>
                                            <option value="color" 			' . iff((isset($value['temp']) and $value['temp'] == 'color'), 'selected') . '>Color</option>
                                            <option value="number" 			' . iff((isset($value['temp']) and $value['temp'] == 'number'), 'selected') . '>Número</option>
                                            <option value="range" 			' . iff((isset($value['temp']) and $value['temp'] == 'range'), 'selected') . '>Range</option>
                                            <option value="url" 			' . iff((isset($value['temp']) and $value['temp'] == 'url'), 'selected') . '>Url</option>
                                            <option value="tel" 			' . iff((isset($value['temp']) and $value['temp'] == 'tel'), 'selected') . '>Telefone</option>
                                            <option value="file" 			' . iff((isset($value['temp']) and $value['temp'] == 'file'), 'selected') . '>File</option>
                                            <option value="checkbox" 		' . iff((isset($value['temp']) and $value['temp'] == 'checkbox'), 'selected') . '>Checkbox</option>
                                            <option value="radio" 			' . iff((isset($value['temp']) and $value['temp'] == 'radio'), 'selected') . '>Radio</option>
                                            <option value="select" 			' . iff((isset($value['temp']) and $value['temp'] == 'select'), 'selected') . '>Select</option>
                                            <option value="textarea" 		' . iff((isset($value['temp']) and $value['temp'] == 'textarea'), 'selected') . '>Textarea</option>
                                            <option value="button" 			' . iff((isset($value['temp']) and $value['temp'] == 'button'), 'selected') . '>Button</option>
                                            <option value="editor" 			' . iff((isset($value['temp']) and $value['temp'] == 'editor'), 'selected') . '>Editor</option>
                                            <option value="file_editor" 	' . iff((isset($value['temp']) and $value['temp'] == 'file_editor'), 'selected') . '>File Editor</option>
                                            <option value="hidden"			' . iff((isset($value['temp']) and $value['temp'] == 'hidden'), 'selected') . '>Hidden</option>
                                            <option value="info" 			' . iff((isset($value['temp']) and $value['temp'] == 'info'), 'selected') . '>Info</option>
                                        </select>
                                        <script>
                                        	$(document).ready(function(){
												$(".menu_admin_select_temp_' . $kabas . '_' . $key . '").on("select2:select", function (e) {
													menu_admin_select_temp(' . A . $kabas . '_' . $key . A . ', e);
												});
											});
                                        </script>
                                    </div>
                                </div>
                                <div class="w95 fll ml7 finput finput_fields">
                                    <div class="input">
                                        <select name="campos[' . $kabas . '][' . $key . '][fields]" class="design">
                                            <option value="0" ' . iff((isset($value['fields']) and $value['fields'] == '0'), 'selected') . '>--</option>
                                            <option value="1" ' . iff((isset($value['fields']) and $value['fields'] == '1'), 'selected') . iff(!isset($value['fields']), 'selected') . '>Fields 1</option>
                                            <option value="2" ' . iff((isset($value['fields']) and $value['fields'] == '2'), 'selected') . '>Fields 2</option>
                                            <option value="3" ' . iff((isset($value['fields']) and $value['fields'] == '3'), 'selected') . '>Fields 3</option>
                                            <option value="4" ' . iff((isset($value['fields']) and $value['fields'] == '4'), 'selected') . '>Fields 4</option>
                                            <option value="5" ' . iff((isset($value['fields']) and $value['fields'] == '5'), 'selected') . '>Fields 5</option>
                                            <option value="6" ' . iff((isset($value['fields']) and $value['fields'] == '6'), 'selected') . '>Fields 6</option>
                                            <option value="7" ' . iff((isset($value['fields']) and $value['fields'] == '7'), 'selected') . '>Fields 7</option>
                                            <option value="8" ' . iff((isset($value['fields']) and $value['fields'] == '8'), 'selected') . '>Fields 8</option>
                                            <option value="9" ' . iff((isset($value['fields']) and $value['fields'] == '9'), 'selected') . '>Fields 9</option>
                                            <option value="10" ' . iff((isset($value['fields']) and $value['fields'] == '10'), 'selected') . '>Fields 10</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w80 fll ml7 finput finput_resp">
                                    <div class="input">
                                        <select name="campos[' . $kabas . '][' . $key . '][resp]" class="design">
                                            <option value="wr1" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr1'), 'selected') . '>WR1</option>
                                            <option value="wr15" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr15'), 'selected') . '>WR1,5</option>
                                            <option value="wr2" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr2'), 'selected') . '>WR2</option>
                                            <option value="wr25" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr25'), 'selected') . '>WR2,5</option>
                                            <option value="wr3" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr3'), 'selected') . '>WR3</option>
                                            <option value="wr35" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr35'), 'selected') . '>WR3,5</option>
                                            <option value="wr4" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr4'), 'selected') . '>WR4</option>
                                            <option value="wr45" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr45'), 'selected') . '>WR4,5</option>
                                            <option value="wr5" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr5'), 'selected') . '>WR5</option>
                                            <option value="wr55" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr55'), 'selected') . '>WR5,5</option>
                                            <option value="wr6" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr6'), 'selected') . '>WR6</option>
                                            <option value="wr65" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr65'), 'selected') . '>WR6,5</option>
                                            <option value="wr7" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr7'), 'selected') . '>WR7</option>
                                            <option value="wr75" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr75'), 'selected') . '>WR7,5</option>
                                            <option value="wr8" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr8'), 'selected') . '>WR8</option>
                                            <option value="wr85" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr85'), 'selected') . '>WR8,5</option>
                                            <option value="wr9" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr9'), 'selected') . '>WR9</option>
                                            <option value="wr95" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr95'), 'selected') . '>WR9,5</option>
                                            <option value="wr10" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr10'), 'selected') . '>WR10</option>
                                            <option value="wr105" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr105'), 'selected') . '>WR10,5</option>
                                            <option value="wr11" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr11'), 'selected') . '>WR11</option>
                                            <option value="wr115" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr115'), 'selected') . '>WR11,5</option>
                                            <option value="wr12" 	' . iff((isset($value['resp']) and $value['resp'] == 'wr12'), 'selected') . iff(!isset($value['resp']), 'selected') . '>WR12</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="calc375 ml370">
                                    <div class="wf15 finput finput_nome" title="Nome">
                                        <label class="p0">&nbsp;</label>
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][nome]" type="text" class="design" value=' . A . value($value, 'nome') . A . '> </div>
                                    </div>
                                    <div class="wf15 pl10 fll finput finput_input_nome" title="Name">
                                        <label class="p0">&nbsp;</label>
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][input][nome]" type="text" class="design" value=' . A . value1($value, 'input', 'nome') . A . '> </div>
                                    </div>
                                    <div class="wf3 pl10 fll finput finput_input_tags" title="Tags">
                                        <label class="p0">&nbsp;</label>
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][input][tags]" type="text" class="design autocomplete" value=' . A . value1($value, 'input', 'tags') . A . '> </div>
                                    </div>
                                    <div class="wf3 pl10 fll finput finput_input_opcoes" title="opçoes ou Onclick para Button">
                                        <label class="p0">&nbsp;</label>
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][input][opcoes]" type="text" class="design" value="' . value1($value, 'input', 'opcoes') . '"> </div>
                                    </div>
                                    <div class="wf3 pl10 fll finput finput_input_extra" title="Extra">
                                        <label class="p0">&nbsp;</label>
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][input][extra]" type="text" class="design" value=' . A . value1($value, 'input', 'extra') . A . '> </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
    
                                <div class="dn outros_campos">
	                                <a onclick="menu_admin_deletar_campos(this)" class="seta mt37 fz14 c_vermelho"> <i class="fa fa-times"></i> </a>
                                    <div class="h10 clear"></div>
                                    <div class="w85 fll ml16 finput finput_input_tipo">
                                        <div class="input">
                                            <select name="campos[' . $kabas . '][' . $key . '][input][tipo]" class="design">
                                                <option value="text" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'text'), 'selected') . '>Text</option>
                                                <option value="file" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'file'), 'selected') . '>File</option>
                                                <option value="checkbox" 	' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'checkbox'), 'selected') . '>Checkbox</option>
                                                <option value="radio" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'radio'), 'selected') . '>Radio</option>
                                                <option value="select" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'select'), 'selected') . '>Select</option>
                                                <option value="textarea" 	' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'textarea'), 'selected') . '>Textarea</option>
                                                <option value="button" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'button'), 'selected') . '>Button</option>
                                                <option value="editor" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'editor'), 'selected') . '>Editor</option>
                                                <option value="file_editor" ' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'file_editor'), 'selected') . '>File Editor</option>
                                                <option value="hidden" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'hidden'), 'selected') . '>Hidden</option>
                                                <option value="info" 		' . iff((isset($value['input']['tipo']) and $value['input']['tipo'] == 'info'), 'selected') . '>Info</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w110 fll ml10 finput finput_input_tipo1">
                                        <div class="input">
                                            <select name="campos[' . $kabas . '][' . $key . '][input][tipo1]" class="design">
                                                <option value="text" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'text'), 'selected') . '>---</option>
                                                <option value="date" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'date'), 'selected') . '>Data</option>
                                                <option value="datetime-local" 	' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'datetime-local'), 'selected') . '>Data e Hora</option>
                                                <option value="email" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'email'), 'selected') . '>Email</option>
                                                <option value="month" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'month'), 'selected') . '>Month</option>
	                                            <option value="color" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'color'), 'selected') . '>Color</option>
                                                <option value="number" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'number'), 'selected') . '>Number</option>
	                                            <option value="range" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'range'), 'selected') . '>Range</option>
                                                <option value="password" 		' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'password'), 'selected') . '>Password</option>
                                                <option value="hidden" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'hidden'), 'selected') . '>Hidden</option>
                                                <option value="search" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'search'), 'selected') . '>Search</option>
                                                <option value="tel" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'tel'), 'selected') . '>Tel</option>
                                                <option value="time" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'time'), 'selected') . '>Time</option>
                                                <option value="url" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'url'), 'selected') . '>Url</option>
                                                <option value="week" 			' . iff((isset($value['input']['tipo1']) and $value['input']['tipo1'] == 'week'), 'selected') . '>Week</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w80 fll ml10 finput finput_input_design">
                                        <div class="input">
                                            <select name="campos[' . $kabas . '][' . $key . '][input][design]" class="design">
                                                <option value="1" ' . iff((isset($value['input']['design']) and (!$value['input']['design'] or $value['input']['design'] == 1)), 'selected') . '>Desgin</option>
                                                <option value="2" ' . iff((isset($value['input']['design']) and $value['input']['design'] == 2), 'selected') . '>Normal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w90 fll ml10 finput finput_input_disabled">
                                        <div class="input">
                                            <select name="campos[' . $kabas . '][' . $key . '][input][disabled]" class="design">
                                                <option value="0" ' . iff((isset($value['input']['disabled']) and $value['input']['disabled'] == 0), 'selected') . '>Enable</option>
                                                <option value="1" ' . iff((isset($value['input']['disabled']) and $value['input']['disabled'] == 1), 'selected') . '>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w50 fll finput finput_dois_pontos ml10">
                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][dois_pontos]" type="text" class="design" value=' . A . value($value, 'dois_pontos', ':') . A . '> </div>
                                    </div>
                                    <div class="calc481 ml475">
                                        <div class="wf6 finput finput_input_executar_funcao" title="Executar Funcao">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][input][executar_funcao]" type="text" class="design" placeholder="Executar Funcao" value="' . value1($value, 'input', 'executar_funcao') . '"> </div>
                                        </div>
                                        <div class="wf15 pl10 finput finput_nome_classe" title="Label">
                                            <label class="p0">&nbsp;</label>
                                            <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][nome_classe]" type="text" class="design" placeholder="Tags da Label" value=' . A . value($value, 'nome_classe') . A . '> </div>
                                        </div>
	                                    <div class="wf15 pl10 fll finput finput_pai_fields_classe">
                                            <label class="p0">&nbsp;</label>
	                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][pai_fields_classe]" type="text" class="design" placeholder="Class do Pai da Fields" value=' . A . value($value, 'pai_fields_classe', '') . A . '> </div>
	                                    </div>
	                                    <div class="wf15 pl10 fll finput finput_fields_classe">
                                            <label class="p0">&nbsp;</label>
	                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][fields_classe]" type="text" class="design" placeholder="Class da Fields" value=' . A . value($value, 'fields_classe', '') . A . '> </div>
	                                    </div>
	                                    <div class="wf15 pl10 fll finput finput_legend">
                                            <label class="p0">&nbsp;</label>
	                                        <div class="input"> <input name="campos[' . $kabas . '][' . $key . '][legend]" type="text" class="design" placeholder="Fields Legenda" value=' . A . value($value, 'legend', '') . A . '> </div>
	                                    </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="clear"></div>
                            </li> ';

    return ($return);
}

// FUNCAO CAMPOS
// GRAVACAO DO EDITOR
function editor_gravar($table, $id, $post)
{
    $mysql = new Mysql();

    for ($i = 0; $i < 10; $i++) {
        if (!$i)
            $c = '';
        else
            $c = $i;

        if (isset($post['txt_editor' . $c])) {
            $mysql->colunas = 'id';
            $mysql->prepare = array($table, $id, $c);
            $mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? AND `tipo` = ? ";
            $z_txt = $mysql->read_unico('z_txt');
            if ($z_txt) {
                $mysql->logs = 0;
                $mysql->prepare = array($table, $id, $c);
                $mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? AND `tipo` = ? ";
                $mysql->campo['txt'] = base64_encode(stripslashes($post['txt_editor' . $c]));
                $mysql->update('z_txt');
            } else {
                $mysql->logs = 0;
                $mysql->campo['tipo'] = $c;
                $mysql->campo['item'] = $id;
                $mysql->campo['tabelas'] = $table;
                $mysql->campo['txt'] = base64_encode(stripslashes($post['txt_editor' . $c]));
                $mysql->insert('z_txt');
            }
        }
    }
}

// GRAVACAO DO EDITOR
// TIRANDO BARRAS NA GRAVAÇÃO
function tirando_barras($array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {

            foreach ($value as $k => $v) {
                if (is_array($v)) {

                    foreach ($v as $k1 => $v1) {
                        if (is_array($v1)) {

                            foreach ($v1 as $k2 => $v2) {
                                if (is_array($v2)) {
                                } else {
                                    $array[$key][$k][$k1][$k2] = stripslashes($v2);
                                }
                            }
                        } else {
                            $array[$key][$k][$k1] = stripslashes($v1);
                        }
                    }
                } else {
                    $array[$key][$k] = stripslashes($v);
                }
            }
        } else {
            $array[$key] = stripslashes($value);
        }
    }
    return $array;
}

// TIRANDO BARRAS NA GRAVAÇÃO
