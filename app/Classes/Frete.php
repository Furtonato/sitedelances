<?php

class Frete extends Mysql {

    // Calcular o Frete
    public $endereco;
    public $cod_servico;
    public $cep_destino;
    public $frete_gratis;
    public $peso;
    public $comprimento = '16';
    public $largura = '12';
    public $altura = '2';
    public $valor_total;
    public $valor_declarado = '1.00';

    public function calcula_frete($cep, $pg_produto = 0) {

        $this->cep_destino = $cep;
        if (!$pg_produto)
            $this->frete_pegar_dados();

        $this->cod_servico = '41106,40010';
        if ($_SERVER['HTTP_HOST'] == 'localhost:4000')
            $return = $this->correios_local();
        else
            $return = $this->correios();

        if ($this->endereco) {
            $endereco = busca_endereco($cep);
            $return['endereco'] = $endereco['cidade'];
            $return['endereco'] .= $endereco['estado'] ? ' / ' . $endereco['estado'] : '';
            ;
        }

        //$return = $this->transportadora($endereco, $return);
        // VERIFICACOES DOS DESCONTOS
        // Frete gratis do cupom se por pac
        if (isset($_SESSION['desconto']['cupons']['frete_gratis'])) {
            $return['valor']['pac'] = '0.00';
        }
        // VERIFICACOES DOS DESCONTOS
        // VERIFICACOES FRETE GRATIS PARA PRODUTOS
        $frete_gratis = 1;
        if (isset($_SESSION['carrinho']['itens'])) {
            foreach ($_SESSION['carrinho']['itens'] as $key => $array) {
                foreach ($array as $ref => $value) {
                    $this->filtro = " where status = 1 and lang = '" . LANG . "' and id = '" . $key . "' ";
                    $produtos = $this->read_unico('produtos');
                    if (isset($produtos->id)) {
                        if ($frete_gratis)
                            $frete_gratis = $produtos->frete;
                    }
                }
            }
        }
        if ($frete_gratis) {
            $return['valor']['pac'] = '0.00';
        }
        // VERIFICACOES FRETE GRATIS PARA PRODUTOS

        return $return;
    }

    public function frete_pegar_dados() {
        // Pegando os dados
        $peso = 0;
        $altura = 0;
        $largura = 0;
        $comprimento = 0;
        $frete_gratis = 1;
        $valor_declarado = 0;
        if (isset($_SESSION['carrinho']['itens'])) {
            foreach ($_SESSION['carrinho']['itens'] as $key => $array) {
                foreach ($array as $ref => $value) {
                    $this->filtro = " where status = 1 and lang = '" . LANG . "' and id = '" . $key . "' ";
                    $produtos = $this->read_unico('produtos');
                    if (isset($produtos->id) and ( !isset($produtos->frete) or ! $produtos->frete)) {
                        $frete_gratis = 0;
                        $peso += isset($produtos->peso) ? $produtos->peso : 0;
                        $altura += isset($produtos->altura) ? $produtos->altura : 0;
                        $largura += isset($produtos->largura) ? $produtos->largura : 0;
                        $comprimento += isset($produtos->comprimento) ? $produtos->comprimento : 0;
                        $valor_declarado += isset($produtos_combinacoes->preco) ? $value->qtd * $produtos_combinacoes->preco : 0;
                    }
                }
            }
        }

        $this->frete_gratis = $frete_gratis;
        $this->peso = $peso;
        $this->comprimento = $comprimento;
        $this->largura = $largura;
        $this->altura = $altura;
        $this->valor_declarado = $valor_declarado;
    }

    public function correios_local() {
        $return['valor'] = array('pac' => '300.00', 'sedex' => '500.00', 'e-sedex' => '200.00');
        $return['prazo'] = array('pac' => ' - 30 Dias', 'sedex' => ' - 50 Dias', 'e-sedex' => ' - 20 Dias');
        $return['erro'] = array('pac' => '', 'sedex' => '', 'e-sedex' => '');
        return $return;
    }

