<?php

class System {

    public function __construct() {

        // Eliminar Bug de LUGAR != site
        if (LUGAR != 'site') {
            echo '<script>window.parent.location="' . DIR_C . DIR_D . '";</script>';
            exit();
        }

        // HTTPS
        //if(!isset($_SERVER['HTTPS']) AND $_SERVER['HTTP_HOST'] != 'localhost:4000'){
        //	header("Location: ".DIR_C.DIR_D);
        //}


        $_GET['cod'] = isset($_GET['cod']) ? strtolower($_GET['cod']) : '';
        $gets = explode('/', $_GET['cod']);

        // Redirecionar para pg/
        if (!isset($gets[1])) {
            header("Location: " . DIR . "/" . $gets[0] . "/");
        }

        $_GET['pg_real'] = $gets[0];

        // Muda para abrir outras paginas
        if ($gets[0] == 'textosp' OR $gets[0] == 'textosp1') {
            $_GET['pg'] = $gets[0] = 'textos';
        }


        // Verificando Qual Pagina Chamar
        if (method_exists(new Controllers(), $gets[0])) {
            $_GET['pg'] = $gets[0];

            // Criando arquivo
            if (!file_exists("../views/" . $gets[0] . ".phtml")) {
                copy("../views/home.phtml", "../views/" . $gets[0] . ".phtml");
                $gravar_file = fopen("../views/" . $gets[0] . ".phtml", 'w');
                $arquivo_novo = "<?\n" .
                        "\techo\n" .
                        "\t\t'<section id=" . A2 . $gets[0] . A2 . " class=" . A2 . "centerr" . A2 . ">'.\n\n" . //animated_ini
                        "\t\t'</section>';\n" .
                        "\n?>";

                $arquivo_novo = "\n\t";
                $arquivo_novo .= '<section id="' . $gets[0] . '">';
                $arquivo_novo .= "\n\t";
                $arquivo_novo .= '</section>';

                fwrite($gravar_file, $arquivo_novo);
                fclose($gravar_file);
            }

            //} else {
            //	$_GET['pg'] = 'home';
        }


        $_GET['nome'] = ( isset($gets[1]) and $gets[1] ) ? $gets[1] : '-';
        $_GET['id'] = ( isset($gets[2]) and $gets[2] ) ? $gets[2] : '-';
        $_GET['categorias'] = ( isset($gets[3]) and $gets[3] ) ? $gets[3] : '-';

        $_GET['url_gets'] = str_replace($gets[0], '', $_GET['cod']);

        unset($gets[0], $gets[1], $gets[2], $gets[3]);

        // OUTROS GETS (a/1/b/2 => a=1, b=2)
        // Excluindo o ultimo array se ult array for 0
        if (!end($gets))
            array_pop($gets);

        // Passando valores para ind e value
        $i = 0;
        if ($gets) {
            foreach ($gets as $val) {
                if (!($i % 2))
                    $ind[] = $val;
                else
                    $value[] = $val;
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }

        // Verificando Cont
        if (( isset($ind) and ! isset($value) ) or count($ind) != count($value))
            $value[] = '';


        if (count($ind) == count($value) and $ind and $value) {
            foreach ($ind as $name => $val) {
                $_GET[$val] = $value[$name];
            }
        }
        // OUTROS GETS
    }

    public function run() {
        // Abrindo Paginas
        if (isset($_GET['pg'])) {
            $Controllers = new Controllers();
            $pg = $_GET['pg'];
            $Controllers->$pg();
        } else {
            echo 'Pagina não encontrada!';
        }
    }

}

?>