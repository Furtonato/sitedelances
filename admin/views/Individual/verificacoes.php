<?php

if ($modulos->id == '60') {

    if (isset($_POST['pago']) AND $_POST['pago']) {

        $mysql->colunas = 'id, nome, pago, ordem, leiloes, lances, lances_cadastro, lances_data, cidades, estados';
        $mysql->prepare = array($_GET['id']);
        $mysql->filtro = " WHERE `id` = ? ";
        $lotes = $mysql->read_unico('lotes');

        if (!$lotes->pago) {

            $mysql->colunas = 'id, nome, email';
            $mysql->filtro = " WHERE id = '" . $lotes->lances_cadastro . "' ";
            $cadastro = $mysql->read_unico('cadastro');

            if (isset($cadastro->id) AND isset($lotes->id)) {
                $mysql->filtro = " WHERE `id` = 55 ";
                $textos = $mysql->read_unico('textos');
                $var_email = email_55($cadastro, $lotes);

                $email = new Email();
                $email->to = $cadastro->email;
                $email->remetente = nome_site();
                $email->assunto = var_email($textos->nome, $var_email, 1);
                $email->txt = var_email(txt($textos), $var_email, 1);
                $email->enviar();

                unset($_POST['to']);
                unset($_POST['remetente']);
                unset($_POST['assunto']);
                unset($_POST['corpo']);
                unset($_POST['html']);
            }
        }
    }
}
