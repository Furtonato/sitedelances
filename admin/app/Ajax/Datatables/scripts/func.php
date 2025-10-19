<?php

// Consulta
function dt_consulta($table, $post, $cols, $cols_extra, $filtro, $modulos) {
    $publicMysql = new publicMysql();
    $mysql = new Mysql();

    // Nao existe a tabela
    $mysql = new Mysql();
    if (!$mysql->existe($table)) {
        $saida["data"] = array();
        $saida["oTable"] = '_boxs';
        $saida["draw"] = 1;
        $saida["recordsTotal"] = 0;
        $saida["recordsFiltered"] = 0;
        $saida["html"] = '';
        echo json_encode($saida);
        exit();
    }

    // Colunas
    $cols_extra1 = array();
    $ex = $cols_extra ? explode(', ', $cols_extra) : array();
    foreach ($ex as $key => $value) {
        $cols_extra1[][] = $value;
    }
    $colunas = array_merge($cols, $cols_extra1);

    // Consulta principal
    $colunas = "SQL_CALC_FOUND_ROWS `id`, `" . implode("`, `", dt_coluna($colunas, 99)) . "`";
    $where = dt_filter($post, $cols, $filtro);
    $order = dt_order($post, $cols);
    $limit = dt_limit($post);


    // Colunas extra
    if ($modulos->modulo == 'leiloes') {
        $colunas .= ", `obs`";
    } elseif ($modulos->modulo == 'lotes') {
        $colunas .= ", `data_fim`, `data_ini1`, `data_fim1`, `lance_min1`, `lances_plaquetas`, `obs`";
    }


    // echo " SELECT {$colunas} FROM `{$table}` $where $order $limit ";
    $sql = $publicMysql->db->query(" SELECT {$colunas} FROM `{$table}` $where $order $limit ");
    $sql->setFetchMode(PDO::FETCH_OBJ);
    $array = $sql->fetchAll();
    foreach ($array as $key => $value) {
        $array = $mysql->modificando_objeto($key, $value, $table, $array);
        $array_final = $mysql->menu_admin_admins($array, $table);
    }
    $return['dados'] = isset($array_final) ? $array_final : array();

    // Total de Itens Filtrado
    $sql = $publicMysql->db->query(" SELECT FOUND_ROWS() ");
    $aResultFilterTotal = $sql->fetchAll();
    $return['total_filtrado'] = $aResultFilterTotal[0][0];

    // Total de Itens
    $sql = $publicMysql->db->query(" SELECT COUNT(`id`) FROM `$table` ");
    $aResultTotal = $sql->fetchAll();
    $return['total'] = $aResultTotal[0][0];


    // Juntando 2 Banco
    /*
      if($table == 'pedidos' and $where != $_POST['filtro']){
      $table1 = 'pedidos1';
      $sql = $publicMysql->db->query(" SELECT {$colunas} FROM `{$table1}` $where $order $limit ");
      $sql->setFetchMode(PDO::FETCH_OBJ);
      $array = $sql->fetchAll();
      foreach ($array as $key => $value){
      $array[$key]->table = $table1;
      }
      $return['dados'] = array_merge($return['dados'], $array);

      $sql = $publicMysql->db->query(" SELECT FOUND_ROWS() ");
      $aResultFilterTotal = $sql->fetchAll();
      $return['total_filtrado'] += $aResultFilterTotal[0][0];

      $sql = $publicMysql->db->query(" SELECT COUNT(`id`) FROM `$table1` ");
      $aResultTotal = $sql->fetchAll();
      $return['total'] += $aResultTotal[0][0];

      }
     */

    return $return;
}

// Limit
function dt_limit($post) {
    $limit = '';
    if (isset($post['start']) && $post['length'] != -1) {
        $limit = "LIMIT " . intval($post['start']) . ", " . intval($post['length']);
    }
    return $limit;
}

// Order
function dt_order($post, $cols) {
    $order = '';
    if (isset($post['order']) and $post['order']) {
        $orderBy = array();
        $col = dt_coluna($cols);
        foreach ($post['order'] as $key => $value) {
            $orderBy[] = '`' . $col[$value['column']] . '` ' . $value['dir'];
        }
        $order = 'ORDER BY ' . implode(', ', $orderBy);
    }
    return $order;
}

