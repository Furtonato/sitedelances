<?php
require_once "../system/conecta.php";
if (!isset($pagina_login)) { // nao mostrar na pagina de login
    require_once '../system/mysql.php';
    require_once '../app/Funcoes/funcoes.php';
    //require_once "../plugins/Tng/tng/tNG.inc.php";
    require_once '../app/Funcoes/funcoesAdmin.php';

    verificar_sessao();
}

$version = '2.1';



$db = $mysql->getDB();

$valor = serialize($_POST);

$sql = $db->prepare("SELECT valor FROM configs WHERE id = 20");
$executa = $sql->execute();
$reg = $sql->fetch(PDO::FETCH_ASSOC);

$res = unserialize($reg['valor']);
?>
<!DOCTYPE html PUBLIC "-//W3C	//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
    <head>
        <style>
            :root{
                --primary-color: <?= $res['cor_principal'] ?>;
                --text-color: <?= $res['cor_texto'] ?>;
            }
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= iff(LUGAR == 'admin', 'Administração do Site', 'Área de ' . ucfirst(LUGAR)) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
            <link rel="shortcut icon" href="<?= DIR ?>/web/img/ico.ico" type="image/x-icon" />
            <?php
//echo isset($views) ? $views : '';

            if (!(isset($_SESSION['x_admin']->id) AND ( $_SESSION['x_admin']->id == 1 OR $_SESSION['x_admin']->id == 2))) {
                echo ' <style> .lista_2 .acoes .botao.novo, .ui-dialog.pg_2 .acoes .botao.salvar, .ui-dialog.pg_2 .acoes .botao.salvar_novo { display: none; } </style> ';
            }
            ?>
            <link rel="stylesheet/less" type="text/css" href="<?= DIR . '/' . ADMIN ?>/css/style.css?version=<?= $version ?>" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/css.css?version=<?= $version ?>" />
            <link rel="stylesheet" type="text/css" media="screen" href="<?= DIR ?>/css/css.php" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Fonts/Fonts_Fa/css/font-awesome.min.css" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Fonts/Fonts_Icon/simple-line-icons.css" />

            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Jquery/Datatables/css/dataTable.css" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Jquery/Select2/css/select2.css" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/plugins/Jquery/UI/css/ui.css" />

            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/efeitos.css?version=<?= $version ?>" />
            <link rel="stylesheet" type="text/css" href="<?= DIR ?>/css/resp.css?version=<?= $version ?>" />



            <link id = "style_color" href = "<?= DIR . '/' . ADMIN ?>/css/cores/azul_escuro.css" rel = "stylesheet" type = "text/css" >


                <script> var HOST = "<?= $_SERVER["HTTP_HOST"] ?>"; var DIR = "<?= DIR ?>"; var ADMIN = "<?= ADMIN ?>"; var LUGAR = "<?= LUGAR ?>"; var $_GET = new Array(); var $_SESSION = new Array();</script>
                </head>

                <body id="admin<?= iff(LUGAR != 'admin', '_' . LUGAR) ?>">

                    <section class="admin">

                        <header style="position: fixed;z-index: 1000;">
                            <?php include_once 'views/Htmls/topo.php'; ?>
                        </header>

                        <article class="principal">

                            <aside class="menu dn_700">
                                <?php include_once 'views/Htmls/menu.php'; ?>
                            </aside>

                            <aside class="views m0_700">
                                <section class="conteudo" style="margin: 35px 50px;border-radius: 5px;background: #fff;-webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);">
                                    <article class="lista" style="padding: 10px;">
                                        <?php
                                        if (!isset($_GET['pg'])) {
                                            include '../' . ADMIN . '/views/default.php';
                                        } else {
                                            if (!is_numeric($_GET['pg'])) {
                                                include '../' . ADMIN . '/views/' . $_GET['pg'] . '.phtml';
                                            }
                                        }
                                        ?>

                                    </article>
                                    <article class="events"></article>
                                </section>
                            </aside>
                            <div class="clear"></div>
                        </article>

                        <footer style="background-color: #f1f1f1;color: #444;">
                            <?php include_once 'views/Htmls/footer.php'; ?>
                        </footer>

                    </section>

                    // Javascript
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery-1.11.3.min.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery.form.js"></script> 
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/jquery-ui.min.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/less-1.7.5.min.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/bootstrap(popover-tooltip).min.js"></script>

                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Datatables/js/jquery.dataTables.min.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Plugins/ImageLightBox/js/imagelightbox.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/jquery.price_format.1.3.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/jquery.mask.min.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Mascara/js/mascara_events.js"></script>
                    <script type="text/javascript" src="<?= DIR ?>/plugins/Jquery/Select2/js/select2.full.js"></script>

                    <script type="text/javascript" src="<?= DIR . '/' . ADMIN ?>/js/eventos.js?version=<?= $version ?>"></script>
                    <script type="text/javascript" src="<?= DIR ?>/js/eventos_all.js?version=<?= $version ?>"></script>

                    <script type="text/javascript" src="<?= DIR ?>/plugins/Ckeditor/ckeditor/ckeditor.js"></script>

                    <script> iniciar_events_admin('.A.A.');</script>


                    <script>
                        $(document).ready(function () {
                            $('.ativar-subnav').click(function (e) {
                                e.preventDefault();
                                $('.subnav').slideUp();
                                $('.selected').removeClass('selected');
                                $(this).addClass('selected').next('.subnav').slideDown();
                                return false;
                            });
                        });
                    </script>

                    <?php
                    if (isset($_GET['pg']) && is_numeric($_GET['pg'])) {
                        ?>

                        <script>
                            views(<?= $_GET['pg'] ?>, 0, '');
                        </script>

                        <?php
                    }
                    ?>


                    <script>

                        $('.remover').click(function () {
                            var i = $(this).data('i');
                            $('#logo-' + i).val('');
                            $('#img-' + i).remove();
                            $(this).remove();
                            return false;
                        });
                    </script>
                </body>
                </html> 

