/* Datatables */

// ----------------------------------------------------------------------------------------------------------------------------------

// Eventos Admin



// NOVO
// Leilao Aovivo
function leilao_aovivo($id) {
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Leiloes/leiloes_aovivo.php",
        data: { id: $id },
        dataType: "json",
        success: function ($json) {
            if ($json.item) {
                $.each($json.item, function ($key, $val) {
                    $(".AOVIVO_lote_" + $key + " .AOVIVO_ordem").html($val.ordem);
                    $(".AOVIVO_lote_" + $key + " .AOVIVO_nome").html($val.nome);
                    $(".AOVIVO_lote_" + $key + " .AOVIVO_data").html($val.data);
                    $(".AOVIVO_lote_" + $key + " .AOVIVO_lances").html($val.lances);
                    $(".AOVIVO_lote_" + $key + " .AOVIVO_cadastro").html($val.cadastro);

                    $(".AOVIVO_lote_" + $key + " td").css('background', $val.back);
                    $(".AOVIVO_lote_" + $key + " td").css('color', $val.cor);

                    if ($val.situacao != 0) {
                        $(".AOVIVO_lote_" + $key + " .AOVIVO_dar_lance").remove();
                    }
                    $(".AOVIVO_lote_" + $key).attr('situacao', $val.situacao);
                });
            }

            $("table.AOVIVO_leilao_" + $id).each(function () {
                setTimeout(function () {
                    leilao_aovivo($id);
                }, 1000);
            });

        }
    });
}
;
// Leilao Aovivo

// Dar Lance
function dar_lance($id, e) {
    $lance = $(e).parent().parent().parent().find('input[name="lance"]').val();
    $plaqueta = $(e).parent().parent().parent().find('input[name="plaqueta"]').val();
    if ($plaqueta <= 0) {
        alerts(0, 'Nº Da Plaqueta Inválido!', 1);
    } else if (!$lance) {
        alerts(0, 'Preencha Um Lance Válido!', 1);
    } else {
        $.ajax({
            type: "POST",
            url: DIR + "/app/Ajax/Leiloes/dar_lance.php",
            data: { id: $id, lance: $lance, plaqueta: $plaqueta },
            dataType: "json",
            beforeSend: function () {
                ajaxIni(0);
            },
            success: function ($json) {
                $(".carregando").hide();
                if ($json.erro != null) {
                    $.each($json.erro, function ($key, $val) {
                        alerts(0, $val, 1);
                    });
                } else {
                    alerts(1, 'Lance Efetuado com Sucesso!');
                }
            }
        });
    }
}
// Dar Lance

// Arrematar Lote
function arrematar_lote($id, e) {
    $.ajax({
        type: "POST",
        url: DIR + "/admin/app/Ajax/Leiloes/arrematar_lote.php",
        data: { id: $id },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        success: function ($json) {
            $(".carregando").hide();
            if ($json.erro != null) {
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1);
                });
            } else {
                alerts(1, 'Lance Efetuado com Sucesso!');
            }
        }
    });
}
// Arrematar Lote

// hreff
function hreff($link) {
    window.open($link, '_blank');
}

// emitir_etiqueta
function emitir_etiqueta($link) {
    $ids = '-';
    $(".ui-selected td:first-child > div").each(function () {
        $dir = $(this).attr('dir');
        if ($dir != undefined)
            $ids += $dir + '-';
    });
    window.open($link + '?ids=' + $ids, '_blank');
}

// excel_all
function excel_all($link) {
    window.open($link, '_blank');
}
// NOVO





// ACOES PAGINA PADRAO
// Abrir views carregando a pagina
function views_url($modulo) {
    window.parent.location = DIR + '/' + ADMIN + '/?pg=' + $modulo;
}

// Views
function views($modulo, $acao, $gets, $gets_data) {

    //$('.ui-dialog, .abas .limit').remove();

    $ids = '-';
    $tables = '-';
    if ($acao == 1) {
        $_GET = converter_gets($gets);
        if ($_GET['tipo'])
            $ids = '-' + $_GET['tipo'] + '-';
        else
            $ids = '-' + $_GET['id'] + '-';
    } else {
        // Ids para edicao e exclusao
        $(".ui-selected td:first-child > div").each(function () {
            $dir = $(this).attr('dir');
            if ($dir != undefined)
                $ids += $dir + '-';
            $table = $(this).attr('table');
            if ($table != undefined)
                $tables += $table + '-';
        });
    }

    // Acoes
    if (!$acao || $acao == 0)
        $acao = 'lista';
    else if ($acao == 1)
        $acao = 'edit';
    else if ($acao == 2)
        $acao = 'boxxs';

    // Url
    url = $acao == 'lista' ? window.location.href.replaceAll('&', ';;z;;') : '';

    $gets = $gets ? '&gets=' + $gets.replaceAll('&', ';;z;;') : '';
    $gets_data = $gets_data ? '&' + $gets_data : '';

    $pg_atual = ($modulo == 1) ? 'menu_admin' : 'padrao';

    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/views/" + $pg_atual + ".php?modulo=" + $modulo + "&acao=" + $acao + $gets,
        data: 'ids=' + $ids + '&tables=' + $tables + $gets_data + '&url=' + url + '&body_width=' + widht_resp(),
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    if ($val != 'zzz') {
                        alerts(0, $val, 1, $delay);
                    }
                });

            } else {
                // Default
                if ($acao == 'default') {
                    window.parent.location = $json.url;

                    // Lista
                } else if ($acao == 'lista' || $acao == 'filtro') {
                    if ($acao == 'lista' && $_SESSION['filtro_avancado[' + $modulo + ']']) {
                        datatable_filtro($modulo);
                    } else {
                        $(".principal .views .conteudo .lista").html($json.html);
                        $(".dataTable").selectable({
                            filter: "tr", selected: function (event, ui) {
                                datatable_button_disabled();
                            }
                        });
                        $('.dataTable tbody').on('dblclick', 'tr', function () {
                            views($modulo, 'edit', $gets);
                        });
                        datatable_filtro_itens($modulo);
                        iniciar_events_admin($json.modulo, '.principal .views .conteudo .lista');
                        history.pushState('Administração', 'Administração', $json.url);
                        topoo();
                    }


                    // Boxxs
                } else if ($acao == 'boxxs') {
                    $(".principal .views .conteudo .lista").html($json.html);
                    boxxs('booxs_mudar_status(e)');

                    // Novo
                } else if ($acao == 'novo' || $acao == 'edit') {
                    if ($(".ui-dialog").hasClass("pg_" + $modulo) == true) {
                        dialog_abrir($modulo);
                        setTimeout(function () {
                            $('.ui-dialog.pg_' + $modulo + ' form input[name="nome"]').focus();
                        }, 0.5);
                        alerts(0, "Esta Janela já se Encontra em Uso!");
                    } else {
                        div = ".principal .views .conteudo .events";
                        $(div).html(' <div id="dialog" title="' + $json.title + '">' + $json.html + '</div> ');
                        $(div + ' #dialog').dialog({ dialogClass: "pg_" + $modulo + " pg_" + $json.modulo, });
                        js = 'dialog_click(' + $modulo + ')';
                        $('.ui-dialog.pg_' + $modulo).attr('onClick', js);
                        $('footer .abas li').removeClass('ativo');
                        $('footer .abas').append('<li class="ativo aba_' + $modulo + ' limit" onclick="dialog_click_aba(' + $modulo + ')">' + $json.title + '</li>');
                        dialog_click($modulo);
                        iniciar_events_admin($json.modulo, '.pg_' + $modulo);
                        setTimeout(function () {
                            $('.ui-dialog.pg_' + $modulo + ' form input[name="nome"]').focus();
                        }, 0.5);
                    }


                    // Excluir
                } else if ($acao == 'delete') {
                    alerts(1);
                    datatable_update('delete', $ids);
                }

                // Selecionando Menu
                $('.selected_sub').removeClass('selected_sub');
                $('.selected').removeClass('selected');
                $('#sub-' + $modulo).addClass('selected_sub');
                $('#sub-' + $modulo).closest('.subnav').show().closest('li').find('.ativar-subnav').addClass('selected');
            }
            $(".carregando").hide();
        }
    });

    // Hide no menu topo
    $('header .barra .menu_topo > a').parent().find('ul').stop(true, true).delay(200).slideUp();
    $('header .barra .menu_topo > a').parent().parent().find('li').removeClass('ativo');

}
;

