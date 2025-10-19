<?php

class Mysql
{

    public $db;
    public $colunas = "*";
    public $prepare = array();
    public $filtro;
    public $campo;
    public $logs = 1;
    public $transacao;
    public $json;

    public function __construct()
    {

        global $localhost_config, $nome_config, $senha_config, $banco_config;

        $this->conecta($localhost_config, $nome_config, $senha_config, $banco_config);
    }

    public function conecta($localhost_config, $nome_config, $senha_config, $banco_config)
    {
        try {
            $this->db = new PDO('mysql:host=' . $localhost_config . ';charset=utf8mb4;dbname=' . $banco_config, $nome_config, $senha_config);
        } catch (PDOException $e) {
            die(':( O servidor esta fora do ar! Aguarde em alguns instantes o site voltara a funcionar! <span style="display:none">' . $e->getMessage() . '</span>');
        }
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $tz = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('P');
        $this->db->exec("SET time_zone='$tz';");
    }

    public function getDB()
    {
        return $this->db;
    }

    // Read
    public $nao_existe;
    public $nao_existe_all;

    public function read($tabela, $pags = 0)
    {
        try {
            if ($pags) {
                $pagg = new Paginacao();
                $pagg->pags = $pags;
                $pagg->colunas = $this->colunas;
                $pagg->prepare = $this->prepare;
                $pagg->filtro = $this->filtro;
                return ($pagg->pag($tabela));
            } else {
                if ($this->prepare) {
                    try {
                        $sql = $this->db->prepare(" SELECT {$this->colunas} FROM `{$tabela}` {$this->filtro} ");
                        $sql->execute($this->prepare);
                    } catch (PDOException $e) {
                        if ($_SERVER['HTTP_HOST'] == 'localhost:4000' and !$this->nao_existe and !$this->nao_existe_all) {
                            echo implode(' - ', $this->prepare) . '<br>';
                            echo " SELECT {$this->colunas} FROM `{$tabela}` {$this->filtro} <br><br>";
                            die($e);
                        }
                    }
                } else {
                    $sql = $this->db->query(" SELECT {$this->colunas} FROM `{$tabela}` {$this->filtro} ");
                }
                $sql->setFetchMode(PDO::FETCH_OBJ);
                $this->nao_existe = '';
                $this->colunas = '*';
                $this->prepare = array();
                $this->filtro = '';
                $array = $sql->fetchAll();
                foreach ($array as $key => $value) {
                    $array = $this->modificando_objeto($key, $value, $tabela, $array);
                }
                return $array;
            }
        } catch (PDOException $e) {
            if ($_SERVER['HTTP_HOST'] == 'localhost:4000' and !$this->nao_existe and !$this->nao_existe_all)
                echo "Tabela '" . $tabela . "' nao existe";
            $this->nao_existe = '';
            return ("Tabela " . $tabela . " nao existe");
        }
    }

    public function read_unico($tabela)
    {
        $this->filtro = (preg_match('(LIMIT)', $this->filtro) or preg_match('(limit)', $this->filtro)) ? $this->filtro : $this->filtro . " LIMIT 1 ";
        $return = $this->read($tabela);
        $return = isset($return[0]) ? $return[0] : array();
        return $return;
    }

    public function existe($tabela)
    {
        $this->nao_existe = 1;
        $this->colunas = "id";
        $this->filtro = preg_match('( LIMIT )', $this->filtro) ? $this->filtro : $this->filtro . " LIMIT 1 ";
        $return = $this->read($tabela);
        $return = $return == "Tabela " . $tabela . " nao existe" ? 0 : 1;
        return $return;
    }

