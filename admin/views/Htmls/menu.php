<?php
/* ESPECIFICACOES DO MENU */

$mysql = new Mysql();

// Permissoes dos usuarios
$filtro_permissoes = verificar_permissoes_usuario();


// Menu e Sub Menus
$i = 0;
$mysql->filtro = " WHERE `status` = 1 AND `tipo` = 0 AND `id` IN ( SELECT `categorias` FROM menu_admin WHERE `status` = 1 " . $filtro_permissoes . " ) ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
$menu_admin1_cate_0 = $mysql->read('menu_admin1_cate');


if (!$menu_admin1_cate_0)
    exit('Nenhum Menu Cadsatrado!');
foreach ($menu_admin1_cate_0 as $key => $value) {
    $i++;

    // Categorias
    $menu_cate[$i] = $value;

    // Menus
    $z = 0;
    $mysql->prepare = array($value->id);
    $mysql->filtro = " WHERE `status` = 1 AND id!=78 AND `status1` = 1 AND `categorias` = ? " . $filtro_permissoes . " ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
    $menu_admin = $mysql->read('menu_admin');
    foreach ($menu_admin as $k => $v) {
        if (!(isset($v->admins) and $v->admins)) {
            $z++;
            $menu[$i][$z] = $v;
        }
    }

    // Sub Categorias
    $mysql->prepare = array($value->id);
    $mysql->filtro = " WHERE `status` = 1 AND `tipo` = 1 AND `subcategorias` = ? AND `id` IN ( SELECT `categorias` FROM menu_admin WHERE `status` = 1 " . $filtro_permissoes . " ) ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
    $menu_admin1_cate_0 = $mysql->read('menu_admin1_cate');
    foreach ($menu_admin1_cate_0 as $k => $v) {
        $menu_subcate[$i] = $v;

        // Menus Sub
        $y = 0;
        $mysql->prepare = array($v->id);
        $mysql->filtro = " WHERE `status` = 1 AND `status1` = 1 AND `categorias` = ? " . $filtro_permissoes . " ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
        $menu_admin = $mysql->read('menu_admin');
        foreach ($menu_admin as $k1 => $v1) {
            if (!(isset($v->admins) and $v->admins)) {
                $y++;
                $menu_sub[$i][$y] = $v1;
            }
        }
    }
}

/*
  /* ESPECIFICACOES DO MENU */





