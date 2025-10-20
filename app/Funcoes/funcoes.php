<?php

// NOVOS
// STATUS LEILOES
function status_leiloes($tipo)
{
    $mysql = new Mysql();

    $ids = array(0);
    $filtro_lotes = '';
    $return = ' AND 1=2';

    $praca1_nao_comecou = " `data_ini` BETWEEN ('" . date('c') . "') AND ('4000-12-31') ";
    $praca1_ja_comecou = " `data_ini` BETWEEN ('0') AND ('" . date('c') . "') ";
    $praca1_ja_terminou = " `data_fim` BETWEEN ('0') AND ('" . date('c') . "') ";
    $praca2_nao_comecou = " `data_ini1` BETWEEN ('" . date('c') . "') AND ('4000-12-31') AND lance_min1 > 0 ";
    $praca2_ja_comecou = " `data_ini1` BETWEEN ('0') AND ('" . date('c') . "') AND lance_min1 > 0 ";
    $praca2_ja_terminou = " `data_fim1` BETWEEN ('0') AND ('" . date('c') . "') AND lance_min1 > 0 ";

    // Em Loteamento
    if ($tipo == 0) {
        $mysql->colunas = "id";
        $mysql->filtro = " WHERE (" . $praca1_nao_comecou . ") OR (" . $praca1_ja_comecou . " AND " . $praca1_ja_terminou . " AND " . $praca2_nao_comecou . ") ";
        $lotes = $mysql->read('lotes');
        foreach ($lotes as $key => $value) {
            $ids[] = $value->id;
        }
        $return = " AND situacao = 0 AND `id` IN (" . implode(',', $ids) . ") ";

        // Aberto
    } elseif ($tipo == 1) {
        $mysql->colunas = "id";
        $mysql->filtro = " WHERE (" . $praca1_ja_comecou . " AND !(" . $praca1_ja_terminou . ") ) OR ( " . $praca1_ja_comecou . " AND " . $praca1_ja_terminou . " AND " . $praca2_ja_comecou . " AND !(" . $praca2_ja_terminou . ") ) ";
        $lotes = $mysql->read('lotes');
        foreach ($lotes as $key => $value) {
            $ids[] = $value->id;
        }
        $return = " AND situacao = 0 AND `id` IN (" . implode(',', $ids) . ") ";

        // Outros
    } else {
        $return = " AND situacao = '" . $tipo . "' ";
    }

    return $return;
}

function status_leiloes_aberto($value)
{
    $return = 0;
    $praca1_ja_comecou = $value->data_ini < date('Y-m-d H:i:s');
    $praca1_ja_terminou = $value->data_fim < date('Y-m-d H:i:s');
    $praca2_ja_comecou = $value->data_ini1 < date('Y-m-d H:i:s') and $value->lance_min1 > 0;
    $praca2_ja_terminou = $value->data_fim1 < date('Y-m-d H:i:s') and $value->lance_min1 > 0;
    if (($praca1_ja_comecou and !($praca1_ja_terminou)) or ($praca1_ja_comecou and $praca1_ja_terminou and $praca2_ja_comecou and !($praca2_ja_terminou))) {
        $return = 1;
    }
    return $return;
}

// STATUS LEILOES
// BUSCA REFINADA
function busca_url($valor = '', $tirar = '', $url_fixa = '')
{
    $url = explode('?', $_SERVER['REQUEST_URI']);
    $gets = isset($url[1]) ? $url[1] : '';
    $url = $url_fixa ? $url_fixa : $url[0];
    $url = DIR . '/lotes/';
    $array = array();
    $gets = $valor ? $gets . '&' . $valor : $gets;
    $ex = explode('&', $gets);
    foreach ($ex as $key => $value) {
        $ex1 = explode('=', $value);
        $ex1[1] = isset($ex1[1]) ? $ex1[1] : '';
        $kk = str_replace('[]', '[' . urldecode($ex1[1]) . ']', $ex1[0]);
        $array[$kk] = $value;
    }
    if ($tirar) {
        $ex1 = explode('=', $tirar);
        $ex1[1] = isset($ex1[1]) ? $ex1[1] : '';
        $kk = str_replace('[]', '[' . $ex1[1] . ']', $ex1[0]);
        unset($array[$kk]);
    }
    $return = $url . '?' . implode('&', $array);
    return $return;
}

function busca_selecionado($id, $val, $icon)
{
    $return = '';
    if (isset($_GET[$val]) and is_array($_GET[$val])) {
        foreach ($_GET[$val] as $key1 => $value1) {
            if ($id == $value1) {
                if ($icon) {
                    $return = '<a href="' . busca_url('', $val . '[]=' . $id) . '" class="posa t13 r3"><i class="fa fa-times p3 pl5 pr5 cor_AE1C20 bd_ccc back_fff hoverr-op8 br50p"></i></a> ';
                } else {
                    $return = '<i class="fa fa-check cor_468C00"></i> ';
                }
            }
        }
    }
    return $return;
}

function qtd_itens($value, $colunas = 0, $parentes = 1)
{
    $mysql = new Mysql();
    $mysql->colunas = 'id';
    if ($colunas) {
        if ($colunas == 'categorias') {
            $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND `leiloes` IN ( SELECT `id` FROM `leiloes` WHERE " . STATUS . " ) AND ( `categorias` = " . $value . " OR `categorias` IN (SELECT `id` FROM `lotes1_cate` WHERE " . STATUS . " AND `subcategorias` = '" . $value . "' ) ) ";
        } else {
            $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND `" . $colunas . "` = '" . $value . "' AND `leiloes` IN ( SELECT `id` FROM `leiloes` WHERE " . STATUS . " ) ";
        }
    } else {
        $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND `leiloes` IN ( SELECT `id` FROM `leiloes` WHERE " . STATUS . " AND `" . $value->table . "` = " . $value->id . " ) ";
    }
    if ($parentes) {
        $return = count($mysql->read('lotes'));
    } else {
        $return = count($mysql->read('lotes'));
    }
    return $return;
}

function qtd_itens_status($value)
{
    $mysql = new Mysql();
    $filtro_lotes = status_leiloes($value->id);
    $mysql->filtro = " WHERE " . STATUS . " " . $filtro_lotes . " ";
    $return = $mysql->read('lotes');
    return '(' . count($return) . ')';
}

// BUSCA REFINADA
// LEILOES
function leiloes($leiloes, $lotes = array())
{
    $ids_leiloes = '';
    foreach ($leiloes as $key => $value) {
        $ids_leiloes .= $value->id . '-';
    }
    $ids_lotes = '';
    foreach ($lotes as $key => $value) {
        $ids_lotes .= $value->id . '-';
    }
    $return = "	<script> atualizar_leiloes('" . $ids_leiloes . "', '" . $ids_lotes . "'); </script>";
    return $return;
}

function lances_dado($value)
{
    $return = 0;
    $mysql = new Mysql();
    $mysql->colunas = 'lance';
    $mysql->filtro = " where " . STATUS . " AND id = '" . $value->id . "' ";
    $lotes = $mysql->read_unico('lotes');
    if (isset($lotes->lance) and $lotes->lance) {
        $return++;
    }
    $mysql->filtro = " where lotes = '" . $value->id . "' ";
    $lotes_lances = $mysql->read('lotes_lances');
    foreach ($lotes_lances as $key => $value) {
        $return++;
    }
    return $return;
}

function data_cronometro($data)
{
    $ex = explode('-', data($data, 'Y-m-d-H-i-s'));
    $return = A . $ex[0] . A . ',' . A . $ex[1] . A . ',' . A . $ex[2] . A . ',' . A . $ex[3] . A . ',' . A . $ex[4] . A . ',' . A . $ex[5] . A;
    return $return;
}

function ids($data)
{
    $return = array();
    $ex = explode('-', $data);
    foreach ($ex as $key => $value) {
        if ($value) {
            $return[] = " id = '" . $value . "' ";
        }
    }
    return $return;
}

function praca($value)
{
    $return = 1;
    if ($value->data_fim > date('Y-m-d H:i:s')) {
        $return = 1;
    } elseif ($value->lance_min1 > 0) {
        $return = 2;
    }
    return $return;
}

function praca_leiloes($lotes)
{
    $return = 'Praça única';
    $praca1 = 0;
    $praca2 = 0;
    foreach ($lotes as $key => $value) {
        if ($value->data_fim > date('Y-m-d H:i:s') and $value->lance_min1 > 0) {
            $praca1 += 1;
        } elseif ($value->lance_min1 > 0) {
            $praca2 += 1;
        }
    }
    if ($praca2) {
        $return = '2ª Praça';
    } elseif ($praca1) {
        $return = '1ª Praça';
    }
    return $return;
}

function plaquetas($plaquetas)
{
    $lances_plaquetas = $plaquetas ? rel('leiloes_plaquetas', $plaquetas, 'cadastro', 0, 0, 'nome') : 0;
    $lances_plaquetas = rel('cadastro', $lances_plaquetas, 'login');
    $return = str_pad($plaquetas, 2, 0, STR_PAD_LEFT) . iff($lances_plaquetas, ' (' . $lances_plaquetas . ')');
    return $return;
}

function tipo_lance($plaquetas)
{
    if ($plaquetas) {
        $return = 'Platéia';
    } else {
        $return = 'On Line';
    }
    return $return;
}

function lances_cadastro($cadastro, $plaquetas)
{
    if ($cadastro) {
        $return = rel('cadastro', $cadastro, 'login');
    } else {
        $return = 'Nº ' . plaquetas($plaquetas);
    }
    return $return;
}

function comitentes($ids, $coluna = 'nome')
{
    $mysql = new Mysql();

    $ex = ex($ids);
    $mysql->filtro = " WHERE " . STATUS . " AND id IN (" . implode(',', $ex) . ") ORDER BY " . ORDER . " ";
    $comitentes = $mysql->read('comitentes');

    $return = array();
    foreach ($comitentes as $key => $value) {
        $return[] = $value->$coluna;
    }
    return implode(',', $return);
}

// INFORMACOES
function leiloes_e_lotes_star($leiloes, $lotes)
{
    $array = array();
    foreach ($leiloes as $key => $value) {
        $array[$value->data_ini . '-leiloes-' . $value->id][$key] = $value;
    }
    foreach ($lotes as $key => $value) {
        $array[$value->data_ini . '-lotes-' . $value->id][$key] = $value;
    }
    ksort($array);

    $return = array();
    foreach ($array as $key => $value) {
        foreach ($value as $key1 => $value1) {
            $return[] = $value1;
        }
    }
    return $return;
}

function tipo_box($value)
{
    $mysql = new Mysql();
    if ($value->table == 'leiloes') {
        $mysql->colunas = "id";
        $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND leiloes = '" . $value->id . "' ";
        $lotes = $mysql->read('lotes');
        if ($lotes) {
            $return = count($lotes) == 1 ? $lotes[0]->id : 'leilao_' . $value->id;
        }
    } elseif ($value->table == 'lotes') {
        $mysql->colunas = "id";
        $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND id = '" . $value->id . "' ";
        $lotes = $mysql->read_unico('lotes');
        if (isset($lotes->id)) {
            $return = $lotes->id;
        }
    }
    $return = isset($return) ? $return : 0;
    return $return;
}

// INFORMACOES
// LEILOES
// NOVOS
// EMAILS
// Leilao Arrematado
function email_leilao_arrematado($lote, $cadastro)
{
    email_leilao($lote, $cadastro, 52);
}

// Leilao Nao Arrematado
function email_leilao_nao_arrematado($lote, $cadastro)
{
    email_leilao($lote, $cadastro, 54);
}

// Leilao Condicional
function email_leilao_condicional($lote, $cadastro)
{
    email_leilao($lote, $cadastro, 53);
}

// Email Leilao
function email_leilao($lote, $cadastro, $enviar_email)
{
    $mysql = new Mysql();

    $mysql->colunas = 'id, nome, email';
    $mysql->filtro = " WHERE id = '" . $cadastro . "' ";
    $cadastro = $mysql->read_unico('cadastro');

    $mysql->colunas = 'id, nome, ordem, leiloes, lances, lances_data, cidades, estados';
    $mysql->filtro = " WHERE id = '" . $lote . "' ";
    $lotes = $mysql->read_unico('lotes');

    $mysql->colunas = 'id, nome, comitentes, data_ini';
    $mysql->filtro = " WHERE id = '" . $lotes->leiloes . "' ";
    $leiloes = $mysql->read_unico('leiloes');


    if (isset($cadastro->id) and isset($lotes->id)) {
        $mysql->filtro = " WHERE `id` = " . $enviar_email . " ";
        $textos = $mysql->read_unico('textos');
        $var_email = 'nome->' . $cadastro->nome . '&-&email->' . $cadastro->email;
        $var_email .= '&-&nome_lote->' . $lotes->nome . '&-&numero_lote->' . ((int) $lotes->ordem) . '&-&cidade->' . $lotes->cidades . '&-&estado->' . $lotes->estados;
        $var_email .= '&-&comitente->' . comitentes($leiloes->comitentes) . '&-&valor->' . preco($lotes->lances) . '&-&comissao->' . preco(($lotes->lances) * 5 / 100);
        $var_email .= '&-&data_leilao->' . data($leiloes->data_ini) . '&-&hora_leilao->' . data($leiloes->data_ini, 'H:i') . '&-&data_arrematacao->' . data($lotes->lances_data, 'd/m/Y H:i');

        if ($enviar_email == 52) {
            $var_email .= '&-&recibo->' . DIR_C . '/imprimir/recibo_provisorio/' . $lotes->id;
        }

        $email = new Email();
        $email->to = $cadastro->email;
        $email->remetente = nome_site();
        $email->assunto = var_email($textos->nome, $var_email, 1);
        $email->txt = var_email(txt($textos), $var_email, 1);
        $email->enviar();
    }
}