// Filter
function dt_filter($post, $cols, $filtro) {
    global $table;
    $where = '';
    if (isset($post['search']['value']) and $post['search']['value']) {

        // Filtro Padrao
        $busca = array();
        foreach ($cols as $key => $col) {

            // Cidade Estado
            if (isset($col[1]) and $col[1] == 'cidade_estado') {
                $ex = explode('-', str_replace(' ', '', $col[2]));
                $busca[] = " `" . $ex[0] . "` IN ( SELECT `id` FROM `local_cidades` where `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ) ";
                $busca[] = " `" . $ex[1] . "` IN ( SELECT `id` FROM `local_estados` where `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ) ";

                // Select
            } elseif (isset($col[1]) and $col[1] == 'select') {
                $coluna = $col[2] ? $col[2] : 'nome';
                if (isset($col[3]) AND $col[3] == 'select') {
                    $coluna1 = $col[4] ? $col[4] : 'nome';
                    $busca[] = " `" . $col[0] . "` IN ( SELECT `id` FROM `" . $col[0] . "` where `" . $col[2] . "` IN ( SELECT `id` FROM `" . $coluna . "` where `" . $coluna1 . "` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ) ) ";
                } else {
                    $busca[] = " `" . $col[0] . "` IN ( SELECT `id` FROM `" . $col[0] . "` where `" . $coluna . "` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ) ";
                }

                // Select1
            } elseif (isset($col[1]) and $col[1] == 'select1') {
                $itens = array();
                foreach ($post['modulos_campos'] as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        if ($v2['input']['nome'] == $col[0] and isset($v2['check']) and $v2['check'])
                            $opcoes = $v2['input']['opcoes'];
                    }
                }
                $opcoes = explode('; ', $opcoes);
                $cont = 0;
                for ($c = 0; $c < count($opcoes); $c++) {
                    $ex = explode('->', $opcoes[$c]);
                    if (isset($ex[1])) {
                        $preg = preg_match('(' . cod('busca', strtoupper($post['search']['value'])) . ')', strtoupper($ex[1]));
                        $itens[] = "( `" . $col[0] . "` = '" . $ex[0] . "' and 1 = " . $preg . " ) ";
                    }
                }
                if ($itens)
                    $busca[] = '(' . implode(' OR ', $itens) . ')';

                // Categorias
            } elseif (isset($col[1]) and $col[0] == 'categorias' and ! preg_match('(1_cate)', $table)) {
                $busca[] = " (`categorias` IN ( SELECT `id` FROM `" . $table . "1_cate` where `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ))    OR    (`categorias` IN ( SELECT `id` FROM `" . $table . "1_cate` where `subcategorias` IN ( SELECT `id` FROM `" . $table . "1_cate` where `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ) )) ";

                // VCategorias
            } elseif (isset($col[1]) and $col[0] == 'vcategorias' and ! preg_match('(1_cate)', $table)) {
                $mysql = new Mysql();
                $mysql->colunas = 'id';
                $mysql->filtro = " WHERE `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ";
                $consulta = $mysql->read($table . "1_cate");
                foreach ($consulta as $key => $value) {
                    $busca[] = " (`vcategorias` LIKE concat('%', '-" . $value->id . "-', '%') )";
                }

                // Subcategorias
            } elseif (isset($col[1]) and $col[0] == 'subcategorias') {
                $busca[] = " (`subcategorias` IN ( SELECT `id` FROM `" . $table . "` where `nome` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ))";

                // Data
            } elseif (isset($col[1]) and $col[1] == 'data') {
                $data = str_replace(' ', '/', $post['search']['value']);
                $data = str_replace(':', '/', $data);
                $data = explode('/', $data);
                $data_final = $data[0];
                if (isset($data[1]))
                    $data_final = $data[1] . '-' . $data[0];
                if (isset($data[2]))
                    $data_final = $data[2] . '-' . $data[1] . '-' . $data[0];
                if (isset($data[3]))
                    $data_final = $data[2] . '-' . $data[1] . '-' . $data[0] . ' ' . $data[3];
                if (isset($data[4]))
                    $data_final = $data[2] . '-' . $data[1] . '-' . $data[0] . ' ' . $data[3] . ':' . $data[4];
                if (isset($data[5]))
                    $data_final = $data[2] . '-' . $data[1] . '-' . $data[0] . ' ' . $data[3] . ':' . $data[4] . ':' . $data[5];
                $busca[] = " `" . $col[0] . "` REGEXP \"" . cod('busca', $data_final) . "\" ";
            } else {
                $busca[] = " `" . $col[0] . "` REGEXP \"" . cod('busca', $post['search']['value']) . "\" ";
            }
        }
        if ($busca)
            $where = '(' . implode(' OR ', $busca) . ')';
        //echo $where;		
    }

    $filtro = str_replace('date(Y-m-d)', date('Y-m-d'), $filtro);
    $filtro = str_replace('date(c)', date('c'), $filtro);

    $return = $where ? $filtro . ' and (' . $where . ')' : $filtro;
    return $return;
}

