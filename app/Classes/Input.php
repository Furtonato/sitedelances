<?php

class Input extends Mysql {

    public $value;
    public $coluna;
    public $tags = ' class="design" ';
    public $opcoes;
    public $extra;
    public $filtro;
    public $p = '';
    public $dois_pontos = ':';
    public $filtro_avancado = 0;

    // Value
    public function value($name) {
        $value = $this->value;
        $value_old = $this->value;
        $coluna = $this->coluna;

        $return = '';
        if ($this->value) {
            if ($this->coluna AND isset($value->$coluna))
                $value = $value->$coluna;
            elseif (isset($value->$name))
                $value = $value->$name;

            if (is_object($value)) {
                $value = '';
            }
        }

        if ($name != 'multifotos')
            $value = cod('html->asc', $value);

        // Preco
        $casas = 2;
        if (preg_match('(casas=)', $this->tags)) {
            $casas = entre('casas="', '"', $this->tags);
        }

        if (!$this->filtro_avancado AND $value) {
            $value = (preg_match('(preco)', $this->tags)) ? number_format($value, $casas, '.', '') : $value;
        }

        return $value;
    }

    // Validate
    private function validate() {
        $return = '';
        if (preg_match("(required)", $this->tags)) {
            $return = '<span class="c_vermelho">*</span>';
        }
        return $return;
    }

    // Text
    public function text($nome, $name, $type = 'text') {

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';
        $value = $this->value($name) != '' ? $this->value($name) : '';

        $return = '<div class="finput finput_' . $name . '"> ';
        $return .= $nome ? '<label class="lnome" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : ' <label class="lnome"> &nbsp; </label> ';

        $return .= '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '"> ';

        $data_filtro_avancado = $this->filtro_av($nome, $name, $type, $id, $value);
        if ($data_filtro_avancado) { // Data Filtro Avancado
            $return .= $data_filtro_avancado;

            // Input Date Normal
        } elseif (browser() != 'chrome' AND $type == 'date') {
            $return .= '<input type="' . $type . '" name="' . $name . '" ' . $id . ' ' . $this->tags . ' value="' . data($value, 'd/m/Y') . '" /> <input type="hidden" name="data_firefox[' . str_replace('[', ';;x;;', str_replace(']', '', $name)) . ']" /> ';

            // Input Date-Local Normal
        } elseif (browser() != 'chrome' AND $type == 'datetime-local') {
            $return .= '<input type="' . $type . '" name="' . $name . '" ' . $id . ' ' . $this->tags . ' value="' . data($value, 'd/m/Y') . 'T' . data($value, 'H:i') . '" /> <input type="hidden" name="datatime_firefox[' . str_replace('[', ';;x;;', str_replace(']', '', $name)) . ']" /> ';
        } elseif ($type == 'datetime-local') {
            $return .= '<input type="' . $type . '" name="' . $name . '" ' . $id . ' ' . $this->tags . ' value="' . data($value, 'Y-m-d') . 'T' . data($value, 'H:i') . '" /> ';

            // Input Normal
        } else {
            $return .= '<input type="' . $type . '" name="' . $name . '" ' . $id . ' ' . iff($value, 'value="' . stripcslashes($value) . '"') . ' ' . $this->tags . ' /> ';
        }

        $return .= '</div>';

        $return .= '</div>';
        $return .= "\n\n";

        $this->extra = '';
        return $return;
    }

    // Data Datatable Filtro (Filtro Avancado)
    public function filtro_av($nome, $name, $type, $id, $value) {

        $input_nome = str_replace('datatable_filtro[value][', '', $name);
        $input_nome = str_replace(']', '', $input_nome);

        // From e To
        $from = isset($_POST['datatable_filtro']['value'][$input_nome]['from']) ? $_POST['datatable_filtro']['value'][$input_nome]['from'] : '';
        $to = isset($_POST['datatable_filtro']['value'][$input_nome]['to']) ? $_POST['datatable_filtro']['value'][$input_nome]['to'] : '';

        // Filtro Avancado Value (item com array e item normal)
        if ($this->filtro_avancado AND $from AND $to AND ( $type == 'date' or $type == 'datetime-local')) {
            $return = '	<div class="design w50p fll pr5"> <input type="' . $type . '" name="' . $name . '[from]" ' . $this->tags . ' value="' . iff(browser() != 'chrome', $from, data($from, 'Y-m-d')) . '" /> </div>
							<div class="design w50p dib pl5"> <input type="' . $type . '" name="' . $name . '[to]"  ' . $this->tags . ' value="' . iff(browser() != 'chrome', $to, data($to, 'Y-m-d')) . '" /> </div>
							<div class="clear"></div> ';


            // Filtro Avancado sem Value
        } elseif ($this->filtro_avancado AND ( $type == 'date' or $type == 'datetime-local')) {
            $return = '	<div class="design w50p fll pr5"> <input type="' . $type . '" name="' . $name . '[from]" ' . $this->tags . ' /> </div>
							<div class="design w50p dib pl5"> <input type="' . $type . '" name="' . $name . '[to]"  ' . $this->tags . ' /> </div>
							<div class="clear"></div> ';
        }

        $return = isset($return) ? $return : '';
        return $return;
    }

