<?php

// DADOS LEILAO (Dados Referente ao Praca Atual)
function tipo_de_leilao($leiloes, $lotes, $key, $value, $dados) {
    // VARIOS LOTES
    if (count($lotes) > 1 AND isset($leiloes->id)) {
        $dados['box_id'] = 'leilao_' . $leiloes->id;
        $dados['nome'] = $leiloes->nome;
        $dados['codigo'] = $leiloes->codigo;
        if ($value->estados) {
            $dados['local'][$value->estados] = $value->estados;
        }
        $dados['count_lotes'] = count($lotes);

        $data_ini = $leiloes->data_ini;
        $data_fim = $leiloes->data_fim;

        // 1 LOTE
    } else {
        $dados['box_id'] = $value->id;
        $dados['nome'] = $value->nome;
        $dados['codigo'] = rel('leiloes', $value->leiloes, 'codigo');
        $dados['local'][0] = limit($value->cidades, 15) . ', ' . $value->estados;
        $dados['lance']['ini'] = $value->lance_ini > 0 ? preco($value->lance_ini, 1) : '';
        $dados['lance']['atual'] = preco($value->lances, 1);

        $dados['praca'] = praca($value);
        if ($dados['praca'] == 2) { // 2 Praca
            $dados['lance']['min'] = $value->lance_min1 > 0 ? preco($value->lance_min1, 1) : '';
            $data_ini = $value->data_ini1;
            $data_fim = $value->data_fim1;
        } else { // 1 Praca
            $dados['lance']['min'] = $value->lance_min > 0 ? preco($value->lance_min, 1) : '';
            $data_ini = $value->data_ini;
            $data_fim = $value->data_fim;
        }
    }

    // DATAS
    $dados['cronometro']['ini'] = sub_data(data($data_ini, 'd-m-Y-H-i-s'), date('d-m-Y-H-i-s'));
    $dados['cronometro']['fim'] = sub_data(data($data_fim, 'd-m-Y-H-i-s'), date('d-m-Y-H-i-s'));

    $dados['cronometro']['praca1']['fim'] = sub_data(data($value->data_fim, 'd-m-Y-H-i-s'), date('d-m-Y-H-i-s'));

    $dados['cronometro']['data']['ini'] = cronometro_data($data_ini);
    $dados['cronometro']['data']['fim'] = cronometro_data($data_fim);

    $dados['data']['ini'] = data($data_ini, 'd/m/Y');
    $dados['data']['hora_ini'] = data($data_ini, 'H:i');
    $dados['data']['fim'] = data($data_fim, 'd/m/Y');
    $dados['data']['hora_fim'] = data($data_fim, 'H:i');
    // DATAS

    return $dados;
}

