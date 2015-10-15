<div id="plantillas_listado" style="position: absolute; display: none; right: 10px; top: -9px; background-color: #EFEFDE; padding: 10px; border: solid 1px #DDDDDD;">
    <?php
        if(count($plantillas)>0){
            foreach ($plantillas as $plantilla) { ?>
                <a href="#" onclick="eliminar_plantilla(<?php echo $plantilla->getId(); ?>); return false;"><?php echo image_tag('icon/delete.png'); ?></a>
                <a href="#" onclick="setear_plantilla(<?php echo $plantilla->getId(); ?>); return false;"><?php echo $plantilla->getNombre(); ?></a><br/>
        <?php }} else { ?>
            No ha guardado ninguna<br/>plantilla para este documento
    <?php } ?>
</div>

<div id="plantillas_contenido" style="display: none;">
    <?php foreach ($plantillas as $plantilla) { ?>
        <div id="plantilla_oficio_contenido_<?php echo $plantilla->getId(); ?>"><?php echo html_entity_decode($plantilla->getCampoUno()); ?></div>
    <?php } ?>
</div>