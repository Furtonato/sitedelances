<?php
//print_r($value);

$n = array('', 'Judicial', 'ExtraJudicial', 'Privado', 'Venda direta');
$t = array('', 'On Line', 'Presencial', 'On line e presencial', 'Rural');

$mysql->colunas = 'natureza, tipos';
$mysql->filtro = " WHERE id = $value->leiloes";
$lotes = $mysql->read('leiloes');


$mysql->colunas = 'COUNT(lotes) AS lance';
$mysql->filtro = " WHERE lotes = $value->id GROUP BY lotes";
$lotes_lances = $mysql->read('lotes_lances');

// comitentes
if (isset($value->comitentes)) {
    $ex_comitentes = ex($value->comitentes);
} else {
    $leiloes_comitentes = rel('leiloes', $value->leiloes, 'comitentes');
    $ex_comitentes = ex($leiloes_comitentes);
}

$mysql->colunas = 'id, nome, foto';
$mysql->filtro = " WHERE " . STATUS . " AND id IN (" . implode(',', $ex_comitentes) . ") ";
$comitentes_foto = $mysql->read_unico('comitentes');
// comitentes


if (@$tipo == 'lotes') {
    $natureza = $n[$value->natureza];
    $tipos = $t[$value->tipos];
} else {
    $natureza = $n[$lotes[0]->natureza];
    $tipos = $t[$lotes[0]->tipos];
}

// Link
$link = url($tipo, $value);
if ($tipo_box == 'star') {
    $link = 'https://' . $_SERVER['HTTP_HOST'] . '/lote' . url($tipo, $value);
}
$mais_fotos = mais_fotos($value);
?>
<div class="p-2 col-sm-6 col-md-4 col-xl-3 col-12 justify-content-center">
    <div class="card p-2">
        <a style="color: #444;text-decoration: none;" href="<?= $link ?>">
            <div style="font-size: 12px;" class="row mt-1 mb-1 ml-0 mr-0 font-weight-bold">
                <div type="button" class="col-6 m-0 text-center bg-light p-2 text-secondary text-uppercase"><?= $natureza ?></div>
                <div type="button" class="col-6 m-0 text-center bg-light p-2 text-info text-uppercase"><?= $tipos ?></div>
            </div>

            <div style="position: relative;">
                <?php if (isset($comitentes_foto->foto) AND $comitentes_foto->foto) { ?>
                    <div style="top:5px;right: 5px;border-radius: 5px;overflow: hidden;" class="comitente position-absolute">
                        <?= $img->img($comitentes_foto, 70, 70) ?>
                    </div>
                <?php } ?>



                <div id="carousel<?= $value->id ?>" class="carousel slide" data-ride="carousel<?= $value->id ?>" data-interval="8000" data-ride="true">

                    <div class="carousel-inner">

                        <?php
                        $i = 0;


                        foreach ($mais_fotos as $foto) {
                            ?>
                            <div class="carousel-item <?= ($i == 0) ? 'active' : ''; ?>">
                                <img class="img-fluid card-img-top rounded-lg rounded lazy"  src="<?= DIR ?>/web/img/load-1.gif" data-src="<?= DIR ?>/web/fotos/<?= $foto->foto ?>" />
                            </div>
                            <?php
                            $i++;
                        }
                        ?>

                    </div>
                    <a style="color:var(--primary-color);" class="carousel-control-prev text-primary" href="#carousel<?= $value->id ?>" role="button" data-slide="prev">
                        <i style="color:white;text-shadow: 1px 1px 2px black; " class="fa fa-arrow-circle-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a style="color:var(--primary-color);" class="carousel-control-next text-primary" href="#carousel<?= $value->id ?>" role="button" data-slide="next">
                        <i style="color:white;text-shadow: 1px 1px 2px black; " class="fa fa-arrow-circle-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <h6 class="card-title"><?= $value->nome ?></h6>
                <?php if (@$tipo != 'lotes') { ?>
                    <div class="row m-0 text-center">
                        <div class="card-text m-0 text-white bg-dark p-1 col-6">
                            <small><?= lang('Lance inicial') ?></small><br>
                            <b>R$ <?= $value->lance_ini ?></b>
                        </div>
                        <div class="card-text m-0 text-white bg-danger p-1 col-6">
                            <b><i class="fas fa-gavel"></i> <?= $lotes_lances[0]->lance + 1 ?></b><br>
                            <b><i class="fa fa-eye"></i> <?= $value->count ?></b>
                        </div>
                    </div>
                <?php } ?>

                <ul style="font-size: 12px;" class="list-group list-group-flush">
                    <?php if (!empty($value->cidades)) { ?>
                        <li class="list-group-item p-1"><strong><i class="fa fa-map-marker"></i> <?= $value->cidades . ' - ' . $value->estados ?></strong></li>
                    <?php } ?>
                    <li class="list-group-item p-1">
                        <strong><i class="fa fa-calendar"></i> Inicio: </strong><?= date('d/m/Y H:i', strtotime($value->data_ini)); ?>
                    </li>
                    <li class="list-group-item p-1">
                        <strong><i class="fa fa-calendar"></i> Fim: </strong><?= date('d/m/Y H:i', strtotime($value->data_fim)); ?>
                    </li>
                </ul>
            </div>
        </a>
    </div>
</div>