// Gravar Item
function gravar_item($modulo, $id, $classe) {
    $pg_atual = ($modulo == 1) ? 'menu_admin' : 'padrao';
    $($classe).attr('action', DIR + "/" + ADMIN + "/views/" + $pg_atual + ".php?acao=gravar&id=" + $id + "&modulo=" + $modulo);

    $($classe).ajaxForm({
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });
            } else if ($json.datatable_boxs) {
                $('.datatable_campos_boxs').html('');
                datatable_boxs_atualuzar_selects($json.table, $json.rand);
            } else {
                alerts(1);
                if ($json.dataup)
                    $($classe).find("#dataup").val($json.dataup);

                // Atualizar
                if ($modulo == 1) {
                    datatable_update();
                } else if ($id != 0) { // Edit
                    datatable_update('row', $id);
                } else { // Novo
                    datatable_update();
                }

                if ($json.acao == 1) {
                    if ($json.ult_id != undefined)
                        $($classe).attr('onSubmit', "gravar_item(" + $modulo + ", '" + $json.ult_id + "', this)");
                } else if ($json.acao == 2) {
                    if ($id) {
                        dialog_fechar($modulo);
                        views($modulo, 'novo', '');
                    } else {
                        $($classe).find("input[name=reset_button]").trigger('click');
                        $($classe).find("select").trigger('change');
                    }
                } else if ($json.acao == 3) {
                    dialog_fechar($modulo);
                } else if ($json.acao == 4) {
                    $(".ui-dialog.pg_" + $modulo + " #dialog").dialog("destroy");
                    $('footer .abas li.aba_' + $modulo).remove();
                    pop_fechar();
                }
                $($classe).find('.input.file input').val('');
                $($classe).find('.input.file span>span').html('Selecionar Arquivo');
            }
            $(".carregando").hide();
        }

    });
}
;

// Deletar Item
function deletar_item($modulo, item) {
    $pg_atual = ($modulo == 1) ? 'menu_admin' : 'padrao';
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/views/" + $pg_atual + ".php?modulo=" + $modulo + "&acao=delete",
        data: { ids: '-' + item + '-' },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });
            } else {
                alerts(1);
                datatable_update();
                dialog_fechar($modulo);
            }
            $(".carregando").hide();
        }
    });
}
;
// ACOES PAGINA PADRAO



// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// DIALOG
function dialog_mini($id) {
    $('footer .abas li.aba_' + $id).addClass('mini');
    x = 0;
    $("ul.abas li").each(function () {
        if (!($(this).hasClass('ativo') || $(this).hasClass('mini')) && x == 0) {
            $(this).trigger('click');
        }
    });
    $(".ui-dialog.pg_" + $id + " #dialog").dialog("close");
    $('footer .abas li.aba_' + $id).removeClass('ativo');
}
;
function dialog_max($id) {
    if ($(".ui-dialog.pg_" + $id).hasClass("max") == true) {
        $(".ui-dialog.pg_" + $id).removeClass('max');
    } else {
        $(".ui-dialog.pg_" + $id).addClass('max');
    }
}
;
function dialog_fechar($id, $ids) {
    $(".ui-dialog.pg_" + $id + " #dialog").dialog("destroy");
    $('footer .abas li.aba_' + $id).remove();
    pop_fechar();
    if ($id != 1 && $id != 19 && $ids) { // id = id do modulo e ids = id do item
        datatable_update('row', $ids);
    } else if ($id == 19) { // Pedidos
        datatable_update();
    }
}
;
function dialog_abrir($id) {
    $('footer .abas li.aba_' + $id).removeClass('mini');
    $(".ui-dialog.pg_" + $id + " #dialog").dialog("open");
}
;
function dialog_click($id) {
    $(".ui-dialog.pg_" + $id + " #dialog").on("dialogfocus", function (event, ui) {
        $('footer .abas li').removeClass('ativo')
        $('footer .abas li.aba_' + $id).addClass('ativo')
    });
}
;
function dialog_click_aba($id) {
    $('footer .abas li').removeClass('ativo')
    $('footer .abas li.aba_' + $id).addClass('ativo')
    dialog_abrir($id);
}
;
function dialog_button_form(e, $tipo) {
    if ($tipo) {
        $(e).parent().parent().find('.tabs form input[name=acao_button]').val($tipo);
        $(e).parent().parent().find('.tabs form input[type=submit]').trigger('click');
    }
}
;
// DIALOG


