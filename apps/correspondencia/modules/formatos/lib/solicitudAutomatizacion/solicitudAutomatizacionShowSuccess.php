<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Servicio: </font>
        <font class="f16n"><?php if(isset($valores['solicitudAutomatizacion_servicio'])) echo html_entity_decode($valores['solicitudAutomatizacion_servicio']); ?></font>
    </div>
    <div>
        <font class="f16b">Comentario: </font>
        <font class="f16n"><?php if(isset($valores['solicitudAutomatizacion_comentario'])) echo html_entity_decode($valores['solicitudAutomatizacion_comentario']); ?></font>
    </div>
     <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
            <?php echo image_tag('print.png'); ?>
        </a>
    </div>
</div>
