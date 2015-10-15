<div id="estatus_detalles_<?php echo $public_mensajes->getId(); ?>" class="partial_open" style="min-width: 200px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
    <input id="actual_detalles_<?php echo $public_mensajes->getId(); ?>" type="hidden" value="0"/>
    <div id="frase_detalles_<?php echo $public_mensajes->getId(); ?>" onclick="javascript:fn_ver_destinatarios(<?php echo $public_mensajes->getId(); ?>)" style="padding-left: 20px; cursor: pointer;">Mostrar</div>
    <div id="contenido_detalles_<?php echo $public_mensajes->getId(); ?>" style="display: none;"></div>
</div>