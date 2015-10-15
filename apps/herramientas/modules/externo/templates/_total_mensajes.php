<?php $total_mensajes = Doctrine::getTable('Public_MensajesMasivos')->mensajesTotalEnvio($public_mensajes->getId()); ?>

<div style="text-align: center">
    <?php echo $total_mensajes[0]->getTotal(); ?>
</div>