    // File
    public function file($nome, $name) {
        $img = new Imagem();

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';
        $value = $this->value;

        $return = '<div class="finput finput_' . $name . '" rel="tooltip" data-original-title="' . $this->extra . '"> ';
        if (LUGAR != 'site')
            $return .= $nome ? '<label for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : ' <label> &nbsp; </label> ';
        $valor = $this->value(str_replace('[]', '', $name));
        $cor = ($valor AND $valor != 'a:0:{}') ? 'c_azul3' : 'c_cinza8';
        $return .= '<label for="' . $name . '" class="input file"> <span class="vm c-p limit"> <i class="fa fa-file-image-o ml2 mr3 ' . $cor . ' "></i> <span>Selecionar Arquivo' . iff(preg_match('(multifotos)', $name), 's') . '</span> </span> <input type="file" name="' . $name . '" ' . $id . ' ' . $this->tags . ' onChange="input_file(this)" ' . iff(preg_match('(multifotos)', $name), 'multiple') . ' /> </label> ';

        // Pop File
        if ($this->value($name) AND ! preg_match('(multifotos)', $name)) {
            $nome_da_foto = nome_da_foto($value->$name);

            $nome_img = (strtolower($nome_da_foto['ext']) == 'png' or strtolower($nome_da_foto['ext']) == 'bmp') ? $value->$name : '/' . $nome_da_foto['nome'] . '.' . $nome_da_foto['ext'];

            $aparcer_img = 0;
            if (strtolower($nome_da_foto['ext']) == 'png' or strtolower($nome_da_foto['ext']) == 'bmp')
                $aparcer_img = 1;
            if (strtolower($nome_da_foto['ext']) == 'pjpeg' or strtolower($nome_da_foto['ext']) == 'jpeg' or strtolower($nome_da_foto['ext']) == 'jpg' or strtolower($nome_da_foto['ext']) == 'gif') {
                $img->caminho = preg_match('(site)', LUGAR) ? '../web/fotos' : '../../web/fotos';
                $img->foto = $name;
                $img->img($value, 50, 50);
                $aparcer_img = 1;
            }

            $img = (object) array();
            $img->tags = ' class="max-w50 max-h50 m1 bd1c br3" ';
            $return .= '<div class="pop_file ml50"> ';
            $return .= '<div class="arrow_all"> <div class="arrow"></div> </div> ';
            if ($aparcer_img) {
                $return .= '<div class="mr10 fll"> ';
                $return .= '<a href="' . DIR . '/web/fotos/' . $value->$name . '" target="_blank"> ';
                $return .= '<img src="' . DIR . '/web/fotos/' . $nome_img . '" class="max-w50 max-h50 m1 bd1c br3"> ';
                $return .= '</a> ';
                $return .= '</div> ';
            }
            $return .= '<div class="w120 fll"> ';
            $return .= '<a href="' . DIR . '/web/fotos/' . $value->$name . '" target="_blank" class="db pt5 pb10 fz14"> <b>Ver Arquivo</b> </a> ';
            $return .= '<label><input type="checkbox" name="sem_foto[' . $name . ']" value="1" class="design vt" /> Excluir Arquivo</label> ';
            $return .= '</div> ';
            $return .= '<div class="clear"></div> ';
            $return .= '</div> ';
        } else {
            $name = str_replace('[]', '', $name);
            $img = new Imagem();
            $img->caminho = '../../web/fotos';
            if ($this->value($name)) {
                $itens = unserialize(base64_decode($this->value($name)));
                if (is_array($itens) AND $itens) {
                    $return .= '<div class="w328 pop_file pb5 ml50"> ';
                    $return .= '<div class="arrow_all"> <div class="arrow"></div> </div> ';
                    $x = 1;
                    $return .= '<div class="max-h140 o-a"> ';
                    foreach ($itens as $k => $v) {
                        $x++;
                        $return .= '<div class="w144 fll"> ';
                        $return .= '<div class="w57 fll limit"> ';
                        $return .= '<a href="' . DIR . '/web/fotos/' . $v . '" target="_blank"> ';
                        $return .= $img->img((object) array('foto' => $v), 50, 50);
                        $return .= '</a> ';
                        $return .= '</div> ';
                        $return .= '<div class="w68 fll">';
                        $return .= '<a href="' . DIR . '/web/fotos/' . $v . '" target="_blank" class="db pt5 pb4"> <b>Ver Arquivo</b> </a> ';
                        $return .= '<label class="db pt3 pb4"><input type="checkbox" name="sem_multifotos[' . $name . '][]" value="' . $k . '" class="design" /> Excluir </label> ';
                        $return .= '</div> ';
                        $return .= '<div class="clear"></div> ';
                        $return .= '</div> ';
                        $return .= ($x % 2) ? '<div class="clear h5"></div> ' : '';
                    }
                    $return .= '<div class="clear"></div> ';
                    $return .= '</div> ';
                    $return .= '</div> ';
                }
            }
        }
        // Pop File

        $return .= '</div>';
        $return .= "\n\n";

        $this->extra = '';
        return $return;
    }