    public function modificando_objeto($key, $value, $tabela, $array)
    {
        $array[$key]->table = $tabela;
        if (LUGAR == 'site' and isset($value->preco) and $tabela == 'produtos')
            $array[$key]->preco = descontos_produtos($value);

        if ($tabela == 'menu_admin' and isset($value->id) and !$this->json) {
            $caminho = caminho('/app/Json/menu_admin/' . $value->id . '.json');
            if (file_exists($caminho)) {
                $json = json_decode(file_get_contents($caminho));
                foreach ($json as $k => $v) {
                    $array[$key]->$k = $v;
                }
            }
        }
        return $array;
    }

    public function menu_admin_admins($array, $tabela)
    {
        if ($tabela == 'menu_admin') {
            foreach ($array as $key => $value) {
                if (isset($_GET['m']) and isset($value->admins) and $value->admins) {
                    unset($array[$key]);
                } elseif (isset($_GET['admins']) and (!isset($value->admins) or (isset($value->admins) and $value->admins != $_GET['admins']))) {
                    unset($array[$key]);
                }
            }
        }
        return $array;
    }

    // Insert
    public function insert($tabela)
    {
        if ($this->campo) {
            foreach ($this->campo as $name => $value) {
                $name = addslashes($name);
                $value = gravando_no_mysql($tabela, $name, $value);
                $campos[] = '`' . $name . '`';
                $valores[] = "?";
                $prepare[] = $value;
            }
            $campos = implode(", ", array_values($campos));
            $valores = implode(",", array_values($valores));
            $prepare = array_merge($prepare, $this->prepare);

            try {
                $sql = $this->db->prepare(" INSERT IGNORE INTO `{$tabela}` ({$campos}) VALUES ({$valores}) ");
                $sql->execute($prepare);
            } catch (PDOException $e) {
                if ($this->transacao)
                    $this->db->rollBack();
                if ($_SERVER['HTTP_HOST'] == 'localhost:4000' and !$this->nao_existe and !$this->nao_existe_all) {
                    echo implode(' - ', $prepare) . '<br>';
                    echo " INSERT IGNORE INTO `{$tabela}` ({$campos}) VALUES ({$valores}) <br><br>";
                    die($e);
                }
            }

            $id = $this->db->lastInsertId();
            if (isset($id)) {
                if ($this->logs)
                    $this->logs_acoes($tabela, $id, 'Criação', $this->campo);

                return $id;
            }
            $this->prepare = array();
            unset($this->campo);
        }
    }

    // Update
    public function update($tabela)
    {
        if ($this->campo) {
            foreach ($this->campo as $name => $value) {
                $name = addslashes($name);
                if (!$this->json)
                    $value = gravando_no_mysql($tabela, $name, $value);
                $campos[] = "`{$name}` = ?";
                $prepare[] = $value;
            }
            $prepare = array_merge($prepare, $this->prepare);
            $campos = implode(", ", $campos);

            if ($this->filtro) {
                try {
                    $sql = $this->db->prepare(" UPDATE IGNORE `{$tabela}` SET {$campos} {$this->filtro} ");
                    $sql->execute($prepare);
                } catch (PDOException $e) {
                    if ($this->transacao)
                        $this->db->rollBack();
                    if ($_SERVER['HTTP_HOST'] == 'localhost:4000' and !$this->nao_existe and !$this->nao_existe_all) {
                        echo implode(' - ', $prepare) . '<br>';
                        echo " UPDATE IGNORE `{$tabela}` SET $campos {$this->filtro} <br><br>";
                        die($e);
                    }
                }
            } else {
                die('Update Sem Filtro');
            }

            $this->colunas = "id";
            $id = $this->read($tabela);
            if (isset($id[0]->id)) {
                if ($this->logs)
                    $this->logs_acoes($tabela, $id[0]->id, 'Edicão', $this->campo);

                return $id[0]->id;
            }
            $this->prepare = array();
            $this->filtro = '';
            unset($this->campo);
        }
    }

