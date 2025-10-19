DOMINIO = '//' + document.domain + '/';

function show_error(input) {
    input.addClass('border-danger');
    input.closest('div').prepend('<i class="info-error fa fa-warning text-danger"></i>');
}

function alertSuccess(txt) {
    $('body').prepend('<div class="alert success alert-success alert-dismissible fade show fixed-top text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + txt + '</div>');
    setTimeout(function () {
        $('.success').remove();
    }, 5000);
}
function alertError(txt) {
    $('body').prepend('<div class="alert alert-error alert-danger alert-dismissible fade show fixed-top text-center" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + txt + '</div>');

    setTimeout(function () {
        $('.alert-error').remove();
    }, 5000);
}

function atualizar() {
    var id = $('#id').val();
    var cor_situacao = {0: 'bg-primary', 1: 'bg-success', 2: 'bg-danger', 3: 'bg-secondary', 10: 'bg-secondary', 20: 'bg-info'};
    var text_situacao = {0: 'EM BREVE', 1: 'Aberto para Lances', 2: 'Arrematado', 3: 'Não Arrematado', 10: 'Leilão Condicional', 20: 'Venda Direta'};
    $.ajax({
        url: DOMINIO + '/app/Ajax/Leiloes/atualizar_leiloes.php',
        method: 'POST',
        dataType: 'json',
        data: {
            lote: id,
            leiloes: '',
            lotes: ''
        },
        success: function (response) {

            var json = response.item[id];

            $('#dias').html(json.cronometro_atual.dias);
            $('#hora').html(json.cronometro_atual.hora);
            $('#min').html(json.cronometro_atual.min);
            $('#seg').html(json.cronometro_atual.seg);
            $('#visua').html(json.count);
            $('#natureza').html(json.natureza);
            $('#tipos').html(json.tipos);
            $('#praca_info').html(json.praca_info);
            $('#lance-ini').html(json.lance.ini);
            $('#lance-atual').html(json.lance.atual);
            $('#count-lances').html(json.count_lances);
            $('#data-ini').html(json.data.ini + ' ás ' + json.data.hora_ini);
            $('#data-fim').html(json.data.fim + ' ás ' + json.data.hora_fim);
            $('#data-lance').html(json.lances_data);
            $('#user-lance').html('Usuário: ' + json.lances_cadastro);
            $('#lote').html(json.codigo);
            $('#localidade').html(json.local);


            $('#txt-encerra').html('LEILÃO ENCERRA EM');
            switch (json.situacao) {
                case 0:
                    $('#txt-encerra').html('LEILÃO INICIA EM');
                    $('#situacao').addClass('bg-primary').html('EM BREVE');
                    break;
                case 1:
                    $('#situacao').addClass('bg-success').html('Aberto para Lances');
                    break;
                case 2:
                    $('#situacao').addClass('bg-danger').html('Arrematado');
                    break;
                case 3:
                    $('#situacao').addClass('bg-secondary').html('Não Arrematado');
                    break;
                case 10:
                    $('#situacao').addClass('bg-secondary').html('Leilão Condicional');
                    break;
                case 20:
                    $('#situacao').addClass('bg-info').html('Venda Direta');
                    break;

                default:

                    break;
            }

        }
    });
}

function historico($id, $acao_dar_lance = 0) {
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Leiloes/historico.php",
        data: {id: $id},
        dataType: "json",
        success: function ($json) {
            $(".historico").html($json.html);

            if (!$acao_dar_lance) {
                setTimeout(function () {
                    historico($id)
                }, 10000);
            }
        }
    });
}

function habilitar_leilao($id) {
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Leiloes/habilitar_leilao.php",
        data: {id: $id},
        dataType: "json",
        success: function ($json) {
            $(".carregando").hide();
            if ($json.evento != null) {
                eval($json.evento);
            }
            if ($json.erro != null) {
                $.each($json.erro, function ($key, $val) {
                    alertError($val);
                });
            } else {

                alertSuccess('Habilitado com Sucesso!');
                $('#habilitar_leilao').fadeOut();

            }
        }
    });
}