    // Select
    public $selecione = '- - -';
    public $onchange = '';
    public $table = '';
    public $val = '';
    public $datatable_box = 0;
    public $gerenciar_item = 1;

    public function select($nome, $name) {

        $niveis = 1000;
        $funcao = '';
        $set = '';

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';
        $this->onchange = $this->onchange ? 'onchange="location = options[selectedIndex].value"' : '';

        // (Catgorias) = (banco)->xxx1cate
        $opcoes = $this->opcoes;
        $this->opcoes = preg_match('(categorias)', $this->opcoes) ? '(banco)->' . $this->table . '1_cate' : $this->opcoes;
        $itens = explode('(banco)->', $this->opcoes);
        if (isset($itens[1]) AND $itens[1]) {
            $itens = explode('->', $this->opcoes);
        }

        // Anos
        $ex = explode('(anos)->', $this->opcoes);
        if (isset($ex[1]) AND $ex[1]) {
            $anos = '';
            for ($i = date('Y'); $i >= $ex[1]; $i = $i - 1) {
                $anos .= $i . '->' . $i . '; ';
            }
            $this->opcoes = $anos;
        }

        // Funcoes
        if (isset($itens[2]) AND $itens[2]) {
            if ($itens[2] == 'func' or ( isset($itens[3]) AND $itens[3] == 'func') or ( isset($itens[4]) AND $itens[4] == 'func'))
                $funcao = $itens[count($itens) - 1];
            if ($itens[2] == 'set' or ( isset($itens[3]) AND $itens[3] == 'set') or ( isset($itens[4]) AND $itens[4] == 'set'))
                $set = $itens[count($itens) - 1];
        }

        // Item de relacionamento (pai="xxx")
        $item = isset($this->value->id) ? ' item="' . $this->value->id . '" ' : '';

        $return = '<div class="finput finput_' . $name . ' ' . iff(preg_match('(multiple)', $this->tags), 'mb5') . ' " > ';
        $return .= $nome ? '<label class="lnome" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : ' <label class="lnome"> &nbsp; </label> ';

        // ESTADOS CIDADES
        if ($this->opcoes == '(estados)' OR $this->opcoes == '(cidades)' OR $this->opcoes == '(bairros)') {
            $return .= $this->select_localizacoes($name, $id, $itens);

            // MULTIPLE
        } elseif (preg_match('(multiple)', $this->tags)) {
            $return .= $this->select_multiple($name, $id, $itens, $niveis, $funcao, $set, $opcoes, $item);
        } else {
            // Verificar se pode criar novo item
            $select2_criar = 0;
            $criar_novo = 0;
            $niveis = 1000;
            if (isset($itens[2]) AND $itens[2] AND $this->gerenciar_item AND ( $itens[2] == 'novo' OR $itens[2] == 'novo1')) {
                $select2_criar = 1;
            } elseif (preg_match('(categorias)', $opcoes) AND $name != 'subcategorias') {
                if (LUGAR == 'admin') {
                    $select2_criar = 1;
                } else {
                    $criar_novo = 1;
                }
            }
            if ($select2_criar) {
                $mysql = new Mysql();
                $mysql->prepare = array($itens[1]);
                $mysql->filtro = " WHERE `modulo` = ? ";
                $modulos = $mysql->read_unico('menu_admin');
                if (isset($modulos->informacoes) AND ! preg_match('(novo)', $modulos->informacoes)) {
                    $criar_novo = 0;
                }
            }
            $select2_criar = (!$this->filtro_avancado AND ! $this->datatable_box) ? $select2_criar : 0;


            // CRIAR NOVO
            if ($select2_criar) {
                $return .= $this->select_criar_novo($name, $id, $itens, $niveis, $funcao, $set, $opcoes, $item);

                // COM BANCO
            } elseif (isset($itens[1]) AND $itens[1]) {
                $return .= $this->select_banco($name, $id, $itens, $niveis, $funcao, $set, $opcoes);

                // NORMAL
            } else {
                $return .= $this->select1($name, $id);
            }
        }

        $return .= '</div> ';
        $return .= "\n\n";

        $this->onchange = '';
        $this->extra = '';
        $this->selecione = '- - -';
        return $return;
    }