    // Delete
    public function delete($tabela)
    {
        $prepare = $this->prepare;
        $filtro = $this->filtro;
        $id = $this->read($tabela);
        if (isset($id[0]->id)) {
            if ($this->logs)
                $this->logs_acoes($tabela, $id[0]->id, 'Exclusão', '');

            if ($filtro) {
                try {
                    $sql = $this->db->prepare(" DELETE FROM `{$tabela}` {$filtro} ");
                    $sql->execute($prepare);
                } catch (PDOException $e) {
                    if ($this->transacao)
                        $this->db->rollBack();
                    if ($_SERVER['HTTP_HOST'] == 'localhost:4000' and !$this->nao_existe and !$this->nao_existe_all) {
                        echo implode(' - ', $prepare) . '<br>';
                        echo " DELETE FROM `{$tabela}` {$filtro} <br><br> ";
                        die($e);
                    }
                }
            } else {
                die('Delete Sem Filtro');
            }
            return $id[0]->id;
        }
        $this->prepare = array();
        $this->filtro = '';
    }

    // Transacoes
    public function ini()
    {
        $this->transacao = 1;
        $this->db->beginTransaction();
    }

    public function rollBack()
    {
        $this->db->rollBack();
    }

    public function fim()
    {
        $this->db->commit();
    }

    // Tables
    public function tables()
    {
        $array = $this->db->query(" SHOW TABLES ", PDO::FETCH_NUM);
        return $array;
    }

    public function colunas($tabela)
    {
        $sql = $this->db->query(" SHOW CREATE TABLE `{$tabela}` ");
        $sql->setFetchMode(PDO::FETCH_OBJ);
        $this->colunas = '*';
        $this->filtro = '';
        $this->nao_existe = '';
        $array = $sql->fetchAll();
        return $array;
    }

    public function delete_table($tabela)
    {
        //$this->db->query(" DROP TABLE `{$tabela}` ");
    }

    // Logs Acoes
    public $logs_caminho = '';

    private function logs_acoes($tabela, $id, $acao, $campo)
    {
        if ($id and LUGAR != 'site' and $tabela != 'log_acoes' and $tabela != 'z_txt' and isset($_SESSION['x_' . LUGAR]->id) and $_SESSION['x_' . LUGAR]->id != 1) {

            $logs_acoes = (isset($_SESSION['x_' . LUGAR]->table) and $_SESSION['x_' . LUGAR]->table) ? $_SESSION['x_' . LUGAR]->table : 'site';
            $logs_acoes_id = (isset($_SESSION['x_' . LUGAR]->id) and $_SESSION['x_' . LUGAR]->id) ? $_SESSION['x_' . LUGAR]->id : 0;

            $item = '';
            $sql = $this->db->query(" SELECT `id` FROM `{$tabela}`  where `id` = '{$id}'  ");
            $sql->setFetchMode(PDO::FETCH_OBJ);
            foreach ($sql->fetchAll() as $row) {
                $item = '#' . $row->id;
            }

            $acao = $this->logs != 1 ? $this->logs : $acao;

            $campo = str_replace('<iframe', 'iframe', $campo);

            $sql = $this->db->prepare(" INSERT IGNORE INTO `log_acoes`	(`lang`,	`acoes`,	`usuarios`,	`usuarios_id`,	`tabelas`,	`item_id`,	`item`,	`ip`,	`id`)
											VALUES 					( ?,		 ?,			 ?,			 ?,				 ?,			 ?,			 ?,		 ?,		 ?  )");

            $idd = time() . $logs_acoes_id . $id;
            $prepare = array(LANG, $acao, $logs_acoes, $logs_acoes_id, $tabela, $id, $item, $_SERVER['REMOTE_ADDR'], $idd);
            $sql->execute($prepare);

            // Gravando Json
            $file = fopen($this->logs_caminho . "../../plugins/Json/log_acoes/" . $idd . ".json", 'w');
            fwrite($file, json_encode($campo));
            fclose($file);

            $this->logs = 1;
        }
    }
}

$mysql = new Mysql();
$mysql->filtro = " where tipo = 'informacoes' ";
$infoo = $mysql->read_unico('configs');
