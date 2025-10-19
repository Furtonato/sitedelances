<?php

require_once "../head.php";
if ($_SESSION['x_admin']->id == 1) {

    // AS VARIAVEIS DO AJAX ESTAO NO HEAD.PHP
    // GRAVAÇÃO
    if ($_GET['acao'] == 'gravar' and $_POST) {

        include '../../plugins/Sql/backup.php';

        // Tirando char especiais dos names
        $_POST = menu_admin_names($_POST);

        $mysql1 = new Mysql();

        $mysql->logs = 0;
        $mysql1->campo['dataup'] = date('c');
        $mysql1->campo = gravar_campos($table, $mysql1->campo);

        // Tirando Barras
        $_POST['colunas'] = tirando_barras($_POST['colunas']);
        $_POST['abas'] = tirando_barras($_POST['abas']);
        $_POST['campos'] = tirando_barras($_POST['campos']);

        $mysql1->campo['colunas'] = base64_encode(serialize($_POST['colunas']));
        $mysql1->campo['abas'] = base64_encode(serialize($_POST['abas']));
        $mysql1->campo['campos'] = base64_encode(serialize($_POST['campos']));

        unset($mysql->campo);
        $mysql->campo['dataup'] = $mysql1->campo['dataup'];
        $mysql->campo['nome'] = $mysql1->campo['nome'];
        $mysql->campo['foto'] = $mysql1->campo['foto'];
        $mysql->campo['modulo'] = $mysql1->campo['modulo'];
        $mysql->campo['tipo_modulo'] = $mysql1->campo['tipo_modulo'];
        $mysql->campo['categorias'] = $mysql1->campo['categorias'];
        $mysql->campo['informacoes'] = $mysql1->campo['informacoes'];

        // Gravando no Banco
        if (isset($_GET['id']) and $_GET['id']) {
            $mysql->filtro = " where id = '" . $_GET['id'] . "' ";
            $arr['ult_id'] = $mysql->update($table);
            $arr['dataup'] = date('d/m/Y H:i');
        } else {
            $arr['ult_id'] = $mysql->insert($table);
        }
        $arr['acao'] = $_POST['acao_button'];


        // Gravando Json
        $caminho = "../../app/Json/menu_admin";
        // Back-up
        if (file_exists($caminho . "/" . $arr['ult_id'] . ".json")) {
            rename($caminho . "/" . $arr['ult_id'] . ".json", "../../plugins/Json/back-up/menu_admin/" . $arr['ult_id'] . "__" . date('Y-m-d-H-i-s') . ".json");
        }
        // Back-up
        $file = fopen($caminho . "/" . $arr['ult_id'] . ".json", 'w');
        fwrite($file, json_encode($mysql1->campo));
        fclose($file);
        // Gravando Json
        // Criando Table
        $criarMysql = new criarMysql();
        $modulo = explode('(&)', $_POST['modulo']);
        $criarMysql->criarTabelas($modulo[0]);

        // Criando Colunas
        $mysql->colunas = 'informacoes';
        $mysql->prepare = array($arr['ult_id']);
        $mysql->filtro = " WHERE `id` = ? ";
        $tabela = $mysql->read_unico($table);
        if (isset($tabela->informacoes)) {
            // Informacoes
            $ex = explode('-', $tabela->informacoes);
            foreach ($ex as $key => $value) {
                if ($value and $value != 'acoes' and $value != 'lista' and $value != 'novo' and $value != 'edit' and $value != 'excluir') {

                    $tipo = 'text';
                    if ((isset($modulo[1]) and $modulo[1] == 'select') or $value == 'categorias' or $value == 'subcategorias' or $value == 'varias_categorias' or $value == 'star' or $value == 'lancamentos' or $value == 'promocao' or $value == 'mapa')
                        $tipo = 'int';

                    $criarMysql->criarColunas($modulo[0], $value, $tipo);
                    if ($value == 'categorias' or $value == 'varias_categorias') {
                        $criarMysql->criarTabelas($modulo[0] . '1_cate');
                    }
                }
            }

            // Boxxs
            if (isset($_POST['tipo_modulo']) and $_POST['tipo_modulo'] == 2)
                $criarMysql->criarColunas($modulo[0], 'boxxs', 'int');

            // Campos
            foreach ($_POST['campos'] as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    if (isset($v2['check']) and $v2['check']) {
                        $tipo = menu_admin_verificar_tipo_coluna($v2);
                        $coluna = str_replace('[]', '', $v2['input']['nome']);
                        $criarMysql->criarColunas($modulo[0], $coluna, $tipo);
                    }
                }
            }

            // Colunas
            foreach ($_POST['colunas'] as $k1 => $v1) {
                if ($v1['value'] != 'relacionamento_categoria_automatico') {
                    $ex = explode('->', $v1['value']);
                    $coluna = str_replace('[]', '', $ex[0]);
                    $criarMysql->criarColunas($modulo[0], $coluna);
                }
            }
        }





        // VIEWS Lista
    } elseif ($_GET['acao'] == 'lista' and $_POST) {
        $arr['html'] .= '<div> ' .
            '<div class="mapa_url"> ' .
            '<h1> <i class="' . $modulos->foto . '"></i> ' . $modulos->nome . ' </h1> ' .
            '</div> ' .
            '<ul class="pb15"> ' .
            '<li class="fll pl10"><a href="javascript:views(1, 0, ' . A . A . ')" class="c_azul link">Todos</a></li> ' .
            '<li class="fll pl10"><a href="javascript:views(1, 0, ' . A . 'm=0' . A . ')" class="c_azul link">Modulos</a></li> ' .
            '<li class="fll pl10"><a href="javascript:views(1, 0, ' . A . 'm=1' . A . ')" class="c_azul link">Modulo Únicos</a></li> ' .
            '<li class="fll pl10"><a href="javascript:views(1, 0, ' . A . 'm=2' . A . ')" class="c_azul link">Modulo Boxxs</a></li> ';
        $admins = array();
        $mysql->filtro = " WHERE `status` = 1 AND `status1` = 1 ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
        $menu_admin = $mysql->read('menu_admin');
        foreach ($menu_admin as $k => $v) {
            if (isset($v->admins) and $v->admins) {
                $admins[$v->admins] = $v->admins;
            }
        }
        foreach ($admins as $k => $v) {
            $arr['html'] .= '<li class="fll pl10"><a href="javascript:views(1, 0, ' . A . 'admins=' . $v . A . ')" class="c_azul link">(Admin ' . ucfirst($v) . ')</a></li> ';
        }
        $arr['html'] .= '<div class="clear"></div> ' .
            '</ul> ' .
            datatable_script($modulos, $passar_para_ajax, $datatables_center, $table_ordem) .
            '<div class="box_table"> ' .
            datatable_acoes($modulos, $datatables_top, $datatables_center) .
            '<form onSubmit="datatable_ordenar(' . A . $modulos->id . A . ', this)" method="post" action="javascript:void()" enctype="multipart/form-data"> ' .
            '<table cellpadding="0" cellspacing="0" border="0" class="calc_1 datatable"> ' .
            datatable_top($modulos, $datatables_top) .
            '</tbody> ' .
            '</table> ' .
            '<button class="dni"></button> ' .
            '<div class="clear"></div> ' .
            '</form> ' .
            '</div> ' .
            '<div class="resultado_extra"></div> ' .
            '</div> ';





        // VIEWS Novo e Edição
    } elseif (($_GET['acao'] == 'novo' or $_GET['acao'] == 'edit') and $_POST) {
        $ids[0] = (isset($ids[0]) and $_GET['acao'] == 'edit') ? $ids[0] : 0;
        if ($ids[0]) {
            $linhas = '';
            $mysql->prepare = array($ids[0]);
            $mysql->filtro = " WHERE `id` = ? ";
            $linhas = $mysql->read_unico($table);
        }

        if (!(isset($linhas->colunas) and $linhas->colunas)) {
            $linhas = (object) array();
            $linhas->colunas = base64_encode('a:8:{i:1;a:3:{s:5:"check";s:1:"1";s:4:"nome";s:6:"Status";s:5:"value";s:6:"status";}i:2;a:3:{s:5:"check";s:1:"1";s:4:"nome";s:4:"Nome";s:5:"value";s:4:"nome";}i:3;a:2:{s:4:"nome";s:10:"Mais Fotos";s:5:"value";s:10:"mais_fotos";}i:4;a:2:{s:4:"nome";s:12:"Comentários";s:5:"value";s:16:"mais_comentarios";}i:5;a:3:{s:5:"check";s:1:"1";s:4:"nome";s:5:"Ordem";s:5:"value";s:5:"ordem";}i:6;a:3:{s:5:"check";s:1:"1";s:4:"nome";s:4:"Foto";s:5:"value";s:4:"foto";}i:7;a:3:{s:5:"check";s:1:"1";s:4:"nome";s:17:"NOME DA CATEGORIA";s:5:"value";s:35:"relacionamento_categoria_automatico";}i:8;a:2:{s:4:"nome";s:0:"";s:5:"value";s:0:"";}}');
        }
        if (!(isset($linhas->abas) and $linhas->abas)) {
            $linhas->abas = base64_encode('a:1:{i:0;a:3:{s:4:"nome";s:0:"";s:5:"check";s:1:"1";s:8:"disabled";s:1:"0";}}');
        }
        if (!(isset($linhas->campos) and $linhas->campos)) {
            $linhas->campos = base64_encode('a:1:{i:0;a:8:{i:1;a:6:{s:5:"check";s:1:"1";s:4:"temp";s:4:"text";s:4:"nome";s:4:"Nome";s:5:"input";a:9:{s:4:"nome";s:4:"nome";s:4:"tags";s:8:"required";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:4:"text";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}i:2;a:6:{s:5:"check";s:1:"1";s:4:"temp";s:4:"text";s:4:"nome";s:21:"Meta (Titulo do Site)";s:5:"input";a:9:{s:4:"nome";s:9:"nome_meta";s:4:"tags";s:0:"";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:4:"text";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}i:3;a:6:{s:5:"check";s:1:"1";s:4:"temp";s:4:"file";s:4:"nome";s:4:"Foto";s:5:"input";a:9:{s:4:"nome";s:4:"foto";s:4:"tags";s:0:"";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:4:"file";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}i:4;a:5:{s:4:"temp";s:4:"text";s:4:"nome";s:0:"";s:5:"input";a:9:{s:4:"nome";s:0:"";s:4:"tags";s:0:"";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:4:"text";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}i:5;a:5:{s:4:"temp";s:4:"text";s:4:"nome";s:0:"";s:5:"input";a:9:{s:4:"nome";s:0:"";s:4:"tags";s:0:"";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:4:"text";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}s:3:"txt";a:5:{s:4:"temp";s:8:"textarea";s:4:"nome";s:17:"Descrição curta";s:5:"input";a:9:{s:4:"nome";s:3:"txt";s:4:"tags";s:0:"";s:6:"opcoes";s:1:"0";s:5:"extra";s:0:"";s:4:"tipo";s:8:"textarea";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}s:8:"txt_meta";a:6:{s:5:"check";s:1:"1";s:4:"temp";s:8:"textarea";s:4:"nome";s:18:"Meta (Descrição)";s:5:"input";a:9:{s:4:"nome";s:8:"txt_meta";s:4:"tags";s:0:"";s:6:"opcoes";s:1:"0";s:5:"extra";s:0:"";s:4:"tipo";s:8:"textarea";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}s:6:"editor";a:6:{s:5:"check";s:1:"1";s:4:"temp";s:6:"editor";s:4:"nome";s:20:"Descrição completa";s:5:"input";a:9:{s:4:"nome";s:10:"txt_editor";s:4:"tags";s:0:"";s:6:"opcoes";s:0:"";s:5:"extra";s:0:"";s:4:"tipo";s:6:"editor";s:5:"tipo1";s:4:"text";s:6:"design";s:1:"1";s:8:"disabled";s:1:"0";s:5:"value";s:0:"";}s:11:"dois_pontos";s:1:":";s:11:"nome_classe";s:0:"";}}}');
        }

        $linhas_colunas = unserialize(base64_decode($linhas->colunas));
        $linhas_abas = unserialize(base64_decode($linhas->abas));
        $linhas_campos = unserialize(base64_decode($linhas->campos));


        $arr['title'] = 'Cadastro de ' . $modulos->nome;
        $arr['html'] .= '<div class="dialog"> ';
        $arr['html'] .= dialog_acoes($modulos, $ids);

        $form = "form_" . rand();
        $arr['html'] .= '<div class="tabs menu_admin"> ';
        $arr['html'] .= '   <form class="' . $form . '" action="javascript:void(0)" method="post" enctype="multipart/form-data"> ';


        // ABAS
        $arr['html'] .= '   <ul class="h31 sortable nav"> ';
        $arr['html'] .= '       <li tabs="tabs_0" class="ativo"> <a onclick="tabs(this)"> Data Table </a> </li> ';
        foreach ($linhas_abas as $key => $value) {
            $arr['html'] .= menu_admin_abas($key, $value);
        }
        $arr['html'] .= '   </ul> ';
        $arr['html'] .= '   <div class="clear"></div> ';
        // ABAS


        $arr['html'] .= '       <ul class="campos_menu_admin box"> ';


        // COLUNAS
        $arr['html'] .= '       <li tabs="tabs_0" tipo="colunas" class="ativo">
                                                    ' . menu_admin_colunas_ini($linhas, $value) . '
                                                    <div class="h5 clear"></div>
                                                    <fieldset>
                                                        <legend> Colunas </legend>
                                                        <ul class="sortable itens">';
        foreach ($linhas_colunas as $key => $value) {
            $arr['html'] .= menu_admin_colunas($key, $value);
        }
        $arr['html'] .= '                   <li dir="txt"></li>
                                                        </ul>
                                                        <nav class="nav4">
                                                            <a onclick="menu_admin_mais_menos(1, this)" class="link c_azul"> mais linhas </a> &nbsp &nbsp
                                                            <a onclick="menu_admin_mais_menos(0, this)" class="link c_azul"> menos linhas </a>
                                                        </nav>
                                                        <div class="h5 clear"></div>
                                                    </fieldset>
                                                </li> ';
        // COLUNAS
        // CAMPOS
        foreach ($linhas_abas as $key => $value) {
            $arr['html'] .= menu_admin_colunas_campos($key, $linhas_campos);
        }
        // CAMPOS


        $arr['html'] .= '       </ul>
                                        <input type="hidden" name="acao_button" value="3">
                                        <input type="reset" name="reset_button" class="dni">
                                        <input type="submit" class="dni">
                                    </form>
                                    <div class="clear"></div>
                                    <script> required_invalid(' . A . '.tabs.menu_admin form.' . $form . A . ') </script>
                                    <script> gravar_item(' . $modulos->id . ', ' . $ids[0] . ', ' . A . '.tabs.menu_admin form.' . $form . A . ') </script>

                                    <div class="dni">
                                        <div class="colunas_novo"> ' . select2_temp(menu_admin_colunas('-key-', array())) . ' </div>
                                        <div class="abas_novo"> ' . select2_temp(menu_admin_abas('-key-', array())) . ' </div>
                                        <div class="colunas_campos_novo"> ' . select2_temp(menu_admin_colunas_campos('-key-', $linhas_campos)) . ' </div>
                                        <div class="camposs_novo"> ' . select2_temp(menu_admin_campos('-kabas-', '-key-', array())) . ' </div>
                                    </div>
                                    <div class="h30 clear"></div>';


        $arr['html'] .= '</div> ';







        // DELETAR
    } elseif ($_GET['acao'] == 'delete' and $_POST) {
        foreach ($ids as $k => $v) {
            $mysql->logs = 0;
            $mysql->filtro = " where id = '" . $v . "' ";
            $mysql->delete($table);
        }
    }
}

echo json_encode(limpa_espacoes($arr));
