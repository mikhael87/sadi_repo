<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">NOMBRE ETIQUETA: </font>
        <font class="f16n"><?php if(isset($valores['nombre_etiqueta'])) echo html_entity_decode($valores['nombre_etiqueta']); ?></font>
    </div>
     <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
            <?php echo image_tag('icon/print.png'); ?>
        </a>
    </div>
</div>