// EMAILS
// ESPECIFICIDADE DOS LEILOES
function atualizar_datas_leiloes($filtro = "")
{
    $mysql = new Mysql();
    $data_ini = '';
    $data_fim = '';

    $mysql->colunas = "id";
    $mysql->filtro = $filtro;
    $leiloes = $mysql->read('leiloes');

    foreach ($leiloes as $key => $value) {

        // Data Ini
        // 1 Praca
        $mysql->colunas = "id, data_ini";
        $mysql->filtro = " WHERE " . STATUS . " " . SITUACAO . " AND leiloes = '" . $value->id . "' ORDER BY data_ini ASC ";
        $lotes = $mysql->read_unico('lotes');
        if (isset($lotes->data_ini)) {
            $data_ini = $lotes->data_ini;
            // 2 praca
        } else {
            $mysql->colunas = "id, data_ini1";
            $mysql->filtro = " WHERE " . STATUS . " " . SITUACAO . " AND lance_min1 > 0 AND leiloes = '" . $value->id . "' ORDER BY data_ini1 ASC ";
            $lotes = $mysql->read_unico('lotes');
            if (isset($lotes->data_ini1)) {
                $data_ini = $lotes->data_ini1;
            }
        }
        // Data Ini
        // Data Fim
        // 1 Praca
        $mysql->colunas = "id, data_fim";
        $mysql->filtro = " WHERE " . STATUS . " " . SITUACAO . " AND leiloes = '" . $value->id . "' ORDER BY data_fim DESC ";
        $lotes = $mysql->read_unico('lotes');
        if (isset($lotes->data_fim)) {
            $data_fim = $lotes->data_fim;
            // 2 praca
        } else {
            $mysql->colunas = "id, data_fim1";
            $mysql->filtro = " WHERE " . STATUS . " " . SITUACAO . " AND lance_min1 > 0 AND leiloes = '" . $value->id . "' ORDER BY data_fim1 DESC ";
            $lotes = $mysql->read_unico('lotes');
            if (isset($lotes->data_fim1)) {
                $data_ini = $lotes->data_fim1;
            }
        }
        // Data Fim

        if ($data_ini and $data_fim) {
            $mysql->campo['data_ini'] = $data_ini;
            $mysql->campo['data_fim'] = $data_fim;
            $mysql->filtro = " where id = '" . $value->id . "' ";
            $mysql->update('leiloes');
        }
    }
}

// ESPECIFICIDADE DOS LEILOES
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// TITULOS
// Titulos
function titulos($item)
{
    $voltar = '<li class="dib">';
    $voltar .= '<a onclick="history.go(-1);" class="cor_005e91"><i class="fa fa-chevron-circle-left"></i>&nbsp; Voltar &nbsp; | &nbsp; </a>';
    $voltar .= '</li>';

    $home = '<li class="dib">';
    $home .= '<a href="<?=DIR?>/home/" class="cor_005e91" title="Página Principal">Início</a>';
    $home .= '<span class="dib pl5 pr5">&raquo;</span>';
    $home .= '</li> ';

    if (isset($item->nome) and $item->nome) {
        $pai = '<li class="dib">';
        $pai .= '<a href="' . DIR . '/' . $item->table . '" class="cor_005e91">' . ucfirst($item->table) . '</a>';
        $pai .= '<span class="dib pl5 pr5">&raquo;</span>';
        $pai .= '</li>';
    }

    if (isset($_GET['categorias']) and $_GET['categorias'] != '-') {
        $cate = 1;
        $item = (object) array();
        $item->categorias = $_GET['categorias'];
        $item->table = $_GET['pg'];
    }

    if (isset($item->categorias) and $item->categorias) {
        $categorias = '';
        $sub = rel($item->table . '1_cate', $item->categorias, 'subcategorias');
        if ($sub) {
            $categorias .= '<li class="dib">';
            $categorias .= '<a href="' . DIR . '/' . $item->table . '/-/-/' . $sub . '" class="cor_005e91">' . rel($item->table . '1_cate', $sub) . '</a>';
            $categorias .= '<span class="dib pl5 pr5">&raquo;</span>';
            $categorias .= '</li>';
        }
        $categorias .= '<li class="dib">';
        $categorias .= '<a href="' . DIR . '/' . $item->table . '/-/-/' . $item->categorias . '" class="cor_005e91">' . rel($item->table . '1_cate', $item->categorias) . '</a>';
        $categorias .= !isset($cate) ? '<span class="dib pl5 pr5">&raquo;</span>' : '';
        $categorias .= '</li>';
    }

    $nome = ucfirst($_GET['pg']);
    if (isset($item->nome) and $item->nome) {
        $nome = $item->nome;
    } elseif ($_GET['pg'] == 'fale') {
        $nome = 'Fale Conosco';
    }
    $pagina = '<li class="dib">';
    if (isset($cate)) {
        $pagina .= '<a href="' . DIR . '/' . $item->table . '/" class="cor_005e91">' . $nome . '</a>';
        $pagina .= '<span class="dib pl5 pr5">&raquo;</span>';
    } else {
        $pagina .= '<a href="" class="cor_005e91"><b>' . $nome . '</b></a>';
    }
    $pagina .= '</li>';

    $return = $voltar . $home;
    $return .= (isset($pai) and $pai) ? $pai : '';
    $return .= (!isset($cate) and isset($categorias)) ? $categorias : '';
    $return .= $pagina ? $pagina : '';
    $return .= isset($cate) ? $categorias : '';
    return $return;
}

// TITULOS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// BANNERS E GALERIAS
// IMAGEM BANNER
function banner($value, $width, $height, $classe)
{
    $img = new Imagem();
    $classe_desk = $classe;
    if (isset($value->foto1) and $value->foto1) {
        if (preg_match('(class=")', $classe)) {
            $classe_desk = str_replace('class="', 'class="desk ', $classe);
        }
    }
    $img->tags = $classe_desk;
    $return = $img->img($value, $width, $height);

    $classe_mob = $classe;
    if (isset($value->foto1) and $value->foto1) {
        if (preg_match('(class=")', $classe)) {
            $classe_mob = str_replace('class="', 'class="mob ', $classe);
        }
        $img->tags = $classe_mob;
        $img->foto = 'foto1';
        $return .= $img->img($value, $width, $height);
    }
    return $return;
}

// IMAGEM BANNER
// PRODUTO Galeria (Galeria)
function Galeria_Produtos($item, $width, $height, $n = 1, $link = 0, $zoom = 0, $nome = 0, $mais_fotos = '')
{
    $img = new Imagem();
    $mais_fotos = $mais_fotos ? $mais_fotos : mais_fotos($item);
    $return = '';

    $width_min = 93;
    $height_min = 60;

    $return .= '<div class="Galeria_Produtos_Img_Maior">';
    $return .= '<div class="tac br4 bd_e5e5e5">';
    $return .= '<figure>';
    $return .= '<a ' . iff($link, 'href="' . DIR . '/web/fotos/' . iff($item->foto100, $item->foto100, $item->foto) . '" data-imagelightbox="b"') . ' class="db tac">';
    $return .= '<img src="' . DIR . '/web/fotos/' . $item->foto . '" class="db max-w100p m-a br4 ' . iff($zoom, 'Plugin_Zoom" data-zoom-image="' . DIR . '/web/fotos/' . iff($item->foto100, $item->foto100, $item->foto) . '', '') . '" style="max-width:' . $width . 'px; max-height:' . $height . 'px;" > ';
    $return .= '</a> ';
    $return .= '</figure>';
    $return .= '<img src="' . DIR . '/web/fotos/' . $item->foto . '" class="dn" > ';
    if ($mais_fotos) {
        $return .= '<div class="dni">';
        foreach ($mais_fotos as $key => $value) {
            $return .= '<a ' . iff($link, 'href="' . DIR . '/web/fotos/' . $value->foto . '" data-imagelightbox="b"') . '>';
            $return .= '<img src="' . DIR . '/web/fotos/' . $value->foto . '" > ';
            $return .= '</a> ';
        }
        $return .= '</div>';
    }
    $return .= '</div>';

    $return .= '<div class="Plugin_CSS_galeriaa no_pagg">';
    $return .= '<ul class="' . iff((count($mais_fotos) + 1) > $n, 'Plugin2') . '" min="1" max="1000" auto="0" vertical="0" itens="0" start="0"> ';
    $return .= '<li class="' . ($width_min + 14) . ' dib p7 vat tac c-p" onclick="Img_Maior(this)" ' . iff($link, 'link="1"') . ' ' . iff($zoom, 'zoom="1"') . ' src="' . DIR . '/web/fotos/' . $item->foto . '"> ';
    $img->tags = ' class="w' . $width_min . ' h' . $height_min . '" ';
    $return .= $img->img($item, $width_min, $height_min);
    $return .= $nome ? '<div class="pt4 fz12 cor_fff">' . $item->nome . '</div>' : '';
    $return .= '</li> ';
    if ($mais_fotos) {
        foreach ($mais_fotos as $key => $value) {
            $return .= '<li class="' . ($width_min + 14) . ' dib p7 vat tac c-p" onclick="Img_Maior(this)" ' . iff($link, 'link="1"') . ' ' . iff($zoom, 'zoom="1"') . ' src="' . DIR . '/web/fotos/' . $value->foto . '"> ';
            $img->tags = ' class="w' . $width_min . ' h' . $height_min . '" ';
            $return .= $img->img($value, $width_min, $height_min);
            $return .= $nome ? '<div class="pt4 fz12 cor_fff">' . $value->nome . '</div>' : '';
            $return .= '</li> ';
        }
    }
    $return .= '</ul> ';
    $return .= '</div> ';
    $return .= '</div> ';
    return $return;
}

// PRODUTO Galeria (Galeria)
// BANNER Galeria
function Galeria_Img_Maior($item, $width, $height, $link = 0, $mais_fotos = '')
{
    $img = new Imagem();
    $mais_fotos = $mais_fotos ? $mais_fotos : mais_fotos($item);
    $return = '';

    $return .= '<a ' . iff($link, 'href="' . DIR . '/web/fotos/' . $item->foto . '" data-imagelightbox="b"') . ' class="db tac">';
    $return .= '<img src="' . DIR . '/web/fotos/' . $item->foto . '" class="db m-a" style="max-width:' . $width . 'px; max-height:' . $height . 'px;" > ';
    $return .= '</a> ';
    if ($mais_fotos) {
        foreach ($mais_fotos as $key => $value) {
            $return .= '<a ' . iff($link, 'href="' . DIR . '/web/fotos/' . $value->foto . '" data-imagelightbox="b"') . '  class="db tac">';
            $return .= '<img src="' . DIR . '/web/fotos/' . $value->foto . '" class="db m-a" style="max-width:' . $width . 'px; max-height:' . $height . 'px;" > ';
            $return .= '</a> ';
        }
    }
    return $return;
}

function Galeria_Img_Menor($item, $n = 1, $width = 100, $height = 60, $nome = 0, $mais_fotos = '')
{
    $img = new Imagem();
    $mais_fotos = $mais_fotos ? $mais_fotos : mais_fotos($item);
    $return = '';

    $return .= '<ul class="' . iff((count($mais_fotos) + 1) > $n, 'Plugin2') . '" min="1" max="1000" auto="0" vertical="0" itens="0" start="0"> ';
    $return .= '<li class="w120 dib p10 vat tac c-p" onclick="Plugin1_a(0)"> ';
    $img->tags = ' class="w100 h60 bdw3 bd_fff" ';
    $return .= $img->img($item, 100, 60);
    $return .= $nome ? '<div class="pt4 fz12 cor_fff">' . $item->nome . '</div>' : '';
    $return .= '</li> ';
    if ($mais_fotos) {
        foreach ($mais_fotos as $key => $value) {
            $return .= '<li class="w120 dib p10 vat tac c-p" onclick="Plugin1_a(' . ($key + 1) . ')"> ';
            $img->tags = ' class="w100 h60 bdw3 bd_fff" ';
            $return .= $img->img($value, 100, 60);
            $return .= $nome ? '<div class="pt4 fz12 cor_fff">' . $value->nome . '</div>' : '';
            $return .= '</li> ';
        }
    }
    $return .= '</ul> ';
    return $return;
}

// BANNER Galeria
// BANNERS E GALERIAS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// NUMEROS
// Preco
function preco($valor, $sedula = 0, $casas = 2, $sinal = ',', $sinal1 = '.', $nao_mostrar_zero = 0)
{

    $valor = str_replace(',', '.', $valor);
    $valor = floatval($valor);
    $casas = (int) $casas ? (int) $casas : 2;
    $valor = number_format($valor, $casas, $sinal, $sinal1);

    if ($nao_mostrar_zero) {
        $valor = str_replace($sinal1, '', $valor);
        $valor = str_replace($sinal, '.', $valor);
        $valor = $nao_mostrar_zero ? (float) $valor : $valor;
        $valor = str_replace('.', $sinal, $valor);
    }

    $return = $sedula ? 'R$&nbsp;' . $valor : $valor;
    return $return;
}

// Preco Verificado
function preco1($valor, $sedula = 0, $casas = 2, $sinal = ',', $sinal1 = '.', $nao_mostrar_zero = 0)
{
    $return = $valor > 0 ? preco($valor, $sedula, $casas, $sinal, $sinal1, $nao_mostrar_zero) : '(Sob Consulta)';
    return $return;
}

function preco2($valor, $sedula = 0, $casas = 2, $sinal = ',', $sinal1 = '.', $nao_mostrar_zero = 0)
{
    $return = preco($valor, $sedula, $casas, $sinal, $sinal1, $nao_mostrar_zero);
    $return = str_replace(',00', '', $return);
    return $return;
}

// Numero
function numero($valor, $casas = 2, $sinal = '.')
{
    $valor = str_replace(',', '.', $valor);
    $valor = floatval($valor);
    $casas = (int) $casas ? (int) $casas : 2;
    $return = number_format($valor, $casas, $sinal, '');
    return $return;
}

// Valor Por Extenso
function extenso($valor = 0, $complemento = true)
{
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

    $z = 0;
    $rt = '';


    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;) 
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        if ($complemento == true) {
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++;
            elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
        }
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    return ($rt ? $rt : "zero");
}

// Retornar Numero
function retornar_numero($numero)
{
    $return = trim($numero);
    $return = str_replace('-', '', $return);
    $return = str_replace(',', '', $return);
    $return = str_replace('.', '', $return);
    return $return;
}