    // Select Estados Cidades
    public function select_localizacoes($name, $id, $itens) {
        // Rel
        if (preg_match('(rel_estados=)', $this->tags)) {
            if (preg_match('(inserir_box)', $this->tags))
                $this->onchange = 'onchange="rel_estados(this, 1)" ';
            else
                $this->onchange = 'onchange="rel_estados(this)" ';
        }

        $return = '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '" > ';
        $return .= '<select name="' . $name . '" ' . $id . ' ' . $this->tags . ' ' . $this->onchange . ' > ';
        $return .= $this->selecione ? '<option value="">' . $this->selecione . '</option> ' : '';

        if ($this->opcoes == '(estados)') {
            if ($this->value($name))
                $_GET['estados_rel'] = $this->value($name);
            $caminho = caminho('/plugins/Json/localidades/estados.json');
            if (file_exists($caminho)) {
                $json = json_decode(file_get_contents($caminho));
                foreach ($json as $value) {
                    $return .= '<option value="' . $value->sigla . '" ' . iff($this->value($name) == $value->sigla, 'selected') . '> ';
                    $return .= $value->nome;
                    $return .= '</option> ';
                }
            }
        } elseif ($this->opcoes == '(cidades)') {
            if (isset($_GET['estados_rel']) AND $_GET['estados_rel']) {
                if ($this->value($name))
                    $_GET['cidades_rel'] = $this->value($name);
                $_GET['estados_rel'] = (isset($_GET['estados_rel']) AND $_GET['estados_rel']) ? $_GET['estados_rel'] : str_replace('cidades', 'estados', $name);
                $caminho = caminho('/plugins/Json/localidades/cidades/' . $_GET['estados_rel'] . '.json');
                if (file_exists($caminho)) {
                    $json = json_decode(file_get_contents($caminho));
                    foreach ($json as $value) {
                        foreach ($value->cidades as $v) {
                            $return .= '<option value="' . $v . '" ' . iff($this->value($name) == $v, 'selected') . '> ';
                            $return .= $v;
                            $return .= '</option> ';
                        }
                    }
                }
            }
        } elseif ($this->opcoes == '(bairros)') {
            if (isset($_GET['estados_rel']) AND $_GET['estados_rel'] AND isset($_GET['cidades_rel']) AND $_GET['cidades_rel']) {
                $return .= '<option value="Centro" ' . iff($this->value($name) == 'Centro', 'selected') . '>Centro</option> ';

                $caminho = caminho('/plugins/Json/localidades/bairros/' . $_GET['estados_rel'] . '.json');
                if (file_exists($caminho)) {
                    $json = json_decode(file_get_contents($caminho));
                    foreach ($json as $value) {
                        if ($value->cidade == $_GET['cidades_rel']) {
                            foreach ($value->bairros as $v) {
                                $return .= '<option value="' . $v . '" ' . iff($this->value($name) == $v, 'selected') . '> ';
                                $return .= $v;
                                $return .= '</option> ';
                            }
                        }
                    }
                }
            }
        }


        $return .= '</select> ';
        $return .= '</div> ';

        return $return;
    }

    // Select Multiple
    public function select_multiple($name, $id, $itens, $niveis, $funcao, $set, $opcoes, $item) {
        $return = '';
        if (!isset($this->value->id) AND preg_match('(multiple rel)', $this->tags)) {
            $return = '<style>.finput.finput_' . $name . ' { display: none; } </style>';
        } else {
            // Filtro avancado
            $value = $this->value($name);

            // Tamanho
            if (isset($itens[1]) AND $itens[1] == 'tamanhos')
                $this->filtro = " where status = 1 AND lang = '" . LANG . "' ORDER BY ordem asc, nome asc ";

            // Pai
            if (isset($this->value->id) AND preg_match('(pai=")', $this->tags)) {
                $pai = entre('pai="', '"', $this->tags);
                $this->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `" . $pai . "` = '" . $this->value->id . "' ORDER BY ordem asc, nome asc ";
            }

            // Cores e Tamanhos
            if ($name == 'produtos_cores_tamanhos' OR $name == 'produtos_opcoes1' OR $name == 'produtos_opcoes2' OR $name == 'produtos_opcoes3' OR $name == 'produtos_opcoes4' OR $name == 'produtos_opcoes5') {
                $this->colunas = "id, nome, preco, estoque";
            } else {
                $this->colunas = "id, nome";
            }

            $return .= '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '" > ';
            $return .= preg_match('(multiple)', $this->tags) ? '<input type="checkbox" checked value="" name="' . $name . '[]" class="dni" />' : '';
            $return .= '<select name="' . $name . iff(preg_match('(multiple)', $this->tags), '[]') . '" ' . $id . ' ' . $this->tags . ' ' . $this->onchange . ' ' . $item . ' > ';

            $this->nao_existe = 1;
            $this->filtro = $this->filtro ? $this->filtro : " WHERE `status` = 1 AND `lang` = '" . LANG . "' ORDER BY `nome` ASC ";
            $consulta = $this->read($itens[1]);
            if (isset($consulta) AND is_array($consulta)) {
                foreach ($consulta as $v) {
                    $z = 0;
                    if ($value) {
                        $opcional = explode('-', $value);
                        for ($i = 0; $i < count($opcional); $i++)
                            if ($opcional[$i] == $v->id)
                                $z++;
                    }
                    $return .= '<option value="' . $v->id . '" ' . iff($z, 'selected') . '> ';
                    if ($name == 'produtos_cores_tamanhos' OR $name == 'produtos_opcoes1' OR $name == 'produtos_opcoes2' OR $name == 'produtos_opcoes3' OR $name == 'produtos_opcoes4' OR $name == 'produtos_opcoes5')
                        $return .= $v->nome . ' (Estoque: ' . $v->estoque . ') (' . preco($v->preco, 1) . ')';
                    else
                        $return .= $v->nome;
                    $return .= '</option> ';
                }
            }

            $return .= '</select> ';
            $return .= '</div> ';
        }

        return $return;
    }

