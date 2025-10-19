<?php

class Controllers extends Mysql {

    // INCLUDES

    public function includes() {

        // CORES CSS
        define('LARANJA', '153C89');
        // CORES CSS
        // Topo
        $this->colunas = 'id';
        $this->filtro = " WHERE " . STATUS . " AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['total_leiloes'] = $this->read('leiloes');

        $this->colunas = 'id';
        $this->filtro = "  WHERE " . STATUS . " " . SITUACAO . " ORDER BY data_ini asc, " . ORDER . " ";
        $dados['total_lotes'] = $this->read('lotes');

        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['topo_frases'] = $this->read('frases');


        // Busca Refinada
        $dados['topo_status'][] = (object) array('id' => 0, 'nome' => lang('Em Loteamento'));
        $dados['topo_status'][] = (object) array('id' => 1, 'nome' => lang('Aberto'));
        $dados['topo_status'][] = (object) array('id' => 2, 'nome' => lang('Arrematado'));
        $dados['topo_status'][] = (object) array('id' => 3, 'nome' => lang('Não Arrematado'));
        $dados['topo_status'][] = (object) array('id' => 10, 'nome' => lang('Em Condicional'));
        $dados['topo_status'][] = (object) array('id' => 20, 'nome' => lang('Venda Direta'));

        $this->colunas = 'id, nome';
        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['topo_tipos'] = $this->read('tipos');

        $this->colunas = 'id, nome';
        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['topo_natureza'] = $this->read('natureza');

        $this->colunas = 'id, nome, cor';
        $this->filtro = " WHERE " . STATUS . " AND star = 1 ORDER BY " . ORDER . " ";
        $dados['topo_lotes1_cate_star'] = $this->read('lotes1_cate');

        $this->colunas = 'id, nome, cor, icon';
        $this->filtro = " WHERE " . STATUS . " AND lancamentos = 1 ORDER BY " . ORDER . " ";
        $dados['topo_lotes1_cate_lancamentos'] = $this->read('lotes1_cate');

        $this->colunas = 'id, nome';
        $this->filtro = " WHERE " . STATUS . " AND `tipo` = 0 ORDER BY nome asc, " . ORDER . " ";
        $dados['topo_lotes1_cate'] = $this->read('lotes1_cate');
        foreach ($dados['topo_lotes1_cate'] as $value) {
            $this->colunas = 'id, nome';
            $this->prepare = array($value->id);
            $this->filtro = " WHERE " . STATUS . " AND `subcategorias` = ? ORDER BY nome asc, " . ORDER . " ";
            $dados['topo_lotes1_cate1'][$value->id] = $this->read('lotes1_cate');
            foreach ($dados['topo_lotes1_cate1'][$value->id] as $value1) {
                $this->colunas = 'id, nome';
                $this->prepare = array($value1->id);
                $this->filtro = " WHERE " . STATUS . " AND `subcategorias` = ? ORDER BY " . ORDER . " ";
                $dados['topo_lotes1_cate2'][$value1->id] = $this->read('lotes1_cate');
            }
        }

        $this->colunas = 'cidades, estados';
        $this->filtro = " WHERE " . STATUS . " AND cidades != '' GROUP BY cidades ORDER BY estados asc, " . ORDER . " ";
        $dados['topo_cidades'] = $this->read('lotes');
        foreach ($dados['topo_cidades'] as $key => $value) {
            $dados['topo_estados'][$value->estados][$key] = $value->cidades;
        }
        // Busca Refinada
        // Topo
        // Footer
        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['footer_servicos'] = $this->read('servicos');
        // Footer
        // Padrao
        $this->filtro = " WHERE `tipo` = 'emails' ";
        $dados['emails'] = $this->read_unico('configs');

        $this->filtro = " WHERE  `tipo` = 'informacoes' AND lang = '" . LANG . "' ";
        $dados['info'] = $dados['informacoes'] = $this->read_unico('configs');

        if (isset($_SESSION['x_site']->id)) {
            $this->prepare = array($_SESSION['x_site']->id);
            $this->filtro = " WHERE `id` = ? ";
            $dados['cadastro_pd'] = $this->read_unico('cadastro');
        }

        $textos = $this->read('textos');
        foreach ($textos as $key => $value) {
            $dados['textos'][$value->id] = $value;
        }
        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['paginas'] = $this->read('paginas');

        $this->filtro = " ORDER BY `lugar` DESC";
        $banner = $this->read_unico('banner');
        if (isset($banner->lugar)) {
            for ($i = 1; $i <= $banner->lugar; $i++) {
                $this->prepare = array($i);
                $this->filtro = " WHERE " . STATUS . " AND `lugar` = ? ORDER BY `ordem` ASC, `id` DESC ";
                $dados['banner'][$i] = $this->read('banner');
            }
        }

        // Todas da informacoes do carrinho
        //$dados = carrinho_dados($dados);
        //$dados = cotacao_dados($dados);
        // Padrao

        return($dados);
    }

