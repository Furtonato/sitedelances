<?php

class Model {

    public $mysql;
    
    public function iniciar() {
        $this->mysql = new Mysql();
    }

}