function cronometro() {

    $.each($_LOTES, function ($key, $val) {
        if ($val) {

            $data_fim = new Date($val.ano, $val.mes - 1, $val.dia, $val.hora, $val.min, $val.seg, 0);
            $seg1 = $data_fim.getTime();

            $today = new Date();
            $today.setMilliseconds(0);
            $seg2 = $today.getTime();

            $segs = $seg1 - $seg2;
            $tempo = new Date($segs);
            $tempo.setMilliseconds(0);

            var $return = {dias: 0, hora: '00', min: '00', seg: '00', hora_total: '00', seg_total: '00'};
            if ($segs > 0) {
                // Segundos
                $data_s = $tempo.getSeconds();
                $return['seg'] = $data_s < 10 ? '0' + $data_s : $data_s;

                // Minutos
                $data_i = $tempo.getMinutes();
                $return['min'] = $data_i < 10 ? '0' + $data_i : $data_i;

                // Horas
                $data_h = $segs - (($data_s * 1000) + ($data_i * 60 * 1000));
                for (var $i = $data_h; $i >= (86400 * 1000); ) {
                    $i = $i - (86400 * 1000);
                }
                $data_h = parseInt($i / (60 * 60 * 1000));
                $return['hora'] = $data_h < 10 ? '0' + $data_h : $data_h;

                // Dias
                $seg_d = ($data_h * 60 * 60) + ($data_i * 60) + $data_s;
                $data_d = ($segs - (86400 * 1000)) > 0 ? parseInt(($segs - $seg_d) / (86400 * 1000)) : 0;
                $return['dias'] = $data_d < 10 ? '0' + $data_d : $data_d;

                // Horas Total
                $data_ht = (($data_d * 24) + $data_h);
                $return['hora_total'] = $data_ht < 10 ? '0' + $data_ht : $data_ht;

                $return['seg_total'] = $segs / 1000;

            }


            if ($return['dias'] > 0) {
                $(".LL_box_" + $key + " .LL_cronometro .LL_dias").show().find("span").html($return['dias']);
            } else {
                $(".LL_box_" + $key + " .LL_cronometro .LL_dias").hide().find("span").html("");
            }
            $(".LL_box_" + $key + " .LL_cronometro .LL_hora span").html($return['hora']);
            $(".LL_box_" + $key + " .LL_cronometro .LL_min span").html($return['min']);
            $(".LL_box_" + $key + " .LL_cronometro .LL_seg span").html($return['seg']);

        }
    });

    setTimeout(function () {
        cronometro();
    }, 1000);
}

function validar(form) {

    $('.border-danger').removeClass('border-danger');
    $('.info-error').remove();
    var fail = false;
    form.find('.input').each(function () {
        if ($(this).prop('required') && !$(this).prop('disabled')) {
            if (!$(this).val()) {

                fail = true;
                show_error($(this));
            } else {
                if ($(this).prop('type') == "email") {// Validar email
                    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if (!filter.test($(this).val())) {
                        fail = true;
                        show_error($(this));
                    }
                }
            }

        }
    });
    return fail;
}

function dar_lance($id, e) {
    $lance = $(e).find('input[name="lance"]').val();
    $lance_mais = $(e).find('input[name="lance_mais"]').val();
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Leiloes/dar_lance.php",
        data: {id: $id, lance: $lance, lance_mais: $lance_mais},
        dataType: "json",
        beforeSend: function () {
            //ajaxIni(0);
        },
        success: function ($json) {
            $(".carregando").hide();
            if ($json.evento != null) {
                eval($json.evento);
            }
            if ($json.erro != null) {
                $.each($json.erro, function ($key, $val) {
                    alertError($val);
                });
            } else {


                alertSuccess('Lance efetuado com Sucesso!');


                $('input[name="lance"]').val('');
                atualizar();
                historico($id);
                //atualizar_leiloes('', '', $id, 1);
                setTimeout(function () {
                    //historico($id)
                }, 1000);

            }
        }
    });
}


$(document).ready(function () {


    $('.lazy').Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function (element) {
            //console.log('error loading ' + element.data('src'));
        }
    });

    $('.popup-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        }
    });

    $('.btn-salvar').click(function (e) {

        var form = $(this).closest('form');

        if (!validar(form)) {
            e.preventDefault();

            var url = form.attr('action');
            var data = form.serialize();

            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                dataType: "json",
                beforeSend: function () {

                },
                error: function ($request, $error) {

                },
                success: function (json) {

                    if (json.status) {
                        window.location.href = json.evento;
                    } else {
                        alertError(json.erro);
                    }

                }
            });
        }


    });

    $('.input').change(function () {

        $(this).removeClass('border-danger').closest('div').find('.info-error').remove();

    });


    $('.validarCPF').cpfcnpj({
        mask: true,
        validate: 'cpf',
        handler: '.validarCPF',
        ifValid: function (input) {
            $('.border-danger').removeClass('border-danger');
            $('.info-error').remove();
            input.removeClass('border-danger').closest('div').find('.info-error').remove();
            input.closest('div').find('.invalid-feedback').fadeOut();
        },
        ifInvalid: function (input) {
            $('.border-danger').removeClass('border-danger');
            $('.info-error').remove();
            show_error(input);
            input.closest('div').find('.invalid-feedback').fadeIn();
        }
    });

    $('.validarCNPJ').cpfcnpj({
        mask: true,
        validate: 'cnpj',
        handler: '.validarCNPJ',
        ifValid: function (input) {
            $('.border-danger').removeClass('border-danger');
            $('.info-error').remove();
            input.removeClass('border-danger').closest('div').find('.info-error').remove();
            input.closest('div').find('.invalid-feedback').fadeOut();
        },
        ifInvalid: function (input) {
            $('.border-danger').removeClass('border-danger');
            $('.info-error').remove();
            show_error(input);
            input.closest('div').find('.invalid-feedback').fadeIn();
        }
    });

    $('.slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.slider-nav'
    });
    $('.slider-nav').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        centerMode: true,
        focusOnSelect: true
    });
});