    public function correios() {
        $this->filtro = " where tipo = 'frete' ";
        $frete = $this->read_unico('configs');

        if ($this->comprimento < 16)
            $this->comprimento = 16;
        if ($this->comprimento > 105)
            $this->comprimento = 105;
        if ($this->largura < 12)
            $this->largura = 12;
        if ($this->largura > 105)
            $this->largura = 99;
        if ($this->altura < 2)
            $this->altura = 2;
        if ($this->altura > 105)
            $this->altura = 99;
        //if($this->peso>30.00)		$this->peso = 30.00;

        if (($this->comprimento + $this->largura + $this->altura) > 200)
            $this->comprimento = 200 - ($this->largura + $this->altura);

        $data['nCdEmpresa'] = $frete->codigo_correios;
        $data['sDsSenha'] = $frete->senha_correios;
        $data['sCepOrigem'] = $frete->cep;
        $data['sCepDestino'] = $this->cep_destino;
        $data['nVlPeso'] = $this->peso;
        $data['nCdFormato'] = '1';
        $data['nVlComprimento'] = $this->comprimento;
        $data['nVlAltura'] = $this->altura;
        $data['nVlLargura'] = $this->largura;
        $data['nVlDiametro'] = (int) sqrt(($this->largura * $this->largura) + ($this->altura * $this->altura));
        $data['sCdMaoPropria'] = 'n';
        $data['nVlValorDeclarado'] = $this->valor_declarado;
        $data['sCdAvisoRecebimento'] = 'n';
        $data['StrRetorno'] = 'xml';
        $data['nCdServico'] = $this->cod_servico;

        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?' . http_build_query($data);
        $xml = curll($url);

        if ($xml) {
            $array = simplexml_load_string($xml);
            $return = array();
            foreach ($array->cServico as $key => $value) {
                if ($value->Codigo == 41106) {
                    $tipo = 'pac';
                } elseif ($value->Codigo == 40010) {
                    $tipo = 'sedex';
                } elseif ($value->Codigo == 81019) {
                    $tipo = 'e-sedex';
                } else {
                    $tipo = '';
                }

                if ($value->Erro == 0 or $value->Erro == '009' or $value->Erro == '010' or $value->Erro == '011') {
                    $return['valor'][$tipo] = numero((string) $value->Valor);
                    $return['prazo'][$tipo] = ' - ' . $value->PrazoEntrega . ' Dias';
                    $return['erro'][$tipo] = '';
                } elseif ($tipo) {
                    $erro = 'Não Disponível<span class="dni">' . $value->Erro . '</span>';
                    if ($value->Erro == -3)
                        $erro = 'CEP de destino inválido';
                    if ($value->Erro == -4)
                        $erro = 'Peso excedido';
                    if ($value->Erro == -4)
                        $erro = 'Peso excedido';
                    $return['valor'][$tipo] = '';
                    $return['prazo'][$tipo] = '';
                    $return['erro'][$tipo] = $erro;
                }
            }
        } else {
            $return = array('erro_ao_calcular' => 'O site dos correios não está retornando o valor do Frete, aguarde um momento e tente novamente!');
        }
        $return['erro_ao_calcular'] = isset($return['erro_ao_calcular']) ? $return['erro_ao_calcular'] : '';
        return $return;
    }

    public function transportadora($endereco, $return) {
        $this->filtro = " where nome = '" . $endereco['cidade'] . "' ";
        $local_cidades = $this->read_unico('local_cidades');
        $this->filtro = " where ab = '" . $endereco['estado'] . "' ";
        $local_estados = $this->read_unico('local_estados');

        $cidade = isset($local_cidades->id) ? $local_cidades->id : 0;
        $estado = isset($local_estados->id) ? $local_estados->id : 0;

        $this->filtro = " where cidades = '" . $cidade . "' and estados = '" . $estado . "' ";
        $transportadora = $this->read('transportadora');
        foreach ($transportadora as $key => $value) {
            $frete_valor = $value ? $this->valor_total * ($value->porc / 100) : 0;
            $frete_prazo = ' - ' . $value->prazo . ' Dias';
        }

        if (!$transportadora) {
            $this->filtro = " where cidades = '' and estados = '" . $estado . "' ";
            $transportadora = $this->read('transportadora');
            foreach ($transportadora as $key => $value) {
                $frete_valor = $value ? $this->valor_total * ($value->porc / 100) : 0;
                $frete_prazo = ' - ' . $value->prazo . ' Dias';
            }
        }

        $return['valor']['transportadora'] = isset($frete_valor) ? preco($frete_valor) : '';
        $return['prazo']['transportadora'] = isset($frete_valor) ? $frete_prazo : '';
        $return['erro']['transportadora'] = isset($frete_valor) ? '' : 'Não Calculado';

        return $return;
    }

}

/*
  41106 PAC sem contrato.
  40010 SEDEX sem contrato.
  40045 SEDEX a Cobrar, sem contrato.
  40215 SEDEX 10, sem contrato.
  40290 SEDEX Hoje, sem contrato.

  81019 e-SEDEX, com contrato.
  40126 SEDEX a Cobrar, com contrato.
  40096 SEDEX com contrato.
  40436 SEDEX com contrato.
  40444 SEDEX com contrato.
  40568 SEDEX com contrato.
  40606 SEDEX com contrato.
  41068 PAC com contrato.
  81027 e-SEDEX Prioritário, com conrato.
  81035 e-SEDEX Express, com contrato.
  81868 (Grupo 1) e-SEDEX, com contrato.
  81833 (Grupo 2) e-SEDEX, com contrato.
  81850 (Grupo 3) e-SEDEX, com contrato.
 */
?>