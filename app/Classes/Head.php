<?php

class Head extends Mysql {

    private $version = '2017-09-15';
    private $quebra = "\n";

    public function css() {



        if (false) {
            // Fonts
            $return = '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Fonts/Fonts_Fa/css/font-awesome.min.css" />' . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Fonts/Fonts_Icon/simple-line-icons.css" />' . $this->quebra . $this->quebra;

            // Plugins
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Jquery/Plugins/ImageLightBox/css/imagelightbox.css" />' . $this->quebra . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Jquery/Plugins/LightSlider/css/lightslider.css" />' . $this->quebra;

            // Events Importantes
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Jquery/Datatables/css/dataTable.css" />' . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Jquery/Select2/css/select2.css" />' . $this->quebra . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/plugins/Jquery/UI/css/ui.css" />' . $this->quebra;

            // Meu
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css/css.php" />' . $this->quebra;
            //$return .= '<link rel="stylesheet" type="text/css" href="'.DIR.'/css/animate.css?version='.$this->version.'" />'.$this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css/efeitos.css?version=' . $this->version . '" />' . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css/resp.css?version=' . $this->version . '" />' . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css/css.css?version=' . $this->version . '" />' . $this->quebra;
            $return .= '<link rel="stylesheet/less" type="text/css" href="' . DIR . '/css/style.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;

            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/web/img/z_leilao/style.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;
        } else {
            $return = '<link rel="stylesheet" type="text/css" href="' . DIR . '/css-NEW/bootstrap.min.v.4.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css-NEW/slick.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css-NEW/slick-theme.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;
            $return .= '<link rel="stylesheet" type="text/css" href="' . DIR . '/css-NEW/magnific-popup.css?version=' . $this->version . '" />' . $this->quebra . $this->quebra;
            $return .= '<script type="text/javascript" src="https://kit.fontawesome.com/dabd3c9a66.js" crossorigin="anonymous"></script>' . $this->quebra . $this->quebra;
        }

        return($return);
    }

    public function js() {

        if (false) {
            // Jquery
            $return = '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/jquery-1.11.3.min.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/jquery.form.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/jquery-ui.min.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/less-1.7.5.min.js"></script> ' . $this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/bootstrap(popover-tooltip).min.js"></script> '.$this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/parallax.min.js"></script> '.$this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/smoothscroll.min.js"></script>'.$this->quebra.$this->quebra;
            // Plugins
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Plugins/ElevateZoom/js/jquery.elevatezoom.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Plugins/ImageLightBox/js/imagelightbox.js"></script> ' . $this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Plugins/WaterFall/js/waterfall-1.0.2.min.js"></script> '.$this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Plugins/WaterFall/js/prism.min.js"></script> '.$this->quebra.$this->quebra;
            // Plugins Carrocel
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Plugins/OwlCarousel/js/owl.carousel.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Plugins/BxSlider/js/jquery.bxslider.js"></script> ' . $this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Plugins/FlexSlider/js/jquery.flexslider.js"></script> '.$this->quebra;
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Plugins/LightSlider/js/lightslider.js"></script> '.$this->quebra;
            // Events Importantes
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Jquery/Datatables/js/jquery.dataTables.min.js"></script>'.$this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Flip/js/jquery.flip.min.js"></script>' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Mascara/js/jquery.price_format.1.3.js"></script>' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Mascara/js/jquery.mask.min.js"></script>' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Mascara/js/mascara_events.js"></script>' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/plugins/Jquery/Select2/js/select2.full.js"></script>' . $this->quebra . $this->quebra;

            // Meu
            $return .= '<script type="text/javascript" src="' . DIR . '/js/eventos_all.js?version=' . $this->version . '"></script>' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/js/eventos.js?version=' . $this->version . '"></script>' . $this->quebra . $this->quebra;

            // Tradutor Google
            //$return .= '<script type="text/javascript" src="'.DIR.'/plugins/Google/Tradutor/tradutor.js"></script>'.$this->quebra.'<div id="google_translate_element"></div>'.$this->quebra.'<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>'.$this->quebra.'<link href="'.DIR.'/plugins/Google/Tradutor/tradutor.css" rel="stylesheet" />'.$this->quebra;

            $return .= '<script type="text/javascript" src="' . DIR . '/web/img/z_leilao/eventos.js?version=' . time() . '"></script>' . $this->quebra . $this->quebra;
        } else {
            $return = '<script type="text/javascript" src="' . DIR . '/js/jquery-3.3.1.min.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/js/popper.min.js"></script> ' . $this->quebra;
            $return .= '<script type="text/javascript" src="' . DIR . '/js/bootstrap.min.v4.js"></script> ' . $this->quebra;
            $return .= '<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script> ' . $this->quebra;
            $return .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.15.2/axios.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/slick.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/jquery.mask.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/jquery.cpfcnpj.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/jquery.magnific-popup.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/jquery.lazy.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/jquery.lazy.plugins.min.js"></script> ' . $this->quebra;
            $return .= '<script src="' . DIR . '/js/js.v1.2.js"></script> ' . $this->quebra;
        }

        return($return);
    }