// DADOS LEILAO
// INFORMACOES
function informacoes($leiloes, $lotes, $value, $dados, $lote = 0) {
    // Natureza e Tipo
    $natureza = rel('leiloes', $value->leiloes, 'natureza');
    $tipos = rel('leiloes', $value->leiloes, 'tipos');
    $dados['natureza'] = rel('natureza', $natureza);
    $dados['natureza_cor'] = rel('natureza', $natureza, 'cor');
    $dados['tipos'] = rel('tipos', $tipos);

    // Qtd de lances
    $dados['count_lances'] += $value->lances ? 1 : 0;
    $mysql = new Mysql();
    $mysql->colunas = 'COUNT(`lances`)';
    $mysql->filtro = " WHERE lotes = '" . $value->id . "' ";
    $lotes_lances = $mysql->read('lotes_lances');
    $dados['count_lances'] += current($lotes_lances[0]);

    // Dados
    $dados['count'] += $value->count;
    $dados['local'] = implode(' / ', $dados['local']);
    $dados['categorias'] = rel('lotes1_cate', $value->categorias);
    ;

    $dados['acrescimo'] = ($value->acrescimo ? $value->acrescimo : rel('leiloes', $value->leiloes, 'acrescimo')) + 2;

    $lance_atual = rel('leiloes', $value->leiloes, 'lote_atual');
    if ($lance_atual) {
        $dados['lote_atual'] = $lance_atual;
    }

    if ($lote) {
        $dados['lances_data'] = data($value->lances_data, 'd/m/Y H:i');
        $dados['lances_cadastro'] = rel('cadastro', $value->lances_cadastro, 'login');
        $dados['lances_plaquetas'] = plaquetas($value->lances_plaquetas);
    }


    // Dados das Pracas
    if (count($lotes) > 1 AND isset($leiloes->id)) {
        $dados['praca_info'] = praca_leiloes($lotes);
    } else {
        $dados['praca1']['data']['ini'] = data($value->data_ini, 'd/m/Y') . ' às ' . data($value->data_ini1, 'H:i');
        $dados['praca1']['data']['hora_ini'] = data($value->data_ini, 'H:i');
        $dados['praca1']['data']['fim'] = data($value->data_fim, 'd/m/Y') . ' às ' . data($value->data_fim1, 'H:i');
        $dados['praca1']['data']['hora_fim'] = data($value->data_fim, 'H:i');
        $dados['praca1']['lance']['ini'] = $value->lance_ini > 0 ? preco($value->lance_ini, 1) : '';
        $dados['praca1']['lance']['min'] = $value->lance_min > 0 ? preco($value->lance_min, 1) : '';

        if ($value->lance_min1 > 0) {
            $dados['praca_info'] = $dados['praca'] . 'ª ' . lang('Praça');

            $dados['praca2']['data']['ini'] = data($value->data_ini1, 'd/m/Y') . ' às ' . data($value->data_ini1, 'H:i');
            $dados['praca2']['data']['hora_ini'] = data($value->data_ini1, 'H:i');
            $dados['praca2']['data']['fim'] = data($value->data_fim1, 'd/m/Y') . ' às ' . data($value->data_fim1, 'H:i');
            $dados['praca2']['data']['hora_fim'] = data($value->data_fim1, 'H:i');
            $dados['praca2']['lance']['min'] = $value->lance_min1 > 0 ? preco($value->lance_min1, 1) : '';
        } else {
            $dados['praca_info'] = lang('Praça única');
        }
    }

    return $dados;
}

