<?php


//if(extension_loaded('zlib')){ob_start('ob_gzhandler');}

require_once "../../../../system/conecta.php";
require_once "../../../../system/mysql.php";
//require_once('../../../../plugins/Tng/tng/tNG.inc.php');

include_once '../../../../app/Funcoes/funcoes.php';
include_once '../../../../app/Funcoes/funcoesAdmin.php';
include_once 'scripts/func_ajax_ini.php';
include_once 'scripts/func.php';

/* ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL); */

function __autoload($class_name)
{
    autoload($class_name, '../../../');
}

$mysql = new Mysql();
$mysql->prepare = array($_POST['modulo']);
$mysql->filtro = " WHERE `id` = ? ";
$modulos = $mysql->read_unico('menu_admin');
$arr["modulos"] = $modulos->id;

// Colunas Datatable Row Data
if (isset($_POST['row']) and $modulos->id != 1) {
    $_POST['filtro'] = " and id = '" . $_POST['row'] . "' ";
    $_POST['col'] = dt_colunas_row_data($modulos);
}


// Pegando dados das colunas
foreach ($_POST['col'] as $key => $value) {
    $ex = explode('->', $value);
    $colx[] = array('num' => $key, 'col' => $ex[0]);
    $ex = explode('->', $value);
    for ($i = 0; $i <= 10; $i++) {
        if (isset($ex[$i]) or $i <= 2)
            $cols[$key][$i] = isset($ex[$i]) ? $ex[$i] : '';
    }
    switch ($cols[$key][0]) {
        case 'mais_fotos':
            $cols[$key][0] = 'id';
            $cols[$key][1] = 'mais_fotos';
            break;
        case 'mais_comentarios':
            $cols[$key][0] = 'id';
            $cols[$key][1] = 'mais_comentarios';
            break;
    }
}


if (isset($_GET['modulo']) and $_GET['modulo'] == 69) {
    $cols[count($cols)] = array('id', 'Ações', '');
}


// Table
if (isset($_POST['pg']) and $_POST['pg'] == 'mais_comentarios')
    $table = 'mais_comentarios';
else
    $table = $modulos->modulo;

// Filtro
$filtro = isset($_POST['filtro']) ? stripcslashes($_POST['filtro']) : '';
$filtro = " where `lang` = '" . LANG . "' " . $filtro . " " . $modulos->table_filtro;


// PERMISSOES
verificar_permissoes_all($modulos, 0, 'lista');
$filtro = verificar_permissoes_itens($table, $filtro, $modulos);
// PERMISSOES
// FILTROS
if ($table == 'menu_admin') { // MENU ADMIN
    $tipo_modulo = isset($_GET['m']) ? " and tipo_modulo = '" . $_GET['m'] . "' " : "";
    $filtro .= ' and id != 1 ' . $tipo_modulo . ' ';
} elseif ($table == 'financeiro') { // FINANCEIRO
    // Tabs Atual
    if (isset($_SESSION['financeiro_tipos']))
        $filtro .= " and financeiro_tipos = '" . $_SESSION['financeiro_tipos'] . "' ";
    // Conta Atual
    if (isset($_SESSION['financeiro_conta_atual']) and $_SESSION['financeiro_conta_atual'] != 'all')
        $filtro .= $filtro_conta_atual = " and financeiro_contas = '" . $_SESSION['financeiro_conta_atual'] . "' ";
    // Mes/Ano Atual
    if (isset($_SESSION['financeiro_mes_atual']) and $_SESSION['financeiro_mes_atual'] and isset($_SESSION['financeiro_ano_atual']) and $_SESSION['financeiro_ano_atual']) {
        $filtro .= $filtro_mes_atual = " and data_data BETWEEN ('" . $_SESSION['financeiro_ano_atual'] . "-" . $_SESSION['financeiro_mes_atual'] . "-01') AND ('" . $_SESSION['financeiro_ano_atual'] . "-" . $_SESSION['financeiro_mes_atual'] . "-31') ";
        $filtro_mes_passado = " and data_data BETWEEN ('" . $_SESSION['financeiro_ano_atual'] . "-" . ($_SESSION['financeiro_mes_atual'] - 1) . "-01') AND ('" . $_SESSION['financeiro_ano_atual'] . "-" . ($_SESSION['financeiro_mes_atual']) . "-00') ";
    }
}


if ($modulos->id == 60 or $modulos->id == 78) {
    if (isset($_GET['leiloes'])) {
        $filtro .= " AND  leiloes = '" . $_GET['leiloes'] . "' ";
    }
}


// Datatable Filtro (Filtro Avancado)
if (isset($_POST['datatable_filtro'])) {
    $filtro .= dt_datatable_filtros($_POST['datatable_filtro']);
}


// STATUS DOS LEILOES
if (isset($_GET['leiloes_status'])) {
    $filtro_lotes = status_leiloes($_GET['leiloes_status']);
}

