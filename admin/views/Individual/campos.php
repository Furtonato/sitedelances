<?php

$conteudo = array('ini' => array(''), 'fim' => array(''));


// Cadastro
if ($modulos->modulo == 'cadastro') {

    if ($linhas) {
        if (LUGAR == 'admin' or LUGAR == 'clientes') {
            $conteudo['fim'][0] .= '<li class="wr12 ml12"> <a onclick="boxs(' . A . 'alterar_senha' . A . ', ' . A . 'modulos=' . $modulos->id . '&id=' . $linhas->id . A . ');" class="c_azul">Alterar senha</a> </li> ';
        }
    } else {
        $input->tags = ' class="design" required ';
        $conteudo['fim'][0] .= '<li class="wr6"> ' . $input->text('Senha', 'senha', 'password') . '</li> ';
        $conteudo['fim'][0] .= '<li class="wr6"> ' . $input->text('Confirmar Senha', 'c_senha', 'password') . '</li> ';
    }


    if ($linhas and 1 == 2) {

        $conteudo['fim'][0] .= '<div class="p20"> ';

        $conteudo['fim'][0] .= '<table class="w100p" border="0" cellspacing="2" cellpadding="2"> ';
        $conteudo['fim'][0] .= '<tr class="cor_777"> ';
        $conteudo['fim'][0] .= '<td class="w50 tal p5 pl10 pr10 back_FFE5AA bd_E0D5C3"><b>Código</b></td> ';
        $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 back_FFE5AA bd_E0D5C3"><b>Data da Compra</b></td> ';
        $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 back_FFE5AA bd_E0D5C3"><b>Data da Aprovação</b></td> ';
        $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 back_FFE5AA bd_E0D5C3"><b>Valor Total</b></td> ';
        $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 back_FFE5AA bd_E0D5C3"><b>Situação</b></td> ';
        $conteudo['fim'][0] .= '</tr> ';

        for ($i = 0; $i <= 1; $i++) {
            $tabela = !$i ? 'pedidos' : 'pedidos1';
            $mysql->nao_existe = 1;
            $mysql->prepare = array($linhas->id);
            $mysql->filtro = " WHERE `cadastro` = ? ORDER BY `id` DESC ";
            $pedidos = $mysql->read($tabela);
            if ($pedidos and is_array($pedidos)) {
                foreach ($pedidos as $key => $value) {

                    $situacao = $value->situacao ? rel('pedidos_situacoes', $value->situacao) : SITUACAO_PD;
                    $situacao_cor = $value->situacao ? rel('pedidos_situacoes', $value->situacao, 'cor') : '';
                    $situacao_icon = $value->situacao ? rel('pedidos_situacoes', $value->situacao, 'icon') : 'fa-clock-o';

                    $mysql->filtro = " where `modulo` = 'pedidos' ";
                    $menu_admin = $mysql->read_unico("menu_admin");

                    $conteudo['fim'][0] .= '<tr class="hover_table" ondblclick="views(' . $menu_admin->id . ', 1, ' . A . 'table=' . $tabela . '&id=' . $value->id . A . ')"> ';
                    $conteudo['fim'][0] .= '<td class="tal p5 pl10 pr10 bd_E0D5C3"> #' . $value->id . ' </td> ';
                    $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 bd_E0D5C3">' . $value->data . '</td> ';
                    $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 bd_E0D5C3">' . iff($value->data_aprovacao != '0000-00-00 00:00:00', data($value->data_aprovacao, 'd/m/Y H:i:s'), '. . .') . '</td> ';
                    $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 bd_E0D5C3">' . preco($value->valor_total, 1) . '</td> ';
                    $conteudo['fim'][0] .= '<td class="tac p5 pl10 pr10 bd_E0D5C3"><i class="mr5 fz14 fa ' . $situacao_icon . '" style="color:' . $situacao_cor . '"></i> ' . $situacao . '</td>';
                    $conteudo['fim'][0] .= '</tr> ';
                }
            }
        }

        $conteudo['fim'][0] .= '</table> ';
        $conteudo['fim'][0] .= '</div> ';
    }


    if ((isset($linhas->tipo) AND $linhas->tipo == 1) OR ( isset($_GET['get']['tipo']) AND $_GET['get']['tipo'] == 1)) {
        $conteudo['fim'][0] .= '<script> ';
        $conteudo['fim'][0] .= 'setTimeout(function(){ $(".finput.finput_cpf, .finput.finput_rg, .finput.finput_sexo, .finput.finput_nascimento").parent().remove(); }, 100); ';
        $conteudo['fim'][0] .= 'setTimeout(function(){ $(".finput.finput_cpf, .finput.finput_rg, .finput.finput_sexo, .finput.finput_nascimento").parent().remove(); }, 1000); ';
        $conteudo['fim'][0] .= '</script> ';
    } else {
        $conteudo['fim'][0] .= '<script> ';
        $conteudo['fim'][0] .= 'setTimeout(function(){ $(".finput.finput_cnpj, .finput.finput_ie").parent().remove(); }, 100); ';
        $conteudo['fim'][0] .= 'setTimeout(function(){ $(".finput.finput_cnpj, .finput.finput_ie").parent().remove(); }, 1000); ';
        $conteudo['fim'][0] .= '</script> ';
    }








    // Lote
} elseif ($modulos->modulo == 'lotes') {

    if (isset($linhas->situacao) AND ( $linhas->situacao == 2 OR $linhas->situacao == 3 OR $linhas->situacao == 10 OR $linhas->situacao == 20)) {
        $conteudo['ini'][0] .= '<li class="wr12 radio ">
                                            <div class="finput finput_situacao">
                                                <label class="lnome" for="situacao">
                                                    <p> Status Leillão: </p>
                                                </label>
                                                <div class="input" rel="tooltip" data-original-title="">
                                                    <label class="lsituacao">
                                                        <input type="radio" value="0" name="situacao" class="design " ' . iff($linhas->situacao == 0, 'checked') . '>
                                                        <p>Abrir para Lances</p>
                                                    </label>
                                                    <label class="lsituacao">
                                                        <input type="radio" value="2" name="situacao" class="design " ' . iff($linhas->situacao == 2, 'checked') . '>
                                                        <p>Arrematado</p>
                                                    </label>
                                                    <label class="lsituacao">
                                                        <input type="radio" value="3" name="situacao" class="design " ' . iff($linhas->situacao == 3, 'checked') . '>
                                                        <p>Não Arrematado</p>
                                                    </label>
                                                    <label class="lsituacao">
                                                        <input type="radio" value="10" name="situacao" class="design " ' . iff($linhas->situacao == 10, 'checked') . '>
                                                        <p>Em Condicional</p>
                                                    </label>
                                                    <label class="lsituacao">
                                                        <input type="radio" value="20" name="situacao" class="design " ' . iff($linhas->situacao == 20, 'checked') . '>
                                                        <p>Venda Direta</p>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="clear"></div>
                                        </li>';
    }


    if (isset($linhas->situacao) AND $linhas->situacao == 2) {
        $conteudo['ini'][0] .= '<li class="wr4 radio ">
                                            <div class="finput finput_leilao_arrematado">
                                                <label class="lnome" for="leilao_arrematado">
                                                    <p> Pagamento do Leilão: </p>
                                                </label>
                                                <div class="input" rel="tooltip" data-original-title="">
                                                    <label class="leilao_arrematado_0 lleilao_arrematado">
                                                        <input type="radio" value="0" name="pago" id="pago_0" class="design " ' . iff($linhas->pago == 0, 'checked') . '>
                                                        <p>Não Pago</p>
                                                    </label>
                                                    <label class="leilao_arrematado_1 lleilao_arrematado">
                                                        <input type="radio" value="1" name="pago" id="pago_1" class="design " ' . iff($linhas->pago == 1, 'checked') . '>
                                                        <p>Pago</p>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="clear"></div>
                                        </li>
                                        <li class="wr6 text ">
                                            <div class="finput finput_emissao">
                                                <label class="lnome" for="emissao">
                                                    <p> Data de Emissão do Termos e Da Nota </p>
                                                </label>
                                                <div class="input" rel="tooltip" data-original-title="">
                                                    <input type="text" name="emissao" value="' . $linhas->emissao . '" class="date design ">
                                                </div>
                                            </div>

                                            <div class="clear"></div>
                                        </li>
                                        <li class="wr6 text ">
                                            <div class="finput finput_emissao">
                                                <label class="lnome" for="emissao">
                                                    <p> Dias úteis para efeutar o deposito </p>
                                                </label>
                                                <div class="input" rel="tooltip" data-original-title="">
                                                    <input type="number" name="dias_uteis" value="' . $linhas->dias_uteis . '" value="2" class="design ">
                                                </div>
                                            </div>

                                            <div class="clear"></div>
                                        </li>
                                        <li class="wr6 text ">
                                            <div class="finput finput_emissao">
                                                <label class="lnome" for="emissao">
                                                    <p> Outras despesas por lote </p>
                                                </label>
                                                <div class="input" rel="tooltip" data-original-title="">
                                                    <input type="text" name="outras_despesas" value="' . $linhas->outras_despesas . '" class="preco design ">
                                                </div>
                                            </div>

                                            <div class="clear"></div>
                                        </li>';
    }








    // Leilao Ao Vivo
} elseif ($modulos->id == '76') {

    $conteudo['ini'][0] .= '<style> .pg_76 { width: 100% !important; height: calc(100% - 44px) !important; margin: 0 !important; overflow: auto; overflow-x: hidden; position: fixed !important; top: 0 !important; left: 0 !important; } </style>
                                    <script>leilao_aovivo(' . $linhas->id . ')</script>
                                    <div class="p10">
                                        <table class="w100p AOVIVO_leilao_' . $linhas->id . '">
                                            <tr>
                                                <th class="table_top tac">Número</th>
                                                <th class="table_top">Nome</th>
                                                <th class="table_top tac">Data</th>
                                                <th class="table_top tac">Lance Atual</th>
                                                <th class="table_top tac">Arrematante</th>
                                                <th class="table_top tac">Todos os Lances</th>
                                                <th class="table_top tac">Lance</th>
                                                <th class="table_top tac">Plaqueta</th>
                                                <th class="table_top tac">Ações</th>
                                            </tr> ';

    $mysql->colunas = 'id';
    $mysql->filtro = " WHERE " . STATUS . " AND `leiloes` = '" . $linhas->id . "' ORDER BY " . ORDER . " ";
    $lotes = $mysql->read('lotes');
    foreach ($lotes as $key => $value) {
        $conteudo['ini'][0] .= '            <tr class="AOVIVO_lote_' . $value->id . '">
                                                    <td class="AOVIVO_ordem table_center1">- - -</td>
                                                    <td class="AOVIVO_nome table_center1 tal">- - -</td>
                                                    <div class="dar_lance"></div>
                                                    <td class="AOVIVO_data table_center1">- - -</td>
                                                    <td class="AOVIVO_lances table_center1">- - -</td>
                                                    <td class="AOVIVO_cadastro table_center1">- - -</td>

                                                    <td class="table_center1"><a onclick="boxs(' . A . 'lances' . A . ', ' . A . 'id=' . $value->id . A . ')"><i class="fa fa-tags fz16"></i></a></td>

                                                    <td class="table_center1">
                                                        <div class="AOVIVO_dar_lance"><input type="text" name="lance" class="design w150 preco1 tac"></div>
                                                    </td>
                                                    <td class="table_center1">
                                                        <div class="AOVIVO_dar_lance"><input type="number" name="plaqueta" class="design w100 tac" min="0"></div>
                                                    </td>
                                                    <td class="w220 table_center1">
                                                        <div class="dib AOVIVO_dar_lance"><button type="button" onclick="dar_lance(' . $value->id . ', this)" class="botao mt2 mb2 m0 fwb back_4977BC_i cor_fff_i">Dar&nbsp;Lance</button></div>
                                                        <div class="dib AOVIVO_dar_lance"><button type="button" onclick="if(confirm(' . A . 'Deseja Realmente Arrematar este Lote?' . A . '))arrematar_lote(' . $value->id . ')" class="botao mt2 mb2 fwb back_E50A19_i cor_fff_i">Arrematar</button></div>
                                                    </td>

                                                </tr> ';
    }

    $conteudo['ini'][0] .= '    </table>
                                    </div>';





    // Produtos Orca
} elseif ($modulos->modulo == 'produtos_orca') {


    $conteudo['fim'][0] .= '<style> .design_x { width:300px; height: 30px; padding:5px 10px; border:1px solid #CCC; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; } </style> ';

    $conteudo['fim'][0] .= '<div class="p10"> ';

    $y = 0;
    if ($_GET['acao'] == 'edit') {
        $html = '<div class="h20"></div>';
        $conteudo['fim'][0] .= '<h3>Comprimentos</h3>';
        $conteudo['fim'][0] .= '<div class="h5"></div>';

        for ($x = 1; $x < 500; $x++) {
            $value = 'comprimento_' . $x;
            if (isset($linhas->$value) and $linhas->$value) {
                $y++;
                $conteudo['fim'][0] .= '<input type="text" name="comprimento_' . $x . '" id="comprimento_' . $x . '" class="design_x" value="' . $linhas->$value . '" /> <div class="h5"></div> ';
                $conteudo['fim'][0] .= '<div class="comprimentos_' . ($x + 1) . '"><a href="javascript:cotacao_add_comprimento(' . ($x + 1) . ')" class="link c_azul">Adicionar mais Comprimentos</a></div> ';
                $conteudo['fim'][0] .= '<style> .comprimentos_' . ($x) . ' { display:none; } </style> ';
            } else
                $x = 500;
        }

        $conteudo['fim'][0] .= '<div class="h20"></div>';
    }

    if (!$y) {
        $conteudo['fim'][0] .= '<div class="h20"></div>';
        $conteudo['fim'][0] .= '<h3>Comprimentos</h3>';
        $conteudo['fim'][0] .= '<div class="h5"></div>';
        $conteudo['fim'][0] .= '<input type="text" name="comprimento_1" id="comprimento_1" class="design_x" /> <div class="h5"></div>';
        $conteudo['fim'][0] .= '<div class="comprimentos_2"><a href="javascript:cotacao_add_comprimento(2)" class="link c_azul">Adicionar mais Comprimentos</a></div>';
        $conteudo['fim'][0] .= '<div class="h20"></div>';
    }

    $conteudo['fim'][0] .= '<script>';
    $conteudo['fim'][0] .= 'function cotacao_add_comprimento(n){ ';
    $conteudo['fim'][0] .= "$('.comprimentos_'+n).html(' <input type=text name=comprimento_'+n+' id=comprimento_'+n+' class=design_x /> <div class=h5></div> <div class=comprimentos_'+(n+1)+'><a href=javascript:cotacao_add_comprimento('+(n+1)+') class=link style=color:#1688CA>Adicionar mais Comprimentos</a></div> '); ";
    $conteudo['fim'][0] .= '} ';
    $conteudo['fim'][0] .= '</script>';


    $conteudo['fim'][0] .= '</div>';






    // Pedidos
} elseif ($modulos->modulo == 'pedidos') {

    $pedidos = $linhas;
    $mysql->prepare = array($pedidos->cadastro);
    $mysql->filtro = " WHERE `id` = ? ";
    $cadastro = $mysql->read_unico("cadastro");

    $carrinho = json_decode(file_get_contents('../../plugins/Json/pedidos/' . $pedidos->id . '.json'));

    $mysql->filtro = " ORDER BY `id` ASC ";
    $pedidos_situacoes = $mysql->read("pedidos_situacoes");

    $conteudo['fim'][0] .= '<div class="pedidos_edit">
                                        <div class="wr6 pr20">
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-th-list c_verde"></i> Status da Compra </legend>
                                                <div class="status mb10">
                                                    <ul></ul>
                                                    <script> ajaxNormalAdmin("Acoes/pedidos_situacoes.php","table=' . $table . '&id=' . $pedidos->id . '&ini=1",1) </script>
                                                    <div class="clear"></div>
                                                </div>
                                                <form id="pedidos_situacoes" action="' . DIR . '/' . ADMIN . '/app/Ajax/Acoes/pedidos_situacoes.php" method="post">
                                                    <select name="pedidos_situacoes" class="designx" required > ';
    if (LUGAR == 'admin')
        $conteudo['fim'][0] .= '<option value="0" ' . iff($pedidos->situacao == 0, 'selected') . '>' . SITUACAO_PD . '</option> ';
    foreach ($pedidos_situacoes as $key => $value) {
        if (LUGAR == 'admin' or ( LUGAR == 'empresas' and $value->id == 3))
            $conteudo['fim'][0] .= '<option value="' . $value->id . '" ' . iff($pedidos->situacao == $value->id, 'selected') . '>' . $value->nome . '</option> ';
    }
    $conteudo['fim'][0] .= '                </select>
                                                    <button class="h24 pt0 pb0 ml5 botao"> <i class="mr5 fa fa-check"></i> Salvar</button>
                                                    <textarea name="txt" class="w100p h50 mt5 p5 design" placeholder="Observação"></textarea>
                                                    <input type="hidden" name="id" value="' . $pedidos->id . '">
                                                    <input type="hidden" name="table" value="' . $table . '">
                                                    <input type="hidden" name="gravar" value="1">
                                                </form>
                                                <script>ajaxForm(' . A . 'pedidos_situacoes' . A . '); </script>
                                            </fieldset>
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-user cor_999"></i> Informações do Cliente </legend>
                                                <b>' . $cadastro->nome . ' (#' . $cadastro->id . ')</b>
                                                <div class="h2"></div>
                                                <b>Email:</b> ' . $cadastro->email . '
                                                <div class="h2"></div>
                                                <b>Telefone:</b> ' . $cadastro->telefone . ' / ' . $cadastro->celular . '
                                                <div class="h2"></div>
                                                <b>Conta registrada:</b> ' . data($cadastro->data, 'd/m/Y') . '
                                            </fieldset>
                                        </div>

                                        <div class="wr6 pl20">
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-truck cor_8F6849"></i> Informações do Rastreamento </legend>
                                                <form id="pedidos_rastreamento" action="' . DIR . '/' . ADMIN . '/app/Ajax/Acoes/pedidos_situacoes.php" method="post">
                                                    <input type="text" name="rastreamento" value="' . $pedidos->rastreamento . '" class="w200 design">
                                                    <button class="h24 pt0 pb0 ml5 botao"> <i class="mr5 fa fa-check"></i> Salvar</button>
                                                    <input type="hidden" name="id" value="' . $pedidos->id . '">
                                                    <input type="hidden" name="table" value="' . $table . '">
                                                </form>
                                                <script>ajaxForm(' . A . 'pedidos_rastreamento' . A . ');</script>
                                            </fieldset>
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-file-text-o c_azul"></i> Detalhes da compra </legend>
                                                <b>ID do Pedido:</b> #' . $pedidos->id . '
                                                <div class="h2"></div>
                                                <b>Data da Compra:</b> ' . data($pedidos->data, 'd/m/Y H:i:s') . '
                                                <div class="h2"></div>
                                                ' . iff($pedidos->data_aprovacao != '0000-00-00 00:00:00', '<b>Data da Aprovação:</b> ' . data($pedidos->data_aprovacao, 'd/m/Y H:i:s') . '<div class="h2"></div>') . '
                                                <b>Método de Pagamento:</b> ' . $pedidos->metodo . '
                                                <div class="h2"></div>
                                                ' . iff($pedidos->forma_pagamento, '<b>Forma do Pagamento:</b> ' . $pedidos->forma_pagamento . '<div class="h2"></div>', '') . '
                                                ' . iff($pedidos->desconto_info, '<b>Informações do Desconto:</b> ' . $pedidos->desconto_info . '<div class="h2"></div>', '') . '
                                                <ul class="bd_ccc mt10 ml20 mr20">
                                                    <li class="w50p pl5 pr5 pt2 pb2">Produtos</li>
                                                    <li class="w50p pl5 pr5 pt2 pb2 tar">' . preco($pedidos->valor_subtotal, 1) . '</li>
                                                    <div class="clear"></div>
                                                </ul>
                                                <ul class="bd_ccc bdt0 ml20 mr20">
                                                    <li class="w50p pl5 pr5 pt2 pb2">Frete</li>
                                                    <li class="w50p pl5 pr5 pt2 pb2 tar">' . preco($pedidos->frete, 1) . '</li>
                                                    <div class="clear"></div>
                                                </ul>
                                                <ul class="bd_ccc bdt0 ml20 mr20 dni">
                                                    <li class="w50p pl5 pr5 pt2 pb2">Crédito</li>
                                                    <li class="w50p pl5 pr5 pt2 pb2 tar">' . preco($pedidos->credito, 1) . '</li>
                                                    <div class="clear"></div>
                                                </ul>
                                                <ul class="bd_ccc bdt0 ml20 mr20">
                                                    <li class="w50p pl5 pr5 pt2 pb2">Desconto</li>
                                                    <li class="w50p pl5 pr5 pt2 pb2 tar">' . preco($pedidos->desconto, 1) . '</li>
                                                    <div class="clear"></div>
                                                </ul>
                                                <ul class="bd_ccc bdt0 ml20 mr20">
                                                    <li class="w50p pl5 pr5 pt2 pb2 fz16 c_verde"><b>Valor Total</b></li>
                                                    <li class="w50p pl5 pr5 pt2 pb2 fz16 c_verde tar"><b>' . preco($pedidos->valor_total, 1) . '</b></li>
                                                    <div class="clear"></div>
                                                </ul>
                                            </fieldset>
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-truck cor_8F6849"></i> Endereço de Entrega </legend>
                                                <b>' . $cadastro->nome . ' (#' . $cadastro->id . ')</b>
                                                <div class="h2"></div>
                                                ' . $carrinho->frete->rua . ', ' . $carrinho->frete->numero . ' ' . $carrinho->frete->complemento . '
                                                <div class="h2"></div>
                                                ' . $carrinho->frete->bairro . ' - ' . $carrinho->frete->cidades . ' / ' . $carrinho->frete->estados . '
                                                <div class="h2"></div>
                                                CEP: ' . $carrinho->frete->cep . '
                                            </fieldset>
                                        </div>

                                        <div class="wr12">
                                            <fieldset>
                                                <legend> <i class="fz14 mr3 fa fa-shopping-cart c_amarelo"></i> Produtos </legend>
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th class="tal"><b>Nome</b></th>
                                                            <th class="tac"><b>Preço</b></th>
                                                            <th class="tac"><b>Qtd</b></th>
                                                            <th class="tac"><b>Total</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody> ';
    $nome = explode('<z></z>', $pedidos->nome);
    $produtos = explode('-', $pedidos->produtos);
    $qtds = explode('-', $pedidos->qtds);
    $precos = explode('-', $pedidos->precos);
    foreach ($produtos as $key => $value) {
        $mysql->prepare = array($value);
        $mysql->filtro = " WHERE `id` = ? ";
        $produtos = $mysql->read_unico("produtos");
        if (isset($produtos->id)) {
            $conteudo['fim'][0] .= '<tr> ';
            $conteudo['fim'][0] .= '<td class="tal">' . str_replace('>> ', '', $nome[$key - 1]) . '</td> ';
            $conteudo['fim'][0] .= '<td class="tac">' . preco($precos[$key], 1) . '</td> ';
            $conteudo['fim'][0] .= '<td class="tac">' . $qtds[$key] . '</td> ';
            $conteudo['fim'][0] .= '<td class="tac">' . preco($qtds[$key] * $precos[$key], 1) . '</td>';
            $conteudo['fim'][0] .= '</tr> ';
        }
    }
    $conteudo['fim'][0] .= '                </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                        <div class="clear"></div>

                                    </div>';









    // Pedidos por Pediodo
} elseif ($modulos->modulo == 'configs' and $ids[0] == 'pedidos_por_pediodo') {
    $arr['html'] .= '<style>
                                .pg_47 .campos_do_modulo .acoes { display: none; } 
                                .pg_47 .campos_do_modulo .campos.box { display: none; }
                            </style>';


    $arr['html'] .= '<div class="p20">
                                <table class="w100p">
                                <tr>
                                    <th class="p5 bd_ccc back_eee tal">Período</th>
                                    <th class="p5 bd_ccc back_eee tac">Qtd.</th> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <th class="p5 bd_ccc back_eee tac">Ticket médio</th> ';
        $arr['html'] .= '           <th class="p5 bd_ccc back_eee tac">Valor</th> ';
    }
    $arr['html'] .= '   </tr>
                                <tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Hoje</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', 'hj') . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', 'hj') . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', 'hj') . '</td> ';
    }
    $arr['html'] .= '   </tr>
                                <tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Ontem</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', 'ot') . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', 'ot') . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', 'ot') . '</td> ';
    }
    $arr['html'] .= '   </tr>
                                <tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Dois dias atras</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', 'dois') . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', 'dois') . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', 'dois') . '</td> ';
    }
    $arr['html'] .= '   </tr>
                                <tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Três dias atras</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', 'tres') . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', 'tres') . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', 'tres') . '</td> ';
    }
    $arr['html'] .= '   </tr>
                                <tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Mês Passado</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', date('m') - 1) . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', date('m') - 1) . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', date('m') - 1) . '</td> ';
    }
    $arr['html'] .= '   </tr> ';
    for ($i = 2; $i < 12; $i++) {
        $x = $i < date('m') ? $i : $i - 12;
        $arr['html'] .= '<tr>
                                                        <td class="p5 bd_ccc back_fcfcfc tal"><u>' . mes(date('m') - $x) . '</u></td>
                                                        <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', date('m') - $i) . '</td> ';
        if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
            $arr['html'] .= '                               <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', date('m') - $i) . '</td> ';
            $arr['html'] .= '                               <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', date('m') - $i) . '</td> ';
        }
        $arr['html'] .= '                        </tr> ';
    }
    $arr['html'] .= '<tr>
                                    <td class="p5 bd_ccc back_fcfcfc tal"><u>Total</u></td>
                                    <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('qtd', 'all') . '</td> ';
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('media', 'all') . '</td> ';
        $arr['html'] .= '           <td class="p5 bd_ccc back_fcfcfc tac">' . pedidos_dados('valor', 'all') . '</td> ';
    }
    $arr['html'] .= '    </tr>
                            </table>
                        </div> ';




    // Usuarios
} elseif ($modulos->modulo == 'usuarios') {

    if ($linhas) {
        $conteudo['fim'][0] .= '<li class="wr12 ml56"> <a onclick="boxs(' . A . 'alterar_senha' . A . ', ' . A . 'modulos=' . $modulos->id . '&id=' . $linhas->id . A . ');" class="c_azul">Alterar senha</a> </li> ';
    } else {
        $input->tags = ' class="design" required ';
        $conteudo['fim'][0] .= '<li class="wr6"> ' . $input->text('Senha', 'senha', 'password') . '</li> ';
    }

    // Permissoes
    if ($_SESSION['x_admin']->id == 1 or $_SESSION['x_admin']->id == 2) {
        if (!isset($linhas->id)) {
            $linhas = (object) array();
            $linhas->id = 0;
        }
        if (!isset($linhas->permissoes))
            $linhas->permissoes = '';

        if ($linhas->id != 2) {
            $conteudo['fim'][0] .= '<div class="pl10 "> ';
            $conteudo['fim'][0] .= '<fieldset class="w100p fll p5 pl10 pr10 mt5 mb5 br1 "> ';
            $conteudo['fim'][0] .= '<legend class="pl5 pr5 ml5 mr5"> <b>Permissão</b> </legend> ';

            $conteudo['fim'][0] .= '<input type="checkbox" checked value="" name="permissoes_all" class="dni"> ';
            $conteudo['fim'][0] .= '<li class="wr12"> ';
            $conteudo['fim'][0] .= '<div class="finput finput_classificacao"> ';
            $conteudo['fim'][0] .= '<div class="input"> ';
            $conteudo['fim'][0] .= '<label class="classificacao_1 lclassificacao"> ';
            $check = (isset($linhas->permissoes_all) and $linhas->permissoes_all == 't') ? 'checked' : '';
            $conteudo['fim'][0] .= '<input ' . $check . ' type="checkbox" name="permissoes_all" id="permissoes_all" value="t" class="design" /> ';
            $conteudo['fim'][0] .= '<p>Todos</p> ';
            $conteudo['fim'][0] .= '</label> ';
            $conteudo['fim'][0] .= '</div> ';
            $conteudo['fim'][0] .= '</div> ';
            $conteudo['fim'][0] .= '</li> ';
            $conteudo['fim'][0] .= '<div class="h5 clear bdt_ccc"></div> ';
            $conteudo['fim'][0] .= '<input type="checkbox" checked value="" name="permissoes[]" class="dni"> ';


            $mysql->filtro = " WHERE `status` = 1 ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
            $consulta = $mysql->read('menu_admin1_cate');
            foreach ($consulta as $key => $value) {

                $mysql->prepare = array($value->id);
                $mysql->filtro = " WHERE `status` = 1 AND `lang` = '" . LANG . "' AND `id` != 1 AND `categorias` = ? ORDER BY `ordem` ASC, `nome` ASC, `id` DESC ";
                $menu_admin = $mysql->read("menu_admin");
                if ($menu_admin) {
                    $conteudo['fim'][0] .= '<li class="wr12 pb0"> <b>' . $value->nome . '</b> </li> ';

                    foreach ($menu_admin as $k => $v) {
                        $conteudo['fim'][0] .= '<li class="wr3"> ';
                        $conteudo['fim'][0] .= '<div class="finput finput_permissoes"> ';
                        $conteudo['fim'][0] .= '<div class="input"> ';
                        $permissoes = explode('-', $linhas->permissoes);
                        $z = 0;
                        for ($i = 0; $i < count($permissoes); $i++) {
                            if ($permissoes[$i] == $v->id)
                                $z++;
                        }
                        $check = $z ? 'checked' : '';
                        $conteudo['fim'][0] .= '<label class="permissoes_' . $v->id . ' lpermissoes"> ';
                        $conteudo['fim'][0] .= '<input type="checkbox" value="' . $v->id . '" name="permissoes[]" id="permissoes_' . $v->id . '" class="design " ' . $check . ' > ';
                        $conteudo['fim'][0] .= '<p>' . $v->nome . '</p> ';
                        $conteudo['fim'][0] .= '</label> ';
                        $conteudo['fim'][0] .= '</div> ';
                        $conteudo['fim'][0] .= '</div> ';
                        $conteudo['fim'][0] .= '</li> ';
                    }

                    $conteudo['fim'][0] .= '<div class="h5 clear bdt_ccc"></div> ';
                }
            }

            $conteudo['fim'][0] .= '</fieldset> ';
            $conteudo['fim'][0] .= '</div> ';
            $conteudo['fim'][0] .= '<div class="clear"></div> ';
        }
    }
} elseif ($modulos->modulo == '') {

    $conteudo['ini'][0] .= '';
    $conteudo['fim'][0] .= '';
}
?>