    public function meta() {

        $this->colunas = 'valor, valor1, valor2';
        $this->filtro = " WHERE `lang` = '" . LANG . "' AND `tipo` = 'busca_google' ";
        $google = $this->read('configs');

        $title_tag = $google[0]->valor;
        $descricao_tag = $google[0]->valor1;
        $palavra_chave_tag = $google[0]->valor2;

        // Meta Automatica
        $achou = 0;
        $pggg = $_GET['pg'] == 'lotes' ? 'leiloes' : $_GET['pg'];
        $tables = array($pggg, $pggg . 's', $pggg . '1', substr($pggg, 0, -1), substr($pggg, 0, -1) . 's');
        foreach ($tables as $table) {
            if (!$achou and $_GET['id'] != '-') {
                $this->nao_existe = 1;
                $this->colunas = 'nome, nome_meta, txt_meta';
                $this->prepare = array($_GET['id']);
                $this->filtro = " WHERE `id` = ? ";
                $consulta = $this->read($_GET['pg_real'] == 'textosp' ? 'paginas' : $table);
                if ($consulta and is_array($consulta)) {
                    $achou++;
                    foreach ($consulta as $key => $value) {
                        if (isset($value->nome_meta) and $value->nome_meta) {
                            $title_tag = $value->nome_meta;
                        } elseif (isset($value->nome) and $value->nome) {
                            $title_tag = $value->nome;
                        }
                        if (isset($value->txt_meta) and $value->txt_meta) {
                            $descricao_tag = $value->txt_meta;
                        } elseif (isset($value->nome) and $value->nome) {
                            $descricao_tag = $value->nome;
                        }
                    }
                }
            }
        }

        // Meta Manual
        if (isset($_GET['nome_meta']) or isset($_GET['txt_meta'])) {
            $title_tag = isset($_GET['nome_meta']) ? $title_tag . ' - ' . $_GET['nome_meta'] : $title_tag;
            $descricao_tag = isset($_GET['txt_meta']) ? $descricao_tag . ' - ' . $_GET['txt_meta'] : $descricao_tag;
        }

        $return = $this->quebra;
        $return .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $this->quebra;

        $return .= '<title>' . $title_tag . '</title>' . $this->quebra;
        $return .= '<meta name="description" content="' . $descricao_tag . '" />' . $this->quebra;
        $return .= '<meta name="KEYWORDS" content="' . $palavra_chave_tag . '" />' . $this->quebra;
        $return .= '<meta name="SUBJECT" content="' . $title_tag . '"/>' . $this->quebra;
        $return .= '<meta name="Abstract" content="' . $descricao_tag . '" />' . $this->quebra;
        $return .= '<meta name="company" content="' . $descricao_tag . '" />' . $this->quebra;

        $return .= '<meta name="distribution" content="Global" />' . $this->quebra;
        $return .= '<meta name="RATING" content="General" />' . $this->quebra;
        $return .= '<meta name="ROBOTS" content="INDEX, FOLLOW" />' . $this->quebra;
        $return .= '<meta name="Googlebot" content="index,follow" />' . $this->quebra;
        $return .= '<meta name="MSNBot" content="index,follow,all" />' . $this->quebra;
        $return .= '<meta name="InktomiSlurp" content="index,follow,all" />' . $this->quebra;
        $return .= '<meta name="Unknownrobot" content="index,follow,all" />' . $this->quebra;
        $return .= '<meta name="REVISIT-AFTER" content="2 days" />' . $this->quebra;
        $return .= '<meta name="language" content="PT-BR" />' . $this->quebra;
        $return .= '<meta name="Audience" content="all" />' . $this->quebra;
        $return .= '<meta name="url" content="' . $_SERVER["HTTP_HOST"] . '" />' . $this->quebra;
        $return .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">' . $this->quebra;
        $return .= '<noscript><meta http-equiv="Refresh" content="1; url=' . DIR . '/views/Errors/erro_java.php"></noscript>' . $this->quebra;

        // Font Google
        //$return .= '<link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet" type="text/css">'.$this->quebra;
        //$return .= "<style>.font_google { font-family: 'Playfair Display', serif; } </style> ";
        // Favicon
        $this->colunas = 'foto';
        $this->filtro = " WHERE `tipo` = 'favicon' ";
        $favicon = $this->read_unico("configs");
        $ico = $favicon->foto ? DIR . '/web/fotos/' . $favicon->foto : DIR . '/web/img/ico.ico';
        $return .= '<link rel="shortcut icon" href="' . $ico . '" type="image/x-icon" />' . $this->quebra;

        // Google Analytics
        $this->colunas = 'valor';
        $this->filtro = " WHERE `tipo` = 'google_analytics' ";
        $google_analytics = $this->read_unico("configs");
        $return .= stripslashes(cod('asc->html', $google_analytics->valor) . $this->quebra . $this->quebra);

        // Visitas
        if ((!isset($_SESSION['visitas']) or $_SESSION['visitas'] != date('Y-m-d')) and $_SERVER['HTTP_HOST'] != 'localhost:4000') {
            $this->colunas = 'nome';
            $this->filtro = " WHERE `tipo` = 'visitas' ";
            $visitas = $this->read_unico("configs");

            unset($this->campo);
            $this->filtro = " WHERE `tipo` = 'visitas' ";
            $this->campo['nome'] = $visitas->nome + 1;
            $this->campo['foto'] = date('Y-m-d');
            $this->update('configs');
            $_SESSION['visitas'] = date('Y-m-d');
        }

        return($return);
    }

}

?>