    // INCLUDES
    // <------------------------></------------------------>----------------------------------------------------------------------------------------------------------------------------------
    // VIEWS
    // Home
    public function inicio_old() {

        // Leiloes e Lotes Star
        $this->colunas = 'id, nome, foto, comitentes, data_ini';
        //$this->filtro = " WHERE ".STATUS." AND star = 1 AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND star = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes_star'] = $this->read('leiloes');

        $this->colunas = 'id, nome, foto, leiloes, data_ini';
        //$this->filtro = " WHERE ".STATUS." AND situacao = 0 AND star = 1 ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . "  " . SITUACAO . " AND star = 1 ORDER BY data_ini asc, " . ORDER . " ";
        $dados['lotes_star'] = $this->read('lotes');

        // Juntar Leiloes e lotes Star
        $dados['leiloes_e_lotes_star'] = leiloes_e_lotes_star($dados['leiloes_star'], $dados['lotes_star']);
        // Leiloes e Lotes Star
        //$this->filtro = " WHERE ".STATUS." AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . "  " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes'] = $this->read('leiloes', 32);

        $this->colunas = 'id, nome, foto, comitentes, data_ini';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " AND !(situacao = 0 OR situacao = 1 OR situacao = 20)) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['leiloes_arrematados'] = $this->read('leiloes');

        $this->colunas = 'id, nome, foto, leiloes, data_ini';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND !(situacao = 0 OR situacao = 1 OR situacao = 20) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['lotes_arrematados'] = $this->read('lotes');

        $this->filtro = " WHERE " . STATUS . " AND foto != '' ORDER BY " . ORDER . " ";
        $dados['comitentes'] = $this->read('comitentes');

        $this->view($_GET['pg'], $dados);
    }

    public function inicio() {

        // Leiloes e Lotes Star
        $this->colunas = '*';
        //$this->filtro = " WHERE ".STATUS." AND star = 1 AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND star = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes_star'] = $this->read('leiloes');

        $this->colunas = '*';
        //$this->filtro = " WHERE ".STATUS." AND situacao = 0 AND star = 1 ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . "  " . SITUACAO . " AND star = 1 ORDER BY data_ini asc, " . ORDER . " ";
        $dados['lotes_star'] = $this->read('lotes');

        // Juntar Leiloes e lotes Star
        $dados['leiloes_e_lotes_star'] = leiloes_e_lotes_star($dados['leiloes_star'], $dados['lotes_star']);
        // Leiloes e Lotes Star
        //$this->filtro = " WHERE ".STATUS." AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . "  " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes'] = $this->read('leiloes', 32);

        $this->colunas = '*';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " AND !(situacao = 0 OR situacao = 1 OR situacao = 20)) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['leiloes_arrematados'] = $this->read('leiloes');

        $this->colunas = '*';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND !(situacao = 0 OR situacao = 1 OR situacao = 20) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['lotes_arrematados'] = $this->read('lotes');

        $this->filtro = " WHERE " . STATUS . " AND foto != '' ORDER BY " . ORDER . " ";
        $dados['comitentes'] = $this->read('comitentes');

        $this->view($_GET['pg'], $dados);
    }
    
    

