<?php

class Imagem extends Mysql {

    public $foto = 'foto';
    public $caminho = '../web/fotos';
    public $dir;
    public $tags;
    public $link;
    public $link2;
    public $zoom;
    public $zoom_w;
    public $zoom_h;
    public $img_real;
    public $sem_img;
    public $fixo;
    public $fixo_png = 0;
    public $back;

    public function img($value, $width, $height) {

        $dir = $this->dir ? $this->dir : DIR;
        $width_2 = $width * 2;
        $height_2 = $height * 2;

        $nome_foto = $this->foto;
        $nome_da_foto = nome_da_foto($value->$nome_foto);
        $title = isset($value->nome) ? $value->nome : $nome_da_foto['nomee'];
        $title = (isset($value->multifotos) AND $value->multifotos == 'ok') ? '' : $title; // Multifotos
        // ZOOM
        if ($this->zoom) {
            if (preg_match('(class=")', $this->tags)) {
                $this->tags = str_replace('class="', 'class="Plugin_Zoom ', $this->tags);
            } else {
                $this->tags = 'class="Plugin_Zoom" ';
            }
            $zoom_w = $this->zoom_w ? $this->zoom_w : $this->zoom;
            $zoom_h = $this->zoom_h ? $this->zoom_h : $this->zoom;
            $this->tags .= ' zoom_w="' . $zoom_w . '" zoom_h="' . $zoom_h . '" data-zoom-image="' . DIR . '/web/fotos/' . $value->foto . '" ';
        }



        // SEM IMG
        if ((!$value->$nome_foto and $this->sem_img)) {
            $return = '<img  class="img-fluid card-img-top rounded-lg rounded lazy" src="' . $dir . '/web/img/load-1.gif" data-src="' . $dir . '/web/img/sem_imagem.png" style="max-width:' . $width . 'px; max-height:' . $height . 'px;" />';


            // NAO EXISTE
        } elseif (!file_exists($this->caminho . '/' . $value->$nome_foto)) {
            $return = '<img  class="img-fluid card-img-top rounded-lg rounded lazy" src="' . $dir . '/web/img/load-1.gif" data-src="' . $dir . '/web/img/sem_imagem.png" />';

            // FLASH
        } elseif (preg_match('(.swf)', strtolower($value->$nome_foto)) and ! $this->link) {
            $return = flash($width, $height, $dir . '/web/fotos/' . $value->foto);


            // IMG -> JPEG, JPJ, GIF, PNG, BMP
        } elseif ((preg_match('(.pjpeg)', strtolower($value->$nome_foto))) or ( preg_match('(.jpeg)', strtolower($value->$nome_foto))) or ( preg_match('(.jpg)', strtolower($value->$nome_foto))) or ( preg_match('(.gif)', strtolower($value->$nome_foto))) or ( preg_match('(.png)', strtolower($value->$nome_foto))) or ( preg_match('(.bmp)', strtolower($value->$nome_foto)))) {
            // PNG ou BMP
            if ($this->img_real or ( preg_match('(.png)', strtolower($value->$nome_foto))) or ( preg_match('(.bmp)', strtolower($value->$nome_foto)))) {
                $this->img_real = 1;
            } else {
                $this->img_real = 1;
            }

            // IMG
            $img = $this->img_real ? $dir . '/web/fotos/' . $value->$nome_foto : $dir . '/web/fotos/thumbnails/' . $nome_da_foto['nome'] . '_' . $width_2 . 'x' . $height_2 . '.' . $nome_da_foto['ext'];

            // TAMANHO
            $tamanho = $this->fixo ? 'width="' . $width . '" height="' . $height . '"' : '';

            // LINK
            if ($this->link or $this->link2) {

                if ($this->link2) {
                    $img_link = $dir . '/web/fotos/' . $value->foto1;
                } else {
                    $img_link = $this->img_real ? $dir . '/web/fotos/' . $value->$nome_foto : $dir . '/web/fotos/thumbnails/' . $nome_da_foto['nome'] . '_800x600.' . $nome_da_foto['ext'];
                }

                $return = '<a href="' . $img_link . '" data-imagelightbox="' . iff($this->link == 1, 'b', 'a') . '">
								<img class="lazy" src="' . $img . '" ' . $this->tags . ' title="' . $title . '" alt="' . $title . '" name="' . $title . '" />
							 </a>';


                // NORMAL
            } else {
                $return = '<img class="img-fluid card-img-top rounded-lg rounded lazy"  src="' . $dir . '/web/img/load-1.gif" data-src="' . $img . '" ' . $this->tags . ' title="' . $title . '" alt="' . $title . '" name="' . $title . '" />';
            }
        } else {
            $return = '';
        }



        $this->foto = 'foto';
        //$this->caminho = '../web/fotos';

        $this->tags = "";
        $this->link = 0;
        $this->link2 = 0;
        $this->zoom = 0;
        $this->zoom_w = 0;
        $this->zoom_h = 0;
        $this->img_real = "";
        $this->sem_img = "";
        $this->fixo = 0;
        $this->fixo_png = 0;
        $this->back = '';

        return($return);
    }