// NUMEROS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// DATAS
// Dividir Data
function dividir_data($data, $tipo = '-')
{
    $data = str_replace(' ', $tipo, $data);
    $data = str_replace(':', $tipo, $data);
    $data = explode($tipo, $data);
    $data[3] = isset($data[3]) ? $data[3] : 0;
    $data[4] = isset($data[4]) ? $data[4] : 0;
    $data[5] = isset($data[5]) ? $data[5] : 0;
    return $data;
}

// Datas
function data($data, $condicao = 'd/m/Y', $tipo = '-')
{
    $data = str_replace('T', $tipo, $data);
    $data = is_object($data) ? $data->data : $data;
    $data = dividir_data($data, $tipo);
    $return = isset($data[2]) ? date($condicao, mktime($data[3], $data[4], $data[5], $data[1], $data[2], $data[0])) : 'erro';
    return $return;
}

// Somar Datas
function somar_datas($data, $ano, $mes, $dia, $hora = 0, $min = 0, $seg = 0, $condicao = 'Y-m-d', $tipo = '-')
{
    $data = dividir_data($data, $tipo);
    if (isset($data[2])) {
        $data = ($data[0] + $ano) . '/' . ($data[1] + $mes) . '/' . ($data[2] + $dia) . ' ' . ($data[3] + $hora) . ':' . ($data[4] + $min) . ':' . ($data[5] + $seg);
        $return = data($data, $condicao, '/');
    } else
        $return = 'erro';
    return $return;
}

function somar_data($n, $tipo)
{
    if ($tipo == 'ano')
        $data = (date('Y') + $n) . date('/m/d H:i:s');
    else if ($tipo == 'mes')
        $data = date('Y/') . (date('m') + $n) . date('/d H:i:s');
    else if ($tipo == 'dia')
        $data = date('Y/m/') . (date('d') + $n) . date(' H:i:s');
    else if ($tipo == 'hora')
        $data = date('Y/m/d ') . (date('H') + $n) . date(':i:s');
    else if ($tipo == 'min')
        $data = date('Y/m/d H:') . (date('i') + $n) . date(':s');
    else if ($tipo == 'seg')
        $data = date('Y/m/d H:i:') . (date('s') + $n);
    $data = data($data, 'Y-m-d H:i:s', '/');
    return $data;
}

// Subtrair Data
function sub_data($data1, $data2, $tipo = '-')
{
    $data1 = dividir_data($data1, $tipo);
    $data2 = dividir_data($data2, $tipo);
    $seg1 = isset($data1[2]) ? mktime($data1[3], $data1[4], $data1[5], $data1[1], $data1[0], $data1[2]) : 'erro';
    $seg2 = isset($data2[2]) ? mktime($data2[3], $data2[4], $data2[5], $data2[1], $data2[0], $data2[2]) : 'erro';
    $segs = $seg1 - $seg2;
    $return = array('dias' => '0', 'hora' => '00', 'min' => '00', 'seg' => '00', 'hora_total' => '00', 'seg_total' => '00');
    if ($segs > 0) {
        // Segundos
        $data_s = date('s', mktime(0, 0, $segs, 0, 0, 0));
        $return['seg'] = $data_s;

        // Minutos
        $data_i = date('i', mktime(0, 0, $segs, 0, 0, 0));
        $return['min'] = $data_i;

        // Horas
        $data_h = date('H', mktime(0, 0, $segs, 0, 0, 0));
        $seg_d = ($data_h * 60 * 60) + ($data_i * 60) + $data_s;

        $return['hora'] = $data_h;

        // Dias
        $data_d = ($segs - 86400) > 0 ? (($segs - $seg_d) / 86400) : 0;
        $return['dias'] = str_pad($data_d, 2, 0, STR_PAD_LEFT);

        // Horas Total
        $data_ht = (($data_d * 24) + $data_h);
        $return['hora_total'] = str_pad($data_ht, 2, 0, STR_PAD_LEFT);

        // Segs Total
        $return['seg_total'] = $segs;
    }
    return $return;
}

// Data -> Seg
function data_seg($data, $tipo = '-')
{
    $data = str_replace(' ', '-', $data);
    $data = str_replace('T', '-', $data);
    $data = str_replace('/', '-', $data);
    $data = str_replace(':', '-', $data);
    $data = dividir_data($data, $tipo);
    $return = isset($data[2]) ? mktime($data[3], $data[4], $data[5], $data[1], $data[0], $data[2]) : 'erro';
    return $return;
}

// Segundos para Hora
function seg_hora($seg, $condicao)
{
    return (data('0000-00-00 00:00:' . $seg, $condicao));
}

// Dia da semana
function dia_semana($data, $tipo = '-')
{
    $data = explode($tipo, $data);
    $dia = (int) $data[0];
    $mes = (int) $data[1];
    $ano = (int) $data[2];
    $diasemana = date("w", mktime(0, 0, 0, (int) $mes, (int) $dia, (int) $ano));

    switch ($diasemana) {
        case 0:
            $diasemana = "Domingo";
            break;
        case 1:
            $diasemana = "Segunda";
            break;
        case 2:
            $diasemana = "Terça";
            break;
        case 3:
            $diasemana = "Quarta";
            break;
        case 4:
            $diasemana = "Quinta";
            break;
        case 5:
            $diasemana = "Sexta";
            break;
        case 6:
            $diasemana = "Sábado";
            break;
    }

    return ($diasemana);
}

// Mês abreviasao
function mes($mes, $ab = 0)
{
    $return = '';
    switch ($mes) {
        case 1:
            ($return = $ab ? 'Jan' : 'Janeiro');
            break;
        case 2:
            ($return = $ab ? 'Fev' : 'Fevereiro');
            break;
        case 3:
            ($return = $ab ? 'Mar' : 'Março');
            break;
        case 4:
            ($return = $ab ? 'Abr' : 'Abril');
            break;
        case 5:
            ($return = $ab ? 'Mai' : 'Maio');
            break;
        case 6:
            ($return = $ab ? 'Jun' : 'Junho');
            break;
        case 7:
            ($return = $ab ? 'Jul' : 'Julho');
            break;
        case 8:
            ($return = $ab ? 'Ago' : 'Agosto');
            break;
        case 9:
            ($return = $ab ? 'Set' : 'Setembro');
            break;
        case 10:
            ($return = $ab ? 'Out' : 'Outubro');
            break;
        case 11:
            ($return = $ab ? 'Nov' : 'Novembro');
            break;
        case 12:
            ($return = $ab ? 'Dez' : 'Dezembro');
            break;
    }
    return $return;
}

// DATAS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// FILTROS SITE
// Filtro tags dinamicas
function filtro($tipo)
{
    $return = '';
    $ex = explode('-', $tipo);
    foreach ($ex as $key => $value) {
        if (isset($_GET[$value]) and $_GET[$value] != '-') {
            $_GET[$value] = cod('html->asc', $_GET[$value]);
            $return .= " AND `" . $value . "` = '" . $_GET[$value] . "' ";
        }
    }
    return $return;
}

// filtro Tags Fixas
function filtro_fixo($tipo)
{
    $return = '';
    $ex = explode('-', $tipo);
    foreach ($ex as $key => $value) {
        if ($value == 'categorias' and isset($_GET['categorias']) and $_GET['categorias'] != '-') {
            if (isset($_GET['tipo']) and $_GET['tipo'] == 'sub') {
                $return .= " AND (" . categorias_subcategorias($_GET['categorias']) . ") ";
            } else {
                $return .= " AND `categorias` = '" . $_GET['categorias'] . "' ";
            }
        } else {
        }
    }
    return $return;
}

// Filtro Busca
function filtro_busca($colunas = 'nome')
{
    if (isset($_GET['busca']) and $_GET['busca'] == 'Buscar')
        $_GET['busca'] = '';
    if (isset($_POST['busca']) and $_POST['busca'] == 'Buscar')
        $_POST['busca'] = '';

    if (isset($_GET['pag'])) {
        if (isset($_SESSION['pesq_session_filtro'])) {
            $_GET['busca'] = $_SESSION['pesq_session_filtro'];
        }
    }
    unset($_SESSION['pesq_session_filtro']);

    $return = '';
    if (isset($_POST['busca']) or isset($_GET['busca'])) {
        if (isset($_POST['busca']))
            $item = trim($_POST['busca']);
        if (isset($_GET['busca']))
            $item = trim($_GET['busca']);
        $_SESSION['pesq_session_filtro'] = $_GET['busca'] = $item;

        $item = cod('html->asc', $item);

        $fitro = array();
        $ex = explode(',', $colunas);
        foreach ($ex as $key => $value) {
            if ($item) {
                $fitro[] = " `" . trim($value) . "` REGEXP \"" . cod('busca', $item) . "\" OR `" . trim($value) . "` LIKE concat('%', '" . $_GET['busca'] . "', '%') ";
            }
        }

        $return = $fitro ? " AND (" . implode(' OR ', $fitro) . ") " : '';
    }
    return $return;
}

// FILTROS SITE
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// UTEIS
// Lang
$langs = array();
$caminho = '';
if (LANG == 1) {
    $caminho = caminho(DIR . '/web/img/z_leilao/Lang/default.json');
}
if (LANG == 2) {
    $caminho = caminho(DIR . '/web/img/z_leilao/Lang/ingles.json');
}
if (LANG == 3) {
    $caminho = caminho(DIR . '/web/img/z_leilao/Lang/espanhol.json');
}
if (file_exists($caminho)) {
    $langs = json_decode(file_get_contents($caminho));
}

function lang($palavra)
{
    global $langs;
    $return = $palavra;
    foreach ($langs as $key => $value) {
        if ($palavra == $key and $value) {
            $return = $value;
        }
    }
    return $return;
}

// Ativo
function ativo($item, $id = '', $ativo = 'active')
{
    $return = '';
    if (preg_match('(' . $item . ')', $_GET['pg']) and ($id == '' or $_GET['id'] == $id))
        $return = $ativo;
    return $return;
}

// Iff
function iff($condicao, $resp1 = '', $resp2 = '')
{
    $return = $condicao ? $resp1 : $resp2;
    return $return;
}

// Clear
function clear($key, $itens)
{
    $key = $key + 1;
    $return = !($key % $itens) ? '<div class="clear"></div>' : '';
    return $return;
}

// Clear1
function clear1($array, $key, $n, $tags)
{
    $return = '';
    $key = $key + 1;
    if ($key == 1 or ($key % $n)) {
        $return = '<div ' . $tags . '>';
    }
    return $return;
}

// Clear2
function clear2($array, $key, $n)
{
    $return = '';
    $key = $key + 1;
    if (!($key % $n) or count($array) == $key) {
        $return = '</div><div class="clear"></div>';
    }
    return $return;
}

// li1
function li1($array, $key, $n, $tags = '')
{ // li1($chamadas, $key, 3, '')
    $return = '';
    $key = $key;
    if (!($key % $n)) {
        $return = '<li ' . $tags . ' >';
    }
    return $return;
}

// li2
function li2($array, $key, $n)
{ // li2($chamadas, $key, 3)
    $return = '';
    $key = $key + 1;
    if (!($key % $n) or count($array) == $key) {
        $return = '</li> ';
    }
    return $return;
}

// Ex
function ex($item, $delimiter = '-', $ini = 1)
{
    $return = $ini ? array(0) : array();
    $ex = explode($delimiter, $item);
    foreach ($ex as $key => $value) {
        if ($value)
            $return[] = $value;
    }
    return $return;
}

// Explode Ini e Fim
function ex_ini($delimiter, $string)
{
    $ex = explode($delimiter, $string);
    $return = array(1 => '');
    foreach ($ex as $key => $value) {
        if ($key) {
            $return[1] .= $value;
        } else {
            $return[0] = $value;
        }
    }
    return $return;
}

function ex_fim($delimiter, $string)
{
    $ex = explode($delimiter, $string);
    $return = array('');
    foreach ($ex as $key => $value) {
        if (isset($ex[$key + 1])) {
            $return[0] .= $value;
        } else {
            $return[1] = $value;
        }
    }
    return $return;
}

// Itens
function itens($item, $delimiter = '-')
{
    $itens = ex($item, $delimiter = '-');
    $return = implode(',', $itens);
    return $return;
}

// Color
function color($cor)
{
    $return = str_replace('#', '', $cor);
    return $return;
}

// Telefone
function tel($numero)
{
    $return = $numero;
    if (strlen($numero) > 14) {
        $numero = str_replace('-', '', $numero);
        $ini = substr($numero, 0, 6);
        $center = substr($numero, 6, 4);
        $fim = substr($numero, -4);
        $return = $ini . ' ' . $center . '-' . $fim;;
    }
    return $return;
}

// Telefone Dados
function tel_ddd($numero)
{
    $return = entre('(', ')', $numero);
    return $return;
}

function tel_numero($numero)
{
    $return = explode(')', $numero);
    $return = trim($return[1]);
    $return = str_replace('-', '', $return);
    return $return;
}

// Cep Dados
function cep_numero($numero)
{
    $return = str_replace('.', '', $numero);
    $return = str_replace('-', '', $return);
    return $return;
}

// Like
function like($item)
{
    return " LIKE concat('%', '" . $item . "', '%') ";
}

// Mapa
function mapa($width, $height, $value = '')
{
    $mysql = new Mysql();
    $return = '';
    if ($value) {
        $mysql->colunas = 'nome, maps_lat, maps_lng, maps_zoom';
        $mysql->prepare = array($value->id);
        $mysql->filtro = " WHERE `id` = ? ";
        $mapa = $mysql->read_unico($value->table);
    } else {
        $mysql->colunas = 'nome, maps_lat, maps_lng, maps_zoom';
        $mysql->filtro = " WHERE `tipo` = 'mapa' ";
        $mapa = $mysql->read_unico('configs');
    }
    if ($mapa->maps_lat and $mapa->maps_lng) {
        $return = '<div> ' .
            '<iframe src="' . DIR . '/plugins/Google/Maps/maps.php?lat=' . $mapa->maps_lat . '&lon=' . $mapa->maps_lng . '&zoom=' . iff($mapa->maps_zoom, $mapa->maps_zoom, 18) . '&nome=' . $mapa->nome . '" ' .
            'width="' . $width . '" height="' . $height . '" background="no" scrolling="No" marginwidth="0" marginheight="0" frameborder="0"></iframe> ' .
            '</div> ';
    } elseif (isset($mapa->rua) and $mapa->rua) {
        $return = '<div> ' .
            '<iframe src="' . DIR . '/plugins/Google/Maps/maps.php?endereco=' . $mapa->rua . '+' . $mapa->cidades . '+' . $mapa->estados . '&zoom=18&nome=' . $mapa->nome . '" ' .
            'width="' . $width . '" height="' . $height . '" background="no" scrolling="No" marginwidth="0" marginheight="0" frameborder="0"></iframe> ' .
            '</div> ';
    }
    return $return;
}

