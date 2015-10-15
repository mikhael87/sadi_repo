<?php $destinatarios_total = Doctrine::getTable('Public_MensajesMasivos')->destinatariosTotalEnvio($public_mensajes->getId()); ?>

<div style="text-align: center">
    <?php echo $destinatarios_total[0]->getTotal(); ?>
</div>