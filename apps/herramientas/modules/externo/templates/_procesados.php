<?php if($public_mensajes->getStatus()=='F') { 
    $total_mensajes = Doctrine::getTable('Public_MensajesMasivos')->mensajesTotalEnvio($public_mensajes->getId());?>
    <div id="procesados_<?php echo $public_mensajes->getId(); ?>" style="text-align: center"><?php echo $total_mensajes[0]->getTotal(); ?></div>
<?php } else { ?>
    <div id="procesados_<?php echo $public_mensajes->getId(); ?>" style="text-align: center"><?php echo image_tag('icon/cargando.gif'); ?></div>
    <input type="hidden" class="calcular_progreso" id="calcular_progreso_<?php echo $public_mensajes->getId(); ?>" value="<?php echo $public_mensajes->getId().'_'.$public_mensajes->getStatus(); ?>"/>
<?php } ?>