if (isset($filtro_lotes)) {
    if ($modulos->modulo == 'leiloes') {
        $filtro .= " AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE 1=1 " . $filtro_lotes . " ) ";
    } else {
        $filtro .= $filtro_lotes;
    }
}
// STATUS DOS LEILOES
// LEILAO AO VIVO
if ($modulos->id == 76 or $modulos->id == 77) {
    $filtro .= " AND (tipos = 2 OR tipos = 3) ";
}
// LEILAO AO VIVO
// FILTROS
// ACOES
// Financeiro
if ($table == 'financeiro')
    parcelamento_infinito($modulos);
// ACOES
// Variaveis para o Filtro Interno
if (isset($modulos->campos))
    $_POST['modulos_campos'] = unserialize(base64_decode($modulos->campos));


// Pegando mais dados da tabela
$cols_extra = '';
foreach ($cols as $k_col => $col) {
    if ($col[1] == 'cidade_estado' or $col[1] == 'cidades_estados') {
        $counas = $col[2] ? $col[2] : $col[1];
        $ex = explode('_', $counas);
        $cols_extra = implode(', ', $ex);
    } elseif ($col[1] == 'logs_acoes') {
        $cols_extra = 'usuarios, usuarios_id, acoes, tabelas, item, campos';
    } elseif ($col[1] == 'star') {
        $cols_extra = 'star, tabelas, item';
    } elseif ($col[1] == 'tempo') {
        $cols_extra = 'data, data_saida';
    } elseif ($col[1] == 'financeiro') {
        $cols_extra = 'parcela_atual, preco, pago';
    }
}

// Fazendo Consulta
$consulta = dt_consulta($table, $_POST, $cols, $cols_extra, $filtro, $modulos);


// Variaveis
$financeiro['saldo_nao_pago'] = 0;
$financeiro['saldo_pago'] = 0;
$financeiro['saldo'] = 0;




// ----------------------------------------------------------------------------------------------------------------------------------------------------
// DADOS

$mysql->nao_existe_all = 1;
$arr["data"] = array();

