<?php

class Upload extends Mysql
{

    // Gerar Nome
    private function gerar_nome($extensao, $nome, $table, $ult_id)
    {
        global $config;

        // Gera um nome único para a imagem
        $temp = substr(md5(uniqid(time())), 0, 10);
        $imagem_nome = $temp . "." . $extensao;

        $diretorio = preg_match('(admin)', LUGAR) ?  "../web/fotos/" :  "web/fotos/";

        // Verifica se o arquivo já existe, caso positivo, chama essa função novamente
        if (file_exists($diretorio . $imagem_nome)) {
            $imagem_nome = $this->gerar_nome($extensao, $nome, $table, $ult_id);
        }

        $nome_da_img = sem('acentos_all', $table) . '_' . sem('acentos_all', $ult_id) . '_';

        $this->colunas = 'nome';
        $this->prepare = array($ult_id);
        $this->filtro = " WHERE `id` = ? ";
        $cadastro = $this->read_unico($table);
        if (isset($cadastro->nome)) {
            $nome_da_img .= sem('acentos_all', $cadastro->nome);
        }

        return $nome_da_img . '_' . sem('url', $_SERVER['HTTP_HOST']) . '_zz' . $imagem_nome;
    }

    // Gravar File
    private function gravar_file($key, $ult_id, $caminho, $array = 0, $k = 0)
    {
        if (isset($_GET['tableeee'])) {
            $table = $_GET['tableeee'];
        } else {
            global $table;
        }

        $erro = array();
        $config = array();

        $config["diretorio"] = preg_match('(admin)', LUGAR) ? $caminho . "../web/fotos/" : $caminho . "web/fotos/";


        if ($array) {
            $arquivo = isset($_FILES[$key][$k]) ? $_FILES[$key][$k] : FALSE;
        } else {
            $arquivo = isset($_FILES[$key]) ? $_FILES[$key] : FALSE;
        }

        if ($arquivo and !isset($erro[0]) and !(preg_match('(.php)', $arquivo["name"]) or preg_match('(.html)', $arquivo["name"]) or preg_match('(.htm)', $arquivo["name"]) or preg_match('(.phtml)', $arquivo["name"]) or preg_match('(.java)', $arquivo["name"]) or preg_match('(.js)', $arquivo["name"]) or preg_match('(.css)', $arquivo["name"]))) {

            if ($arquivo["size"] > 2100000) { // 2000000 = 2MB
                $imagem_nome = 'erro';
            } else {
                $ext_fim = '';
                $ex = explode('.', $arquivo["name"]);
                foreach ($ex as $k => $v) {
                    $ext_fim = $v;
                }
                //$ext = explode('.' . $ext_fim, $arquivo["name"]);
                $imagem_nome = $this->gerar_nome($ext_fim, $arquivo["name"], $table, $ult_id);

                $imagem_dir = $config["diretorio"] . $imagem_nome;
                if (move_uploaded_file($arquivo["tmp_name"], $imagem_dir) and $ult_id and !$array) {
                    $this->campo[$key] = $imagem_nome;
                    $this->filtro = " where id = '" . $ult_id . "' ";
                    $this->update($table);

                    if ($table == 'banner') {
                        copy($imagem_dir, "../../web/banner/" . $imagem_nome);
                    }
                }
            }

            return $imagem_nome;
        }
    }

    // Verificar Files
    public function fileUpload($ult_id, $caminho = '../', $mais_fotos = 0, $table = '')
    {
        if ($table) {
            $_GET['tableeee'] = $table;
        } else {
            global $table;
        }

        // Fotos
        foreach ($_FILES as $key => $value) {
            if (preg_match('(foto)', $key)) {
                $ex = explode('foto', $key);
                if (!$ex[0] and isset($_FILES[$key]) and $_FILES[$key]['type']) {
                    $return[] = $this->gravar_file($key, $ult_id, $caminho);
                }
            }
        }

        // Multi Fotos
        foreach ($_FILES as $key => $value) {
            if (preg_match('(multifotos)', $key)) {
                $itens = array();
                $ex = explode('multifotos', $key);
                if (!$ex[0] and is_array($value)) {
                    $_FILES[$key] = inverter_key($_FILES[$key]);
                    foreach ($_FILES[$key] as $k => $v) {
                        $itens[] = $this->gravar_file($key, $ult_id, $caminho, 1, $k);
                    }
                }
                if ($itens and !$mais_fotos) {
                    $this->colunas = $key;
                    $this->prepare = array($ult_id);
                    $this->filtro = " WHERE `id` = ? ";
                    $consulta = $this->read_unico($table);
                    if (isset($consulta->$key)) {
                        $array = unserialize(base64_decode($consulta->$key));
                        $itens = (is_array($array) and $array) ? array_merge($array, $itens) : $itens;
                        $this->campo[$key] = base64_encode(serialize($itens));
                        $this->filtro = " where id = '" . $ult_id . "' ";
                        $this->update($table);
                    }
                } elseif ($itens and $mais_fotos) {
                    $return = $itens;
                }
            }
        }

        // Excluir Fotos
        foreach ($_POST as $key => $value) {
            if ($key == 'sem_foto') {
                foreach ($value as $k => $v) {
                    $this->campo[$k] = '';
                    $this->filtro = " where id = '" . $ult_id . "' ";
                    $this->update($table);
                }
            }
        }

        // Excluir Multi Fotos
        foreach ($_POST as $key => $value) {
            if ($key == 'sem_multifotos') {
                foreach ($value as $k => $v) {
                    $this->colunas = $k;
                    $this->prepare = array($ult_id);
                    $this->filtro = " WHERE `id` = ? ";
                    $consulta = $this->read_unico($table);
                    $array = isset($consulta->$k) ? unserialize(base64_decode($consulta->$k)) : array();
                    foreach ($v as $k1 => $v1) {
                        foreach ($array as $k2 => $v2) {
                            if ($v1 == $k2) {
                                unset($array[$k2]);
                            }
                        }
                    }
                    $this->campo[$k] = base64_encode(serialize($array));
                    $this->filtro = " where id = '" . $ult_id . "' ";
                    $this->update($table);
                }
            }
        }


        if (isset($return)) {
            return $return;
        }
    }
}
