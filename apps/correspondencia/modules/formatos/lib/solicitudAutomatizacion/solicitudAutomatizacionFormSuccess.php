<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'solicitudAutomatizacion_servicio')) ?>

    <div>
        <label for="solicitudAutomatizacion_servicio">Servicio</label>
        <div class="content">
            <select name="correspondencia[formato][solicitudAutomatizacion_servicio]">
                <option value="Telefonia">Telefonia</option>
                <option value="Sistemas">Sistemas</option>
                <option value="Internet">Internet</option>
                <option value="Solicitud de correo">Solicitud de correo</option>
            </select>
        </div>
    </div>

    <div class="help">Seleccione el servicio que desea.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'solicitudAutomatizacion_comentario')) ?>

    <div>
        <label for="solicitudAutomatizacion_comentario">Comentario</label>
        <div class="content">
            <textarea rows="4" cols="30" name="correspondencia[formato][solicitudAutomatizacion_comentario]"><?php if(isset($formulario['solicitudAutomatizacion_comentario'])) echo $formulario['solicitudAutomatizacion_comentario']; ?></textarea>
        </div>
    </div>

    <div class="help">Explique de manera resumida el problema que tiene.</div>

</div>


</fieldset>