foreach ($consulta['dados'] as $key => $value) {

    // INFOS DA TABLE
    $row = array();
    foreach ($cols as $k_col => $col) {
        $row_atual = array();
        $col_autal = $col[0];
        $item = $value->$col_autal;
        $info = dt_info($value, $col, $k_col);

        // COLUNAS
        // ACOES
        if ($col[1] == 'acoes') {
            $dt_value = dt_value($value);
            dt_criar_colunas($modulos, $dt_value);

            $return = '';
            $bloquear = 0;
            foreach (unserialize(base64_decode($modulos->colunas)) as $k1 => $v1) {
                if (preg_match('(block)', $v1['value']) and isset($v1['check']) and $v1['check'])
                    $bloquear = 1;
            }
            if ($bloquear)
                $return .= '<a onclick="datatable_acoes(' . A . 'block' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')"		class="dib p5" 	title="Ativar ou Bloquear Item">   				  <i class="di fz16 fa fa-check ' . iff($dt_value->status, 'c_verde', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-star-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes(' . A . 'star' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')"  		class="dib p5" 	title="Selecione os Itens como Destaque">		  <i class="di fz16 fa fa-star ' . iff($dt_value->star, 'c_amarelo', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-lancamentos-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes(' . A . 'lancamentos' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')"	class="dib p5" 	title="Selecione os Itens como Lançamento">		  <i class="di fz16 fa fa-dot-circle-o ' . iff($dt_value->lancamentos, 'c_verde', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-promocao-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes(' . A . 'promocao' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')"		class="dib p5" 	title="Mostrar Video Ao Vivo"> 		  			<i class="di fz16 fa fa-youtube-play ' . iff($dt_value->promocao, 'c_preto', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-clonar-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes(' . A . 'clonar' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')"		class="dib p5" 	title="Clonar Item">  							  <i class="di fz16 c_azul fa fa-files-o ativo"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-mapa-)', $modulos->informacoes))
                $return .= '<a onclick="boxs(' . A . 'mais_mapas' . A . ', ' . A . 'item=' . $value->id . '&modulos=' . $modulos->id . A . ', 1);"		class="dib p5" 	title="Cadastrar uma Localização para esse Item"> <i class="di fz16 c_azul fa fa-globe ativo"></i> </a> ';
            //if($modulos->id == 54){
            //	$return .= '<a onclick="window.open('.A.DIR.'/produto/-/'.$value->id.'?preview=ok'.A.', '.A.'_blank'.A.')" target="_blank" class="dib p5" title="Preview"><i class="di fz16 c_azul fa fa-file-image-o"></i> </a> ';
            //}

            if ($modulos->id == 60) {
                $return .= '<div class="dib posr pl5 pr5"><a onclick="boxs(' . A . 'lances' . A . ', ' . A . 'id=' . $value->id . A . ')" title="Ver Lances Efeturados"><i class="di fz16 fa fa-tags"></i> </a></div> ';
            }
            if ($modulos->id == 63) {
                $return .= '<div class="dib posr pl5 pr5"><a onclick="boxs(' . A . 'leiloes_horarios' . A . ', ' . A . 'id=' . $value->id . A . ')" title="Horários do Leião e Lotes"><i class="di fz16 fa fa-clock-o"></i> </a></div> ';
            }

            if ($modulos->id == 60 and isset($value->situacao) and $value->situacao == 2) {
                $return .= '<div class="dib posr pl5 pr5"><a onclick="boxs(' . A . 'enviar_email' . A . ', ' . A . 'cadastro=' . $value->lances_cadastro . A . ')" title="Enviar email para arrematados"><i class="di fz16 fa fa-envelope-o"></i> </a></div> ';
                $return .= '<div class="dib posr pl5 pr5"><a onclick="hreff(' . A . DIR . '/imprimir/recibo_provisorio/' . $item . A . ')" title="Termo Provisório de Arrematação"><i class="di fz16 fa fa-external-link-square"></i> </a></div> ';
                $return .= '<div class="dib pl5 pr5"><a onclick="hreff(' . A . DIR . '/imprimir/termo_arrematacao/' . $item . A . ')" title="Termo de Arrematação"><i class="di fz16 fa fa-external-link"></i> </a></div> ';
                $return .= '<div class="dib posr pl5 pr5"><a onclick="boxs(' . A . 'enviar_email' . A . ', ' . A . 'cadastro=' . $value->lances_cadastro . '&lote=' . $value->id . A . ')" title="Enviar Termo de Arrematação e Nota de Venda por Email"><i class="di fz16 fa fa-envelope"></i> </a></div> ';
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = '(nao_mostrar)';

            // ACOES
            // ACOES BOXS
        } elseif ($col[1] == 'acoes_boxs') {
            $dt_value = dt_value($value);
            dt_criar_colunas($modulos, $dt_value);

            $return = '';
            $bloquear = 0;
            if (isset($modulos->colunas)) {
                foreach (unserialize(base64_decode($modulos->colunas)) as $k1 => $v1) {
                    if (preg_match('(status)', $v1['value']) and isset($v1['check']) and $v1['check'])
                        $bloquear = 1;
                }
            } else {
                $bloquear = 1;
            }
            if ($bloquear)
                $return .= '<a onclick="datatable_acoes_boxs(' . A . 'block' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 			class="dib fz16 pl5 pr5"	title="Ativar ou Bloquear Item">   			<i class="fa fa-check ' . iff($dt_value->status, 'c_verde', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-star-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes_boxs(' . A . 'star' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 			class="dib fz16 pl5 pr5"  	title="Selecione os Itens como Destaque">	<i class="fa  fa-star ' . iff($dt_value->star, 'c_amarelo', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-lancamentos-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes_boxs(' . A . 'lancamentos' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 	class="dib fz16 pl5 pr5"	title="Selecione os Itens como Lançamento">	<i class="fa  fa-dot-circle-o ' . iff($dt_value->lancamentos, 'c_verde', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-promocao-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes_boxs(' . A . 'promocao' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 		class="dib fz16 pl5 pr5"	title="Selecione os Itens como Promoção"> 	<i class="fa  fa-certificate ' . iff($dt_value->promocao, 'c_azul', 'n_ativo') . '"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-clonar-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_acoes_boxs(' . A . 'clonar' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 		class="dib fz16 pl5 pr5"	title="Clonar Item">  						<i class="c_azul fa fa-files-o ativo"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-edit-)', $modulos->informacoes))
                $return .= '<a onclick="datatable_campos_boxs(' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" 					class="dib fz16 pl5 pr5"	title="Editar Item"> 						<i class="c_cinza fa fa-edit (alias) ativo"></i> </a> ';
            if (isset($modulos->informacoes) and preg_match('(-excluir-)', $modulos->informacoes))
                $return .= '<a onclick="if(confirm(' . A . 'Deseja realmente deletar este item?' . A . '))datatable_acoes_boxs(' . A . 'delete' . A . ', ' . A . $modulos->id . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" class="dib fz16 pl5 pr5 c_vermelho"> <i class="fa fa-times" title="Deletar Item"></i> </a> ';
            if (!$key)
                $return .= '<script> $(document).ready(function() { $(".datatable_boxs tbody td .td_datatable").on("dblclick", function(){ datatable_campos_boxs_selecionar_select2(' . A . $_POST['rand'] . A . ', this); javascript:fechar_all(); }) }); </script> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = '(nao_mostrar)';
        } elseif ($col[1] == 'acoes_boxs1') {
            $dt_value = dt_value($value);
            dt_criar_colunas($modulos, $dt_value);

            $modulo = $table == 'mais_comentarios' ? $modulos->id . '&table=' . $table . '&item=' . $value->item : $table;

            $return = '';
            $return .= '<a onclick="datatable_acoes_boxs(' . A . 'block' . A . ', ' . A . $modulo . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" class="dib fz16 pl5 pr5"> 		<i class="fa fa-check ' . iff($dt_value->status, 'c_verde', 'n_ativo') . '"></i> </a> ';
            $return .= '<a onclick="datatable_acoes_boxs(' . A . 'clonar' . A . ', ' . A . $modulo . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" class="dib fz16 pl5 pr5"> 		<i class="c_azul fa fa-files-o ativo"></i> </a> ';
            $return .= '<a onclick="datatable_campos_boxs(' . A . $modulo . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" class="dib fz16 pl5 pr5"> 				<i class="c_333 fa fa-edit (alias) ativo"></i> </a> ';
            $return .= '<a onclick="if(confirm(' . A . 'Deseja realmente deletar este item?' . A . '))datatable_acoes_boxs(' . A . 'delete' . A . ', ' . A . $modulo . A . ', ' . A . $_POST['rand'] . A . ', ' . A . $value->id . A . ')" class="dib fz16 pl5 pr5 c_vermelho"> <i class="fa fa-times"></i> </a> ';
            if (!$key)
                $return .= '<script> $(document).ready(function() { $(".datatable_boxs tbody td .td_datatable").on("dblclick", function(){ datatable_campos_boxs_selecionar_select2(' . A . $_POST['rand'] . A . ', this); javascript:fechar_all(); }) }); </script> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = '(nao_mostrar)';

            // ACOES BOXS
            // ----------------------------------------------------------------------------------------------------------------------------------------------------
            // OUTROS PADROES
            // Cidade Estado
        } elseif ($col[1] == 'cidade_estado' or $col[1] == 'cidades_estados') {
            $counas = $col[2] ? $col[2] : $col[1];
            $ex = explode('_', $counas);
            $ex_0 = $ex[0];
            $ex_1 = $ex[1];
            $cidade = $value->$ex_0 ? $value->$ex_0 : '- - - - -';
            $estado = $value->$ex_1 ? $value->$ex_1 : '- - - - -';
            $return = $cidade != '- - - - -' ? $cidade . ' / ' . $estado : $estado;

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Logs Acoes
        } elseif ($col[1] == 'logs_acoes') {
            $return = '';
            $align = '';
            if ($col[0] == 'usuarios') {
                if ($value->usuarios) {
                    if ($value->usuarios == 'site') {
                        $item = 'Site';
                    } else {
                        $mysql->colunas = "nome";
                        $mysql->prepare = array($value->usuarios_id);
                        $mysql->filtro = " WHERE `id` = ? ";
                        $usuarios = $mysql->read_unico($value->usuarios);
                        if (isset($usuarios->nome))
                            $return = $usuarios->nome;
                    }
                }
            } else if ($col[0] == 'acoes') {
                $return = $value->acoes;
            } else if ($col[0] == 'tabelas') {
                $return = ucfirst(str_replace('_', ' ', $value->tabelas));
            } else if ($col[0] == 'item') {
                $return = $value->item;
            } else if (isset($col[2]) and $col[2] == 'campos') {
                $align = 'tal';
                $caminho = caminho('/plugins/Json/log_acoes/' . $item . '.json');
                if (file_exists($caminho)) {
                    $campos = json_decode(file_get_contents($caminho));
                    if (is_object($campos)) {
                        foreach ($campos as $nome_post => $valor_post) {
                            if ($nome_post != 'tipo' and $nome_post != 'subcategorias' and $nome_post != 'varias_categorias')
                                $return .= str_replace(',', '', $nome_post) . ' = ' . $valor_post . '<br>';
                        }
                    }
                }
            }

            $row_atual['tags'] = $info;
            $row_atual['tags']['class'] .= $align;
            $row_atual['value'] = $return;

            // OUTROS PADROES
            // ----------------------------------------------------------------------------------------------------------------------------------------------------
            // NUMEROS DE ITENS RELACIONADOS
            // lotes
        } elseif ($col[1] == 'lotes') {
            $mysql->colunas = "id";
            $mysql->prepare = array($value->id);
            if (isset($filtro_lotes)) {
                $mysql->filtro = " WHERE `leiloes` = ? " . $filtro_lotes . " ";
            } else {
                $mysql->filtro = " WHERE `leiloes` = ? ";
            }
            $lotes = $mysql->read("lotes");
            $return = '<i class="fa fa-bars"></i> | <span>' . count($lotes) . '</span> ';

            $leiloes_status = isset($_GET['leiloes_status']) ? '&leiloes_status=' . $_GET['leiloes_status'] : '';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'views(60, 0, ' . A . 'leiloes=' . $value->id . $leiloes_status . A . ');';
            $row_atual['onclick_class'] = 'p5 n_mais_fotos';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = count($lotes);



            // leiloes_plaquetas
        } elseif ($col[1] == 'leiloes_plaquetas') {
            $mysql->colunas = "id";
            $mysql->prepare = array($value->id);
            $mysql->filtro = " WHERE `leiloes` = ? ";
            $leiloes_plaquetas = $mysql->read("leiloes_plaquetas");
            $return = '<i class="fa fa-bars"></i> | <span>' . count($leiloes_plaquetas) . '</span> ';

            $leiloes_status = isset($_GET['leiloes_status']) ? '&leiloes_status=' . $_GET['leiloes_status'] : '';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'views(78, 0, ' . A . 'leiloes=' . $value->id . $leiloes_status . A . ');';
            $row_atual['onclick_class'] = 'p5 n_mais_fotos';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = count($leiloes_plaquetas);



            // Mais Fotos
        } elseif ($col[1] == 'mais_fotos') {
            $mysql->colunas = "id";
            $mysql->prepare = array($table, $value->id);
            $mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? ";
            $mais_fotos = $mysql->read("mais_fotos");
            $return = '<i class="fa fa-bars"></i> | <span>' . count($mais_fotos) . '</span> ';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'boxs(' . A . 'mais_fotos' . A . ', ' . A . 'item=' . $value->id . '&modulos=' . $modulos->id . A . ', 1);';
            $row_atual['onclick_class'] = 'p5 n_mais_fotos';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = count($mais_fotos);



            // Mais Comentarios
        } elseif ($col[1] == 'mais_comentarios') {
            $mysql->colunas = "id";
            $mysql->prepare = array($table, $value->id);
            $mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? ";
            $mais_comentarios = $mysql->read("mais_comentarios");
            $return = '<i class="fa fa-bars"></i> | <span>' . count($mais_comentarios) . '</span> ';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'boxs(' . A . 'gerenciar_itens' . A . ', ' . A . 'table=mais_comentarios&item=' . $value->id . '&modulos=' . $modulos->id . A . ', 1);';
            $row_atual['onclick_class'] = 'p5 n_mais_comentarios';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = count($mais_comentarios);



            // Mais Star
        } elseif ($col[1] == 'mais_star') { // *star
            if ($col[2] == 'unico') {
                $total = $item;
            } else {
                $total = star($value);
            }
            $return = '<div rel="tooltip" data-original-title="' . star($value, 'votacao') . '" class="fz16"> ' . star_icon($total) . '</div> ';
            $return .= '<script> $("[rel=tooltip]").tooltip() </script> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $total;



            // Star
        } elseif ($col[1] == 'star') { // *star
            $n = $col[2] ? $col[2] : 5;
            $total = $item;
            $return = '<div rel="tooltip" data-original-title="' . $total . ' estrelas" class="fz16"> ' . star_icon($total, $n) . '</div> ';
            $return .= '<script> $("[rel=tooltip]").tooltip() </script> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $total;

            // NUMEROS DE ITENS RELACIONADOS
            // ----------------------------------------------------------------------------------------------------------------------------------------------------
            // CATEGORIAS
            // Select
        } elseif ($col[1] == 'select') {
            $ex = explode('class=', $col[2]);
            $coluna = $ex[0] ? $ex[0] : 'nome';
            $return = rel($col[0], $item, $coluna, '- - - - - - -', 1);
            if (isset($col[3]) and $col[3] == 'select') {
                $col[4] = (isset($col[4]) and $col[4]) ? $col[4] : 'nome';
                $coluna = $col[4];
                $return = rel($col[2], $return, $coluna, '- - - - - - -', 1);
                $select_preco = $col[4] == 'preco' ? 1 : '';
                $col[4] = isset($col[6]) ? $col[6] : '';
                $col[5] = isset($col[7]) ? $col[7] : '';;
            }
            if (isset($col[3]) and ($col[3] == 'preco' or (isset($select_preco) and $select_preco))) {
                $col[4] = (isset($col[4]) and $col[4]) ? $col[4] : 2;
                $casas = $col[4];
                $col[5] = (isset($col[5]) and $col[5]) ? $col[5] : '';
                $return = preco($return, 0, $casas, ',', '.', 1) . $col[5];
                unset($select_preco);
            }
            $limit_div = (isset($col[4]) and $col[4] == 'nome') ? 'max-w200 m-a' : '';

            $row_atual['tags'] = $info;
            $row_atual['tags']['class'] .= $limit_div . ' limit';
            $row_atual['value'] = $return;



            // Select1
        } elseif ($col[1] == 'select1') {
            $return = '- - - - - - -';
            $opcoes = '';
            foreach (unserialize(base64_decode($modulos->campos)) as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    if ($v2['input']['nome'] == $col[0]) // and isset($v2['check']) and $v2['check']
                        $opcoes = $v2['input']['opcoes'];
                }
            }
            $opcoes = explode('; ', $opcoes);
            for ($c = 0; $c < count($opcoes); $c++) {
                $ex = explode('->', $opcoes[$c]);
                if (isset($ex[1]) and $item == $ex[0])
                    $return = $ex[1];
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Check
        } elseif ($col[1] == 'check') {
            $return = td_check($col, $item, $modulos);

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Categorias
        } elseif ($col[0] == 'categorias') { // *categorias
            $return = '- - - - - - -';
            $mysql->prepare = array($item);
            $mysql->filtro = " WHERE `id` = ? ";
            $cate1 = $mysql->read($value->table . '1_cate');
            foreach ($cate1 as $v1) {
                $return = '(' . $v1->nome . ')';
                $id_sub = $v1->subcategorias;

                // Niveis da categoria
                for ($x = ($v1->tipo - 1); $x >= 0; $x = $x - 1) {
                    $mysql->prepare = array($id_sub);
                    $mysql->filtro = " WHERE `id` = ? ";
                    $cate2 = $mysql->read($value->table . '1_cate');
                    foreach ($cate2 as $v2) {
                        $return = '(' . $v2->nome . ') <br> ' . $return;
                        $id_sub = $v2->subcategorias;
                    }
                }
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Subcategorias
        } elseif ($col[0] == 'subcategorias') {
            $return = '- - - - - - -';
            $mysql->prepare = array($value->id);
            $mysql->filtro = " WHERE `id` = ? ";
            $cate1 = $mysql->read($value->table);
            foreach ($cate1 as $v1) {
                $return = '(' . $v1->nome . ')';
                $id_sub = $v1->subcategorias;

                // Niveis da categoria
                for ($x = ($v1->tipo - 1); $x >= 0; $x = $x - 1) {
                    $mysql->prepare = array($id_sub);
                    $mysql->filtro = " WHERE `id` = ? ";
                    $cate2 = $mysql->read($value->table);
                    foreach ($cate2 as $v2) {
                        $return = '(' . $v2->nome . ') <br> ' . $return;
                        $id_sub = $v2->subcategorias;
                    }
                }
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;


            // VCategorias
        } elseif ($col[0] == 'vcategorias') {
            $return = '';
            $categorias = explode('-', $item);
            foreach ($categorias as $k => $v) {
                if ($v)
                    $return .= '<div>(' . rel($table . '1_cate', $v) . ')</div>';
            }
            $return = $return ? $return : '- - - - - - -';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;


            // Lances Cadastro
        } elseif ($col[0] == 'lances_cadastro') {
            $lances_cadastro = rel('cadastro', $item, 'login');
            $lances_plaquetas = plaquetas($value->lances_plaquetas);

            $return = '- - - - - - -';
            if ($lances_cadastro) {
                $return = 'Usuário: ' . $lances_cadastro;
            } elseif ($lances_plaquetas != 00) {
                $return = 'Nº Plaqueta: ' . $lances_plaquetas;
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // CATEGORIAS
            // ----------------------------------------------------------------------------------------------------------------------------------------------------
            // NOVOS
            // Mais Avise-me
            //} elseif($col[0] == 'mais_aviseme'){
            // NOVOS
            // ----------------------------------------------------------------------------------------------------------------------------------------------------
            // PADROES
            // Preco
        } elseif ($col[1] == 'preco' or $col[1] == 'preco1' or $col[1] == 'preco2' or $col[1] == 'preco3') {
            $col[3] = isset($col[3]) ? $col[3] : '';
            $casas = $col[2] ? $col[2] : 2;
            if ($col[1] == 'preco')
                $return = preco($item, 1, $casas) . $col[3];
            elseif ($col[1] == 'preco1')
                $return = preco($item, 0, $casas, ',', '', 1) . $col[3];
            elseif ($col[1] == 'preco2')
                $return = preco($item, 0, $casas, ',') . $col[3];
            elseif ($col[1] == 'preco3')
                $return = preco($item, 1, $casas, ',', '', 1) . $col[3];

            // Some dos precos
            $preco[$col[0]] = isset($preco[$col[0]]) ? $preco[$col[0]] + $item : $item;

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Data
        } elseif ($col[0] == 'data' or $col[1] == 'data') { // *data
            $col[2] = $col[2] ? $col[2] : 'd/m/Y';
            $return = $item != '0000-00-00 00:00:00' ? data($item, $col[2]) : '- - - - - - -';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // data_praca1
        } elseif ($col[1] == 'data_praca1') {
            $lance_min = rel('lotes', $value->id, 'lance_min');
            $data_fim = rel('lotes', $value->id, 'data_fim');

            $return = '<div><b class="dib">Início:</b> ' . data($item, 'd/m/Y H:i') . '</div>';
            $return .= '<div class="h2"></div>';
            $return .= '<div><b class="dib">Término:</b> ' . data($data_fim, 'd/m/Y H:i') . '</div>';
            $return .= '<div class="h2"></div>';
            $return .= '<div><b class="dib">Lance Mínimo:</b>&nbsp;' . preco($lance_min, 1) . '</div>';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
        } elseif ($col[1] == 'data_praca2') {
            $lance_min1 = rel('lotes', $value->id, 'lance_min1');
            $data_fim1 = rel('lotes', $value->id, 'data_fim1');

            if ($lance_min1 > 0) {
                $return = '<div><b class="dib">Início:</b> ' . data($item, 'd/m/Y H:i') . '</div>';
                $return .= '<div class="h2"></div>';
                $return .= '<div><b class="dib">Término:</b> ' . data($data_fim1, 'd/m/Y H:i') . '</div>';
                $return .= '<div class="h2"></div>';
                $return .= '<div><b class="dib">Lance Mínimo:</b>&nbsp;' . preco($lance_min1, 1) . '</div>';
            } else {
                $return = '- - - - - - -';
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Tempo
        } elseif ($col[1] == 'tempo') {
            $return = '...';
            if ($value->data_saida and $value->data_saida != '0000-00-00 00:00:00') {
                $data = sub_data($value->data_saida, $value->data);
                $return = $data['dias'] . ' ' . $data['hora'] . ':' . $data['min'] . ':' . $data['seg'];
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Set
        } elseif ($col[1] == 'set') {
            $return = set($table, $value->id, $col[2]);

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Color
        } elseif ($col[0] == 'color') {
            if ($value->foto)
                $return = '';
            else
                $return = '<div style="width:30px; height:30px; display: inline-block; background:' . $item . '" class="br5"></div>';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Online
        } elseif ($col[1] == 'online') {
            $return = 'Off';
            $return1 = 'Off';
            if ($item) {
                if (somar_datas($item, 0, 0, 0, 0, 10, 0, 'Y-m-d-H-m-s') > date('Y-m-d-H-m-s')) { // *ult_acesso
                    $return = '<b style="color:#0C0">On</b>';
                    $return1 = 'On';
                }
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $return1;



            // Download
        } elseif ($col[1] == 'download') { // *foto
            $return = '';
            if ($item) {
                $return .= '<i class="fa fa-download c_azul"></i> ';
            }

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'downloadd(' . A . $item . A . ')';
            $row_atual['onclick_class'] = 'dib fz16 pl5 pr5';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = '(nao_mostrar)';



            // Ver
        } elseif ($col[1] == 'ver') { // *foto
            $return = '';
            if ($item) {
                $return .= '<a onclick="boxs(' . A . 'ver' . A . ', ' . A . 'modulos=' . $modulos->id . '&box=' . $col[2] . '&id=' . $value->id . A . ', 1)" class="dib fz16 pl5 pr5"><i class="fa fa-' . iff($col[3], $col[3], 'file-video-o') . ' c_azul"></i> </a> ';
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = '(nao_mostrar)';



            // Situacao
        } elseif ($col[0] == 'situacao') { // *situacao
            $situacao = 'Em Loteamento';
            $situacao_id = 0;
            $back = LOTEAMENTO;
            $cor = '#666';
            if ($item == 2) {
                $situacao = 'Arrematado';
                $situacao_id = 2;
                $back = ARREMATADO;
                $cor = '#fff';
            } elseif ($item == 3) {
                $situacao = 'Não Arrematado';
                $situacao_id = 3;
                $back = NAO_ARREMATADO;
                $cor = '#666';
            } elseif ($item == 10) {
                $situacao = 'Em Condicional';
                $situacao_id = 10;
                $back = CONDICIONAL;
                $cor = '#fff';
            } elseif ($item == 20) {
                $situacao = 'Venda Direta';
                $situacao_id = 20;
                $back = VENDA_DIRETA;
                $cor = '#fff';
            } elseif ($item == 0) {
                if (status_leiloes_aberto($value)) {
                    $situacao = 'Aberto';
                    $situacao_id = 1;
                    $back = ABERTO;
                    $cor = '#fff';
                }
            }

            $return = '<div situacao="' . $situacao_id . '">' . $situacao . '</div>';
            $return .= '<script> $("[situacao=' . $situacao_id . ']").parent().parent().parent().css("background", "' . $back . '").css("border-bottom-color", "' . iff($back != '#fff', $back, '#ccc') . '").css("color", "' . $cor . '"); </script> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Status
        } elseif ($col[0] == 'status') { // *status
            $return = $item ? '<i class="di fz16 c_verde fa fa-check"></i>' : '<i class="di fz16 n_ativo fa fa-check"></i>';
            $return1 = $item ? 'Ativo' : 'Bloqueado';
            $script = (isset($_POST['oTable']) and $_POST['oTable'] == '_boxs') ? '<script> setTimeout(function(){ $(".datatable_boxs .td_0").parent().hide(); $(".datatable_boxs .th_0").hide(); }, 0.5); </script>' : '';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'datatable_acoes(' . A . 'block' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')';
            $row_atual['onclick_class'] = 'dib pt5 pb5 pl10 pr10';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $return1;



            // Status1
        } elseif ($col[0] == 'status1') { // *status1
            $return = $item ? '<i class="di fz16 c_verde fa fa-check"></i>' : '<i class="di fz16 n_ativo fa fa-check"></i>';
            $return1 = $item ? 'Ativo' : 'Bloqueado';

            $row_atual['tags'] = $info;
            $row_atual['onclick'] = 'datatable_acoes(' . A . 'block1' . A . ', ' . A . $modulos->id . A . ', ' . A . $value->id . A . ')';
            $row_atual['onclick_class'] = 'dib pt5 pb5 pl10 pr10';
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $return1;



            // Ordem
        } elseif ($col[0] == 'ordem') { // *ordem
            $boxs = '';
            if (isset($_POST['rand'])) {
                $boxs = 'onkeyup="enter(' . A . 'datatable_ordenar_boxs(e)' . A . ', event, this)" modulos="' . $modulos->id . '" rand="ordemm_' . $_POST['rand'] . '" ';
            }
            $return = '<span class="dni">' . $item . '</span> <input type="text" name="ordem[' . $value->id . ']" id="ordem_' . $value->id . '" size="1" class="w60 datatable_ordem pl10 pr10 design tac" value="' . $item . '" maxlength="3" onclick="this.select()" ' . $boxs . ' /> ';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $item;



            // Input
        } elseif ($col[1] == 'input') {
            $boxs = '';
            $return = '<span class="dni">' . $item . '</span> <input type="text" name="input[' . $col[0] . '][' . $value->id . ']" id="input_' . $col[1] . '_' . $value->id . '" size="1" class="datatable_ordem pl10 pr10 design tac" value="' . $item . '" maxlength="3" onclick="this.select()" /> ' . $col[2];

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $item . $col[2];



            // Icon
        } elseif ($col[1] == 'icon') {
            $return = $item ? '	<i class="fa fa-' . $item . ' fz16"></i> ' : '';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = $item;



            // Txt
        } elseif ($col[0] == 'txt' or $col[0] == 'txt1' or $col[0] == 'txt2') {
            if ($col[1] == 'msg') {
                $return = '<div> ' . $item . '</div> ';
            } else {
                $return = '<div rel="tooltip" data-original-title="' . $item . '"> ' . limit($item, 70) . '</div> ';
                $return .= '<script> $("[rel=tooltip]").tooltip() </script> ';
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;


            // Imagem
        } elseif ($col[0] == 'foto' or $col[1] == 'foto') { // *foto
            $foto = '...';
            if ($item and file_exists('../../../../web/fotos/' . $item)) {
                if (preg_match('(.swf)', strtolower($item))) {
                    $return = $foto = 'flash';
                } elseif (preg_match('(.png)', strtolower($item)) or preg_match('(.bmp)', strtolower($item))) {
                    $foto = 'web/fotos/' . $item;
                    $return = '<img src="' . DIR . '/' . $foto . '" class="img" />';
                } else {
                    $img = new Imagem();
                    $img->caminho = '../../../../web/fotos';
                    $img->foto = $col[0];
                    $img->img($value, 100, 100);
                    $nome_da_foto = nome_da_foto($item);
                    $foto = 'web/fotos/' . $item;
                    $foto_exportar = 'web/fotos/' . $item;
                    $return = '<img src="' . DIR . '/' . $foto . '" class="img" />';
                }
            } else {
                $return = '<img src="' . DIR . '/web/img/outros/sem_img.jpg" class="img" />';
            }

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;
            $row_atual['exportar'] = isset($foto_exportar) ? $foto_exportar : $foto;



            // Nome Financeiro
        } elseif ($value->table == 'financeiro' and isset($value->parcela_atual) and $value->parcela_atual) {
            $return = $item . ' <span class="p1 pl3 pr3 ml2 bd19 c_999 br2">' . $value->parcela_atual . '</span>';

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Id
        } elseif ($col[0] == 'id') { // *id
            $return = '#' . $item;

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;



            // Nome
        } else { // *nome
            $return = $item;

            // Campo de Observacoes
            if ($modulos->modulo == 'leiloes' or $modulos->modulo == 'lotes') {
                $quebra_linha = '
';
                $return .= '<script> $(".td_datatable[dir=' . $value->id . ']").parent().parent().attr("rel", "tooltip").attr("data-original-title", "' . str_replace($quebra_linha, " ", $value->obs) . '") </script> ';
                $return .= '<script> $("[rel=tooltip]").tooltip() </script> ';
            }
            // Campo de Observacoes

            $row_atual['tags'] = $info;
            $row_atual['value'] = $return;


            // PADROES
            // EXTRAS
            if ($table == 'financeiro') {
                if ($value->pago)
                    $financeiro['saldo_pago'] += $value->preco;
                else
                    $financeiro['saldo_nao_pago'] += $value->preco;
                $financeiro['saldo'] += $value->preco;
            }
            // EXTRAS
        }
        // COLUNAS
        // ROWS
        if (isset($row_atual['tags'])) {
            foreach ($row_atual['tags'] as $k => $v) {
                if ($v)
                    $row[$k_col][$k] = $v;
            }
        }
        if (isset($row_atual['onclick'])) {
            $row[$k_col]['onclick'] = $row_atual['onclick'];
            $row[$k_col]['onclick_class'] = isset($row_atual['onclick_class']) ? $row_atual['onclick_class'] : '';
        }
        if (isset($row_atual['span'])) {
            $row[$k_col]['span'] = $row_atual['span'];
        }

        if (isset($row_atual['exportar']))
            $row[$k_col]['exportar'] = sem('tags', $row_atual['exportar']);

        $row[$k_col]['value'] = $row_atual['value'];
        // ROWS
    }
    $arr["data"][] = $row;
    // INFOS DA TABLE
}


// DADOS
// ----------------------------------------------------------------------------------------------------------------------------------------------------
// Saidas
$arr["oTable"] = isset($_POST['oTable']) ? $_POST['oTable'] : '';
if (!isset($_POST['row'])) {
    $arr["draw"] = intval($_POST['draw']);
    $arr["recordsTotal"] = intval($consulta['total']);
    $arr["recordsFiltered"] = intval($consulta['total_filtrado']);
}

// Gravar dados no banco
if ($arr["oTable"] == '_gravar_datatable') {
    $arr['gravar_dados_table'] = $_POST['gravar_dados_table'];
}

// Html
$arr["html"] = isset($arr["html"]) ? $arr["html"] : ' ';
if ($table == 'pedidos') {
    if (isset($preco['valor_total'])) {
        $arr["html"] .= '<div class="tar p20 fz20 fwb">Valor Total: ' . preco($preco['valor_total'], 1) . '</div>';
    }
}


include_once 'scripts/func_ajax_fim.php';



echo json_encode($arr);