// Info da Div das linhas
function dt_info($value, $col, $key) {
    $classe = ($col[0] == 'nome') ? 'tal ' : '';
    foreach ($col as $k => $v) {
        if (isset($col[$k]) and preg_match('(class=)', $col[$k]))
            $classe .= str_replace('class=', '', $v);
    }
    $return['class'] = $classe ? $classe . ' ' : '';
    if (!$key) {
        $return['dir'] = $value->id;
        //} elseif($key==1){
        //$return['data'] = data($value->data, 'd/m/Y H:i');
    }
    return ($return);
}

// Pegar values da table
function dt_value($value, $colunas = '*') {
    $mysql = new Mysql();
    $mysql->colunas = $colunas == '*' ? $colunas : implode(',', $colunas);
    $mysql->prepare = array($value->id);
    $mysql->filtro = " WHERE `id` = ? ";
    $return = $mysql->read_unico($value->table);
    return ($return);
}

// Pluck
function dt_pluck($a, $prop) {
    $out = array();
    for ($i = 0, $len = count($a); $i < $len; $i++) {
        $ex = explode('->', $a[$i][$prop]);
        $out[] = $ex[0];
    }
    return $out;
}

// Colunas
function dt_coluna($array, $k = 0) {
    $out = array();
    foreach ($array as $key => $value) {
        if ($k and $k != 99) {
            $out[] = $key;
        } elseif ($k == 0) {
            $out[] = $value[0];
        } elseif ($value[0] != 'id') {
            $out[] = $value[0];
        }
    }
    //$out = array_unique($out);
    return $out;
}

// Check
function td_check($col, $item, $modulos) {
    $return = array();
    $opcoes = '';
    if ($col[2]) {
        $mysql = new Mysql();
        $mysql->colunas = "id, nome";
        $mysql->filtro = " ORDER BY `nome` ASC ";
        $consulta = $mysql->read($col[2]);
        foreach ($consulta as $key => $value) {
            if (preg_match('(-' . $value->id . '-)', $item)) {
                $nome = '>>&nbsp;' . str_replace(' ', '&nbsp;', $value->nome);
                if ($modulos->modulo == 'cupons') {
                    $x = 0;
                    $ex = explode('-', $item);
                    foreach ($ex as $k => $v) {
                        if ($v == $value->id)
                            $x++;
                    }
                    $nome .= '&nbsp;(' . $x . ')';
                }
                $return[] = $nome;
            }
        }
    } else {
        $array = unserialize($modulos->campos);
        foreach ($array as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v['input']['nome'] == $col[0])
                    $opcoes = $v['input']['opcoes'];
            }
        }
        $itens = explode('; ', $opcoes);
        for ($x = 0; $x < count($itens); $x++) {
            $ex = explode('->', $itens[$x]);
            if (isset($ex[1]) and $item == $ex[0]) {
                $return[] = '>> ' . $ex[1];
            }
        }
    }
    return implode('<br>', $return);
}

// Datatable Filtro
function dt_datatable_filtros($post) {
    $return = '';
    if (isset($post['value'])) {
        foreach ($post['value'] as $key => $value) {
            if ($value != '') {
                if (isset($post['filtro'][$key]) and $post['filtro'][$key]) {
                    $filtro = $post['filtro'][$key];
                    $filtro = str_replace('date(Y-m-d)', date('Y-m-d'), $filtro);
                    $filtro = str_replace('date(c)', date('c'), $filtro);
                    eval("\$eval = \"$filtro\";");
                    $return .= $eval;
                } else {
                    $return .= dt_datatable_filtro($post, $key, $value);
                }
            }
        }
    }
    //echo $return;
    return $return;
}