// POP
function pop_novo_item(e) {
    $(e).popover({
        html: true,
        container: 'body',
        placement: 'top',
        title: 'Cadastrar Novo Item:',
        content: function () {
            $return = '<p style="margin:0 0 5px;">Digite abaixo o nome do item que você deseja cadastrar:</p> ';
            $classe = "'.popover .popover-content .gravar_categoria'";
            $return += '<input type="text" name="nome" onkeyup="enter_click(' + $classe + ', event)" class="w100p mt3 design" placeholder="Digite aqui o nome..." autocomplete="off"> ';
            $return += '<div class="pt10 tac"> ';
            $table = "'" + $(e).attr('pop') + "'";
            $rand = "'" + $(e).attr('rand') + "'";
            $return += '	<a href="javascript:void(0)" onclick="pop_gravar_categoria(' + $table + ', ' + $rand + ')"; class="gravar_categoria dibi h-a pt5 pb5 mr5 botao"><i class="mr5 fa fa-check c_verde"></i> Salvar</a> ';
            $return += '	<a href="javascript:void(0)" onclick="pop_fechar()"; class="dibi h-a pt5 pb5 ml5 botao"><i class="mr5 fa fa-times c_vermelho"></i> Cancelar</a> ';
            $return += '</div> ';
            return $return;
        }
    });
    setTimeout(function () {
        $('.popover .popover-content input[name=nome]').focus()
    }, 100);
}
function pop_gravar_categoria($table, $rand) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Pop/gravar_categoria.php",
        data: { table: $table, nome: $('.popover .popover-content input[name=nome]').val() },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });

            } else {
                if ($json.id) {
                    alerts(1);
                    pop_fechar();
                    pop_mostrar_categoria($table, $rand, $json.id);
                }
            }
            $(".carregando").hide();
        }
    });
}
function pop_mostrar_categoria($table, $rand, $id) {
    $multiple = $('select[rand="' + $rand + '"]').attr('multiple') == 'multiple';
    $value = $multiple ? $('select[rand="' + $rand + '"]').val().join() : '';
    $pai = $('select[rand="' + $rand + '"]').attr('pai') ? $('select[rand="' + $rand + '"]').attr('pai') : '';
    $item = $('select[rand="' + $rand + '"]').attr('item') ? $('select[rand="' + $rand + '"]').attr('item') : '';
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Pop/mostrar_categoria.php",
        data: { table: $table, rand: $rand, id: $id, value: $value, pai: $pai, item: $item, multiple: $multiple },
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            $('[rand="' + $rand + '"]').each(function () {
                $(this).html($json.html).trigger('change');
                setTimeout(function () {
                    $(this).trigger('change');
                }, 500);
            });
        }
    });
}
function pop_fechar() {
    $('.popover').popover('destroy');
}
// POP

// SELECT2
function select2_val(ev) {
    if (!ev) {
        var $val = "";
    } else {
        var $val = JSON.stringify(ev.params, function (key, value) {
            return value.data.id;
        });
    }
    return $val.replaceAll('"', '');
}
function select2_select(e, ev) {
    $val = select2_val(ev);
    zerar = 0;
    $name = $(e).attr('name').replace("[", "").replace("]", "");
    $rand = $name + '_' + parseInt(Math.random() * 10000000);
    $(e).attr('rand', $rand);
    $value = $(e).val();
    if ($val == '(cn)') {
        pop_novo_item(e);
        $(e).trigger('click');
        zerar = 1;
    }
    if ($val == '(gi)') {
        $('.carregando').show();
        $pai = $(e).attr('pai') ? $(e).attr('pai') : '';
        boxs('gerenciar_itens', 'table=' + $(e).attr('pop') + '&rand=' + $rand + '&item=' + $(e).attr('item') + '&pai=' + $pai, 1);
        setTimeout(function () {
            $(".carregando").hide();
        }, 500);
        zerar = 1;
    }
    if (zerar)
        $(e).val('').trigger("change");
    // multiple
    if ($(e).attr('multiple') == 'multiple') {
        $(e).val($value).trigger("change");
        $("select[rand=" + $rand + "] option[value='(cn)']").attr('selected', false).trigger("change");
        $("select[rand=" + $rand + "] option[value='(gi)']").attr('selected', false).trigger("change");
    }
}
// SELECT2

// INPUT DISABLE
function input_disable() {
    $('.dialog form ul.nav li').each(function () {
        e = $(this).parent().parent().find('ul.campos.box li[tabs="' + $(this).attr('tabs') + '"]');
        if ($(this).hasClass("disabled") == true) {
            e.find('input[required], select[required], textarea[required]').addClass('requiredx').removeAttr('required');
        } else {
            e.find('input.requiredx, select.requiredx, textarea.requiredx').each(function () {
                $(this).removeClass('requiredx');
                $(this).attr('required', '');
            });
        }
    });
}
// INPUT DISABLE

// BOXXS
function booxs_mudar_status(e) {
    $('.carregando').show();
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxxs/mudar_status.php",
        data: 'id=' + $(e).attr('dir') + '&modelos=' + $(e).parent().attr('modelos') + '&boxxs=' + $(e).parent().attr('boxxs'),
        dataType: "json",
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });

            } else {
                if ($json.id)
                    alerts(1);
            }
            $(".carregando").hide();
        }
    });
}
;
// BOXXS

// BOXX LOOP DE CAMPOS (ENDEREÇO)
// Fieldset button (Inserir box (campos) de Enderecos, Contatos, etc...)
function fieldset_ini(e, $table, $id) {
    $tipo = $(e).attr('name');
    $extra = $(e).parent().find('.extra');
    $extra.load(DIR + "/" + ADMIN + "/app/Ajax/Boxx/endereco.php?tipo=" + $tipo + "&table=" + $table + "&id=" + $id);
    fieldset_scripts($tipo);
}
function fieldset(e) {
    $tipo = $(e).attr('name');
    $extra = $(e).parent().find('.extra');
    $rand = 'boxx_' + parseInt(Math.random() * 10000000);
    $extra.append($('<div class="boxx ' + $rand + '">').load(DIR + "/" + ADMIN + "/app/Ajax/Boxx/endereco.php?novo=1&tipo=" + $tipo));
    required_invalid('.' + $rand, 1000);
    fieldset_scripts($tipo);
    fieldset_principal_posicao($extra);
}
function fieldset_fechar(e) {
    $item = $(e).find_parent('class', 'boxx');
    $extra = $(e).find_parent('class', 'extra');
    if ($item.hasClass("boxx_pinc") == false)
        $item.remove();
    fieldset_principal_posicao($extra);

    $checked = 0;
    if ($extra.find('.topo input:radio').is(':checked')) {
        $checked = 1;
    }
    if (!$checked)
        $extra.find('.boxx_pinc .topo input:radio').trigger('click');
}
;
function fieldset_principal_posicao($extra) {
    setTimeout(function () {
        $x = 0;
        $($extra).find(".boxx").each(function () {
            $(this).find('input, select, textarea').attr('dir', $x);
            $(this).find('.topo input:radio').val($x);
            $x++;
        });
    }, 500);
}
;
function fieldset_scripts($tipo) {
    setTimeout(function () {
        mascaras();
        $(".fbutton .extra." + $tipo + " .boxx select.designx").select2();
    }, 500);
}
;
// BOXX LOOP DE CAMPOS (ENDEREÇO, CONTATO)



// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// DATATABLE
// Filtros
function datatable_filtro_itens($modulo) {
    if ($_SESSION['filtro_avancado[' + $modulo + ']']) {
        $.ajax({
            type: "POST",
            url: DIR + "/" + ADMIN + "/app/Ajax/Boxs/filtro_avancado.php",
            data: 'acao=itens&modulo=' + $modulo + '&' + $_SESSION['filtro_avancado[' + $modulo + ']'],
            dataType: "json",
            error: function ($request, $error) {
                ajaxErro($request, $error);
            },
            success: function ($json) {
                $('.datatable_filtro_itens').html($json.html);
            }
        });
    }
}
;
function datatable_filtro($modulo, e) {
    if (e)
        $_SESSION['filtro_avancado[' + $modulo + ']'] = $(e).serialize();
    views($modulo, 'filtro', '', $_SESSION['filtro_avancado[' + $modulo + ']']);
}
;
function datatable_filtro_itens($modulo) {
    if ($_SESSION['filtro_avancado[' + $modulo + ']']) {
        $.ajax({
            type: "POST",
            url: DIR + "/" + ADMIN + "/app/Ajax/Boxs/filtro_avancado.php",
            data: 'acao=itens&modulo=' + $modulo + '&' + $_SESSION['filtro_avancado[' + $modulo + ']'],
            dataType: "json",
            error: function ($request, $error) {
                ajaxErro($request, $error);
            },
            success: function ($json) {
                $('.datatable_filtro_itens').html($json.html);
            }
        });
    }
}
;
function datatable_filtro_add_item($modulo, $itens, $acao) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxs/filtro_avancado.php",
        data: 'acao=' + $acao + '&modulo=' + $modulo + '&' + $itens,
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($acao == 'filtro_inicial') {
                if ($json.post) {
                    $_SESSION['filtro_avancado[' + $modulo + ']'] = $json.post;
                    datatable_filtro_itens($modulo);
                } else {
                    boxs($json.boxs, 'modulos=' + $modulo);
                }
            } else {
                $_SESSION['filtro_avancado[' + $modulo + ']'] = $json.post;
                views($modulo, 'lista', '', $_SESSION['filtro_avancado[' + $modulo + ']']);
            }
        }
    });
}
;
function datatable_filtro_delete_item($modulo, $name, e) {
    if ($_SESSION['filtro_avancado[' + $modulo + ']']) {
        $.ajax({
            type: "POST",
            url: DIR + "/" + ADMIN + "/app/Ajax/Boxs/filtro_avancado.php",
            data: 'acao=delete&modulo=' + $modulo + '&name=' + $name + '&' + $_SESSION['filtro_avancado[' + $modulo + ']'],
            dataType: "json",
            error: function ($request, $error) {
                ajaxErro($request, $error);
            },
            success: function ($json) {
                $(e).find_parent('tags', 'li').fadeOut();
                $_SESSION['filtro_avancado[' + $modulo + ']'] = $json.post;
                views($modulo, 'lista', '', $_SESSION['filtro_avancado[' + $modulo + ']']);
            }
        });
    }
}
;

// Acoes
function datatable_acoes($acao, $modulos, $id, $table, $item) {
    // Ids para edicao e exclusao
    if ($id != undefined) {
        $ids = '-' + $id + '-';
    } else {
        $ids = '-';
        $(".ui-selected td:first-child > div").each(function () {
            $dir = $(this).attr('dir');
            if ($dir != undefined)
                $ids += $dir + '-';
        });
    }

    $table = $table ? $table : '';
    $item = $item ? $item : '';
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Acoes/acoes.php",
        data: { ids: $ids, modulos: $modulos, acao: $acao, table: $table, item: $item },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });

            } else {
                alerts(1);
                if (!$table) {
                    $ids = $ids.split("-");
                    $.each($ids, function ($key, $val) {
                        if ($val) {
                            if ($acao == 'clonar' || $modulos == 1)
                                datatable_update();
                            else
                                datatable_update('row', $val);
                        } else {
                            datatable_update();
                        }
                    });
                    datatable_mais_acoes_fechar();
                } else if ($table == 'mais_fotos') {
                    mais_fotos_update($json.tabelas, $item, $modulos);
                }

            }
            $(".carregando").hide();
        }
    });
}
;
function datatable_mais_acoes_abrir() {
    $('.box_table .acoes ul.mais_acoes').css('display', 'inline-block');
}
function datatable_mais_acoes_fechar() {
    $('.box_table .acoes ul.mais_acoes').hide();
}
function datatable_selecionar_todos() {
    $('.datatable tbody tr').addClass('ui-selected');
    datatable_button_disabled();
    datatable_mais_acoes_fechar();
}
;

// Update
function datatable_update($tipo, $id) {
    if ($('table.datatable').html()) {
        if ($tipo == 'row')
            $.atualizar_datatable_row($id);
        else
            $.atualizar_datatable();
    }
    ;
    $(".box_table .acoes .edit").attr('disabled', true);
    $(".box_table .acoes .delete").attr('disabled', true);
}
;
function ajax_reload($oTable, $tipo, $modulo, $id) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Datatables/atualizar_session_javascript.php",
        data: {
            financeiro_tipos: $_SESSION['financeiro_tipos'],
            financeiro_conta_atual: $_SESSION['financeiro_conta_atual'],
            financeiro_mes_atual: $_SESSION['financeiro_mes_atual'],
            financeiro_ano_atual: $_SESSION['financeiro_ano_atual'],
        },
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($tipo && $modulo && $id) {
                ajax_reload_rows1($tipo, $oTable, $modulo, $id);
            } else if ($oTable) {
                $oTable.ajax.reload(null, false);
            }
        }
    });
}
;
function ajax_reload_rows($tipo, $oTable, $modulo, $id) {
    ajax_reload($oTable, $tipo, $modulo, $id);
}
function ajax_reload_rows1($tipo, $oTable, $modulo, $id) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Datatables/ajax.php?" + $tipo,
        data: { modulo: $modulo, row: $id },
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            $json = datatable_valores_do_ajax($json);
            if ($tipo == 'row') {
                $oTable.row($('[dir="' + $id + '"]').parent().parent()).data($json.data[0]);
            }
        }
    });
}
;