    public function inicio_new() {

        // Leiloes e Lotes Star
        $this->colunas = '*';
        //$this->filtro = " WHERE ".STATUS." AND star = 1 AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND star = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes_star'] = $this->read('leiloes');

        $this->colunas = '*';
        //$this->filtro = " WHERE ".STATUS." AND situacao = 0 AND star = 1 ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . "  " . SITUACAO . " AND star = 1 ORDER BY data_ini asc, " . ORDER . " ";
        $dados['lotes_star'] = $this->read('lotes');

        // Juntar Leiloes e lotes Star
        $dados['leiloes_e_lotes_star'] = leiloes_e_lotes_star($dados['leiloes_star'], $dados['lotes_star']);
        // Leiloes e Lotes Star
        //$this->filtro = " WHERE ".STATUS." AND `data_fim` BETWEEN ('".date('c')."') AND ('4000-12-31 00:00') AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE ".STATUS." AND situacao = 0) ORDER BY data_ini asc, ".ORDER." ";
        $this->filtro = " WHERE " . STATUS . " AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . "  " . SITUACAO . ") ORDER BY data_ini asc, " . ORDER . " ";
        $dados['leiloes'] = $this->read('leiloes', 32);

        $this->colunas = '*';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND `id` IN ( SELECT `leiloes` FROM `lotes` WHERE " . STATUS . " AND !(situacao = 0 OR situacao = 1 OR situacao = 20)) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['leiloes_arrematados'] = $this->read('leiloes');

        $this->colunas = '*';
        $this->filtro = " WHERE " . STATUS . " AND status = 1 AND !(situacao = 0 OR situacao = 1 OR situacao = 20) ORDER BY data_fim DESC, " . ORDER . " LIMIT 20 ";
        $dados['lotes_arrematados'] = $this->read('lotes');

        $this->filtro = " WHERE " . STATUS . " AND foto != '' ORDER BY " . ORDER . " ";
        $dados['comitentes'] = $this->read('comitentes');