// Mapa
function mapaa($url, $width, $height)
{
    $return = $url;
    return $return;
}

// Como Chegar
function como_chegar($endereco)
{
    $return = 'https://maps.google.com/maps?f=d&daddr=' . $endereco . '+Brasil';
    return $return;
}

// Mapa Lat Long
function mapa_google($endereco)
{
    $data['address'] = urlencode($endereco);
    $data['components'] = 'country:BR';
    $data['sensor'] = 'false';
    $data = http_build_query($data);
    $url = 'http://maps.google.com/maps/api/geocode/json';
    $curl = curl_init($url . '?' . $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resultado = json_decode(curl_exec($curl));
    curl_close($curl);
    $dados['lat'] = $resultado->results[0]->geometry->location->lat;
    $dados['lon'] = $resultado->results[0]->geometry->location->lng;
    return ($dados);
}

// Geomapeamento
function geomapeamento($geocode)
{
    $return = array('rua' => '', 'numero' => '', 'bairro' => '', 'cidades' => '', 'estados' => '', 'pais' => '');
    $array = json_decode($geocode);
    foreach ($array->results[0]->address_components as $key => $value) {
        $return['rua'] = $value->types[0] == 'route' ? $value->long_name : '';
        $return['numero'] = $value->types[0] == 'street_number' ? $value->long_name : '';
        $return['bairro'] = $value->types[0] == 'political' ? $value->long_name : '';
        $return['cidades'] = $value->types[0] == 'locality' ? $value->long_name : '';
        $return['estados'] = $value->types[0] == 'administrative_area_level_1' ? $value->short_name : '';
        $return['pais'] = $value->types[0] == 'country' ? $value->long_name : '';
    }
    return $return;
}

// Email Personalizado
function var_email($txt, $var, $plano2 = 0)
{
    if ($plano2) {
        $ex = explode('&-&', $var);
    } else {
        $ex = explode('&', $var);
    }
    for ($i = 0; $i < count($ex); $i++) {
        $ex1 = explode('->', $ex[$i]);
        $txt = str_replace('{' . $ex1[0] . '}', $ex1[1], $txt);
    }
    return ($txt);
}

// Limitar Char
function limit($text, $limit, $ellipsis = '...')
{
    if (strlen($text) > $limit) {
        $text = trim(substr($text, 0, $limit));

        $limit_ult = htmlentities(substr($text, $limit - 1, 1));
        $a_com_tio = htmlentities(substr('ã', 0, 1));

        if (preg_match('(' . $a_com_tio . ')', $limit_ult)) {
            $text = substr($text, 0, -1);
        }

        $text .= $ellipsis;
    }

    return $text;
}

// Minusculo
function minusculo($txt)
{
    $trocarIsso = array('Â', 'â', 'Ê', 'ê', 'Î', 'î', 'Ô', 'ô', 'Û', 'û', 'Ã', 'ã', 'Õ', 'õ', 'Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'À', 'à', 'È', 'è', 'Ì', 'ì', 'Ò', 'ò', 'Ù', 'ù', 'Ç', 'ç',);
    $porIsso = array('(am->flex)', '(a->flex)', '(em->flex)', '(e->flex)', '(im->flex)', '(i->flex)', '(om->flex)', '(o->flex)', '(um->flex)', '(u->flex)', '(am->tio)', '(a->tio)', '(om->tio)', '(o->tio)', '(am->agudo)', '(a->agudo)', '(em->agudo)', '(e->agudo)', '(im->agudo)', '(i->agudo)', '(om->agudo)', '(o->agudo)', '(um->agudo)', '(u->agudo)', '(am->crase)', '(a->crase)', '(em->crase)', '(e->crase)', '(im->crase)', '(i->crase)', '(om->crase)', '(o->crase)', '(um->crase)', '(u->crase)', '(cm->cedilha)', '(c->cedilha)',);
    $txt = strtolower(str_replace($trocarIsso, $porIsso, $txt));
    $trocarIsso = array('(am->flex)', '(a->flex)', '(em->flex)', '(e->flex)', '(im->flex)', '(i->flex)', '(om->flex)', '(o->flex)', '(um->flex)', '(u->flex)', '(am->tio)', '(a->tio)', '(om->tio)', '(o->tio)', '(am->agudo)', '(a->agudo)', '(em->agudo)', '(e->agudo)', '(im->agudo)', '(i->agudo)', '(om->agudo)', '(o->agudo)', '(um->agudo)', '(u->agudo)', '(am->crase)', '(a->crase)', '(em->crase)', '(e->crase)', '(im->crase)', '(i->crase)', '(om->crase)', '(o->crase)', '(um->crase)', '(u->crase)', '(cm->cedilha)', '(c->cedilha)',);
    $porIsso = array('Â', 'â', 'Ê', 'ê', 'Î', 'î', 'Ô', 'ô', 'Û', 'û', 'Ã', 'ã', 'Õ', 'õ', 'Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'À', 'à', 'È', 'è', 'Ì', 'ì', 'Ò', 'ò', 'Ù', 'ù', 'Ç', 'ç',);
    $return = str_replace($trocarIsso, $porIsso, $txt);
    return ($return);
}

// Nome da Foto
function nome_da_foto($foto)
{
    $return = array();
    if ($foto) {
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $nome = explode('.' . $ext, $foto);
        $nomee = explode('_', $nome[0]);
        $return['nome'] = $nome[0];
        $return['ext'] = $ext;
        $return['nomee'] = str_replace('-', ' ', $nomee[0]);
    }
    return $return;
}

// Nome do site
function nome_site()
{
    $array = array('www.', '.com', '.br', '.net', '.org', ':4000');
    $nome = ucfirst(str_replace($array, '', $_SERVER['HTTP_HOST']));
    return ($nome);
}

// Produtos Fotos (Galeria)
function produtos_fotos_galeria($item, $classe = '', $mais_fotos = '', $link = 1, $zoom = 1, $n = 100)
{
    $img = new Imagem();
    $mais_fotos = $mais_fotos ? $mais_fotos : mais_fotos($item);

    $return = '<div class="img_maior galeria_' . $item->table . ' bd_ccc"> ';
    $return .= '<span class="db">  ';
    $return .= $link ? '<a href="' . DIR . '/web/fotos/' . $item->foto . '" data-imagelightbox="b"> ' : '';
    $return .= '<img src="' . DIR . '/web/fotos/' . $item->foto . '" class="db m-a ' . iff($zoom, 'Plugin_Zoom" data-zoom-image="' . DIR . '/web/fotos/' . $item->foto . '"', '"') . ' > ';
    $return .= $link ? '</a> ' : '';
    $return .= '</span> ';
    $return .= '</div> ';
    if ($mais_fotos) {
        $return .= '<ul class="h83 o-h img_menor ' . iff(count($mais_fotos) > 2, 'Plugin2') . '" min="1" max="' . $n . '" auto="0" vertical="0" itens="0" start="0"> ';
        $return .= '<li class="w107 ' . $classe . ' dib p6 tac c-p" ' . iff($link, 'link="1"') . ' ' . iff($zoom, 'zoom="1"') . ' src="' . DIR . '/web/fotos/' . $item->foto . '"> ';
        $return .= '<img src="' . DIR . '/web/fotos/' . $item->foto . '" class="w90 h60"> ';
        $return .= '</li> ';
        foreach ($mais_fotos as $key => $value) {
            $return .= '<li class="w107 ' . $classe . ' dib p6 tac c-p" ' . iff($link, 'link="1"') . ' ' . iff($zoom, 'zoom="1"') . ' src="' . DIR . '/web/fotos/' . $value->foto . '"> ';
            $return .= '<img src="' . DIR . '/web/fotos/' . $value->foto . '" class="w90 h60"> ';
            $return .= '</li> ';
        }
        $return .= '</ul> ';
        $return .= '<div class="dni"> ';
        foreach ($mais_fotos as $key => $value) {
            $img->link = 1;
            $return .= $img->img($value, 1, 1);
        }
        $return .= '</div> ';
    }

    return $return;
}

// Countt
function countt($value, $banco)
{
    $mysql = new Mysql();
    $mysql->logs = 0;
    $mysql->campo['count'] = $value->count + 1;
    $mysql->filtro = " where id = '" . $value->id . "' ";
    $mysql->update($banco);
}

// Inverter Key
function inverter_key($arary)
{
    $return = array();
    foreach ($arary as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $return[$k][$key] = $v;
            }
        } else
            $return[$key] = $value;
    }
    return $return;
}

// Degrade
function degrade($back1, $back2)
{
    $return = "filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='" . $back1 . "', endColorstr='" . $back2 . "');  background:-moz-linear-gradient(top, " . $back1 . ", " . $back2 . "); background:-webkit-gradient(linear, left top, left bottom, from(" . $back1 . "), to(" . $back2 . "));";
    return $return;
}

// Degrade
function degrade1($back1, $back2, $back3, $back4, $porc = 50)
{
    $return = "filter:background: " . $back1 . "; background: -moz-linear-gradient(top, " . $back1 . " 0%, " . $back2 . " " . $porc . "%, " . $back3 . " " . ($porc + 1) . "%, " . $back4 . " 100%); background: -webkit-linear-gradient(top, " . $back1 . " 0%," . $back2 . " " . $porc . "%," . $back3 . " " . ($porc + 1) . "%," . $back4 . " 100%);  background: linear-gradient(to bottom, " . $back1 . " 0%," . $back2 . " " . $porc . "%," . $back3 . " " . ($porc + 1) . "%," . $back4 . " 100%);  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $back1 . "', endColorstr='" . $back4 . "',GradientType=0 );";
    return $return;
}

// Nbsp
function nbsp($txt)
{
    $return = str_replace(' ', '-', $txt);
    return $return;
}

// Limpar Espacoes (Tabs)
function limpa_espacoes($array)
{
    $return = $array;
    foreach ($array as $key => $value) {
        $value = str_replace('          ', ' ', $value);
        $return[$key] = str_replace('   ', ' ', $value);
    }
    return $return;
}

// Palavra entre duas palavras
function entre($ini, $fim, $item)
{
    $ex = explode($ini, $item);
    $ex = isset($ex[1]) ? explode($fim, $ex[1]) : '';
    $return = isset($ex[0]) ? $ex[0] : '';
    return $return;
}

// Tirar Barras
function tirar_barras($item)
{
    $return = str_replace('[', '_', $item);
    $return = str_replace(']', '', $return);
    return $return;
}

// Verificar se tem ? na url
function interrogacao()
{
    $ex = explode('?', DIR_ALL);
    $return = isset($ex[1]) ? '&' : '?';
    return $return;
}

// Existe
function existe($variavel, $elemento)
{
    $return = '';
    if (is_array($variavel)) {
        $return = isset($variavel[$elemento]) ? $variavel[$elemento] : '';
    }
    if (is_object($variavel)) {
        $return = isset($variavel->$elemento) ? $variavel->$elemento : '';
    }
    return $return;
}

// Extensao
function extensao($arquivo)
{
    $return = substr($arquivo, -3);;
    return ($return);
}

// Location
function location($url)
{
    $url = $url ? $url : $_SERVER['REQUEST_URI'];
    header("Location: " . $url);
}

function location_js($url)
{
    $url = $url ? $url : $_SERVER['REQUEST_URI'];
    echo '<script> ';
    echo "window.parent.location='" . $url . "' ";
    echo '</script> ';
}

function location_txt($txt = 'Enviado com sucesso!', $url = '')
{
    $url = $url ? $url : $_SERVER['REQUEST_URI'];
    echo '<script> ';
    echo 'alert("' . $txt . '"); ';
    echo "window.parent.location='" . $url . "' ";
    echo '</script> ';
}

// Captcha
function captcha_confirmar()
{
    if (md5($_POST['ct_captcha']) == $_SESSION['randomnr2']) {
        return (1);
    } else {
        return (0);
    }
}

function captcha()
{
    $captcha = '<img src="' . DIR . '/plugins/Captcha/captcha.php" id="captcha_img" class="mr10" style="border:1px solid #000" />
							<a href="javascript:carregar_captcha_img()"><img src="' . DIR . '/plugins/Captcha/images/refresh.png" ></a>
							<script> function carregar_captcha_img(){ $("#captcha_img").attr("src", "' . DIR . '/plugins/Captcha/captcha.php"); } </script>
							<div style="h5"></div>
							<strong>' . lang('Digite o Código') . '*:</strong><br />
							<input type="text" name="ct_captcha" id="ct_captcha" class="w180 h20 tac fz16"  required="true" />';
    return ($captcha);
}

// Browser
function browser()
{
    $user_agente = $_SERVER["HTTP_USER_AGENT"];
    $Browser_Nome = strtok($user_agente, "/");
    $Browser_Versao = strtok(" ");
    if (preg_match("(MSIE)", $user_agente)) {
        $Browser_Nome = "Internet Explorer";
        $Browser_Versao = strtok("MSIE");
        $Browser_Versao = strtok(" ");
        $Browser_Versao = strtok(";");
        $return = "ie";
    } elseif (preg_match("(Firefox)", $user_agente)) {
        $return = "firefox";
    } elseif (preg_match("(Chrome)", $user_agente)) {
        $return = "chrome";
    } else {
        $return = "outros";
    }
    return $return;
}

// Firebox
function firefox()
{
    $user_agente = $_SERVER["HTTP_USER_AGENT"];
    $Browser_Nome = strtok($user_agente, "/");
    $Browser_Versao = strtok(" ");
    $return = 0;
    if (preg_match("(Firefox)", $user_agente)) {
        $return = 1;
    }
    return $return;
}

// firefox_calendar
function firefox_calendar($post)
{
    foreach ($post as $key => $value) {
        if ($key == 'calendar') {
            foreach ($value as $k => $v) {
                $ex = explode('/', $post[$k]);
                $post[$k] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
            }
        } else {
            $post[$key] = $value;
        }
    }
    return $post;
}

// Data Firefox
function data_firefox()
{
    foreach ($_POST as $key => $value) {
        if ($key == 'data_firefox') {
            foreach ($value as $k => $v) {
                $val = explode(';;x;;', $k);
                if (isset($val[1])) {
                    $ex = explode(' ', $_POST[$val[0]][$val[1]]);
                    $_POST[$val[0]][$val[1]] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
                } else {
                    $ex = explode('/', $_POST[$k]);
                    $_POST[$k] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
                }
            }
        }
        if ($key == 'datatime_firefox') {
            foreach ($value as $k => $v) {
                $val = explode(';;x;;', $k);
                if (isset($val[1])) {
                    $ex = explode(' ', $_POST[$val[0]][$val[1]]);
                    $ex_data = explode('/', $ex[0]);
                    $ex_hora = explode(':', $ex[1]);
                    $_POST[$val[0]][$val[1]] = $ex_data[2] . '-' . $ex_data[1] . '-' . $ex_data[0] . 'T' . $ex_hora[0] . ':' . $ex_hora[1];
                } else {
                    $ex = explode(' ', $_POST[$k]);
                    $ex_data = explode('/', $ex[0]);
                    if (isset($ex[1])) {
                        $ex_hora = explode(':', $ex[1]);
                        $_POST[$k] = $ex_data[2] . '-' . $ex_data[1] . '-' . $ex_data[0] . 'T' . $ex_hora[0] . ':' . $ex_hora[1];
                    } else {
                        $_POST[$k] = '';
                    }
                }
            }
        }
    }
    unset($_POST['data_firefox']);
    unset($_POST['datatime_firefox']);
}

// Txt
function txt($value, $tipo = 0)
{
    $return = '';
    $id = isset($value->table) ? $value->id : $value;
    $table = isset($value->table) ? $value->table : 'textos';
    $mysql = new Mysql();
    $mysql->colunas = "txt";
    $mysql->prepare = array($table, $id, $tipo);
    $mysql->filtro = " WHERE `tabelas` = ? AND `item` = ? AND `tipo` = ? ";
    $banco = $mysql->read("z_txt");
    foreach ($banco as $linhas) {
        $return = base64_decode($linhas->txt);
    }
    if ($return) {
        $return = '<div class="editor taj">' . $return . '</div>';
    }
    return $return;
}

// Selects Temp
function select2_temp($html)
{
    $return = str_replace("design", "z-design-z", $html);
    return $return;
}

// Set
function set($table, $id, $set)
{
    $mysql = new Mysql();
    $mysql->prepare = array($id);
    $mysql->filtro = " WHERE `id` = ? ";
    $linhas = $mysql->read_unico($table);
    $set_explode = explode('set[', $set);
    $setado = '';
    for ($i_set = 0; $i_set < count($set_explode); $i_set++) {
        $set_explode_explode = explode(']', $set_explode[$i_set]);
        if (isset($linhas->$set_explode_explode[0])) {
            $setado .= $linhas->$set_explode_explode[0];
        } else {
            $setado .= $set_explode_explode[0];
        }
        if (isset($set_explode_explode[1])) {
            $setado .= $set_explode_explode[1];
        }
    }
    return ($setado);
}

// Buscar Endereco
function busca_endereco($endereco)
{
    $geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $endereco . '&components=country:BR&sensor=true');
    $resultado = json_decode($geocode);

    $return = array('rua' => '', 'numero' => '', 'bairro' => '', 'cidade' => '', 'estado' => '', 'cep' => '', 'cep_prefix' => '', 'lat' => '', 'lng' => '');
    if (isset($resultado->results[0]->address_components) and $resultado->results[0]->address_components) {
        foreach ($resultado->results[0]->address_components as $key => $value) {
            if ($value->types[0] == 'route')
                $return['rua'] = $value->long_name;
            else if ($value->types[0] == 'street_number')
                $return['numero'] = $value->long_name;
            else if ($value->types[0] == 'neighborhood' or $value->types[0] == 'sublocality_level_1')
                $return['bairro'] = $value->long_name;
            else if ($value->types[0] == 'locality' or $value->types[0] == 'administrative_area_level_2')
                $return['cidade'] = $value->long_name;
            else if ($value->types[0] == 'administrative_area_level_1')
                $return['estado'] = $value->short_name;
            else if ($value->types[0] == 'postal_code')
                $return['cep'] = str_replace('-', '', $value->short_name);
            else if ($value->types[0] == 'postal_code_prefix')
                $return['cep_prefix'] = str_replace('-', '', $value->short_name);
        }
        $return['lat'] = isset($resultado->results[0]->geometry->location->lat) ? $resultado->results[0]->geometry->location->lat : 0;
        $return['lng'] = isset($resultado->results[0]->geometry->location->lng) ? $resultado->results[0]->geometry->location->lng : 0;
    }
    return $return;
}

