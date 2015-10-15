<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $receptores= $parametros_all['receptores']['externos'];
}else
    $receptores= 'false';
?>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_receptores_externos">
    <div>
        <label>Receptores externos</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][receptores][externos]" value="false" <?php echo (($receptores== 'false')? 'checked' : ''); ?>/> "NO" solo receptores internos &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][receptores][externos]" value="true" <?php echo (($receptores== 'true')? 'checked' : ''); ?>/> "SI" este formato lo pueden recibir receptores externos
        </div>
    </div>
</div>