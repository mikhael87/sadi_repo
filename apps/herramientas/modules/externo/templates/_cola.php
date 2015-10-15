<?php if($public_mensajes->getStatus()=='A') { ?>
    <div id="cola_<?php echo $public_mensajes->getId(); ?>" style="text-align: center"><?php echo image_tag('icon/cargando.gif'); ?></div>
<?php } else if($public_mensajes->getStatus()=='C') { ?>
    <div style="text-align: center"><fond style='color: red;'>Cancelado</fond></div>
<?php } else if($public_mensajes->getStatus()=='F') { ?>
    <div style="text-align: center"><fond style='color: #04B404;'>Finalizado</fond></div>
<?php } else if($public_mensajes->getStatus()=='P') { ?>
    <div id="cola_<?php echo $public_mensajes->getId(); ?>" style="text-align: center"><?php echo image_tag('icon/cargando.gif'); ?></div>
    <div style="text-align: center"><fond style='color: #fbcb09;'>Pausado</fond></div>
<?php } ?>

