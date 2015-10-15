<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $additional_actions= $parametros_all['additional_actions'];
}else
    $additional_actions= Array('crear'=>'false', 'enviar'=>'false', 'anular'=>'false', 'devolver'=>'false');
?>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_additional_actions">
    <div>
        <label>Crear</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][crear]" value="true" <?php echo (($additional_actions['crear']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][crear]" value="false" <?php echo (($additional_actions['crear']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Al CREAR la correspondencia de este formato se ejecutara un proceso mediante una función?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_additional_actions">
    <div>
        <label>Enviar</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][enviar]" value="true" <?php echo (($additional_actions['enviar']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][enviar]" value="false" <?php echo (($additional_actions['enviar']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Al ENVIAR la correspondencia de este formato se ejecutara un proceso mediante una función?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_additional_actions">
    <div>
        <label>Anular</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][anular]" value="true" <?php echo (($additional_actions['anular']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][anular]" value="false" <?php echo (($additional_actions['anular']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Al ANULAR la correspondencia de este formato se ejecutara un proceso mediante una función?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_additional_actions">
    <div>
        <label>Devolver</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][devolver]" value="true" <?php echo (($additional_actions['devolver']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][additional_actions][devolver]" value="false" <?php echo (($additional_actions['devolver']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Al DEVOLVER la correspondencia de este formato se ejecutara un proceso mediante una función?.</div>
    </div>
</div>