// Arrays dos Valores do Ajax
function datatable_valores_do_ajax($json) {
    $data = $json.data;
    $.each($data, function ($key, $val) {
        $.each($val, function ($key1, $val1) {
            $classe = 'td_datatable td_' + $key1;

            $json.data[$key][$key1] = '<div ';
            $json.data[$key][$key1] += $val1['class'] != null ? ' class="' + $classe + ' ' + $val1['class'] + '" ' : 'class="' + $classe + '" ';
            $json.data[$key][$key1] += $val1['exportar'] != null ? ' exportar="' + $val1['exportar'] + '" ' : '';
            $json.data[$key][$key1] += $val1['dir'] != null ? ' dir="' + $val1['dir'] + '" ' : '';
            $json.data[$key][$key1] += '> ';
            $json.data[$key][$key1] += $val1['onclick'] != null ? '<a onclick="' + $val1['onclick'] + '" class="' + $val1['onclick_class'] + '">' : '';
            $json.data[$key][$key1] += $val1['span'] != null ? '<span class="' + $val1['span'] + '">' : '';
            $json.data[$key][$key1] += ($val1['dir'] != null && '#' + $val1['dir'] != $val1['value']) ? ' <span class="posa t0 l0 fz10">#' + $val1['dir'] + '</span> ' : '';
            //$json.data[$key][$key1] += $val1['data']!=null ? ' <span class="posa t0 r0 fz10">'+$val1['data']+'</span> ' : '';
            $json.data[$key][$key1] += $val1['value'];
            $json.data[$key][$key1] += $val1['span'] != null ? '</span> ' : '';
            $json.data[$key][$key1] += $val1['onclick'] != null ? '</a> ' : '';
            $json.data[$key][$key1] += '</div> ';
        });
    });
    return $json;
}
;

// Ajax Extras (Funcao Chamada No Js do DataTable)
function datatable_ajax_extras($json) {
    if ($json.oTable == '_gravar_datatable') {
        alert('Olhar na funcao datatable_ajax_extras($json) a area comentada');
        /*
         $.ajax({
         type: "POST",
         url: DIR+"/"+ADMIN+"/app/Ajax/Datatables/gravar_datatable.php",
         data: { acao: 'gravar', dados: $json.data, table: $json.gravar_dados_table },
         dataType: "json",
         error: function($request, $error){ ajaxErro($request, $error); },
         success: function($json){
         $(".carregando").hide();
         alerts(1, 'Gravado com Sucesso!');
         $(".events_externos .outros").html('');
         }
         });
         */

    } else if ($json.oTable == '_boxs') {

    } else {
        datatable_button_disabled(0, $json.oTable);
    }
    $('.conteudo .resultado_extra').html($json.html);

    //if($json.tipo == '_financeiro'){
    //	financeiro_saldo($json);
    //}

}
;

// Exportar
function datatable_exportar_excel() {
    $('#exportarr').attr('action', DIR + '/app/Exportacoes/exportar_para_excel.php');
    datatable_exportar_formatando();
    setTimeout(function () {
        submitt('#exportarr');
    }, 0.5);
}
;
function datatable_exportar_pdf() {
    $('#exportarr').attr('action', DIR + '/app/Exportacoes/exportar_para_pdf.php');
    datatable_exportar_formatando()
    setTimeout(function () {
        submitt('#exportarr');
    }, 0.5);
}
;
function datatable_exportar_formatando() {
    $('#exportarr .exportar_center').val('');
    $("table.datatable tbody tr").each(function () {
        $(this).find('.td_datatable').each(function () {
            $dados = $(this).attr('exportar') != null ? $(this).attr('exportar') : strip_tags($(this).html()).trim();
            $('#exportarr .exportar_center').val($('#exportarr .exportar_center').val() + $dados + 'z|z');
        });
        $('#exportarr .exportar_center').val($('#exportarr .exportar_center').val() + 'z-z');
    });
    datatable_mais_acoes_fechar();
}

// Ordenar
function datatable_ordenar($modulos, e) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Acoes/ordenar.php?modulos=" + $modulos,
        data: $(e).serialize(),
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });

            } else {
                $(".carregando").hide();
                alerts(1);
                datatable_update();
            }
            $(".carregando").hide();
        }
    });
}
;

// Disabilitar Botoes da Acao (novo, edit, etc...) se Precisar
function datatable_button_disabled($n_datatable, $oTable) {
    if (!$oTable)
        fechar_all();
    //$n = $(".dataTable tbody tr.ui-selected").length;
    if ($n_datatable == undefined)
        $n_datatable = $(".dataTable tbody tr.ui-selected td .td_datatable").length;
    if ($n_datatable > 1) {
        $(".box_table .acoes .edit").removeAttr('disabled');
        $(".box_table .acoes .delete").removeAttr('disabled');
    } else {
        $(".box_table .acoes .edit").attr('disabled', true);
        $(".box_table .acoes .delete").attr('disabled', true);
    }
}
;

// Mudar Posicao da Acao (novo, edit, etc...)
function datatable_acao_pos() {
    setTimeout(function () {
        $html = $('.box_table .acoes').html();
        $('.box_table .dataTables_filter').after(' <div class="clear"></div> <div class="acoes"> ' + $html + ' </div> ');
        //$('.A.'.box_table .acoes_temp'.A.').addClass('.A.'dni'.A.');
        $('.box_table .acoes_temp').remove();
        $('.box_table table.dataTable').wrap('<div class="table_mobile"></div>');
        fundoo_fechar();
    }, 0.5);
}

// Datatable Colunas (Boxxs)
function datatable_colunas_gravar($modulos) {
    $('.carregando').show();
    $cols = "modulos=" + $modulos;
    $x = 0;
    $('ul.boxxs .colunas ul[boxxs="1"] li').each(function () {
        if ($(this).attr('dir')) {
            $x++
            $cols += '&cols[]=' + $(this).attr('dir');
        }
    });
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxxs/datatable_colunas_gravar.php",
        data: $cols,
        dataType: "json",
        success: function ($json) {
            if ($json.id) {
                alerts(1);
            }
            $('.carregando').hide();
        }
    });
}
;
function datatable_colunas_delete($modulos) {
    $('.carregando').show();
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxxs/datatable_colunas_delete.php",
        data: { modulos: $modulos },
        dataType: "json",
        success: function ($json) {
            if ($json.id) {
                alerts(1);
            }
            $('.carregando').hide();
            datatable_update();
        }
    });
}
;

// Datatable Boxs (datatable extra)
function datatable_acoes_boxs($acao, $modulos, $rand, $id) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Acoes/acoes.php",
        data: 'acao=' + $acao + '&modulos=' + $modulos + '&id=' + $id,
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });
            } else {
                alerts(1);
                $(".carregando").hide();
                datatable_boxs_atualuzar_selects($json.table, $rand);
            }
        }
    });
}
/*
 function datatable_gravar_dados($modulo, $table){
 $.ajax({
 type: "POST",
 url: DIR+"/"+ADMIN+"/app/Ajax/Datatables/gravar_datatable.php?modulo="+$modulo,
 data: { table: $table } ,
 dataType: "json",
 error: function($request, $error){ ajaxErro($request, $error); },
 success: function($json){
 $(".events_externos .outros").html($json.html);
 }
 });
 };
 */