    // Select Criar Novo
    public function select_criar_novo($name, $id, $itens, $niveis, $funcao, $set, $opcoes, $item) {
        $pop = ' pop="' . $itens[1] . '" ';
        $criar_novo = (isset($itens[2]) AND $itens[2]) == 'novo1' ? 1 : 0;

        $gerenciar_itens = 1;

        // Categorias
        if (preg_match('(categorias)', $opcoes)) {
            $criar_novo = 1;
            $pop = ' pop="' . $this->table . '1_cate" ';
            if (LUGAR != 'admin') {
                $gerenciar_itens = 0;
            }
        }

        // Pai
        $filtro_pai = '';
        if (isset($this->value->id) and preg_match('(pai=")', $this->tags)) {
            $pai = entre('pai="', '"', $this->tags);
            $filtro_pai = " AND " . $pai . " = '" . $this->value->id . "' ";
        }

        $return = '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '" > ';
        $return .= '<select name="' . $name . '" ' . $id . ' ' . $this->tags . ' ' . $this->onchange . ' ' . $pop . ' ' . $item . ' > ';
        $return .= $this->selecione ? '<option value="">' . iff(preg_match('(subcategorias)', $opcoes) and ! $this->filtro_avancado, 'Categoria Principal', $this->selecione) . '</option> ' : '';

        $return .= '<optgroup label="Ações" id="acoes"> ';
        $return .= $criar_novo ? '<option value="(cn)">Cadastrar Novo</option> ' : '';
        $return .= $gerenciar_itens ? '<option value="(gi)">Gerenciar Itens</option> ' : '';
        $return .= '</optgroup> ';
        $return .= '<optgroup label="Itens" id="itens"> ';

        $this->colunas = "id, nome" . iff(preg_match('(1_cate)', $itens[1]), ', tipo');
        $this->nao_existe = 1;
        $filtro_extra = '';
        if (!preg_match('(admin)', LUGAR)) {
            $filtro_extra .= " `status` = 1 AND ";
            if (preg_match('(1_cate)', $itens[1])) {
                $filtro_extra .= " (`" . table_admin() . "` = '' OR `" . table_admin() . "` = '" . $_SESSION['x_' . LUGAR]->id . "') AND ";
            }
        }
        if (preg_match('(1_cate)', $itens[1])) {
            $filtro_extra .= " `tipo` = 0 AND ";
        }
        $this->filtro = $this->filtro ? $this->filtro : " WHERE " . $filtro_extra . "  `lang` = '" . LANG . "' " . $filtro_pai . " ORDER BY `nome` ASC ";
        if ($itens[1] == 'usuarios')
            $this->filtro = " WHERE `id` != 1 ORDER BY `nome` ASC ";
        $consulta = $this->read($itens[1]);
        if (isset($consulta) and is_array($consulta)) {
            foreach ($consulta as $value) {
                $return .= '<option value="' . $this->val . $value->id . '" ' . iff($this->value($name) == $value->id, 'selected') . '> ';
                $return .= preg_match('(1_cate)', $itens[1]) ? tracos_nivels($value->tipo) . ' ' : '';
                if (isset($funcao) AND $funcao) {
                    $valor = (double) $value->nome;
                    eval("\$eval = $funcao;");
                    $return .= $eval;
                } elseif (isset($set) AND $set) {
                    $return .= set($itens[1], $value->id, $set);
                } else {
                    $return .= $value->nome;
                }
                $return .= '</option> ';
                $return .= preg_match('(1_cate)', $itens[1]) ? categorias_nivels($itens[1], $value->id, $niveis, '', $this->value($name)) : '';
            }
        }

        $return .= '</optgroup> ';

        $return .= '</select> ';
        $return .= '</div> ';

        return $return;
    }