// Rastreamento
function rastreamento($rastreamento)
{
    $data['Usuario'] = 'ECT';
    $data['Senha'] = 'SRO';
    $data['Tipo'] = 'L';
    $data['Resultado'] = 'U';
    $data['Objetos'] = $rastreamento;

    $curl = curl_init('http://websro.correios.com.br/sro_bin/sroii_xml.eventos');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    $xml = curl_exec($curl);
    curl_close($curl);
    $xml = simplexml_load_string($xml);

    return $xml->objeto->evento;
}

// Voucher
function voucher($tamanho = 10, $numeros = true, $simbolos = false)
{
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '12345678901234567890';
    $simb = '!@#$%*-';
    $return = '';
    $caracteres = '';
    $caracteres .= $lmai;
    if ($numeros)
        $caracteres .= $num;
    if ($simbolos)
        $caracteres .= $simb;
    $len = strlen($caracteres);
    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $return .= $caracteres[$rand - 1];
    }
    return $return;
}

// Include
function get_include_contents($filename)
{
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

// Nome do site
function select_tables($tables, $filtro, $coluna = 'id', $ordem = '')
{
    $mysql = new Mysql();
    $return = array();
    foreach ($tables as $key => $table) {
        $mysql->filtro = $filtro[$key];
        $consulta = $mysql->read($table);
        foreach ($consulta as $key => $value) {
            $return[$value->$coluna . '-' . $table . '-' . $value->id] = $value;
        }
    }
    if ($ordem == 'ASC') {
        ksort($return);
    } elseif ($ordem == 'DESC') {
        krsort($return);
    }
    return $return;
}

// UTEIS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// URL
// Http
function http($texto)
{
    $return = (!preg_match("(http)", $texto)) ? 'http://' . $texto : $texto;
    return $return;
}

// Url
function url($pg, $value, $categorias = 0)
{
    $mysql = new Mysql();
    $mysql->colunas = 'valor3';
    $mysql->filtro = " WHERE `lang` = '" . LANG . "' AND `tipo` = 'busca_google' ";
    $mais_url = $mysql->read_unico('configs');

    $categorias = ($categorias and $categorias == 'ok') ? $value->id : $categorias;
    $value = $value ? $value : (object) array('id' => '', 'nome' => '');

    $return = !$categorias ? DIR . '/' . $pg . '/' . sem('url', $value->nome . '-' . $mais_url->valor3) . '/' . $value->id . '/' : DIR . '/' . $pg . '/' . sem('url', $value->nome . '-' . $mais_url->valor3) . '/-/' . $categorias . '/';
    return $return;
}

// Url
function url_txt($item, $pg = 'textos')
{
    $return = DIR . '/' . $pg . '/' . amg($item) . '/' . $item . '/';
    return $return;
}

// Gets (Converte GET URL em GET ARRAY)
function gets($url)
{
    $return = array();
    $ex = explode('?', $url);
    if (isset($ex[1]) and $ex[1]) {
        $ex = explode(';;z;;', $ex[1]);
        foreach ($ex as $key => $value) {
            $ex1 = explode('=', $value);
            $return[$ex1[0]] = $value;
        }
    }
    return $return;
}

// Url Get (Substitui a GET pela VARIAVEL get)
function tirar_url($get = '', $url_fixa = '')
{
    $url = explode('?', $_SERVER['REQUEST_URI']);
    $gets = isset($url[1]) ? tirar_url1($url[1]) : array();
    $gets = tirar_url1($get, $gets);
    $url = $url_fixa ? $url_fixa : $url[0];
    $return = $url . '?' . implode('&', $gets);
    return $return;
}

function tirar_url1($url, $gets = array())
{
    $return = $gets;
    $ex = explode('&', $url);
    foreach ($ex as $key => $value) {
        $ex1 = explode('=', $value);
        $return[$ex1[0]] = $value;
    }
    return $return;
}

// Url Amigavel
function amg($id, $txt = '')
{
    $return = '-';
    $mysql = new Mysql();
    $mysql->colunas = 'nome';
    $mysql->prepare = array($id);
    $mysql->filtro = " WHERE `id` = ? ";
    foreach ($mysql->read('textos') as $key => $value) {
        $return = sem('url', $value->nome);
    }
    return $return;
}

// URL
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// FUNCOES RELACIONADAS TABLE
// Value
function value($array, $item, $outro_condicao = '')
{
    if (is_object($array)) {
        foreach ($array as $key => $value) {
            $array_temp[$key] = $value;
        }
        $array = $array_temp;
    }
    $return = isset($array[$item]) ? $array[$item] : $outro_condicao;
    return $return;
}

function value1($array, $elemento, $item, $outro_condicao = '')
{
    $return = array();
    if (isset($array[$elemento])) {
        $return = cod('html->asc', $array[$elemento]);
    }
    return (value($return, $item, $outro_condicao));
}

// Auto Complete
function datalist($array)
{
    $rand = rand();
    $return = 'list="datalist_' . $rand . '" />';
    $return .= '<datalist id="datalist_' . $rand . '"> ';
    foreach ($array as $key => $value) {
        $return .= '<option label="" value="' . $value->nome . '">' . $value->nome . '</option> ';
    }
    $return .= '</datalist> <p class="dni"></p';
    return $return;
}

// Option
function option($table, $array = array(), $item = '', $criar = 0, $filtro = '')
{
    $return = '';
    if (is_object($array)) {
        foreach ($array as $key => $value) {
            $array_temp[$key] = $value;
        }
        $array = $array_temp;
    }
    if ($criar) {
        $return .= ' <option value="" >- - -</option> ';
        $return .= '<optgroup label="Ações" id="acoes"> ';
        $return .= '<option value="(cn)">Cadastrar Novo</option> ';
        $return .= '<option value="(gi)">Gerenciar Itens</option> ';
        $return .= '</optgroup> ';
        $return .= '<optgroup label="Itens" id="itens"> ';
    }
    $mysql = new Mysql();
    $mysql->colunas = 'id, nome';
    $mysql->filtro = $filtro ? $filtro : ' WHERE `status` = 1 AND `lang` = "' . LANG . '" ';
    $consulta = $mysql->read($table);
    foreach ($consulta as $key => $value) {
        $return .= '<option value="' . $value->id . '" ';
        $return .= (isset($array[$item]) and $array[$item] == $value->id) ? 'selected' : '';
        $return .= '>' . $value->nome . '</option> ';
    }
    $return .= ($criar) ? '</optgroup> ' : '';
    return $return;
}

// Option banco
function option_banco($table, $filtro = '')
{
    $return = '';
    $mysql = new Mysql();
    $mysql->colunas = 'id, nome';
    $mysql->filtro = $filtro ? $filtro : ' WHERE `status` = 1 AND `lang` = "' . LANG . '" ORDER BY `nome` ASC ';
    $consulta = $mysql->read($table);
    foreach ($consulta as $key => $value) {
        $return .= $value->id . '->' . $value->nome . '; ';
    }
    return $return;
}

// Val
function val($id, $table = '', $coluna = '*')
{
    $mysql = new Mysql();
    $mysql->colunas = $coluna;
    $mysql->prepare = array($id);
    $mysql->filtro = " WHERE `id` = ? ";
    return ($mysql->read_unico($table));
}

// Rel
function rel($table, $categoria, $coluna = 'nome', $nenhum = 0, $nao_existe = 0, $key = 'id')
{
    $return = $nenhum ? $nenhum : '';
    $mysql = new Mysql();
    $mysql->nao_existe = $nao_existe;
    $mysql->colunas = $coluna;
    $mysql->prepare = array($categoria);
    $mysql->filtro = " WHERE `lang` = " . LANG . " AND `" . $key . "` = ? ";
    $consulta = $mysql->read($table);
    if (isset($consulta) and is_array($consulta)) {
        foreach ($consulta as $linhas) {
            $return = $linhas->$coluna;
        }
    }
    return $return;
}

// Rel Nome
function rel_nome($table, $categoria, $coluna = 'nome', $nenhum = 0, $nao_existe = 0)
{
    $return = $nenhum ? $nenhum : '';
    $mysql = new Mysql();
    $mysql->nao_existe = $nao_existe;
    $mysql->colunas = 'id';
    $mysql->prepare = array($categoria);
    $mysql->filtro = " WHERE `lang` = " . LANG . " AND `" . $coluna . "` = ? ";
    $consulta = $mysql->read($table);
    if (isset($consulta) and is_array($consulta)) {
        foreach ($consulta as $linhas) {
            $return = $linhas->id;
        }
    }
    return $return;
}

// Mais Fotos
function mais_fotos($value)
{
    $mysql = new Mysql();
    $return = array();
    if (isset($value->table) and isset($value->id)) {
        $mysql->prepare = array($value->table, $value->id);
        $mysql->filtro = " WHERE " . STATUS . " AND `tabelas` = ? AND `item` = ? ORDER BY `ordem` ASC, `id` DESC ";
        $return = $mysql->read("mais_fotos");
    }
    return $return;
}

// Estados
function estados($ab)
{
    $trocarIsso = array('AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO',);
    $porIsso = array('Acre', 'Alagoas', 'Amazonas', 'Amapá', 'Bahia', 'Ceará', 'Distrito Federal', 'Espírito Santo', 'Goiás', 'Maranhão', 'Minas Gerais', 'Mato Grosso do Sul', 'Mato Grosso', 'Pará', 'Paraíba', 'Pernambuco', 'Piauí', 'Paraná', 'Rio de Janeiro', 'Rio Grande do Norte', 'Rondônia', 'Roraima', 'Rio Grande do Sul', 'Santa Catarina', 'Sergipe', 'São Paulo', 'Tocantins',);
    $return = str_replace($trocarIsso, $porIsso, $ab);
    return $return;
}

// CÓDIGO NOVO E CORRETO
function caminho($file)
{
    // Remove a barra inicial se ela existir, para evitar caminhos duplicados como "//app"
    if (substr($file, 0, 1) === '/') {
        $file = substr($file, 1);
    }
    return DIR_F . '/' . $file;
}

// Star
function star($value, $tipo = '')
{
    $mysql = new Mysql();
    $mysql->colunas = 'nome, star';
    $mysql->prepare = array($value->table, $value->id);
    $mysql->filtro = " WHERE " . STATUS . " AND `tabelas` = ? AND `item` = ? ";
    $mais_star = $mysql->read("mais_comentarios");
    $array = array();
    $star = 0;
    $n_star = 0;
    foreach ($mais_star as $k => $v) {
        $n_star++;
        $star += $v->star;
        $array[] = $v->nome . ': ' . $v->star . ' estrelas';
    }
    $votacao = implode(' - ', $array);
    $total = $n_star ? $star / $n_star : 0;
    $total = $total < 0 ? 0 : $total;
    $total = $total > 5 ? 5 : $total;

    if (!$tipo) {
        $return = $total;
    } elseif ($tipo == '%') {
        $return = $total * 100 / 5;
    } elseif ($tipo == 'count') {
        $return = count($mais_star);
    } elseif ($tipo == 'votacao') {
        $return = $votacao;
    }
    return $return;
}

// Star Databale Ajax
function star_icon($total, $n = 5)
{
    $total = is_object($total) ? star($total) : $total;
    $return = '';
    for ($i = 0; $i < $n; $i++) {
        if ($total > 0) {
            if ($total < 1)
                $return .= ' <i class="fa fa-star-half-o c_amarelo"></i> ';
            else
                $return .= ' <i class="fa fa-star c_amarelo"></i> ';
        } else {
            $return .= ' <i class="fa fa-star-o c_amarelo"></i> ';
        }
        $total = $total - 1;
    }
    return $return;
}

// Sistema Mautic
function sistema_mautic()
{
    $return = '';
    $mysql = new Mysql();
    $mysql->colunas = 'valor';
    $mysql->filtro = " WHERE `tipo` = 'sistema_mautic' ";
    $sistema_mautic = $mysql->read_unico("configs");
    if ($sistema_mautic->valor) {
        $return .= '<style>.sistema_mautic { display: none !important; } </style> ';
        $return .= '<div class="sistema_mautic">' . cod('asc->html', $sistema_mautic->valor) . '</div>';
    }
    return $return;
}

// Letreiro
function letreiro($txt, $tags = '')
{
    $return = '<marquee ' . $tags . ' direction="LEFT" onmouseout="this.start()" onmouseover="this.stop()" scrollamount="6">' . $txt . '</marquee> ';
    return $return;
}

// Multifotos
function multifotos($value, $normal = 0)
{
    $return = (isset($value->multifotos) and $value->multifotos) ? unserialize(base64_decode($value->multifotos)) : array();
    if ($return and !$normal) {
        foreach ($return as $key => $value) {
            $array[$key] = (object) array();
            $array[$key]->foto = $value;
            $array[$key]->multifotos = 'ok';
        }
        $return = $array;
    }
    return $return;
}

// Categorias - Subcategorias
function categorias_subcategorias($id)
{
    $return = " (`categorias` = '" . $id . "' OR `categorias` IN (SELECT `id` FROM `lotes1_cate` WHERE " . STATUS . " AND `subcategorias` = '" . $id . "' )) ";
    return $return;
}

// SubCategorias
function subcategorias($value)
{
    $return = 0;
    if (isset($value->vcategorias)) {
        if ($value->tipo) {
            $ex = explode('-', $value->vcategorias);
            $return = isset($ex[count($ex) - 3]) ? $ex[count($ex) - 3] : 0;
        }
    } else {
        $ex = explode('-', $value);
        $return = isset($ex[count($ex) - 3]) ? $ex[count($ex) - 3] : 0;
    }
    return ($return . ' / ');
}

// VCategorias
function vcategorias($id)
{
    $mysql = new Mysql();
    $mysql->filtro = " WHERE " . STATUS . " AND `id` = '" . $id . "' ORDER BY " . ORDER . " ";
    $item = $mysql->read_unico('produtos1_cate');

    $return = "";
    if (isset($item->id)) {
        $categorias[] = $item->id;

        $mysql->filtro = " WHERE " . STATUS . " AND `subcategorias` = '" . $item->id . "' ORDER BY " . ORDER . " ";
        $sub = $mysql->read('produtos1_cate');
        foreach ($sub as $k => $v) {
            $categorias[] = $v->id;
        }
        $return = " AND ( ";
        foreach ($categorias as $k => $v) {
            $return .= $k ? 'OR' : '';
            $return .= " `vcategorias` " . like('-' . $v . '-') . " ";
        }
        $return .= " ) ";
    }

    return $return;
}

// Numero Categorias
function numero_categorias($value)
{
    $mysql = new Mysql();
    $mysql->prepare = array($value->id);
    $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `categorias` = ? and " . VERIFICACAO_PRODUTOS . " ";
    $produtos = $mysql->read('produtos');
    $return = count($produtos);

    $mysql->prepare = array($value->id);
    $mysql->filtro = "  WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `subcategorias` = ? ";
    $produtos1_cate = $mysql->read('produtos1_cate');
    foreach ($produtos1_cate as $linhas) {
        $mysql->prepare = array($linhas->id);
        $mysql->filtro = "  WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `categorias` = ? and " . VERIFICACAO_PRODUTOS . " ";
        $produtos = $mysql->read('produtos');
        $return += count($produtos);
    }
    return $return;
}

// Numero VCategorias
function numero_vcategorias($value)
{
    $mysql = new Mysql();
    $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `vcategorias` " . like('-' . $value->id . '-') . " AND " . VERIFICACAO_PRODUTOS . " ";
    $produtos = $mysql->read('produtos');
    $return = count($produtos);

    $mysql->prepare = array($value->id);
    $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `subcategorias` = ? ";
    $produtos1_cate = $mysql->read('produtos1_cate');
    foreach ($produtos1_cate as $linhas) {
        $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `vcategorias` " . like('-' . $linhas->id . '-') . " AND " . VERIFICACAO_PRODUTOS . " ";
        $produtos = $mysql->read('produtos');
        $return += count($produtos);
    }
    return $return;
}

// FUNCOES RELACIONADAS TABLE
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// VIDEOS
// Player
function player($video, $width, $height, $foto = '')
{
    $return = '';
    if ($video) {
        if (extensao($video) == 'mp4') {
            $return = '	<video width="' . $width . '" height="' . $height . '" id="player1"  class="back_000" 
										src="' . DIR . '/web/fotos/' . $video . '" type="video/' . extensao($video) . '" 
										' . iff($foto, 'poster="' . DIR . '/web/fotos/' . $foto . '" preload="none"') . '
										controls="controls">
									</video> ';
        } elseif (extensao($video) == 'swf') {
            $return = '	<video controls>
										<object src="' . DIR . '/web/fotos/' . $video . '"
										  type="application/x-shockwave-flash"
										  width="500" height="396"
										  allowscriptaccess="always"
										  allowfullscreen="true"/>
									</video> ';
        } else {
            $return = 'Player Não Suporta Formado Do Video!';
        }
    }
    return ($return);
}

// Video
function video($url, $width, $height)
{
    $return = '';
    $url = is_object($url) ? $url->link : $url;
    $img = explode('v=', $url);
    $img = isset($img[1]) ? explode('&', $img[1]) : '';
    if (isset($img[0]) and $img[0]) {
        $return = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $img[0] . '" frameborder="0" allowfullscreen></iframe> '; //?autoplay=1
    }
    return ($return);
}

// Video Img
function video_img($url, $width = 60, $height = 60)
{
    $url = is_object($url) ? $url->link : $url;
    if ($url) {
        $img = explode('v=', $url);
        $img = explode('&', $img[1]);
        $img = explode('#', $img[0]);
        $img = $img[0];

        $tamanho = '';
        if ($width and $height)
            $tamanho = ' width="' . $width . '" height="' . $height . '" ';

        $return = '<div class="posr">';
        $return .= '	<span class="play1_youtube"></span>';
        $return .= '	<span class="play2_youtube"></span>';
        $return .= '	<img src="http://i.ytimg.com/vi/' . $img . '/mqdefault.jpg" ' . $tamanho . ' class="w100p" />';
        //$return .= '	<img src="http://i.ytimg.com/vi/'.$img.'/default.jpg" '.$tamanho.' />';
        //$return .= '	<img src="http://i1.ytimg.com/vi/'.$img.'/mqdefault.jpg" '.$tamanho.' />';
        //$return .= '	<img src="http://i2.ytimg.com/vi/'.$img.'/mqdefault.jpg" '.$tamanho.' />';
        //$return .= '	<img src="http://i3.ytimg.com/vi/'.$img.'/mqdefault.jpg" '.$tamanho.' />';
        //$return .= '	<img src="http://i4.ytimg.com/vi/'.$img.'/mqdefault.jpg" '.$tamanho.' />';
        $return .= '</div>';

        return ($return);
    }
}

// Flash
function flash($width, $height, $caminho)
{

    $flash = '<object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' . $width . '" height="' . $height . '">
								<param name="movie" value="' . $caminho . '" />
								<param name="quality" value="high" />
								<param name="wmode" value="transparent" />
								<param name="swfversion" value="6.0.65.0" />
								<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you dont want users to see the prompt. -->
								<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
								<!--[if !IE]>-->
								<object type="application/x-shockwave-flash" data="' . $caminho . '" width="' . $width . '" height="' . $height . '">
								  <!--<![endif]-->
								  <param name="quality" value="high" />
								  <param name="wmode" value="opaque" />
								  <param name="swfversion" value="6.0.65.0" />
								  <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
								  <!--[if !IE]>-->
								</object>
								<!--<![endif]-->
							  </object>';


    return ($flash);
}

// VIDEOS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// CARRINHO
function verificar_estoque_cores_tamanhos($id, $cores, $tamanhos)
{
    $return = array();
    $mysql = new Mysql();
    $mysql->coluna = 'id, estoque, preco';
    $mysql->prepare = array($id, $cores, $tamanhos);
    $mysql->filtro = " WHERE " . STATUS . " AND `produtos` = ? AND `produtos_cores` = ? AND `produtos_tamanhos` = ? ORDER BY " . ORDER . " ";
    $consulta = $mysql->read_unico('produtos_cores_tamanhos');
    if (isset($consulta->estoque)) {
        $return['id'] = $consulta->id;
        $return['estoque'] = $consulta->estoque;
        $return['preco'] = $consulta->preco;
    } else {
        $return['null'] = 1;
    }
    return $return;
}

function verificar_estoque_opcoes($id, $opcao, $n)
{
    $return = array();
    $mysql = new Mysql();
    $mysql->coluna = 'id, estoque, preco';
    $mysql->prepare = array($id, $opcao);
    $mysql->filtro = " WHERE " . STATUS . " AND `produtos` = ? AND `id` = ? ORDER BY " . ORDER . " ";
    $consulta = $mysql->read_unico('produtos_opcoes' . $n);
    if (isset($consulta->estoque)) {
        $return['id'] = $consulta->id;
        $return['estoque'] = $consulta->estoque;
        $return['preco'] = $consulta->preco;
    } else {
        $return['null'] = 1;
    }
    return $return;
}

// CARRINHO
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// ESTATISTICAS
// Pedidos na Home do Admin
function pedidos_dados($tipo, $n)
{
    $mysql = new Mysql();
    $ped = array(0);

    $filtro = '';
    if ($n and $n == 'all') {
        $filtro = " ";
    } elseif ($n and $n == 'hj') {
        $filtro = " AND `data` BETWEEN ('" . date('Y-m-d') . "') AND ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))) . "') ";
    } elseif ($n and $n == 'ot') {
        $filtro = " AND `data` BETWEEN ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))) . "') AND ('" . date('Y-m-d') . "') ";
    } elseif ($n and $n == 'dois') {
        $filtro = " AND `data` BETWEEN ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 2, date('Y'))) . "') AND ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))) . "') ";
    } elseif ($n and $n == 'tres') {
        $filtro = " AND `data` BETWEEN ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'))) . "') AND ('" . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 2, date('Y'))) . "') ";
    } else {
        $filtro = " AND `data` BETWEEN ('" . date('Y-' . $n . '-01') . "') AND ('" . date('Y-' . ($n + 1) . '-01') . "') ";
    }

    $mysql->filtro = " WHERE (`situacao` = 1 OR `situacao` >= 100) " . $filtro . " ";
    $pedidos = $mysql->read('pedidos');
    $qtd = 0;
    $total = 0;
    foreach ($pedidos as $key => $value) {
        $qtds = explode('-', $value->qtds);
        foreach ($qtds as $k => $v) {
            $qtd += $v;
        }
        $total += $value->valor_total;
    }

    if ($tipo == 'qtd') {
        $return = $qtd;
    } elseif ($tipo == 'media') {
        $return = $qtd ? preco($total / $qtd, 1) : preco(0, 1);
    } elseif ($tipo == 'valor') {
        $return = preco($total, 1);
    }
    return $return;
}

