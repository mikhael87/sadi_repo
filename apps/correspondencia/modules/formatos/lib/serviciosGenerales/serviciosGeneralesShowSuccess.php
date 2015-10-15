<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Servicio: </font>
        <font class="f16n"><?php if(isset($valores['servicios_generales_servicio_show'])) echo $valores['servicios_generales_servicio_show']; ?></font>
    </div>
    <hr>
    <div>
        <font class="f16n"><?php if(isset($valores['servicios_generales_descripcion'])) echo $valores['servicios_generales_descripcion']; ?></font>
    </div>
     <div>
         <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$valores['id']; ?>" title="Descargar">
            <?php echo image_tag('icon/pdf.png'); ?>
        </a>
    </div>
</div>

