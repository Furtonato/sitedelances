<?php
echo '<div style="overflow: hidden;" class="views m_900"> ';
echo '<div class="default"> ';
echo '<div class="mapa_url"> ';
echo '<h1> Menus do Painel de Controle ' . iff(LUGAR == 'admin', 'da Administração do Site') . ' </h1> ';
echo '</div> ';

$default = array();
// Categorias
foreach ($menu as $key => $value) {
    // Sub Categorias
    if (isset($menu_subcate[$key])) {
        // Menus Sub
        if (isset($menu_sub[$key])) {
            foreach ($menu_sub[$key] as $k => $v) {
                $default[] = $v;
            }
        }
    }
    // Menus
    foreach ($value as $k => $v) {
        $default[] = $v;
    }
}




/* / Tabela de Estatisticas
  echo '<div class="p20"> ';
  echo '<table class="w100p"> ';
  echo '<tr> ';
  echo '<th class="p5 bd_ccc back_eee tal">Periodo</th> ';
  echo '<th class="p5 bd_ccc back_eee tac">Qtd.</th> ';
  echo '<th class="p5 bd_ccc back_eee tac">Ticket médio</th> ';
  echo '<th class="p5 bd_ccc back_eee tac">Valor</th> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Hoje</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', 'hj').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', 'hj').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', 'hj').'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Ontem</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', 'ot').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', 'ot').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', 'ot').'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Dois dias atrás</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', 'dois').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', 'dois').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', 'dois').'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Três dias atrás</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', 'tres').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', 'tres').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', 'tres').'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Mês Passado</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', date('m')-1).'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', date('m')-1).'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', date('m')-1).'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tal"><u>Total</u></td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('qtd', 'all').'</td> ';
  echo '<td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('media', 'all').'</td> ';
  echo ' <td class="p5 bd_ccc back_fcfcfc tac">'.pedidos_dados('valor', 'all').'</td> ';
  echo '</tr> ';
  echo '<tr> ';
  echo '<td class="p5 bd_ccc backf tal" colspan="4"><a href="javascript:views(47, 1, '.A.'tipo=pedidos_por_pediodo'.A.')"><u>Veja relatórios completos</u></a></td> ';
  echo '</tr> ';
  echo '</table> ';
  echo '</div> ';
 */




echo '<ul> ';
foreach ($default as $k => $v) {
    echo '<li class="w200 h100 fll p10 mb5 tac fz16"> ';
    if ($v->link)
        echo '<a href="?pg=' . $v->link . '" >';
    else
        echo '<a href="javascript:' . VIEWS . '(' . A . $v->id . A . ', ' . $v->tipo_modulo . ', ' . A . $v->gets . A . ')" >';
    $icon_cate = rel('menu_admin1_cate', $v->categorias, 'foto');
    echo '<i class="' . iff($v->foto, $v->foto, iff($icon_cate, $icon_cate, 'fa fa-asterisk')) . ' fz30"></i> ';
    echo '<div class="h3"></div> ';
    echo $v->nome;
    echo '</a> ';
    echo '</li> ';
}
echo '</ul> ';
echo '</div> ';
echo '</div> ';
?>


