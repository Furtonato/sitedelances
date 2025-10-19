<?php

/* HEADER */

$html_header = '<a href="' . DIR . '/' . LUGAR . '/" class="logo"> ' . iff(LUGAR == 'admin', 'Administração do Site', 'Área de ' . ucfirst(LUGAR)) . ' </a> ';

function menu_topo($tipo) {
    $return = '<ul class="barra" ' . iff($tipo == 1, 'click="true"', 'hover="true"') . '> ';

    $return .= '<li class="menu_topo dnix"> ';
    $return .= '<a href="javascript:void(0)"> ';
    $return .= '<i class="icon fa fa-reorder (alias)"></i> ';
    $return .= '<span class="nome"> Menu </span> ';
    $return .= '</a> ';
    $return .= '</li> ';


    $return .= '<li class="usuario"> ';
    $return .= '<a href="javascript:void(0)"> ';
    $return .= '<i class="fa fa-gears (alias)"></i> &nbsp ';
    $return .= '<span class="nome"> Configurações </span> ';
    $return .= '<i class="fa fa-angle-down"></i> ';
    $return .= '</a> ';
    $return .= '<ul class="boxx"> ';
    $return .= '<li class="temas_cores"> <a onclick="boxs_branco(' . A . 'temas' . A . ')" class="open"> <i class="fa fa-file-photo-o (alias)"></i> Alterar Tema </a> </li> ';
    if (LUGAR == 'admin')
        $return .= '<li> <a onclick="boxs(' . A . 'itens_por_pagina' . A . ')" class="open"> <i class="fa fa-gear (alias)"></i> Itens por Pagina </a> </li> ';
    $return .= '<li> <a href="' . DIR . '/' . LUGAR . '/login.php?logout=ok"> <i class="icon-key"></i> Sair </a> </li> ';
    $return .= '</ul> ';
    $return .= '</li> ';

    $return .= '<li class="menu_hide"> ';
    $return .= '</li> ';

    $return .= '</ul> ';
    return $return;
}


$html_header .= '<div class="clear"></div> ';

echo $html_header;
/* HEADER */
?>