function datatable_ordenar_boxs(e) {
    $modulos = $(e).attr('modulos');
    $rand = $(e).attr('rand').replace('ordemm_', '');
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Acoes/ordenar.php?modulos=" + $modulos + "&rand=" + $rand,
        data: $('.boxs_' + $rand + ' input.datatable_ordem').serialize(),
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            alerts(1);
            datatable_boxs_update();
        }
    });
}
function datatable_boxs_atualuzar_selects($table, $rand) {
    $('.gravar_item_0').hide();
    $('.boxs .form_0_' + $rand + ' input[type=reset]').trigger('click');
    $('.boxs .form_0_' + $rand + ' select').trigger('change');
    $('.gravar_item_1').hide();
    $('.boxs .form_1_' + $rand + ' input[type=reset]').trigger('click');
    $('.boxs .form_1_' + $rand + ' select').trigger('change');
    $('.boxs .form_0_' + $rand + ' [name=subcategorias]').load(DIR + "/" + ADMIN + "/app/Ajax/Boxs_acoes/edit_categorias.php?acao=novo&table=" + $table + "&niveis=" + $('.boxs .form_1_' + $rand + ' .niveis_edit').html());
    datatable_boxs_update();
    pop_mostrar_categoria($table, $rand, 0);
}

function datatable_campos_boxs($modulos, $rand, $id) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Acoes/campos_boxs.php",
        data: 'modulos=' + $modulos + '&id=' + $id + '&rand=' + $rand + '&body_width=' + widht_resp(),
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.evento != null)
                eval($json.evento);
            if ($json.erro != null) {
                $delay = $json.delay ? $json.delay : '';
                $.each($json.erro, function ($key, $val) {
                    alerts(0, $val, 1, $delay);
                });
            } else {
                alerts(1);
                $('.datatable_campos_boxs').html($json.html);
                $('.datatable_campos_boxs form input[name=nome]').focus();
                $('.boxs .datatable_campos_boxs').draggable();
                mascaras();
                criar_css();
                $('select.design').select2();
                $('[rel="tooltip"]').tooltip();
            }
        }
    });
}
function datatable_boxs_update() {
    $.atualizar_datatable_boxs();
}
;
function datatable_campos_boxs_fechar() {
    $('.datatable_campos_boxs').html('');
}
function datatable_campos_boxs_selecionar_select2($rand, e) {
    $val = $(e).parent().parent().find('.td_datatable.td_0').attr('dir');
    $('select[rand=' + $rand + ']').val($val).trigger('change');
}

// DATATABLE



// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// FINANCEIRO
function financeiro($table, $lugar) {
    $(document).ready(function () {
        if ($table == 'financeiro') {
            financeiro_contas_sel(0);

            $($lugar + " .finput.finput_se_repete select").on("select2:select", function (e) {
                financeiro_se_repete($lugar, e);
            });
            $($lugar + " .finput.finput_data_acabar input").on("click", function (e) {
                financeiro_qtd_parcelas($lugar, this);
            });
            $($lugar + ' .finput.finput_data_acabar').addClass('dni');
            $($lugar + ' .finput.finput_qtd_parcelas').addClass('dni');

            $(".lista_financeiro .mapa_url h1 .extra .financeiro button.contas").on("click", function (e) {
                e.stopPropagation();
            })
        }
    });
    $(document).on('click', function (e) {
        e.stopPropagation();
        $(".lista_financeiro .mapa_url h1 .extra .financeiro").removeClass('ativo');
    });
}
function financeiro_se_repete($lugar, ev) {
    $val = select2_val(ev);
    if ($val == 0) {
        $($lugar + ' .finput.finput_data_acabar input[value=0]').trigger('click');
        $($lugar + ' .finput.finput_data_acabar input').attr('disabled', true);
        $($lugar + ' .finput.finput_qtd_parcelas input').attr('disabled', true);
        $($lugar + ' .finput.finput_data_acabar').addClass('dni');
        $($lugar + ' .finput.finput_qtd_parcelas').addClass('dni');
    } else {
        $($lugar + ' .finput.finput_data_acabar input').attr('disabled', false);
        $($lugar + ' .finput.finput_data_acabar').removeClass('dni');
    }
}
function financeiro_qtd_parcelas($lugar, e) {
    if ($(e).val() == 0) {
        $($lugar + ' .finput.finput_qtd_parcelas input').attr('disabled', true);
        $($lugar + ' .finput.finput_qtd_parcelas').addClass('dni');
    } else {
        $($lugar + ' .finput.finput_qtd_parcelas input').attr('disabled', false);
        $($lugar + ' .finput.finput_qtd_parcelas').removeClass('dni');
    }
}
function financeiro_saldo($json) {
    if ($json.financeiro_saldo) {
        // Top -> Saldo Conta Atual
        $('.lista_financeiro .financeiro .contas b').attr('class', $json.financeiro_saldo['cor']);
        $('.lista_financeiro .financeiro .contas b').html($json.financeiro_saldo['valor']);

        // Fim da Pagina
        // Saldo Mes Passado
        $('.lista_financeiro .saldo_total p[mes="passado"]').attr('class', $json.financeiro_saldo_mes_passado['back']);
        $('.lista_financeiro .saldo_total p[mes="passado"] span').html($json.financeiro_saldo_mes_passado['valor']);

        // Saldo Mes Atual
        $('.lista_financeiro .saldo_total h3').html($json.financeiro_mes_atual);
        $('.lista_financeiro .saldo_total p[mes="atual"]').attr('class', $json.financeiro_saldo_mes_atual['back']);
        $('.lista_financeiro .saldo_total p[mes="atual"] span').html($json.financeiro_saldo_mes_atual['valor']);

        // Financeiro Tipos
        for (var $i = 0; $i <= 5; $i++) {
            $('.lista_financeiro .saldo_estatisticas .center table tr[dir="' + $i + '"] p b').html($json.saldo_tipos_pago[$i]);
            $('.lista_financeiro .saldo_estatisticas .center table tr[dir="' + $i + '"] p span').html($json.saldo_tipos_todos[$i]);
            $('.lista_financeiro .saldo_estatisticas .center table tr[dir="' + $i + '"] .back_eee div').css('width', $json.saldo_tipos_porc[$i] + '%');
        }
        ;

        // Saldo Right
        $('.lista_financeiro .saldo_estatisticas .right ul li.pago b').html($json.saldo_pago);
        $('.lista_financeiro .saldo_estatisticas .right ul li.falta b').html($json.saldo_nao_pago);
        $('.lista_financeiro .saldo_estatisticas .right ul li.total b').html($json.saldo);
        // Fim da Pagina

    }
    $('.lista_financeiro .financeiro').removeClass('ativo');

}
function financeiro_tipos(e) {
    $dir = e ? $(e).parent().attr('dir') : 1;
    $saldo = e ? $(e).parent().attr('saldo') : 1;
    $('.lista_financeiro .box_table ul.financeiro_tipos li').removeClass('ativo');
    $('.lista_financeiro .box_table ul.financeiro_tipos li[dir=' + $dir + ']').addClass('ativo');
    $('.lista_financeiro').attr('saldo', $saldo);
    $_SESSION['financeiro_tipos'] = $dir;
    datatable_update();
}
function financeiro_contas($table) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Financeiro/contas.php",
        data: { table: $table },
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.html)
                $(".lista_financeiro .mapa_url h1 .extra .financeiro .contas_lista ul").html($json.html);
            $(".lista_financeiro .mapa_url h1 .extra .financeiro").toggleClass('ativo');

            $(".lista_financeiro .mapa_url h1 .extra .financeiro .contas_lista ul li a").on("click", function (e) {
                financeiro_contas_sel($(this).parent().attr('dir'));
            })
        }
    });
}
function financeiro_contas_sel($id) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Financeiro/contas_sel.php",
        data: { id: $id, financeiro_conta_atual: $_SESSION['financeiro_conta_atual'] },
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.html) {
                $_SESSION['financeiro_conta_atual'] = $json.financeiro_conta_atual;
                $(".lista_financeiro .mapa_url h1 .extra .financeiro .contas p").html($json.html);
                datatable_update();
            }
            $(".lista_financeiro .mapa_url h1 .extra .financeiro").removeClass('ativo');
        }
    });
}
// FINANCEIRO