// INFORMACOES
// CRONOMETRO
function cronometro($value, $dados) {
    $mysql = new Mysql();

    $mysql_campo_lotes = array();

    // CRONOMETRO ATUAL
    $data_ini = $dados['cronometro']['ini'];
    $dados['cronometro_atual'] = $data_ini;
    $dados['cronometro_atual']['data'] = $dados['cronometro']['data']['ini'];

    if ((int) $data_ini['seg_total'] == 0) {
        $dados['cronometro_atual'] = $dados['cronometro']['fim'];
        $dados['cronometro_atual']['data'] = $dados['cronometro']['data']['fim'];
    }
    // CRONOMETRO ATUAL
    // CRONOMETRO ACRESCIMO
    // Cronometro do acrescimo rodando
    if ((int) $dados['cronometro_atual']['seg_total'] <= ((int) $dados['acrescimo'])) {
        $mysql_campo_lotes['data_acrescimo_ok'] = 1;

        $dados['cronometro']['acrescimo'] = sub_data(data($value->data_acrescimo, 'd-m-Y-H-i-s'), date('d-m-Y-H-i-s'));
        $dados['cronometro']['acrescimo']['data'] = cronometro_data($value->data_acrescimo);

        $data = $dados['cronometro_atual']['data'];
        $data_cronometro_atual = $data['ano'] . '-' . $data['mes'] . '-' . $data['dia'] . ' ' . $data['hora'] . ':' . $data['min'] . ':' . $data['seg'];
        if ($data_cronometro_atual < $value->data_acrescimo) {
            $dados['acrescimo_usando'] = 1;
            $dados['cronometro_atual'] = $dados['cronometro']['acrescimo'];
        }
    } else {
        $mysql_campo_lotes['data_acrescimo_ok'] = 0;
    }
    // CRONOMETRO ACRESCIMO 
    // STATUS
    // Em Breve
    $dados['situacao'] = 0;
    // Aberto
    if ($dados['cronometro']['ini']['seg_total'] == 0) {
        $dados['situacao'] = 1;
    }
    // Arrematado
    if ($dados['cronometro_atual']['seg_total'] == 0) {
        $dados['situacao'] = 2;
    }
    // STATUS
    // ZERANDO O CRONOMETRO
    // Arrematado
    if ($value->situacao == 2) {
        $dados['situacao'] = 2;

        // Não Arrematado
    } elseif ($value->situacao == 3) {
        $dados['situacao'] = 3;

        // Condicionado
    } elseif ($value->situacao == 10) {
        $dados['situacao'] = 10;

        // Condicionado
    } elseif ($value->situacao == 20) {
        $dados['situacao'] = 20;


        // Em Loteamento ou Aberto
    } else {

        $situacao = '';
        $enviar_email = 0;
        if ((int) $dados['cronometro_atual']['seg_total'] == 0) {
            // Verificando Zerado na 2 praca
            if ($dados['praca'] == '2ª Praça') {
                if ($value->lances == 0) {
                    $situacao = 'lance_zerado';
                } elseif ($value->lances < $value->lance_min1) {
                    $situacao = 'condicionado';
                } else {
                    $situacao = 'arrematado';
                }

                // Verificando Zerado na 1 praca
            } else {
                if ($value->lances == 0) {
                    $situacao = 'lance_zerado';
                } else if ($value->lances < $value->lance_min) {
                    $situacao = 'condicionado';
                } else {
                    $situacao = 'arrematado';
                }
            }
        }

        // Verificando de lance min do 1 praca vou atingido quando zerar Cronometro no 1 praca indo para a 2 praca
        if ((int) $dados['cronometro']['praca1']['fim']['seg_total'] == 0) {
            if ($dados['praca'] == 2 AND ! $value->praca2) {
                if ($value->lances >= $value->lance_min) {
                    $situacao = 'arrematado';
                    $dados['praca'] = 1;
                    $dados['praca_info'] = '1ª ' . lang('Praça');
                } else {
                    unset($mysql->campo);
                    $mysql->logs = 0;
                    $mysql->campo['praca2'] = 1; // So para saber q ja fpi para a praca2 sem ser arrematado na praca1
                    $mysql->filtro = " where id = '" . $value->id . "' ";
                    $mysql->update('lotes');
                }
            }
        }

        // Leilao Condicionado
        if ($situacao == 'condicionado') {
            $dados['situacao'] = 10;
            unset($mysql->campo);
            $mysql->logs = 0;
            $mysql->campo['situacao'] = 10;
            $mysql->filtro = " where id = '" . $value->id . "' ";
            $mysql->update('lotes');

            email_leilao_condicional($value->id, $value->lances_cadastro);

            // Leilao Arrematado
        } elseif ($situacao == 'arrematado') {
            $dados['situacao'] = 2;
            unset($mysql->campo);
            $mysql->logs = 0;
            $mysql->campo['situacao'] = 2;
            $mysql->filtro = " where id = '" . $value->id . "' ";
            $mysql->update('lotes');

            email_leilao_arrematado($value->id, $value->lances_cadastro);

            // Leilao Não Arrematado (Venda Direta)
        } elseif ($situacao == 'lance_zerado') {
            $dados['situacao'] = 3;
            unset($mysql->campo);
            $mysql->logs = 0;
            $mysql->campo['situacao'] = 3;
            $mysql->filtro = " where id = '" . $value->id . "' ";
            $mysql->update('lotes');
        }
    }
    // ZERANDO O CRONOMETRO
    // ACERTANDO STATUS
    if ($dados['situacao'] == 0 OR $dados['situacao'] == 1) {
        $mysql_campo_lotes['situacao'] = 0;
    }
    if ($dados['praca'] == 1) {
        $mysql_campo_lotes['praca2'] = 0;
    }
    // ACERTANDO STATUS
    // GRAVANDO DADOS DO LOTE
    $mysql->logs = 0;
    unset($mysql->campo);
    $mysql->campo = $mysql_campo_lotes;
    $mysql->filtro = " where id = '" . $value->id . "' ";
    $ult_id = $mysql->update('lotes');
    // GRAVANDO DADOS DO LOTE


    return $dados;
}

// CRONOMETRO
// CRONOMETRO_DATA
function cronometro_data($data) {
    $return = array();
    $ex = explode('-', data($data, 'd-m-Y-H-i-s'));
    $return['dia'] = $ex[0];
    $return['mes'] = $ex[1];
    $return['ano'] = $ex[2];
    $return['hora'] = $ex[3];
    $return['min'] = $ex[4];
    $return['seg'] = $ex[5];
    return $return;
}

// CRONOMETRO_DATA
?>