    // Select Banco
    public function select_banco($name, $id, $itens, $niveis, $funcao, $set, $opcoes) {
        // Categorias
        if (preg_match('(subcategorias)', $opcoes)) {
            $itens[1] = $this->table;
            $ex = explode('->', $opcoes);
            $niveis = (isset($ex[1]) AND $ex[1]) ? $ex[1] : 0;
        }

        $return = '';
        //if(1){
        $return .= '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '" > ';
        $return .= '<select name="' . $name . '" ' . $id . ' ' . $this->tags . ' ' . $this->onchange . ' > ';

        if (isset($_GET['get']['leiloes']) AND $name == 'leiloes') {
            $return .= '<option value="' . $_GET['get']['leiloes'] . '">' . rel('leiloes', $_GET['get']['leiloes']) . '</option> ';
        } else {

            $return .= $this->selecione ? '<option value="">' . iff(preg_match('(subcategorias)', $opcoes) AND ! $this->filtro_avancado, 'Categoria Principal', $this->selecione) . '</option> ' : '';

            $this->colunas = "id, nome" . iff(preg_match('(1_cate)', $itens[1]), ', tipo') . iff($itens[1] == 'cadastro', ', login');
            $this->nao_existe = 1;
            $this->filtro = $this->filtro ? $this->filtro : " WHERE " . iff(!preg_match('(admin)', LUGAR), '`status` = 1 AND') . " " . iff(preg_match('(1_cate)', $itens[1]), '`tipo` = 0 AND') . "  `lang` = '" . LANG . "' ORDER BY `nome` ASC ";
            if ($itens[1] == 'usuarios')
                $this->filtro = " WHERE `id` != 1 ORDER BY `nome` ASC ";
            $consulta = $this->read($itens[1]);
            if (isset($consulta) AND is_array($consulta)) {
                foreach ($consulta as $value) {
                    $return .= '<option value="' . $this->val . $value->id . '" ' . iff($this->value($name) == $value->id, 'selected') . '> ';
                    $return .= preg_match('(1_cate)', $itens[1]) ? tracos_nivels($value->tipo) . ' ' : '';
                    if (isset($funcao) AND $funcao) {
                        $valor = (double) $value->nome;
                        eval("\$eval = $funcao;");
                        $return .= $eval;
                    } elseif (isset($set) AND $set) {
                        $return .= set($itens[1], $value->id, $set);
                    } else {
                        if ($itens[1] == 'cadastro') {
                            $return .= $value->nome . ' (' . $value->login . ')';
                        } else {
                            $return .= $value->nome;
                        }
                    }
                    $return .= '</option> ';
                    $return .= preg_match('(1_cate)', $itens[1]) ? categorias_nivels($itens[1], $value->id, $niveis, '', $this->value($name)) : '';
                }
            }
        }

        $return .= '</select> ';
        $return .= '</div> ';
        //} else {
        //$return = '<div class="h30"></div> <script>$(".finput_leiloes label ").hide();</script>';
        //}

        return $return;
    }

    // Select1
    public function select1($name, $id) {
        $return = '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '" > ';
        $return .= '<select name="' . $name . '" ' . $id . ' ' . $this->tags . ' ' . $this->onchange . '> ';
        $itens = explode('; ', $this->opcoes);
        for ($c = 0; $c < count($itens); $c++) {
            $ex = explode('->', $itens[$c]);
            if (!$c AND ( $ex[0] or $this->filtro_avancado))
                $return .= $this->selecione ? '<option value="">' . $this->selecione . '</option> ' : '';
            if (isset($ex[1])) {
                $return .= '<option value="' . $this->val . $ex[0] . '" ' . iff($this->value($name) == $ex[0], 'selected') . '> ';
                $return .= $ex[1];
                $return .= '</option> ';
            }
        }
        $return .= '</select> ';
        $return .= '</div> ';

        return $return;
    }

    // Checkbox
    public $check_ini = 1;

    public function checkbox($nome, $name) {

        $return = '<div class="finput finput_' . $name . '" > ';
        $return .= $nome ? '<label class="lnome" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : '';

        $return .= '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '"> ';
        $return .= $this->check_ini ? '<input type="checkbox" checked value="" name="' . $name . '[]" class="dni" />' : '';

        $value = $this->value($name);
        if ($this->filtro_avancado AND $value) {
            $it = '-';
            foreach ($value as $k => $v) {
                $it .= $v . '-';
            }
            $value = $it;
        }

        $itens = explode('(banco)->', $this->opcoes);
        if (isset($itens[1]) AND $itens[1]) {

            $this->colunas = "id, nome";
            $this->nao_existe = 1;
            $this->filtro = $this->filtro ? $this->filtro : " WHERE `status` = 1 AND `lang` = '" . LANG . "' ORDER BY `nome` ASC ";
            $consulta = $this->read($itens[1]);
            if (isset($consulta) AND is_array($consulta)) {
                foreach ($consulta as $v) {
                    $id = !preg_match('(id=")', $this->tags) ? 'id="' . $name . '_' . $v->id . '" ' : '';
                    $z = 0;
                    if ($value) {
                        $opcional = explode('-', $value);
                        for ($i = 0; $i < count($opcional); $i++)
                            if ($opcional[$i] == $v->id)
                                $z++;
                    }
                    $return .= '<label class="' . $name . '_' . $v->id . ' l' . $name . '"> ';
                    $return .= '<input type="checkbox" value="' . $v->id . '" name="' . $name . '[' . $v->id . ']" ' . $id . ' ' . $this->tags . ' ' . iff($z, 'checked') . '/> ';
                    $return .= '<p ' . $this->p . '>' . $v->nome . '</p> ';
                    $return .= '</label>';
                }
            }
        } else {

            $itens = explode('; ', $this->opcoes);
            for ($c = 0; $c < count($itens); $c++) {
                $ex = explode('->', $itens[$c]);
                if (isset($ex[1])) {
                    $id = !preg_match('(id=")', $this->tags) ? 'id="' . $name . '_' . $ex[0] . '" ' : '';
                    $z = 0;
                    if ($value) {
                        $opcional = explode('-', $value);
                        for ($i = 0; $i < count($opcional); $i++)
                            if ($opcional[$i] == $ex[0])
                                $z++;
                    }
                    $return .= '<label class="' . $name . '_' . $ex[0] . ' l' . $name . '"> ';
                    $return .= '<input type="checkbox" value="' . $ex[0] . '" name="' . $name . '[' . $ex[0] . ']" ' . $id . ' ' . $this->tags . ' ' . iff($z, 'checked') . '/> ';
                    $return .= '<p ' . $this->p . '>' . $ex[1] . '</p> ';
                    $return .= '</label>';
                }
            }
        }
        $return .= '</div> ';

        $return .= '</div> ';
        $return .= "\n\n";


        $this->extra = '';
        return $return;
    }