// ESTATISTICAS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// PLUGINS
// Banner Revolution
function revolution()
{
    //$transition = array('boxslide', 'boxfade', 'slotzoom-horizontal', 'slotslide-horizontal', 'slotfade-horizontal', 'slotzoom-vertical', 'slotslide-vertical', 'slotfade-vertical', 'curtain-1', 'curtain-2', 'curtain-3', 'slideleft', 'slideright', 'slideup', 'slidedown', 'fade', 'random', 'slidehorizontal', 'slidevertical', 'papercut', 'flyin', 'turnoff', 'cube', '3dcurtain-vertical', '3dcurtain-horizontal');
    $transition = array('slideleft');
    shuffle($transition);
    return ('data-transition="' . $transition[0] . '" data-slotamount="7" data-masterspeed="800" data-target="_blank"');
}

// PLUGINS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// REDES SOCIAIS E COMPARTILHAMENTOS
// Rede Social
function rede($w = 16, $h = 16)
{
    $return = '<div class="redes_sociais addthis_toolbox addthis_default_style ">
							<a class="addthis_button_preferred_1"></a>
							<a class="addthis_button_preferred_3"></a>
							<a class="addthis_button_preferred_2"></a>
							<a class="addthis_button_preferred_4"></a>
							<a class="addthis_button_compact"></a>
						</div>							
						<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4d7ff3113d47df6d"></script> ';
    return $return;
};

