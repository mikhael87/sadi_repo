<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Asunto: </font>
        <font class="f16n"><?php if(isset($valores['punto_informacion_asunto'])) echo html_entity_decode($valores['punto_informacion_asunto']); ?></font>
    </div>
    <div>
        <font class="f16b">Sintesis: </font>
        <font class="f16n"><?php if(isset($valores['punto_informacion_sintesis'])) echo html_entity_decode($valores['punto_informacion_sintesis']); ?></font>
    </div>
    <div>
        <font class="f16b">Recomendaciones: </font>
        <font class="f16n"><?php if(isset($valores['punto_informacion_recomendaciones'])) echo html_entity_decode($valores['punto_informacion_recomendaciones']); ?></font>
    </div>
     <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
            <?php echo image_tag('icon/print.png'); ?>
        </a>
    </div>
</div>
