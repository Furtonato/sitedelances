<?php

class ItemLeilao extends Model {

    public function __construct() {
        $this->iniciar();
    }

    public function leilao($id) {
        $mysql = $this->mysql;

        $mysql->colunas = '*';
        $mysql->filtro = "  WHERE id = $id";
        $result = $mysql->read_unico('leiloes');


        return ($result);
    }

    public function lote($id) {
        $mysql = $this->mysql;

        $mysql->colunas = '*';

        $mysql->filtro = " WHERE " . STATUS . " AND situacao = 0 AND leiloes = $id ORDER BY estados ASC, " . ORDER . " ";
        $result = $mysql->read_unico('lotes');


        return ($result);
    }

}