function rede1($w = 16, $h = 16)
{
    $return = '<div class="rede rede1 addthis_toolbox addthis_default_style addthis_' . $w . 'x' . $h . '_style">
								<a class="dt fln addthis_button_facebook"></a>
								<a class="dt fln addthis_button_twitter"></a>
								<a class="dt fln addthis_button_email"></a>
								<a class="dt fln addthis_button_print dni"></a>
								<a class="dt fln addthis_button_compact"></a>
							</div>
							<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
							<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4facc88d029b64fa"></script> ';
    return $return;
};

// FACEBOOK -> https://developers.facebook.com/docs/plugins/like-button
// Script
function facebook_script()
{
    $return = '	<div id="fb-root"></div>
								<script>(function(d, s, id) {
								  var js, fjs = d.getElementsByTagName(s)[0];
								  if (d.getElementById(id)) return;
								  js = d.createElement(s); js.id = id;
								  js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.6&appId=1023643787709692";
								  fjs.parentNode.insertBefore(js, fjs);
								}(document, "script", "facebook-jssdk"));</script> ';
    return $return;
};

// Curtir 
function facebook_curtir($url)
{
    $return = facebook_script() . '
							  <div class="fb-like" data-href="' . $url . '" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div> ';
    return $return;
};

// Compartinhar
function facebook_compartilhar($url = '')
{
    $url = $url ? $url : DIR_C . DIR_ALL;
    $return = facebook_script() . '
							  <div class="fb-share-button" data-href="' . $url . '" data-layout="button" data-mobile-iframe="true"></div> ';
    return $return;
};

function facebook_compartilhar_iframe($url = '')
{
    $url = $url ? $url : DIR_C . DIR_ALL;
    $return = '<iframe src="https://www.facebook.com/plugins/share_button.php?href=' . $url . '&layout=button&mobile_iframe=true&appId=1023643787709692&width=57&height=20" width="57" height="20" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe> ';
    return $return;
};

// Postar
function facebook_postar($url = '')
{
    $url = $url ? $url : DIR_C . DIR_ALL;
    $return = facebook_script() . '
							  <div class="fb-send" data-href="' . $url . '"></div> ';
    return $return;
};

// Box
function facebook($url, $width = '500', $height = '300')
{
    $return = facebook_script() . '
							  <div class="fb-page" data-href="' . $url . '" data-width="' . $width . '" data-height="' . $height . '" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="' . $url . '"><a href="' . $url . '">' . $url . '</a></blockquote></div></div> ';
    return $return;
}

function facebook_box($url, $width = '500', $height = '300')
{
    return facebook($url, $width, $height);
}

// Login
function facebook_login($direcionar)
{
    $return = '<script> ';
    $return .= 'window.fbAsyncInit = function() { ';
    $return .= 'FB.init({ ';
    $return .= 'appId : "1753201391632030", ';
    $return .= 'cookie	: true, ';
    $return .= 'version : "v2.6" ';
    $return .= '}); ';
    $return .= '}; ';
    $return .= '</script> ';
    $return .= facebook_script();
    $return .= '<script> ';
    $return .= 'function facebook_logout() { ';
    $return .= 'FB.logout(function(response){}) ';
    $return .= '} ';
    $return .= 'function facebook_login() { ';
    $return .= 'FB.login(function(response){ ';
    $return .= '$(".carregando").show(); ';
    $return .= 'if (response.status === "connected") { ';
    $return .= 'FB.api("/me", {fields: "id,name,email"}, function(response){ ';
    $return .= '$.ajax({ ';
    $return .= 'type: "POST", ';
    $return .= 'url: "' . DIR . '/app/Ajax/Facebok/login.php?direcionar=' . $_GET['nome'] . '", ';
    $return .= 'data: { id: response.id, nome: response.name, email: response.email, url: ' . $direcionar . ' }, ';
    $return .= 'dataType: "json", ';
    $return .= 'error: function($request, $error){ ajaxErro($request, $error); }, ';
    $return .= 'success: function($json){ ';
    $return .= 'if($json.evento!=null) eval($json.evento); ';
    $return .= '} ';
    $return .= '}); ';
    $return .= '}); ';
    //$return .= '} else if (response.status === "not_authorized") { '; // Nao esta Logado no Site
    $return .= '} else { '; // Nao esta logado no Facebook
    $return .= 'setTimeout(function(){ ';
    $return .= 'alerts(0, "Não Foi Possivel Conectar com o Facebook! Tente Novamente!"); ';
    $return .= '$(".carregando").hide(); ';
    $return .= '}, 3000); ';
    $return .= '} ';
    $return .= '}, {scope: "email"}) ';
    $return .= '} ';
    $return .= '</script> ';
    return $return;
};

// FACEBOOK
// REDES SOCIAIS E COMPARTILHAMENTOS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// GRAVACOES NO BANCO
// Gravar campos
function gravar_campos($table, $campo, $excecoes = '')
{

    $mysql = new Mysql();

    $variaveis = $excecoes . ', calendar, update, gravar, direcionar, datatable_boxs, table, newsletter, termos, select_text, lang, c_senha, senha, data, txt_editor, txt_editor1, txt_editor2, txt_editor3, txt_editor4, txt_editor5, txt_editor6, sem_foto, sem_multifotos';
    $variaveis = str_replace(' ', '', $variaveis);
    $array_ex = explode(',', $variaveis);

    foreach ($_POST as $nome_get => $valor_get) {

        // tirar _hidden
        if (preg_match('(_hidden)', $nome_get) or preg_match('(_button)', $nome_get))
            $array_ex[] = $nome_get;

        if (!in_array($nome_get, $array_ex) and !preg_match('(_temp_)', $nome_get)) {

            if (!is_object($valor_get) and !is_array($valor_get)) {
                $campo[$nome_get] = $valor_get;
            } elseif (is_array($valor_get)) {

                // Checkbox
                $opcionais_total = '-';
                foreach ($valor_get as $nome_get_checkbox => $valor_get_checkbox)
                    if (!is_array($valor_get_checkbox)) {
                        $opcionais_total .= str_replace('-', '&shy;', $valor_get_checkbox) . '-';
                    }
                $campo[$nome_get] = $opcionais_total;
            }
        }
    }

    // Varias Categorias
    if (isset($_POST['varias_categorias']) and $_POST['varias_categorias']) {
        $varias_categorias_total = '-';
        for ($i = 0; $i <= count($_POST['varias_categorias']); $i++) {
            if (isset($_POST['varias_categorias'][$i]))
                $varias_categorias_total .= $_POST['varias_categorias'][$i] . '-';
        }
        $campo['varias_categorias'] = $varias_categorias_total;
    }

    // SubCategorias
    if (isset($_POST['subcategorias'])) {
        if ($_POST['subcategorias']) {
            $mysql->nao_existe = 1;
            $mysql->colunas = 'tipo';
            $mysql->prepare = array($_POST['subcategorias']);
            $mysql->filtro = " WHERE `id` = ? ";
            $subcategorias_post = $mysql->read_unico($table);
            $niveis = $subcategorias_post->tipo + 1;
        } else {
            $niveis = 0;
        }
        $campo['tipo'] = $niveis;
        $campo['subcategorias'] = $_POST['subcategorias'];
    }

    // Senha
    if (isset($_POST['senha'])) {
        $campo['senha'] = md5($_POST['senha']);
    }

    return $campo;
}

// Validacoes
function validacoes($table, $modulos, $post, $id, $modulos_site = '')
{

    $modulos_abas = (isset($modulos->abas) and $modulos->abas) ? unserialize(base64_decode($modulos->abas)) : array();
    $modulos_campos = (isset($modulos->campos) and $modulos->campos) ? unserialize(base64_decode($modulos->campos)) : array();
    if (LUGAR == 'site') {
        $modulos_abas = $modulos_site['abas'];
        $modulos_campos = $modulos_site['campos'];
    } else
        require "../views/Individual/validade.php";

    // Preenchimento Obrigatorio
    foreach ($modulos_abas as $kabas => $value_abas) {
        if (isset($modulos_campos[$kabas])) {
            foreach ($modulos_campos[$kabas] as $key => $value) {
                if (isset($value['check']) and $value['check']) {
                    if (preg_match('(required)', $value['input']['tags'])) {
                        if (isset($_POST[$value['input']['nome']]) and $_POST[$value['input']['nome']] == '') {
                            $arr['erro'][] = 'Preencha o campo: ' . $value['nome'];
                        }
                    }
                }
            }
        }
    }

    // Outras Validacoes
    foreach ($modulos_abas as $kabas => $value_abas) {
        if (isset($modulos_campos[$kabas])) {
            foreach ($modulos_campos[$kabas] as $key => $value) {
                if (isset($value['check']) and $value['check']) {
                    if (preg_match('(validar)', $value['input']['tags'])) {
                        $entre = entre('validar="', '"', $value['input']['tags']);
                        if ($entre == 'cpf') {
                            if (LUGAR == 'site')
                                require "../../../app/Validades/cpf.php";
                            else
                                require "../../app/Validades/cpf.php";
                        } elseif ($entre == 'cnpj') {
                            if (LUGAR == 'site')
                                require "../../../app/Validades/cnpj.php";
                            else
                                require "../../app/Validades/cnpj.php";
                        } else {
                            if (LUGAR == 'site')
                                require "../../../app/Validades/se_existe.php";
                            else
                                require "../../app/Validades/se_existe.php";
                        }
                    }
                    if (preg_match('(comparar)', $value['input']['tags'])) {
                        if (isset($_POST[$value['input']['nome']])) {
                            $entre = entre('comparar="', '"', $value['input']['tags']);
                            if ($_POST[$value['input']['nome']] != $_POST[$entre])
                                $arr['erro'][] = 'O campo ' . $value['nome'] . ' não está conferindo com o campo de confirmação!';
                        }
                    }
                }
            }
        }
    }
    if (isset($arr['erro'])) {
        echo json_encode($arr);
        exit();
    }
}

// GRAVACOES NO BANCO
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// DESCONTOS
// Produtos -> tem q descomentar no arquivo mysql
function descontos_produtos($value)
{
    $mysql = new Mysql();
    $preco = $value->preco;
    $desconto = 0;

    $mysql->colunas = 'preco, porc';
    $mysql->filtro = " WHERE `produtos` LIKE concat('%', '-" . $value->id . "-', '%') ";
    $produtos_descontos = $mysql->read('produtos_descontos');
    foreach ($produtos_descontos as $key => $value) {
        if ($value->porc)
            $desconto += $preco * ($value->porc / 100);
        if ($value->preco)
            $desconto += $value->preco;
    }

    return $preco - $desconto;
}

// DESCONTOS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// CARRINHO
// Gravar no Carrinho
function carrinho($value)
{
    $return = "carrinhoo_gravar(" . $value->id . ")";
    return $return;
}

// Gravar no Carrinho Mobile
function carrinho_mobile($value)
{
    $return = "carrinhoo_gravar(" . $value->id . ", 1)";
    return $return;
}

// Carrinho Dados
function carrinho_dados($dados, $carrinho = '')
{
    $mysql = new Mysql();
    $dados['carrinho'] = array();
    $dados['preco_total'] = '';
    $i = 0;

    $carrinho = $carrinho ? $carrinho : (isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : '');
    if (isset($carrinho['itens'])) {
        foreach ($carrinho['itens'] as $key => $array) {
            foreach ($array as $ref => $value) {

                $mysql->prepare = array($key);
                $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `id` = ? ";
                $produtos = $mysql->read_unico('produtos');
                if (isset($produtos->id)) {

                    $dados['carrinho'][$i] = $produtos;
                    $dados['carrinho'][$i]->ref = $ref;

                    foreach ($value as $k => $v) {
                        $dados['carrinho'][$i]->$k = $v;
                    }

                    $dados['preco_total'] += $value->qtd * $produtos->preco;

                    $i++;
                }
            }
        }
    }
    $dados['total_itens'] = $i;
    return ($dados);
}

// CARRINHO
// COTACAO
// Gravar no Cotacao
function cotacao($value)
{
    $return = "cotacao_gravar('" . $value->id . "', '" . $value->table . "')";
    return $return;
}

// Excluir no Cotacao
function cotacao_excluir($value)
{
    $return = "cotacao_excluir('" . $value->id . "', '" . $value->table . "')";
    return $return;
}

// Cotacao Dados
function cotacao_dados($dados)
{
    $i = 0;
    $mysql = new Mysql();
    $dados['cotacao'] = array();
    if (isset($_SESSION['cotacao']) and $_SESSION['cotacao']) :
        foreach ($_SESSION['cotacao']['id'] as $banco => $array) :
            foreach ($array as $id => $valor) :

                $mysql->prepare = array($id);
                $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `id` = ? ";
                $cotacao = $mysql->read($banco);
                if ($cotacao and $banco) :
                    $dados['cotacao'][$i] = $cotacao[0];
                    $dados['cotacao'][$i]->c_qtd = isset($_SESSION['cotacao']['qtd'][$banco][$id]) ? $_SESSION['cotacao']['qtd'][$banco][$id] : 1;
                    $dados['cotacao'][$i]->banco = $banco;
                    $i++;
                endif;

            endforeach;
        endforeach;
    endif;
    return ($dados);
}