// MAIS FOTOS
function mais_fotos_update($tabelas, $item, $modulos) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxs_acoes/mais_fotos_update.php",
        data: { item: $item, tabelas: $tabelas, modulos: $modulos },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            $(".carregando").hide();
            $('.mais_fotos_update').html($json.html);
            if ($json.n != undefined) {
                $('table.datatable').find('.td_datatable[dir="' + $item + '"]').parent().parent().find('.n_mais_fotos span').html($json.n);
            }
        }
    });
}
function mais_fotos_gravar($modulos, $item, e) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxs_acoes/mais_fotos_gravar.php?modulos=" + $modulos,
        data: $(e).serialize(),
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.id.delete)
                mais_fotos_update($json.tabelas, $item, $modulos);
        }
    });
}
// MAIS FOTOS

// MAIS MAPAS
function mapa_google(e) {
    $.ajax({
        type: "POST",
        url: DIR + "/" + ADMIN + "/app/Ajax/Boxs_acoes/mapa_google.php",
        data: $(e).serialize(),
        dataType: "json",
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            $('div.mapa_google').html($json.html);
            $('[name="acao_mapa_google"]').val('gravar');
        }
    });
}
// MAIS MAPAS

// NEWSLETTER
// Newsletter -> Marcar Todos checkbox
function selecionar_checkbox() {
    if ($('#marcar_todos:checked').val()) {
        $('#formNewsletter input').prop("checked", true);
    } else {
        $('#formNewsletter input').prop("checked", false);
    }
}

// Newsletter -> Marcar Todos checkbox da categorias
function selecionar_checkbox_categorias(id) {
    if ($('#categorias_' + id).is(':checked'))
        $('.selecionar_' + id).prop("checked", true);
    else
        $('.selecionar_' + id).prop("checked", false);
}
// NEWSLETTER

// MENU ADMIN
// Abas
function menu_admin_abas($acao, e) {
    if ($acao == 'novo') {
        $n = 0;
        $(e).parent().parent().parent().find('> li').each(function (e) {
            if ($n <= parseInt($(this).attr('dir')))
                $n = parseInt($(this).attr('dir'));
        });
        $n++;
        $html = $('.abas_novo').html();
        $html = $html.replaceAll("-key-", $n);
        $(e).parent().parent().parent().append($html);
        $html = $('.colunas_campos_novo').html();
        $html = $html.replaceAll("-key-", $n);
        $(e).parent().parent().parent().parent().find('.box.campos_menu_admin').append($html);
    } else if ($acao == 'check' || $acao == 'disable') {
        $(e).find('i').toggle();
        $status = $(e).find('i.ativo').css('display');
        $status = $status == 'none' ? 0 : 1;
        $(e).parent().find('.menu_admin_abas_nome .' + $acao).val($status);
    } else if ($acao == 'edit') {
        if ($(e).parent().find('.menu_admin_abas_nome').is(":visible")) {
            $(e).parent().find('.menu_admin_abas_nome').stop(true, true).fadeOut();
        } else {
            $(e).parent().find('.menu_admin_abas_nome').stop(true, true).fadeIn();
        }
    } else if ($acao == 'delete') {
        $(e).parent().parent().remove();
    }
}
;
function menu_admin_abas_nome(e, ev) {
    if (ev.keyCode == 13)
        $(e).parent().stop(true, true).fadeOut();
    $(e).parent().parent().parent().find('.abas_nome').$html($(e).val());

}
;


// Colunas e Campos
function menu_admin_mais_menos($acao, e) {
    if ($acao == 1) {
        $n = 0;
        $(e).parent().parent().find('> ul > li').each(function (e) {
            if ($n <= parseInt($(this).attr('dir')))
                $n = parseInt($(this).attr('dir'));
        });
        $n++;
        tipo = $(e).parent().parent().parent().attr('tipo');
        $html = $('.' + tipo + '_novo').html();
        if (tipo == 'camposs') {
            kabas = $(e).parent().parent().parent().attr('dir');
            $html = $html.replaceAll("-kabas-", kabas);
        }
        $html = $html.replaceAll("-key-", $n);
        $html = $html.replaceAll("z-design-z", "design");
        $(e).parent().parent().find('> ul > li[dir=txt]').before($html);
        setTimeout(function () {
            $('ul.menu_admin li.menu_admin_campos_' + kabas + '_' + $n + ' select.design').select2()
        }, 100);
    } else if ($acao == 0) {
        $(e).parent().parent().find('> ul > li[dir=txt]').prev().remove();
    }
}
;


// Outros Campos
function menu_admin_outros_campos(e) {
    if ($(e).parent().find('> .outros_campos').is(":visible")) {
        $(e).parent().find('> .outros_campos').hide();
        $(e).find('> i').removeClass('fa-chevron-up');
        $(e).find('> i').addClass('fa-chevron-down');
    } else {
        $('.menu_admin  .outros_campos').hide();
        $(e).parent().find('> .outros_campos').show();
        $(e).find('> i').removeClass('fa-chevron-down');
        $(e).find('> i').addClass('fa-chevron-up');
    }
}
function menu_admin_deletar_campos(e) {
    var $apagar = confirm('Deseja excluir este item?');
    if ($apagar)
        $(e).parent().parent().remove();
}

