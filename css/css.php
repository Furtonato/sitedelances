<?php

if (extension_loaded('zlib')) {
    ob_start('ob_gzhandler');
}
ini_set('display_errors', 0);
require_once "../system/conecta.php";
header("Content-Type: text/css");

$espaco = '
';

// Css Dinamico
for ($x = 0; $x <= 250; $x++) {

    // Font size
    if ($x <= 100) {
        $css[1][] = '.fz' . $x . ' { font-size:' . $x . 'px !important; } ';
    }


    // Height
    if ($x < 10) {
        $css[10][] = '.h' . $x . ', .hh' . $x . ' { height:' . $x . 'px !important; } ';
    }
    for ($i = 0; $i < 10; $i++) {
        if ($x . $i <= 1000) {
            $css[10][] = '.h' . $x . $i . ' { height:' . $x . $i . 'px !important; } ';
        }
    }
    if ($x <= 100) {
        $css[11][] = '.min-h' . $x . '0 { min-height:' . $x . '0px !important; } ';
        $css[12][] = '.max-h' . $x . '0 { max-height:' . $x . '0px !important; } ';
        $css[13][] = '.lh' . $x . ' { line-height:' . $x . 'px !important; } ';
    }


    // Width
    if ($x < 10) {
        $css[20][] = '.w' . $x . ' { width:' . $x . 'px !important; } ';
    }
    for ($i = 0; $i < 10; $i++) {
        if ($x . $i <= 1500) {
            $css[20][] = '.w' . $x . $i . ' { width:' . $x . $i . 'px !important; } ';
        }
    }

    if ($x <= 100) {
        $css[21][] = '.min-w' . $x . '0 { min-width:' . $x . '0px !important; } ';
        $css[22][] = '.max-w' . $x . '0 { max-width:' . $x . '0px !important; } ';
        $css[23][] = '.w' . $x . 'p { width:' . $x . '% !important; } ';
    }


    // Margin
    $css[30][] = '.m' . $x . ' { margin:' . $x . 'px !important; } ';

    $css[31][] = '.mt-' . $x . ' { margin-top:-' . $x . 'px !important; } ';
    $css[32][] = '.ml-' . $x . ' { margin-left:-' . $x . 'px !important; } ';
    $css[33][] = '.mr-' . $x . ' { margin-right:-' . $x . 'px !important; } ';
    $css[34][] = '.mb-' . $x . ' { margin-bottom:-' . $x . 'px !important; } ';

    if ($x < 10) {
        $css[35][] = '.mt' . $x . ' { margin-top:' . $x . 'px !important; } ';
        $css[36][] = '.ml' . $x . ' { margin-left:' . $x . 'px !important; } ';
        $css[37][] = '.mr' . $x . ' { margin-right:' . $x . 'px !important; } ';
        $css[38][] = '.mb' . $x . ' { margin-bottom:' . $x . 'px !important; } ';
    }
    for ($i = 0; $i < 10; $i++) {
        if ($x . $i <= 500) {
            $css[35][] = '.mt' . $x . $i . ' { margin-top:' . $x . $i . 'px !important; } ';
            $css[36][] = '.mb' . $x . $i . ' { margin-bottom:' . $x . $i . 'px !important; } ';
            $css[37][] = '.ml' . $x . $i . ' { margin-left:' . $x . $i . 'px !important; } ';
            $css[38][] = '.mr' . $x . $i . ' { margin-right:' . $x . $i . 'px !important; } ';
        }
    }


    // Padding
    $css[40][] = '.p' . $x . ' { padding:' . $x . 'px !important; } ';
    if ($x < 10) {
        $css[41][] = '.pt' . $x . ' { padding-top:' . $x . 'px !important; } ';
        $css[42][] = '.pl' . $x . ' { padding-left:' . $x . 'px !important; } ';
        $css[43][] = '.pr' . $x . ' { padding-right:' . $x . 'px !important; } ';
        $css[44][] = '.pb' . $x . ' { padding-bottom:' . $x . 'px !important; } ';
    }
    for ($i = 0; $i < 10; $i++) {
        if ($x . $i <= 500) {
            $css[41][] = '.pt' . $x . $i . ' { padding-top:' . $x . $i . 'px !important; } ';
            $css[42][] = '.pb' . $x . $i . ' { padding-bottom:' . $x . $i . 'px !important; } ';
            $css[43][] = '.pl' . $x . $i . ' { padding-left:' . $x . $i . 'px !important; } ';
            $css[44][] = '.pr' . $x . $i . ' { padding-right:' . $x . $i . 'px !important; } ';
        }
    }


    // Top Bottom Left Right
    $css[50][] = '.t' . $x . ' { top: ' . $x . 'px !important; } ';
    $css[51][] = '.b' . $x . ' { bottom: ' . $x . 'px !important; } ';
    $css[52][] = '.l' . $x . ' { left: ' . $x . 'px !important; } ';
    $css[53][] = '.r' . $x . ' { right: ' . $x . 'px !important; } ';

    if ($x <= 100) {
        $css[54][] = '.t' . $x . 'p { top: ' . $x . '% !important; } ';
        $css[55][] = '.b' . $x . 'p { bottom: ' . $x . '% !important; } ';
        $css[56][] = '.l' . $x . 'p { left: ' . $x . '% !important; } ';
        $css[57][] = '.r' . $x . 'p { right: ' . $x . '% !important; } ';
    }


    // Calc
    if ($x < 10) {
        $css[60][] = '.calc' . $x . ' { width: -webkit-calc(100% - ' . $x . 'px) !important; width: -moz-calc(100% - ' . $x . 'px) !important; width: calc(100% - ' . $x . 'px) !important; } ';
    }
    for ($i = 0; $i < 10; $i++) {
        if ($x . $i <= 500) {
            $css[61][] = '.calc' . $x . $i . ' { width: -webkit-calc(100% - ' . $x . $i . 'px) !important; width: -moz-calc(100% - ' . $x . $i . 'px) !important; width: calc(100% - ' . $x . $i . 'px) !important; } ';
        }
    }
    if ($x <= 10) {
        $css[62][] = '.calc_' . $x . ' { width: -webkit-calc(100% + ' . $x . 'px) !important; width: -moz-calc(100% + ' . $x . 'px) !important; width: calc(100% + ' . $x . 'px) !important; } ';
    }


    // Border Radius
    if ($x <= 100 AND ( $x <= 20 OR ! ($x % 5))) {
        $css[70][] = '.br' . $x . ' { -webkit-border-radius: ' . $x . 'px !important; -moz-border-radius: ' . $x . 'px !important; border-radius: ' . $x . 'px !important; } ';
        $css[71][] = '.brt' . $x . ' { -webkit-border-radius: ' . $x . 'px ' . $x . 'px 0 0 !important; -moz-border-radius: ' . $x . 'px ' . $x . 'px 0 0 !important; border-radius: ' . $x . 'px ' . $x . 'px 0 0 !important; } ';
        $css[72][] = '.brb' . $x . ' { -webkit-border-radius: 0 0 ' . $x . 'px ' . $x . 'px !important; -moz-border-radius: 0 0 ' . $x . 'px ' . $x . 'px !important; border-radius: 0 0 ' . $x . 'px ' . $x . 'px !important; } ';
        $css[73][] = '.brl' . $x . ' { -webkit-border-radius: ' . $x . 'px 0 0 ' . $x . 'px !important; -moz-border-radius: ' . $x . 'px 0 0 ' . $x . 'px !important; border-radius: ' . $x . 'px 0 0 ' . $x . 'px !important; } ';
        $css[74][] = '.brr' . $x . ' { -webkit-border-radius: 0 ' . $x . 'px ' . $x . 'px 0 !important; -moz-border-radius: 0 ' . $x . 'px ' . $x . 'px 0 !important; border-radius: 0 ' . $x . 'px ' . $x . 'px 0 !important; } ';
    }


    // Border Width
    if ($x <= 20) {
        $css[80][] = '.bdw' . $x . ' { border-width: ' . $x . 'px !important; } ';
        $css[81][] = '.bdwt' . $x . ' { border-top-width: ' . $x . 'px !important; } ';
        $css[82][] = '.bdwb' . $x . ' { border-bottom-width: ' . $x . 'px !important; } ';
        $css[83][] = '.bdwl' . $x . ' { border-left-width: ' . $x . 'px !important; } ';
        $css[84][] = '.bdwr' . $x . ' { border-right-width: ' . $x . 'px !important; } ';
    }


    // Resp
    $y = $x * 5;
    if ($y >= 300 and ! ($y % 50)) {
        $css[600][] = '.dnn_' . $y . ', .dib_' . $y . ', .dii_' . $y . ' { display: none !important; } ';
        $css[610][] = '@media (max-width: ' . $y . 'px) { ';
        $css[610][] = '.dn_' . $y . ' { display: none !important; } ';
        $css[610][] = '.dnn_' . $y . ' { display: block !important; } ';
        $css[610][] = '.db_' . $y . ' { display: block !important; } ';
        $css[610][] = '.dib_' . $y . ', .dii_' . $y . ' { display: inline-block !important; } ';

        $css[610][] = '.w100p_' . $y . ' { width: 100% !important; } ';
        $css[610][] = '.h100p_' . $y . ' { height: 100% !important; } ';
        $css[610][] = '.w-a_' . $y . ' { width: auto !important; } ';
        $css[610][] = '.h-a_' . $y . ' { height: auto !important; } ';

        $css[610][] = '.t0_' . $y . ' { top: 0 !important; } ';
        $css[610][] = '.b0_' . $y . ' { bottom: 0 !important; } ';
        $css[610][] = '.l0_' . $y . ' { left: 0 !important; } ';
        $css[610][] = '.r0_' . $y . ' { right: 0 !important; } ';

        $css[610][] = '.p0_' . $y . ' { padding: 0 !important; } ';
        $css[610][] = '.pt0_' . $y . ' { padding-top: 0 !important; } ';
        $css[610][] = '.pb0_' . $y . ' { padding-bottom: 0 !important; } ';
        $css[610][] = '.pr0_' . $y . ' { padding-left: 0 !important; } ';
        $css[610][] = '.m0_' . $y . ' { margin: 0 !important; } ';
        $css[610][] = '.mt0_' . $y . ' { margin-top: 0 !important; } ';
        $css[610][] = '.mb0_' . $y . ' { margin-bottom: 0 !important; } ';
        $css[610][] = '.ml0_' . $y . ' { margin-left: 0 !important; } ';
        $css[610][] = '.mr0_' . $y . ' { margin-right: 0 !important; } ';
        $css[610][] = '.m-a_' . $y . ' { margin: auto !important; } ';


        $css[610][] = '.fln_' . $y . ' { float: none !important; } ';
        $css[610][] = '.tac_' . $y . ' { text-align: center !important; } ';

        $css[610][] = '.jc_' . $y . ' { justify-content: center !important; } ';
        $css[610][] = '.jr_' . $y . ' { justify-content: flex-end !important; } ';

        $css[610][] = '.bd0_' . $y . ' { border:0 !important; } ';
        $css[610][] = '.back0_' . $y . ' { background: none !important; } ';
        $css[610][] = '} ';
    }
}




foreach ($css as $key => $value) {
    echo $key == 600 ? $espaco . $espaco . '// Responsivo --------------------------------------------------------' . $espaco . $espaco : '';
    if ($value) {
        foreach ($value as $k => $v) {
            echo $key >= 610 ? $espaco : '';
            echo $v;
        }
    }
    echo $espaco . $espaco . $espaco;
}



if (extension_loaded('zlib')) {
    ob_end_flush();
}
?>