function dt_datatable_filtro($post, $key, $value) {
    $return = '';
    $tipo = $post['tipo'][$key];
    $coluna = str_replace(FF, '', $key);

    if ($tipo == 'date' or $tipo == 'datetime-local') {
        if (isset($value['from']) and $value['from'] and isset($value['to']) and $value['to']) {
            if (browser() == 'firefox') {
                $ex = dividir_data($value['from'], '/');
                $value['from'] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
                $ex = dividir_data($value['to'], '/');
                $value['to'] = $ex[2] . '-' . $ex[1] . '-' . $ex[0];
            }
            $ex = explode('-', $value['to']);
            $to = date('Y-m-d', mktime(0, 0, 0, $ex[1], $ex[2] + 1, $ex[0]));
            $return .= " and `" . $coluna . "` BETWEEN ('" . $value['from'] . "') AND ('" . $to . "') ";
        }

        // Categorias e Subcategorias
    } elseif ($tipo == 'select' and $coluna == 'categorias') {
        $opcoes = str_replace('(banco)->', '', $post['opcoes'][$key]);
        $return .= " and (`categorias` = $value  or `categorias` IN (SELECT id FROM " . $opcoes . " where subcategorias = $value ) ) ";
    } elseif ($tipo == 'radio' or $tipo == 'select' or $tipo == 'number') {
        $return .= " and `" . $coluna . "` = '" . $value . "' ";
    } elseif ($tipo == 'checkbox') {
        $it = array();
        foreach ($value as $k1 => $v1)
            $it[] = " `" . $coluna . "` LIKE concat('%', '-" . $v1 . "-', '%') ";
        if ($it)
            $return .= " and (" . implode('or', $it) . ") ";
    } elseif ($tipo == 'preco') {
        $return .= " and `" . $coluna . "` = '" . $value . "' ";
    } else {
        $return .= " and `" . $coluna . "` REGEXP '" . cod('busca', $value) . "' ";
    }
    return $return;
}

// Criar Colunas
function dt_criar_colunas($modulos, $dt_value) {
    $criarMysql = new criarMysql();
    if (isset($modulos->informacoes) and preg_match('(-star-)', $modulos->informacoes) and ! isset($dt_value->star)) {
        $criarMysql->criarColunas($modulos->modulo, 'star', 'int');
    }
    if (isset($modulos->informacoes) and preg_match('(-lancamentos-)', $modulos->informacoes) and ! isset($dt_value->lancamentos)) {
        
    }
    if (isset($modulos->informacoes) and preg_match('(-promocao-)', $modulos->informacoes) and ! isset($dt_value->promocao)) {
        
    }
}

// Colunas Datatable Row Data
function dt_colunas_row_data($modulos) {
    $col = array();
    $colunas = unserialize(base64_decode($modulos->colunas));
    $campos = unserialize(base64_decode($modulos->campos));
    $col_foto = 0;
    foreach ($campos[0] as $key => $value) {
        if (isset($value['check']) and $value['check']) {
            if ($value['input']['nome'] == 'foto')
                $col_foto = 1;
        }
    }
    foreach ($colunas as $key => $value) {
        if (isset($value['check']) and $value['check']) {
            $ex = explode('->', $value['value']);
            if ($ex[0] == 'foto') {
                if ($col_foto) {
                    $col[] = $value['value'];
                }
            } elseif ($ex[0] == 'relacionamento_categoria_automatico') {
                if (preg_match('(-categorias-)', $modulos->informacoes)) {
                    $col[] = 'categorias';
                } elseif (preg_match('(-vcategorias-)', $modulos->informacoes)) {
                    $col[] = 'vcategorias';
                } elseif (preg_match('(-subcategorias-)', $modulos->informacoes)) {
                    $col[] = 'subcategorias';
                }
            } else {
                $col[] = $value['value'];
            }
        }
    }
    if (!isset($gerenciar_itens) and ( preg_match('(-star-)', $modulos->informacoes) or preg_match('(-lancamentos-)', $modulos->informacoes) or preg_match('(-promocao-)', $modulos->informacoes) or preg_match('(-mapa-)', $modulos->informacoes) )) {
        $col[] = 'id->acoes';
    }
    return $col;
}

?>