        $this->view($_GET['pg'], $dados);
    }

    // lotes
    public function lotes() {

        $this->colunas = '*';
        $this->prepare = array($_GET['id']);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ORDER BY " . ORDER . " ";
        $dados['leiloes'] = $this->read_unico('leiloes');

        $this->colunas = '*';
        if (isset($dados['leiloes']->id)) {
            $this->prepare = array($dados['leiloes']->id);
            $this->filtro = " WHERE " . STATUS . " AND situacao = 0 AND `leiloes` = ? ORDER BY " . ORDER . " ";
            $dados['lotes'] = $this->read('lotes');

            if (count($dados['lotes']) == 1) {
                location(url('lote', $dados['lotes'][0]));
            }
        } else {
            // FILTRO
            $filtro = " ";
            if (isset($_GET['status'])) {
                $filtro .= status_leiloes($_GET['status']);
            } else {
                $filtro .= " AND situacao = 0";
            }

            /*
              if(isset($_GET['status_2']) AND is_array($_GET['status_2'])){
              $array = array();
              foreach ($_GET['status_2'] as $key => $value) {
              $array[] = "(1=1 ".status_leiloes($value).") ";
              }
              $filtro .= $array ? " AND (".implode(' OR ', $array).") " : "";
              } else {
              $filtro .= " AND ".STATUS." AND situacao = 0";
              }
             */

            if (isset($_GET['tipos']) AND is_array($_GET['tipos'])) {
                $array = array();
                foreach ($_GET['tipos'] as $key => $value) {
                    $array[] = " tipos = '" . $value . "' ";
                }
                $filtro .= $array ? " AND `leiloes` IN ( SELECT `id` FROM `leiloes` WHERE (" . implode(' OR ', $array) . ") ) " : "";
            }
            if (isset($_GET['natureza']) AND is_array($_GET['natureza'])) {
                $array = array();
                foreach ($_GET['natureza'] as $key => $value) {
                    $array[] = " natureza = '" . $value . "' ";
                }
                $filtro .= $array ? " AND `leiloes` IN ( SELECT `id` FROM `leiloes` WHERE (" . implode(' OR ', $array) . ") ) " : "";
            }
            if (isset($_GET['cate']) AND is_array($_GET['cate'])) {
                $array = array();
                foreach ($_GET['cate'] as $key => $value) {
                    $array[] = " categorias = '" . $value . "' ";
                }
                $filtro .= $array ? " AND (" . implode(' OR ', $array) . ") " : "";
            }
            if (isset($_GET['estados']) AND is_array($_GET['estados'])) {
                $array = array();
                foreach ($_GET['estados'] as $key => $value) {
                    $array[] = " estados = '" . $value . "' ";
                }
                $filtro .= $array ? " AND (" . implode(' OR ', $array) . ") " : "";
            }
            if (isset($_GET['cidades']) AND is_array($_GET['cidades'])) {
                $array = array();
                foreach ($_GET['cidades'] as $key => $value) {
                    $array[] = " cidades = '" . $value . "' ";
                }
                $filtro .= $array ? " AND (" . implode(' OR ', $array) . ") " : "";
            }
            // FILTRO

            $this->filtro = " WHERE " . STATUS . " " . $filtro . " " . filtro_fixo('categorias') . " " . filtro_busca() . " ORDER BY " . ORDER . " ";
            $dados['lotes'] = $this->read('lotes', 40);
        }

        $this->view($_GET['pg'], $dados);
    }

    // lote
    public function lote() {

        $this->colunas = '*';
        $this->prepare = array($_GET['id']);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ORDER BY " . ORDER . " ";
        $dados['item'] = $this->read_unico('lotes');

        $this->prepare = array($dados['item']->leiloes);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ORDER BY " . ORDER . " ";
        $dados['leiloes'] = $this->read_unico('leiloes');

        $cadastro = isset($_SESSION['x_site']->id) ? $_SESSION['x_site']->id : 0;
        $this->prepare = array($dados['item']->id);
        $this->filtro = " WHERE cadastro = '" . $cadastro . "' AND `leiloes` IN ( SELECT `leiloes` FROM `lotes` WHERE id = ? ) ";
        $dados['leiloes_habilitacoes'] = $this->read('leiloes_habilitacoes');

        $this->prepare = array($dados['item']->id);
        $this->filtro = " WHERE cadastro = '" . $cadastro . "' AND `lotes` = ? ";
        $dados['lotes_habilitacoes_sucata'] = $this->read('lotes_habilitacoes_sucata');

        countt($dados['item'], 'lotes');

        $dados['mais_fotos'] = mais_fotos($dados['item']);


        $this->colunas = '*';
        $this->prepare = array($dados['item']->leiloes);
        $this->filtro = " WHERE " . STATUS . " AND `leiloes` = ? ORDER BY " . ORDER . " ";
        $dados['lotes'] = $this->read('lotes');

        $this->view($_GET['pg'], $dados);
    }

    // lote
    public function lote_old() {

        $this->colunas = '*';
        $this->prepare = array($_GET['id']);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ORDER BY " . ORDER . " ";
        $dados['item'] = $this->read_unico('lotes');

        $this->prepare = array($dados['item']->leiloes);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ORDER BY " . ORDER . " ";
        $dados['leiloes'] = $this->read_unico('leiloes');

        $cadastro = isset($_SESSION['x_site']->id) ? $_SESSION['x_site']->id : 0;
        $this->prepare = array($dados['item']->id);
        $this->filtro = " WHERE cadastro = '" . $cadastro . "' AND `leiloes` IN ( SELECT `leiloes` FROM `lotes` WHERE id = ? ) ";
        $dados['leiloes_habilitacoes'] = $this->read('leiloes_habilitacoes');

        $this->prepare = array($dados['item']->id);
        $this->filtro = " WHERE cadastro = '" . $cadastro . "' AND `lotes` = ? ";
        $dados['lotes_habilitacoes_sucata'] = $this->read('lotes_habilitacoes_sucata');

        countt($dados['item'], 'lotes');

        $dados['mais_fotos'] = mais_fotos($dados['item']);


        $this->colunas = '*';
        $this->prepare = array($dados['item']->leiloes);
        $this->filtro = " WHERE " . STATUS . " AND `leiloes` = ? ORDER BY " . ORDER . " ";
        $dados['lotes'] = $this->read('lotes');

        $this->view($_GET['pg'], $dados);
    }

    // faq
    public function faq() {

        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['faq'] = $this->read('faq');

        $this->view($_GET['pg'], $dados);
    }
    
    
    // faq
    public function leilaoseguro() {

        $this->filtro = " WHERE " . STATUS . " ORDER BY " . ORDER . " ";
        $dados['faq'] = $this->read('faq');

        $this->view($_GET['pg'], $dados);
    }
    
    // servico
    public function servico() {

        $this->prepare = array($_GET['id']);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ";
        $dados['item'] = $this->read_unico('servicos');

        $dados['mais_fotos'] = mais_fotos($dados['item']);

        $this->view($_GET['pg'], $dados);
    }

    // imprimir
    public function imprimir() {
        $this->view($_GET['pg'], '');
    }

    // excel
    public function excel() {
        if (!isset($_SESSION['x_admin']->id)) {
            echo '<script> window.parent.location="' . DIR . '/' . ADMIN . '/login.php"; </script>';
            exit();
        } else {
            require_once('../views/excel.phtml');
        }
    }

    // VIEWS
    // ------------------------------------------------------------------------------------------------------------------------------------------
    // VIEWS PADROES
    // Textos
    public function textos() {
        $banco = 'textos';
        if ($_GET['pg_real'] == 'textosp') {
            $banco = 'paginas';
        } elseif ($_GET['pg_real'] == 'textosp1') {
            $banco = 'paginas1_cate';
        }

        $this->prepare = array($_GET['id']);
        $this->filtro = " WHERE " . STATUS . " AND `id` = ? ";
        $dados['item'] = $this->read_unico($banco);
        $dados['titulo'] = $dados['item']->nome;

        $dados['mais_fotos'] = $banco == 'textos' ? multifotos($dados['item']) : mais_fotos($dados['item']);

        $this->view($_GET['pg'], $dados);
    }

    // Paginas Padroes ou com Ajax
    public function fale() {
        $this->view($_GET['pg'], '');
    }

    public function login() {
        $this->view($_GET['pg'], '');
    }

    public function cadastro() {
        $this->view($_GET['pg'], '');
    }

    public function carrinho() {
        //verificar_sessao('carrinho');
        unset($_SESSION['desconto']);
        $this->view($_GET['pg'], '');
    }

    public function minha_conta() {
        verificar_sessao('minha_conta');
        $this->view($_GET['pg'], '');
    }

    // Cotacao
    public function cotacao() {
        $this->filtro = " WHERE `tipo` = 'emails' ";
        $dados['configs_contato'] = $this->read('configs');

        // Excluir Cotacao
        if (isset($_GET['cotacao_excluir']) and $_GET['cotacao_excluir']) {
            unset($_SESSION['cotacao']['id'][$_GET['banco']][$_GET['cotacao_excluir']]);
            unset($_SESSION['cotacao']['qtd'][$_GET['banco']][$_GET['cotacao_excluir']]);
            location(DIR . '/cotacao/');
        }

        $dados = cotacao_dados($dados);

        if (isset($_POST['enviar_email']))
            unset($_SESSION['cotacao']);

        $this->view($_GET['pg'], $dados);
    }

    // VIEWS PADROES
    // ------------------------------------------------------------------------------------------------------------------------------------------
    // Apps

    private function view($pg, $vars = null) {
        global $dados;

        $globais = array('pagg');
        foreach ($globais as $value) {
            $vars[$value] = $dados[$value];
        }

        global $config_zz;
        foreach (@$config_zz as $key => $value) {
            $vars[$key] = $value;
        }

        // Padroes
        $head = new Head();
        define('META', $head->meta());

        if ($_GET['pg'] != 'imprimir') {
            define('CSS', $head->css());
            define('JAVASCRIPT', $head->js());
        } else {
            define('CSS', '');
            define('JAVASCRIPT', '');
        }

        // Includes
        extract($this->includes(), EXTR_OVERWRITE);

        // Variaveis
        if (is_array($vars) and count($vars)) {
            extract($vars, EXTR_OVERWRITE);
        }

        // Pagina Real Existe?
        if (file_exists('../views/' . $_GET['pg_real'] . '.phtml'))
            $pg = $_GET['pg_real'];

        return require_once('../views/index.phtml');
    }

    // Apps
}

?>