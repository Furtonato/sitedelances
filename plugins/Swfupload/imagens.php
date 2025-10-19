<?

// Prepara a vari�vel caso o formul�rio tenha sido postado
$arquivo = isset($_FILES["foto"]) ? $_FILES["foto"] : FALSE;

$config = array();
// Tamano m�ximo da imagem, em bytes
$config["tamanho"] = 2000000;
// Largura M�xima, em pixels
$config["largura"] = 3000;
// Altura M�xima, em pixels
$config["altura"] = 3000;
// Diret�rio onde a imagem ser� salva
$config["diretorio"] = "../../web/fotos/";

// Gera um nome para a imagem e verifica se j� n�o existe, caso exista, gera outro nome e assim sucessivamente..
// Fun��o Recursiva
function nome($extensao)
{
    global $config;

    // Gera um nome �nico para a imagem
    $temp = substr(md5(uniqid(time())), 0, 10);
    $imagem_nome = $temp . "." . $extensao;

    // Verifica se o arquivo j� existe, caso positivo, chama essa fun��o novamente
    if (file_exists($config["diretorio"] . $imagem_nome)) {
        $imagem_nome = nome($extensao);
    }

    $nome_da_img = explode('.', $_FILES["foto"]["name"]);
    return RemoveAcentos($nome_da_img[0]) . '_zz' . $imagem_nome;
}

if ($arquivo) {
    $erro = array();

    if (!preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["type"])) {
        if ($arquivo["type"] != '') {
            $_FILES['arquivo'] = $_FILES["foto"];
            include 'imagens_extra.php';
            $imagem_nome = $arquivo;
        }
        //$erro[] = "erro";
    } else {
        // Verifica tamanho do arquivo
        if ($arquivo["size"] > $config["tamanho"]) { ?>

            <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                alert("Tamanha da imagem muito grando, tamanho maximo 10mb!")
                //window.location.href='<?= $PHP_SELF ?>?pg=<?= $pos_banco ?>&ok=0';
            </SCRIPT>

        <?
            $imagem_nome = "erro";
            $erro[] = "erro";
        }

        // Para verificar as dimens�es da imagem
        $tamanhos = getimagesize($arquivo["tmp_name"]);

        // Verifica largura
        if ($tamanhos[0] > $config["largura"]) { ?>

            <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                alert("Imagem muito grande, diminua o tamanho a imagem! Tamanho permitido-> Atura: 3000 pixel; Largura: 3000 pixel!")
                //window.location.href='<?= $PHP_SELF ?>?pg=<?= $pos_banco ?>&ok=0';
            </SCRIPT>

        <?
            $imagem_nome = "erro";
            $erro[] = "erro";
        }

        // Verifica altura
        if ($tamanhos[1] > $config["altura"]) { ?>

            <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                alert("Imagem muito grande, diminua o tamanho a imagem! Tamanho permitido-> Atura: 3000 pixel; Largura: 3000 pixel!")
                //window.location.href='<?= $PHP_SELF ?>?pg=<?= $pos_banco ?>&ok=0';
            </SCRIPT>

<?
            $imagem_nome = "erro";
            $erro[] = "erro";
        }
    }

    if (!sizeof($erro)) {
        // Pega extens�o do arquivo, o indice 1 do array conter� a extens�o
        preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["name"], $ext);

        // Gera nome �nico para a imagem
        $imagem_nome = nome($ext[1]);

        // Caminho de onde a imagem ficar�
        $imagem_dir = $config["diretorio"] . $imagem_nome;

        // Faz o upload da imagem
        move_uploaded_file($arquivo["tmp_name"], $imagem_dir);
    }
}
?>