/* MENU LEFT */
/*
  $html_menu_left = '';
  $html_menu_left_conteudo = '';

  $html_menu_left .= '<div class="menu_fundo"></div> ';

  $html_menu_left .= '<a href="javascript:void(0)" class="menu_lista">  </a> ';
  $html_menu_left .= '<a href="' . DIR . '/home/" class="visitar_site" target="_blank">Visitar meu site</a> ';
  $html_menu_left .= '<div class="clear"></div> ';
  $html_menu_left .= '<hr> ';

  $html_menu_left .= '<ul class="menu_left" click="true"> ';

  foreach ($menu as $key => $value) {

  // Categorias
  if ($menu_cate[$key]->nome) {
  $html_menu_left_conteudo .= '<li class="heading"> ';
  $html_menu_left_conteudo .= '<h3>' . $menu_cate[$key]->nome . '</h3> ';
  $html_menu_left_conteudo .= '</li> ';
  }

  // Sub Categorias
  if (isset($menu_subcate[$key])) {
  $html_menu_left_conteudo .= '<li> ';
  $html_menu_left_conteudo .= '<a href="javascript:void(0)"> ';
  $html_menu_left_conteudo .= '<i class="' . iff($menu_subcate[$key]->foto, $menu_subcate[$key]->foto, 'fa fa-asterisk') . '"></i> ';
  $html_menu_left_conteudo .= '<span class="open"></span> ';
  $html_menu_left_conteudo .= '<i class="seta up fa fa-angle-left"></i> ';
  $html_menu_left_conteudo .= '<i class="seta down fa fa-angle-down"></i> ';
  $html_menu_left_conteudo .= '<span class="nome"> ' . $menu_subcate[$key]->nome . ' </span> ';
  $html_menu_left_conteudo .= '</a> ';

  // Menus Sub
  if (isset($menu_sub[$key])) {
  $html_menu_left_conteudo .= '<ul class="dn"> ';
  foreach ($menu_sub[$key] as $k => $v) {
  $html_menu_left_conteudo .= '<li class="menu_' . $v->id . '" > ';
  if ($v->url)
  $url = ' href="' . $v->url . '" ';
  elseif (preg_match('(-boxs-)', $v->informacoes))
  $url = ' onclick="boxs(' . A . 'gerenciar_itens' . A . ', ' . A . 'id=' . $v->id . '&rand=' . rand() . A . ', 1);" ';
  else
  $url = ' href="javascript:' . VIEWS . '(' . A . $v->id . A . ', ' . $v->tipo_modulo . ', ' . A . $v->gets . A . ')" ';
  $html_menu_left_conteudo .= '<a ' . $url . ' > ';
  $html_menu_left_conteudo .= '<span class="nome"> ' . $v->nome . ' </span> ';
  $html_menu_left_conteudo .= '</a> ';
  $html_menu_left_conteudo .= '</li> ';
  }
  $html_menu_left_conteudo .= '</ul> ';
  }

  $html_menu_left_conteudo .= '</li> ';
  }

  // Menus
  foreach ($value as $k => $v) {
  $html_menu_left_conteudo .= '<li class="menu_' . $v->id . '" > ';
  if ($v->url) {
  $url = ' href="' . $v->url . '" ';
  } elseif (preg_match('(-boxs-)', $v->informacoes)) {
  $url = ' onclick="boxs(' . A . 'gerenciar_itens' . A . ', ' . A . 'id=' . $v->id . '&rand=' . rand() . A . ', 1);" ';
  } else {
  $url = ' href="javascript:' . VIEWS . '(' . A . $v->id . A . ', ' . $v->tipo_modulo . ', ' . A . $v->gets . A . ')" ';
  }
  $html_menu_left_conteudo .= '<a ' . $url . ' > ';
  $html_menu_left_conteudo .= '<i class="' . iff($v->foto, $v->foto, iff($menu_cate[$key]->foto, $menu_cate[$key]->foto, 'fa fa-asterisk')) . '"></i> ';
  $html_menu_left_conteudo .= '<span class="open"></span> ';
  $html_menu_left_conteudo .= '<span class="nome"> ' . str_replace(' (Email)', '', $v->nome) . ' </span> ';
  $html_menu_left_conteudo .= '</a> ';
  $html_menu_left_conteudo .= '</li> ';
  }
  }

  $html_menu_left .= $html_menu_left_conteudo . '</ul> ';
 */
?>

<div id="sidebar">

    <nav>
        <ul id="nav-sidebar">

            <?php
            foreach ($menu as $key => $value) {

                if ($menu_cate[$key]->nome) {
            ?>
                    <li>
                        <a href="#" class="ativar-subnav" id="empresas">
                            <span class="icon">
                                <i class="<?= $menu_cate[$key]->foto ?>"></i>
                            </span>
                            <span class="label"><?= $menu_cate[$key]->nome ?></span>
                        </a>
                        <ul class="subnav">
                            <?php
                            foreach ($value as $k => $v) {

                                if ($v->link) {
                                    $url = ' href="?pg=' . $v->link . '" ';
                                } elseif (preg_match('(-boxs-)', $v->informacoes)) {
                                    $url = ' onclick="boxs(' . A . 'gerenciar_itens' . A . ', ' . A . 'id=' . $v->id . '&rand=' . rand() . A . ', 1);" ';
                                } else {
                                    $url = ' href="javascript:' . VIEWS . '(' . A . $v->id . A . ', ' . $v->tipo_modulo . ', ' . A . $v->gets . A . ')" ';
                                }
                            ?>
                                <li>
                                    <a <?= $url ?> id="sub-<?= $v->id ?>">
                                        <span class="icon">
                                            <i class="<?= iff($v->foto, $v->foto, iff($menu_cate[$key]->foto, $menu_cate[$key]->foto, 'fa fa-asterisk')) ?>"></i>
                                        </span>
                                        <span class="label"><?= str_replace(' (Email)', '', $v->nome) ?></span>
                                        <div class="arrow-right"></div>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
    </nav>
</div>

<?php
/* Inserir nos outros bancos ou criar alguma rotina para inserir
 * 
 * INSERT INTO `menu_admin` (`id`, `status`, `status1`, `lang`, `nome`, `foto`, `modulo`, `link`, `data`, `dataup`, `ordem`, `tipo_modulo`, `categorias`, `informacoes`, `categorias_nivel`) VALUES (NULL, '1', '1', '1', 'Logos Rodapé', 'fa fa-tint', 'configs', 'logos-rodape', '2017-08-08 01:09:05', '2017-08-08 11:28:49', '900', '1', '30', '-acoes-edit-', '');
 * 
 */

?>