function tipo_modulo(e) {
    if ($(e).val() == 2) {
        $(".campos_menu_admin.box [tipo=colunas] .sortable.itens input[type=text]").val('');
        $(".campos_menu_admin.box [tipo=colunas] .sortable.itens input[type=checkbox]").attr('checked', false);
        $(".campos_menu_admin.box [tipo=camposs] .sortable.itens input[type=checkbox]").attr('checked', false);

        $(".campos_menu_admin.box [tipo=colunas] .sortable.itens li:first-child .finput_linhas_check input").trigger('click');
        $(".campos_menu_admin.box [tipo=colunas] .sortable.itens li:first-child .finput_linhas_nome  input").val('Em Aberto');
        $(".campos_menu_admin.box [tipo=colunas] .sortable.itens li:first-child .finput_linhas_value input").val('0->boxxs_amarelo');
    }
}

// menu_admin_select_temp
function menu_admin_select_temp($key, ev) {
    $val = select2_val(ev);
    if ($val == 'text' || $val == 'file' || $val == 'checkbox' || $val == 'radio' || $val == 'select' || $val == 'textarea' || $val == 'button' || $val == 'editor' || $val == 'file_editor') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val($val).trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'categorias') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_nome input').val('Categorias');
        $('.menu_admin_campos_' + $key + ' .finput_input_nome input').val('categorias');
        $('.menu_admin_campos_' + $key + ' .finput_input_opcoes input').val('(categorias)');
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('select').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'subcategorias') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_nome input').val('Categorias');
        $('.menu_admin_campos_' + $key + ' .finput_input_nome input').val('subcategorias');
        $('.menu_admin_campos_' + $key + ' .finput_input_opcoes input').val('(subcategorias)');
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('select').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'preco') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_nome input').val('Preço');
        $('.menu_admin_campos_' + $key + ' .finput_input_nome input').val('preco');
        $('.menu_admin_campos_' + $key + ' .finput_input_tags input').val('class="preco"');
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('text').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'estados') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_nome input').val('Estados');
        $('.menu_admin_campos_' + $key + ' .finput_input_nome input').val('estados');
        $('.menu_admin_campos_' + $key + ' .finput_input_tags input').val('rel_estados="cidades"');
        $('.menu_admin_campos_' + $key + ' .finput_input_opcoes input').val('(estados)');
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('select').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'cidades') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_nome input').val('Cidades');
        $('.menu_admin_campos_' + $key + ' .finput_input_nome input').val('cidades');
        $('.menu_admin_campos_' + $key + ' .finput_input_tags input').val('');
        $('.menu_admin_campos_' + $key + ' .finput_input_opcoes input').val('(cidades)');
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('select').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else if ($val == 'info') {
        $('.menu_admin_campos_' + $key + ' .finput_check input').attr("checked", true);
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('info').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val('text').trigger("change");

    } else {
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo select').val('text').trigger("change");
        $('.menu_admin_campos_' + $key + ' .finput_input_tipo1 select').val($val).trigger("change");
    }
}
// MENU ADMIN



// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// OUTRAS FUNCOES
// Menu Left Responsivo
// Menu Listas (hide)
$('.principal > aside.menu > .menu_lista').on('click', function () {
    if ($('.principal > aside.menu').hasClass('hide')) {
        show_menu_resp();
        gravarCookie('menu_hide', 0, 365);
    } else {
        hide_menu_resp();
        gravarCookie('menu_hide', 1, 365);
    }
});
function hide_menu_resp() {
    $('.principal > aside.menu').addClass('hide');
    $('.principal > aside.views').css('marginLeft', '63px');
    $('.principal > aside.menu > ul > li > ul').hide();
}
function show_menu_resp() {
    $('.principal > aside.menu').removeClass('hide');
    $('.principal > aside.views').css('marginLeft', '235px');
}
// Menu Listas

// Menu Responsivo
$('.principal > aside.menu > ul.menu_left li').hover(function () {
    if ($('.principal > aside.menu').hasClass('hide')) {
        $(this).addClass('ativo');
        $(this).find('ul').show();
    }
}, function () {
    if ($('.principal > aside.menu').hasClass('hide')) {
        $(this).removeClass('ativo');
        $(this).find('ul').hide();
    }
});
// Menu Responsivo

// Verificar se o menu eh hide (Responsivo)
setTimeout(function () {
    if (lerCookie('menu_hide') == 1) {
        $('.principal > .menu').addClass('hide');
        $('.principal > aside.views').css('marginLeft', '63px');
    }
}, 0.5);

// Menu < 900px
$(window).resize(function () {
    if (window.innerWidth < 900)
        hide_menu_resp();
});
if (window.innerWidth < 900)
    hide_menu_resp();
$(window).resize(function () {
    if (window.innerWidth > 900)
        show_menu_resp();
});
if (window.innerWidth > 900)
    show_menu_resp();
// Menu Left Responsivo

// Footer Seta
$(window).scroll(function () {
    if ($(window).scrollTop() > 200)
        $("footer .seta").stop(true, true).delay(200).fadeIn(200);
    else
        $("footer .seta").stop(true, true).delay(200).fadeOut(200);
});
$('footer .seta').on('click', function () {
    $("html,body").animate({ scrollTop: $("html").offset().top }, "fast");
});

// Temas
function temas(e) {
    var $cor = $(e).attr('class');
    $('#style_color').attr('href', DIR + '/' + ADMIN + '/css/cores/' + $cor + '.css');
    gravarCookie('temas', $cor, 365);
    //fechar_all();					
}
// OUTRAS FUNCOES



// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// PAGAMENTO
// Pagamentoo
function Pagamentoo($id, $metodo) {
    $('.events_externos .boxs .planos').hide();
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Pagamentos/pagamento.php",
        data: { id: $id, metodo: $metodo },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            if ($json.alert) {
                alerts(0, $json.alert);
                if ($json.evento != null)
                    eval($json.evento);
            } else {
                $('.events_externos .outros').html($json.form);
                $('.events_externos .outros #form_pagamento').submit();
                $('.carregando_pagamento').show();
            }
        }
    });
}

// Pagamentoo Id
function Pagamentoo_id($metodo, $pedido) {
    $.ajax({
        type: "POST",
        url: DIR + "/app/Ajax/Pagamentos/pagamento_id.php",
        data: { metodo: $metodo, pedido: $pedido },
        dataType: "json",
        beforeSend: function () {
            ajaxIni(0);
        },
        error: function ($request, $error) {
            ajaxErro($request, $error);
        },
        success: function ($json) {
            $(".carregando").hide();
            if ($json.alert) {
                alerts(0, $json.alert);
                if ($json.evento != null)
                    eval($json.evento);
            } else {
                $('.events_externos .outros').html($json.form);
                $('.events_externos .outros #form_pagamento').submit();
            }
        }
    });
}
// PAGAMENTO



/* Eventos Admin
 
 
 ----------------------------------------------------------------------------------------------------------------------------------
 
 
 */
