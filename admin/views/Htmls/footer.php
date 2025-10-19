<div>

    <ul class="abas">
    </ul>
    <?php
    if (LUGAR == 'admin') {
        echo date('Y') . ' Administração do Site ';
    } else {
        echo date('Y') . ' Área do ' . ucfirst(LUGAR);
    }
    ?>
    <div class="seta">
        <i class="icon-arrow-up"></i>
    </div>

</div>