    public function img2($value, $width, $height) {

        $dir = $this->dir ? $this->dir : DIR;
        $width_2 = $width * 2;
        $height_2 = $height * 2;

        $nome_foto = $this->foto;
        $nome_da_foto = nome_da_foto($value->$nome_foto);
        $title = isset($value->nome) ? $value->nome : $nome_da_foto['nomee'];
        $title = (isset($value->multifotos) AND $value->multifotos == 'ok') ? '' : $title; // Multifotos
 

        // SEM IMG
        if ((!$value->$nome_foto and $this->sem_img)) {
            $return = '<img  class="img-fluid card-img-top rounded-lg rounded lazy" src="' . $dir . '/web/img/load-1.gif" data-src="' . $dir . '/web/img/sem_imagem.png" style="max-width:' . $width . 'px; max-height:' . $height . 'px;" />';


            // NAO EXISTE
        } elseif (!file_exists($this->caminho . '/' . $value->$nome_foto)) {
            $return = '<img  class="img-fluid card-img-top rounded-lg rounded lazy" src="' . $dir . '/web/img/load-1.gif" data-src="' . $dir . '/web/img/sem_imagem.png" />';

            // FLASH
        } elseif (preg_match('(.swf)', strtolower($value->$nome_foto)) and ! $this->link) {
            $return = flash($width, $height, $dir . '/web/fotos/' . $value->foto);


            // IMG -> JPEG, JPJ, GIF, PNG, BMP
        } elseif ((preg_match('(.pjpeg)', strtolower($value->$nome_foto))) or ( preg_match('(.jpeg)', strtolower($value->$nome_foto))) or ( preg_match('(.jpg)', strtolower($value->$nome_foto))) or ( preg_match('(.gif)', strtolower($value->$nome_foto))) or ( preg_match('(.png)', strtolower($value->$nome_foto))) or ( preg_match('(.bmp)', strtolower($value->$nome_foto)))) {
            // PNG ou BMP
            if ($this->img_real or ( preg_match('(.png)', strtolower($value->$nome_foto))) or ( preg_match('(.bmp)', strtolower($value->$nome_foto)))) {
                $this->img_real = 1;
            } else {
                $this->img_real = 1;
            }

            // IMG
            $img = $this->img_real ? $dir . '/web/fotos/' . $value->$nome_foto : $dir . '/web/fotos/thumbnails/' . $nome_da_foto['nome'] . '_' . $width_2 . 'x' . $height_2 . '.' . $nome_da_foto['ext'];

            // TAMANHO
            $tamanho = $this->fixo ? 'width="' . $width . '" height="' . $height . '"' : '';

            // LINK
            if ($this->link or $this->link2) {

                if ($this->link2) {
                    $img_link = $dir . '/web/fotos/' . $value->foto1;
                } else {
                    $img_link = $this->img_real ? $dir . '/web/fotos/' . $value->$nome_foto : $dir . '/web/fotos/thumbnails/' . $nome_da_foto['nome'] . '_800x600.' . $nome_da_foto['ext'];
                }

                $return = '<a href="' . $img_link . '" data-imagelightbox="' . iff($this->link == 1, 'b', 'a') . '">
								<img class="lazy" src="' . $img . '" ' . $this->tags . ' title="' . $title . '" alt="' . $title . '" name="' . $title . '" />
							 </a>';


                // NORMAL
            } else {
                $return = '<img class="img-fluid card-img-top rounded-lg rounded lazy"  src="' . $dir . '/web/img/load-1.gif" data-src="' . $img . '" ' . $this->tags . ' title="' . $title . '" alt="' . $title . '" name="' . $title . '" />';
            }
        } else {
            $return = '';
        }



        $this->foto = 'foto';
        //$this->caminho = '../web/fotos';

        $this->tags = "";
        $this->link = 0;
        $this->link2 = 0;
        $this->zoom = 0;
        $this->zoom_w = 0;
        $this->zoom_h = 0;
        $this->img_real = "";
        $this->sem_img = "";
        $this->fixo = 0;
        $this->fixo_png = 0;
        $this->back = '';

        return($return);
    }

}

?>