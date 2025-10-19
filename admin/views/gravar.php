<?php

/* ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL); */

define('LUGAR', 'admin');
define('DIR', $_SERVER['HTTP_HOST']);

// Auto Load Class
function __autoload($class_name)
{
    // Classes
    if (file_exists("../../app/Classes/" . $class_name . ".php")) {
        require_once("../../app/Classes/" . $class_name . ".php");
    } elseif (file_exists("../app/Classes/" . $class_name . ".php")) {
        require_once("../app/Classes/" . $class_name . ".php");
        // TNG
    } else if (file_exists("../../plugins/Tng/tng/triggers/" . $class_name . ".class.php")) {
        //require_once("../../plugins/Tng/tng/triggers/".$class_name.".class.php");
    }
}

require_once '../../plugins/cn/config.php';
require_once '../../system/mysql.php';

$db = $mysql->getDB();

$valor = serialize($_POST);

if (isset($_POST['whatsapp'])) {
    $sql = $db->prepare("UPDATE configs SET valor = '$valor' WHERE id = 14");
} elseif (isset($_POST['cor_principal'])) {


    if ($_FILES['logo_topo']['error'] != 4) {
        $dir = "../../web/img";
        $file = $_FILES["logo_topo"];
        $ext = explode('.', $file['name']);

        $logo_topo = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

        if (move_uploaded_file($file["tmp_name"], "$dir/" . $logo_topo)) {

            $sql = $db->prepare("UPDATE configs SET valor = '$logo_topo' WHERE id = 15");
            $executa = $sql->execute();
        }
    }

    if ($_FILES['logo_roda']['error'] != 4) {
        $dir = "../../web/img";
        $file = $_FILES["logo_roda"];
        $ext = explode('.', $file['name']);

        $logo_roda = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

        if (move_uploaded_file($file["tmp_name"], "$dir/" . $logo_roda)) {

            $sql = $db->prepare("UPDATE configs SET valor = '$logo_roda' WHERE id = 16");
            $executa = $sql->execute();
        }
    }

    if ($_FILES['fundo_topo']['error'] != 4) {
        $dir = "../../web/img";
        $file = $_FILES["fundo_topo"];
        $ext = explode('.', $file['name']);

        $fundo_topo = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

        if (move_uploaded_file($file["tmp_name"], "$dir/" . $fundo_topo)) {
            $sql = $db->prepare("UPDATE configs SET valor = '$fundo_topo' WHERE id = 17");
            $executa = $sql->execute();
        }
    }

    if ($_FILES['bg_fundo']['error'] != 4) {
        $dir = "../../web/img";
        $file = $_FILES["bg_fundo"];
        $ext = explode('.', $file['name']);

        $bg_fundo = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

        if (move_uploaded_file($file["tmp_name"], "$dir/" . $bg_fundo)) {
            $sql = $db->prepare("UPDATE configs SET valor = '$bg_fundo' WHERE id = 19");
            $executa = $sql->execute();
        }
    }


    /* if ($_FILES['marca_dagua']['error'] != 4) {
        $dir = "../../web/img";
        $file = $_FILES["marca_dagua"];
        $ext = explode('.', $file['name']);

        $marca_dagua = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

        if (move_uploaded_file($file["tmp_name"], "$dir/" . $marca_dagua)) {
            $sql = $db->prepare("UPDATE configs SET valor = '$marca_dagua' WHERE id = 23");
            $executa = $sql->execute();


            include 'canvas.php';

            $path = "../../web/fotos/";
            $diretorio = dir($path);

            $new_path = "../../web/fotos/backup/";

            $canvas = new Canvas();

            if (!file_exists($new_path)) {
                mkdir($new_path, 0777);
            }

            while ($arquivo = $diretorio->read()) {

                //Verifica se é imagem e se não está no pasta backup
                if (!file_exists($new_path . $arquivo)) {
                    if (is_file($path . $arquivo)) {
                        copy($path . $arquivo, $new_path . $arquivo);
                    }
                }
                $file = $new_path . $arquivo;

                if (is_file($file)) {

                    $valida = @getimagesize($file);
                    if (is_array($valida) || !empty($valida)) {
                        $canvas->carrega($file);
                        $canvas->marca("$dir/" . $marca_dagua, 'meio', 'centro', 50);
                        $canvas->grava($path . $arquivo, 80);
                    }
                }
                //echo "<a href='" . $path . $arquivo . "'>" . $path . $arquivo . "</a><br />";
            }
            $diretorio->close();
        }
    } */

    $valor = serialize($_POST);

    $sql = $db->prepare("UPDATE configs SET valor = '$valor' WHERE id = 20");
} elseif (isset($_POST['logos_rodape'])) {

    $logos = array();

    for ($i = 1; $i <= 6; $i++) {
        if ($_FILES['logo_' . $i]['error'] != 4) {
            $dir = "../../web/img";
            $file = $_FILES['logo_' . $i];
            $ext = explode('.', $file['name']);

            $logos[$i] = substr(md5(uniqid(time())), 0, 10) . '.' . strtolower(end($ext));

            if (move_uploaded_file($file["tmp_name"], "$dir/" . $logos[$i])) {
            }
        } else {
            $logos[$i] = $_POST['logo-old-' . $i];
        }
    }

    $valor = serialize($logos);

    $sql = $db->prepare("UPDATE configs SET valor = '$valor' WHERE id = 19");
    $executa = $sql->execute();
} else if (isset($_POST['dados_empresa'])) {

    $valor = serialize($_POST);
    $sql = $db->prepare("UPDATE configs SET valor = :valor WHERE id = 21");
    $sql->bindParam(":valor", $valor, PDO::PARAM_STR);
} else {
    $sql = $db->prepare("UPDATE configs SET valor = '$valor' WHERE id = 22");
}
$executa = $sql->execute();
if ($executa) {
    if (isset($_POST['whatsapp'])) {
        header("Location: //" . DIR . "/admin/?pg=whatsapp");
    } elseif (isset($_POST['cor_principal'])) {
        header("Location: //" . DIR . "/admin/?pg=corsite");
    } elseif (isset($_POST['logos_rodape'])) {
        header("Location: //" . DIR . "/admin/?pg=logos-rodape");
    } elseif (isset($_POST['dados_empresa'])) {
        header("Location: //" . DIR . "/admin/?pg=dados-impressao");
    } else {
        header("Location: //" . DIR . "/admin/?pg=leilaoseguro");
    }
} else {
    echo 'ERRO';
}
