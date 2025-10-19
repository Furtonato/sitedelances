<?php
require_once "../../../../system/conecta.php";
require_once "../../../../system/mysql.php";
require_once "../../../../app/Funcoes/funcoes.php";
require_once "../../../../app/Funcoes/funcoesAdmin.php";


$arr = array();

$arr['title'] = 'Dar Lances';

$db = $mysql->getDB();

$sql = $db->prepare("SELECT * FROM cadastro");
$executa = $sql->execute();
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="mb10" style="width: 900px;">
    <form id="alterarSenha" method="post" action="<?= "/admin/controlers/dar_lances_automaticos.php" ?>">

        <input name="_ids" type="hidden" value="<?= $_POST['_ids'] ?>">

        <div id="lances">
            <div style="display: flex;">
                <div style="width: 60%;">
                    <label for="">Usuário</label><br>
                    <select name="usuario[]" id="" class="design" required>
                        <option value="" disabled selected>Selecione</option>
                        <?php foreach ($usuarios as $usuario) {  ?>
                            <option value="<?= $usuario['id'] ?>"><?= $usuario['nome'] ?></option>
                        <?php } ?>
                    </select>

                </div>
                <div style="width: 30%;margin-left: 10px;">
                    <label for="">Valor Acrescentar</label><br>
                    <input type="text" name="valor_lance[]" class="design preco" style="width: 100%;" required>
                </div>

                <div style="width: 10%;display:flex;justify-content: center;align-items: center;">
                    <i class="fa fa-trash c_vermelho" style="font-size: 16px;"></i>
                </div>

            </div>
        </div>
        <div style="display: block;"><button type="button" class="fll botao add_lance" style="margin-top: 10px;margin-bottom: 10px;float: none !important;"> <i class="icon mr5 fa fa-plus-circle c_verde"></i> Adicionar lance </button></div>

        <div style="display: block;"><button class="fll botao" style="margin-top: 10px;margin-bottom: 10px;float: none !important;"><i class="icon mr5 fa fa-gavel cor_CEAC68"></i> DAR LANCE </button></div>

    </form>

    <script>
        $(function() {
            var template = $('#lances').html();

            $('.add_lance').click(function(e) {
                e.preventDefault();

                $('#lances').append(template);
                $('select.design').select2();
                mascaras();

            });

        });
    </script>

</div>
<?php
$arr['html'] = ob_get_clean();
ob_end_flush();

echo json_encode($arr);
