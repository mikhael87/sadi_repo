<script>
    <?php foreach($mensajes_totales as $mensaje) { ?>
            mostrarMensaje('<?php echo $mensaje->getContenido(); ?>');
    <?php } ?>
</script>