// COTACAO
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// CODIFICACOES
function sem($tipo, $txt)
{
    switch ($tipo) {
        case 'tags':
            $return = strip_tags($txt, '');
            break;

        case 'url':
            $trocarIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ',);
            $porIsso = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'Y',);
            $txt = str_replace($trocarIsso, $porIsso, $txt);
            $trocarIsso = array('#', '%', '&', '?', '\ ', '\\', '\\\ ', "\"", '\'', '/', '"', "'", '´', '`', '~', '^', '!', '@', '#', '$', '%', '¨', '&', '=', ':', ';', '*', '<', '>', '|', ' ',);
            $porIsso = array('-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',);
            $return = str_replace($trocarIsso, $porIsso, $txt);
            break;

        case 'acentos':
            $trocarIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ',);
            $porIsso = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'Y',);
            $return = str_replace($trocarIsso, $porIsso, $txt);
            break;

        case 'acentos_all':
            //cod('asc->html', $txt);
            $txt = str_replace(array('[\', \']'), '', $txt);
            $txt = preg_replace('/\[.*\]/U', '', $txt);
            $txt = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $txt);
            $txt = htmlentities($txt, ENT_COMPAT, 'utf-8');
            $txt = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $txt);
            $txt = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $txt);
            $return = strtolower(trim($txt, '-'));
            break;

        case 'simbolos':
            $trocarIsso = array(' ', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ', '\ ', '\\', '\\\ ', "\"", '\'', '/',);
            $porIsso = array('_', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'Y', '_', '_', '_', '_', '_', '_',);
            $return = str_replace($trocarIsso, $porIsso, $txt);
            $return = preg_replace("/[^a-zA-Z0-9\s]/", "_", $return);
            break;
    }
    return $return;
}

function cod($tipo, $txt)
{
    switch ($tipo) {
        case 'busca':
            $txt = sem('acentos', $txt);
            $trocarIsso = array('a', 'e', 'i', 'o', 'u', 'c', 'n', 'y', 'A', 'E', 'I', 'O', 'U', 'C', 'N', 'Y',);
            $porIsso = array('(a|à|á|â|ã|ä|å|A)', '(e|è|é|ê|ë|E)', '(i|ì|í|î|ï|I)', '(o|ò|ó|ô|õ|ö|O)', '(u|ù|ü|ú|U)', '(c|ç|C)', '(n|ñ|N)', '(y|ÿ|Y)', '(a|à|á|â|ã|ä|å|A|À|Á|Â|Ã|Ä|Å)', '(e|è|é|ê|ë|E|È|É|Ê|Ë)', '(i|ì|í|î|ï|I|Ì|Í|Î|Ï)', '(o|ò|ó|ô|õ|ö|O|Ò|Ó|Ô|Õ|Ö)', '(u|ù|ü|ú|U|Ù|Ü|Ú)', '(c|ç|C|Ç)', '(n|ñ|N|Ñ)', '(y|ÿ|Y|Ÿ)',);
            break;

        case 'html->asc':
            $trocarIsso = array("'", '"', "´", "`", "?",);
            $porIsso = array('&#39;', '&#34;', '&#180;', '&#96;', '&#63;',);
            break;

        case 'asc->html':
            $trocarIsso = array('&#39;', '&#34;', '&#180;', '&#96;', '&#63;',);
            $porIsso = array("'", '"', "´", "`", "?",);
            break;

        case 'html->iso':
            $trocarIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ',);
            $porIsso = array('&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&ugrave;', '&uuml;', '&Uacute;', '&yuml;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Ugrave;', '&uuml;', '&Uacute;', '&Yuml;',);
            break;

        case 'html->iso1':
            $trocarIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ', '&', '<', '>', '¡', '¤', '¢', '£', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '®', '¯', '°', '±', '²', '³', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', '×', '÷',);
            $porIsso = array('&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&ugrave;', '&uuml;', '&Uacute;', '&yuml;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Ugrave;', '&uuml;', '&Uacute;', '&Yuml;', '&amp;', '&lt;', '&gt;', '&iexcl;', '&curren;', '&cent;', '&pound;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '	&sup2;', '&sup3;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&times;', '&divide;',);
            break;

        case 'iso->html':
            $trocarIsso = array('&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&ugrave;', '&uuml;', '&Uacute;', '&yuml;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Ugrave;', '&uuml;', '&Uacute;', '&Yuml;', '&amp;', '&lt;', '&gt;', '&iexcl;', '&curren;', '&cent;', '&pound;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '	&sup2;', '&sup3;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&times;', '&divide;',);
            $porIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ', '&', '<', '>', '¡', '¤', '¢', '£', '¥', '¦', '§', '¨', '©', 'ª', '«', '¬', '®', '¯', '°', '±', '²', '³', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½', '¾', '¿', '×', '÷',);
            break;

        case 'especial':
            $trocarIsso = array('º',);
            $porIsso = array('&deg;',);
            break;

        case 'msql->html':
            $trocarIsso = array('Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¹', 'Ã¼', 'Ãº', 'Ã¿', 'Ã€', 'Ã', 'Ã‚', 'Ãƒ', 'Ã"', 'Ã…', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã', 'ÃŽ', 'Ã', "Ã'", "Ã'", 'Ã"', 'Ã"', 'Ã•', 'Ã–', 'Ã™', 'Ãœ', 'Ãš', 'Å¸', 'Âº',);
            $porIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ', 'º',);
            break;

        case 'html->msql':
            $trocarIsso = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', 'Ÿ', 'º',);
            $porIsso = array('Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¹', 'Ã¼', 'Ãº', 'Ã¿', 'Ã€', 'Ã', 'Ã‚', 'Ãƒ', 'Ã"', 'Ã…', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã', 'ÃŽ', 'Ã', "Ã'", "Ã'", 'Ã"', 'Ã"', 'Ã•', 'Ã–', 'Ã™', 'Ãœ', 'Ãš', 'Å¸', 'Âº',);
            break;
    }
    $return = str_replace($trocarIsso, $porIsso, $txt);
    return $return;
}

function gravando_no_mysql($table, $name, $value)
{
    $return = $value;
    //if($name!='multifotos' and $table!='usuarios_config' and $table!='z_txt'){
    if ($table != 'menu_admin' and $table != 'z_txt') {
        $return = cod('html->asc', $return); // htmlspecialchars
    } else {
        $return = stripslashes($return);
    }
    //$return = utf8_decode($return);
    return $return;
}

function voltar_cod($txt)
{
    $return = cod('asc->html', $txt);
    $return = cod('iso->html', $return);
    return $return;
}

// CODIFICACOES
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// VERIFICAR SESSION E CRIACAO DE LOGIN
// Table Admin
function table_admin()
{
    $table = LUGAR;
    if (LUGAR == 'admin')
        $table = 'usuarios';
    if (LUGAR == 'clientes')
        $table = 'cadastro';
    return $table;
}

// Fazer Login
function fazer_login($id)
{
    $mysql = new Mysql();

    $mysql->prepare = array($id);
    $mysql->filtro = "  WHERE `id` = ? ";
    $cadastro = $mysql->read_unico('cadastro');

    // Log
    if ($_SERVER['HTTP_HOST'] != 'localhost:4000') {
        $mysql->campo['item'] = $cadastro->id ?? null;
        $mysql->campo['nome'] = $cadastro->nome ?? null;
        $mysql->campo['lugar'] = LUGAR;
        $mysql->campo['ip'] = $_SERVER['REMOTE_ADDR'];
        $ult_id = $mysql->insert('log');
    }

    // Session
    $_SESSION['x_site'] = (object) array();
    $_SESSION['x_site']->id = $cadastro->id ?? null;
    $_SESSION['x_site']->lugar = 'site';
    $_SESSION['x_site']->log = isset($ult_id) ? $ult_id : 0;
}

// Verificar Sessao
function verificar_sessao($lugar = '')
{

    if (LUGAR == 'admin') {
        if (!isset($_SESSION['x_admin']->id)) {
            echo '<script> window.parent.location="' . DIR . '/' . ADMIN . '/login.php"; </script>';
            exit();
        }
    } elseif (LUGAR == 'site') {
        if (!isset($_SESSION['x_site']->id)) {
            header('Location: ' . DIR . '/login/' . $lugar);
        }
    } else {
        if (!isset($_SESSION['x_' . LUGAR]->id)) {
            echo '<script> window.parent.location="' . DIR . '/' . LUGAR . '/login.php"; </script>';
            exit();
        }
    }
}

// VERIFICAR SESSION E CRIACAO DE LOGIN
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// PADROES SISTEM
function curll($url, $post = NULL, array $header = array())
{
    $ch = curl_init($url); //Inicia o cURL			 		    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Pede o que retorne o resultado como string
    if (count($header) > 0) { //Envia cabeçalhos (Caso tenha)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if ($post !== null) { //Envia post (Caso tenha)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Ignora certificado SSL
    $data = curl_exec($ch); //Manda executar a requisição
    curl_close($ch); //Fecha a conexão para economizar recursos do servidor		 
    return $data;
}

function array_obj($array)
{
    $return = '';
    foreach ($array as $key => $value) {
        $return->$key = $value;
    }
    return $return;
}

function obj_array($obj)
{
    $return = '';
    foreach ($obj as $key => $value) {
        $return[$key] = $value;
    }
    return $return;
}

function pre($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function printr($array)
{
    $return = '';
    foreach ($array as $key => $value) {
        $return .= $key . ' => ' . $value . '<br>';
    }
    return $return;
}

function autoload($class_name, $caminho)
{
    if (file_exists($caminho . '../app/Classes/' . $class_name . '.php'))
        require_once($caminho . '../app/Classes/' . $class_name . '.php');
    elseif (file_exists($caminho . '../admin/app/Classes/' . $class_name . '.php'))
        require_once($caminho . '../admin/app/Classes/' . $class_name . '.php');
    else if (file_exists($caminho . '../plugins/Tng/tng/triggers/' . $class_name . '.class.php'))
        require_once($caminho . '../plugins/Tng/tng/triggers/' . $class_name . '.class.php');
}

// PADROES SISTEM
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// DEFINES e SETS
define('MOBILE', 500);

define('VIEWS', 'views'); // Views_url
define('DIALOG_MAX', 0); // Dialog Maximizado
define('FF', 'ff;;'); // Filtro Avancado Admin
define('A', "'");
define('A2', '"');

// DEFINES E SETS
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// EMAILS
// Tabela
function emails_tabela_de_pedidos($pedidos)
{
    $mysql = new Mysql();
    $return = '<table style="width:100%" style="color: #888;"> ';
    $return .= '<tbody> ';
    $return .= '<tr> ';
    $return .= '<th style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">Produto</th> ';
    $return .= '<th align="center" style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">Preço</th> ';
    $return .= '<th align="center" style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;;">Quantidade</th> ';
    $return .= '<th style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">Total</th> ';
    $return .= '</tr> ';

    $nome = explode('<z></z>', $pedidos->nome);
    $produtos = explode('-', $pedidos->produtos);
    $qtds = explode('-', $pedidos->qtds);
    $precos = explode('-', $pedidos->precos);
    foreach ($produtos as $key => $value) {
        $mysql->prepare = array($value);
        $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `id` = ? ";
        $produtos = $mysql->read_unico("produtos");
        if (isset($produtos->id)) {
            $return .= '<tr> ';
            $return .= '<td style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">' . str_replace('>> ', '', $nome[$key - 1]) . '</td> ';
            $return .= '<td align="center" style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">' . preco($precos[$key], 1) . '</td> ';
            $return .= '<td align="center" style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">' . $qtds[$key] . '</td> ';
            $return .= '<td style="padding: 5px 10px; border-bottom: 1px solid #d8d8d8;">' . preco($precos[$key] * $qtds[$key], 1) . '</td> ';
            $return .= '</tr> ';
        }
    }
    $return .= '<tr> ';
    $return .= '<td colspan="3" style="padding: 5px 10px; text-align: right; border-bottom: 1px solid #d8d8d8;"><b>Frete</b>:</td> ';
    $return .= '<td style="padding: 5px 10px; text-align: left; border-bottom: 1px solid #d8d8d8;">' . preco($pedidos->frete, 1) . '</td> ';
    $return .= '</tr> ';
    if ($pedidos->desconto > 0) {
        $return .= '<tr> ';
        $return .= '<td colspan="3" style="padding: 5px 10px; text-align: right; border-bottom: 1px solid #d8d8d8;"><b>Desconto</b>:</td> ';
        $return .= '<td style="padding: 5px 10px; text-align: left; border-bottom: 1px solid #d8d8d8;">' . preco($pedidos->desconto, 1) . '</td> ';
        $return .= '</tr> ';
    }
    $return .= '<tr> ';
    $return .= '<td colspan="3" style="padding: 5px 10px; text-align: right; border-bottom: 1px solid #d8d8d8;"><b>Total</b>:</td> ';
    $return .= '<td style="padding: 5px 10px; text-align: left; border-bottom: 1px solid #d8d8d8;"><b>' . preco($pedidos->valor_total, 1) . '</b></td> ';
    $return .= '</tr> ';
    $return .= '</tbody> ';
    $return .= '</table> ';
    return $return;
}

// Tabela
// EMAILS
// EMAILS VARIAVEIS
function email_55($cadastro, $lotes)
{
    $return = 'nome->' . $cadastro->nome;
    $return .= '&-&email->' . $cadastro->email;
    $return .= '&-&nome_lote->' . $lotes->nome;
    $return .= '&-&numero_lote->' . ((int) $lotes->ordem);
    $return .= '&-&cidade->' . $lotes->cidades;
    $return .= '&-&estado->' . $lotes->estados;
    $return .= '&-&nota_venda-><a href="' . DIR_C . '/imprimir/nota/?ids=-' . $lotes->id . '-">' . DIR_C . '/imprimir/nota/</a>';
    $return .= '&-&termo_arrematacao-><a href="' . DIR_C . '/imprimir/termo_arrematacao/' . $lotes->id . '">' . DIR_C . '/imprimir/termo_arrematacao/</a>';
    return $return;
}

// EMAILS VARIAVEIS
