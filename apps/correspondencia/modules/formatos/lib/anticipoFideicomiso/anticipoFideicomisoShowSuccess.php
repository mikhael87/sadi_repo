<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Motivo: </font>
        <font class="f16n"><?php if(isset($valores['anticipo_fideicomiso_motivo'])) echo html_entity_decode($valores['anticipo_fideicomiso_motivo']); ?></font>
    </div>
     <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
    <?php echo image_tag('icon/print.png'); ?>
        </a>
    </div>
</div>