    // Radio
    public function radio($nome, $name) {

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';

        $return = '<div class="finput finput_' . $name . '" > ';
        $return .= $nome ? '<label class="lnome" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : '';

        $value = $this->value($name);

        $return .= '<div class="input" rel="tooltip" data-original-title="' . $this->extra . '"> ';
        $itens = explode('(banco)->', $this->opcoes);
        if (isset($itens[1]) AND $itens[1]) {

            $cont = 0;
            $this->colunas = "id, nome";
            $this->nao_existe = 1;
            $this->filtro = $this->filtro ? $this->filtro : " WHERE `status` = 1 AND `lang` = '" . LANG . "' ORDER BY `nome` ASC ";
            $consulta = $this->read($itens[1]);
            if (isset($consulta) AND is_array($consulta)) {
                foreach ($consulta as $v) {
                    $cont++;
                    $return .= '<label class="' . $name . '_' . $v->id . ' l' . $name . '"> ';
                    $return .= '<input type="radio" value="' . $v->id . '" name="' . $name . '" id="' . $name . '_' . $v->id . '" ' . $this->tags . ' ' . iff(($cont == 1 AND ! $value ) or ( $value AND $value == $v->id), 'checked') . '/> ';
                    $return .= '<p ' . $this->p . '>' . $v->nome . '</p> ';
                    $return .= '</label> ';
                }
            }
        } else {
            $itens = explode('; ', $this->opcoes);
            $cont = 0;
            for ($c = 0; $c < count($itens); $c++) {
                $ex = explode('->', $itens[$c]);
                if (isset($ex[1])) {
                    $cont++;
                    $return .= '<label class="' . $name . '_' . $ex[0] . ' l' . $name . '"> ';
                    $return .= '<input type="radio" value="' . $ex[0] . '" name="' . $name . '" id="' . $name . '_' . $ex[0] . '" ' . $this->tags . ' ' . iff(($cont == 1 AND ! $value ) or ( $value AND $value == $ex[0]), 'checked') . '/> ';
                    $return .= '<p ' . $this->p . '>' . $ex[1] . '</p> ';
                    $return .= '</label>';
                }
            }
        }
        $return .= '</div> ';

        $return .= '</div> ';
        $return .= "\n\n";

        $this->extra = '';
        return $return;
    }

    // Textarea
    public $limit;

    public function textarea($nome, $name) {

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';

        $limit1 = '';
        $limit2 = '';
        $this->limit = (int) $this->limit;
        if ($this->limit) {
            $id = "'" . $name . "'";
            $limit1 = 'onkeyup="progreso_tecla(this, ' . $this->limit . ', ' . $id . ')" maxlength="' . $this->limit . '"';
            $limit2 = '<span id="progreso_' . $name . '" class="extra height20"></span>';
            $id = 'id="' . tirar_barras($name) . '" ';
        }
        $return = '<div class="finput finput_' . $name . ' ftextarea pl10" > ';
        $return .= $nome ? '<label class="lnome p0" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : ' ';
        $return .= '<div class="clear"></div> ';
        //$return .= $nome ? '<label class="lnome p0" for="'.$name.'" > <p '.$this->p.'> &nbsp; </p> </label> ' : ' ';
        $return .= '<div class="input dbi" rel="tooltip" data-original-title="' . $this->extra . '"> <textarea name="' . $name . '" ' . $id . ' ' . $this->tags . ' ' . $limit1 . ' >' . $this->value($name) . '</textarea> </div> ';
        $return .= $limit2;
        $return .= '<div class="clear"></div> ';
        $return .= '</div> ';
        $return .= "\n\n";

        $this->extra = '';
        return $return;
    }

    // Editor
    public $enviar_termo_nota;

    public function editor($nome, $name) {

        $return = '';
        $tipo = str_replace('txt_editor', '', $name);

        $value = $this->value;
        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';

        $return .= '<div class="finput finput_editor min-h300 pl10" > ';
        $return .= $nome ? '<label class="lnome p0" for="' . $name . '" > <p ' . $this->p . '> ' . $nome . $this->validate() . $this->dois_pontos . ' </p> </label> ' : ' ';
        $return .= '<div class="clear"></div> ';

        $txt = '';
        if (isset($value->table) AND $value->table) {
            $this->colunas = "txt";
            $this->prepare = array($value->table, $value->id, $tipo);
            $this->filtro = " WHERE `tabelas` = ? AND `item` = ? AND `tipo` = ? ";
            $z_txt = $this->read_unico('z_txt');
            if (isset($z_txt->txt)) {
                $txt = base64_decode($z_txt->txt);

                if ($this->enviar_termo_nota) {
                    $mysql = new Mysql();
                    $mysql->colunas = 'id, nome, pago, ordem, leiloes, lances, lances_cadastro, lances_data, cidades, estados';
                    $mysql->filtro = " WHERE `id` = '" . $_POST['lote'] . "' ";
                    $lotes = $mysql->read_unico('lotes');

                    $mysql->colunas = 'id, nome, email';
                    $mysql->filtro = " WHERE id = '" . $lotes->lances_cadastro . "' ";
                    $cadastro = $mysql->read_unico('cadastro');

                    $var_email = email_55($cadastro, $lotes);
                    $txt = var_email($txt, $var_email, 1);
                }
            }
        }
        $return .= '<div class="input dbi"> <textarea ' . $id . ' name="' . $name . '">' . $txt . '</textarea> </div> ';
        $return .= ' <script> editor_criar_extarea(' . A . $name . A . '); </script> ';

        $return .= '</div> ';
        $return .= "\n\n";

        return $return;
    }

    // Button
    public function button($nome, $name) {

        $id = !preg_match('(id=")', $this->tags) ? 'id="' . tirar_barras($name) . '" ' : '';
        $value = $this->value($name) != '' ? 'value="' . $this->value($name) . '" ' : '';
        $opcoes = $this->opcoes ? ' onclick="' . str_replace(' ', '', $this->opcoes) . '" ' : '';
        $extra = '<div class="extra ' . $name . '">' . $this->extra . '</div> <div class="clear"></div> ';

        $return = '<div class="finput fbutton finput_' . $name . '" rel="tooltip" data-original-title="' . $this->extra . '"> ';
        $return .= $extra;
        $return .= '<button type="button" name="' . $name . '" ' . $id . ' ' . $value . ' ' . $this->tags . ' ' . $opcoes . '> <p ' . $this->p . '> ' . $nome . ' </p> </button> ';
        $return .= '</div>';
        $return .= "\n\n";

        $this->extra = '';
        return $return;
    }

    // File Editor
    public function file_editor($nome, $name) {
        $return = '';

        $value = $this->value;
        if ($value)
            $value = $value->$name;


        $return .= '<div class="funcao_input ' . $name . '_input"> ';
        $return .= $nome ? '<span>' . $nome . '</span> ' : '';
        $return .= '<div class="clear"></div> ';

        if (!isset($_SESSION['plugin']['editor'])) {
            $return .= '<script src="' . DIR . '/plugins/Ckeditor/ckeditor/ckeditor.js"></script>';
            $_SESSION['plugin']['editor'] = 'ok';
        }
        if (!isset($_SESSION['plugin']['ckfinder'])) {
            $return .= '<script src="' . DIR . '/plugins/Ckeditor/ckfinder/ckfinder.js"></script>';
            $_SESSION['plugin']['ckfinder'] = 'ok';
        }
        $return .= '<script type="text/javascript"> ';
        $return .= 'function BrowseServer_' . $name . '(){ ';
        $return .= 'var finder = new CKFinder(); ';
        $return .= 'finder.basePath = "../../";	/* The path for the installation of CKFinder (default = "/ckfinder/"). */ ';
        $return .= 'finder.selectActionFunction = SetFileField_' . $name . '; ';
        $return .= 'finder.popup(); ';
        $return .= '}; ';
        $return .= 'function SetFileField_' . $name . '( fileUrl ){ ';
        $return .= 'document.getElementById( "' . $name . '" ).value = fileUrl; ';
        $return .= '}; ';
        $return .= '</script> ';
        $return .= '<input id="' . $name . '" name="' . tirar_barras($name) . '" type="text" class="width300 design" value="' . $value . '" /> ';
        $return .= '<input type="button" value="Buscar Arquivo" onclick="BrowseServer_' . $name . '();" class="design_submit" /> ';
        $return .= '</div> ';
        $return .= "\n\n";

        return $return;
    }

    // Info
    public function info() {
        return '';
    }

}

?>