<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_redireccion_instruccion">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'redireccion_instruccion')) ?>

    <div>
        <label for="redireccion_instruccion">Instrucción</label>
        <div class="content">
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Archivar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Archivar') { echo 'checked'; } } ?>/>&nbsp;Archivar<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Revisar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Revisar') { echo 'checked'; } } ?>/>&nbsp;Revisar<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Para su conocimiento" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Para su conocimiento') { echo 'checked'; } } ?>/>&nbsp;Para su conocimiento<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Analizar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Analizar') { echo 'checked'; } } ?>/>&nbsp;Analizar<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Hacer seguimiento" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Hacer seguimiento') { echo 'checked'; } } ?>/>&nbsp;Hacer seguimiento<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Procesar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Procesar') { echo 'checked'; } } ?>/>&nbsp;Procesar<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Tramitar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Tramitar') { echo 'checked'; } } ?>/>&nbsp;Tramitar<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Informarme" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Informarme') { echo 'checked'; } } ?>/>&nbsp;Informarme<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Traer a cuenta" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Traer a cuenta') { echo 'checked'; } } ?>/>&nbsp;Traer a cuenta<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Preparar Informe" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Preparar Informe') { echo 'checked'; } } ?>/>&nbsp;Preparar Informe<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Preparar Respuesta" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Preparar Respuesta') { echo 'checked'; } } ?>/>&nbsp;Preparar Respuesta<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Contestar al Interesado" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Contestar al Interesado') { echo 'checked'; } } ?>/>&nbsp;Contestar al Interesado<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Atender o darle audiencia" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Atender o darle audiencia') { echo 'checked'; } } ?>/>&nbsp;Atender o darle audiencia<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Proceder" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Proceder') { echo 'checked'; } } ?>/>&nbsp;Proceder<br/>
            <input type="radio" name="correspondencia[formato][redireccion_instruccion]" value="Coordinar" <?php if(isset($formulario['redireccion_instruccion'])) { if($formulario['redireccion_instruccion']=='Coordinar') { echo 'checked'; } } ?>/>&nbsp;Coordinar<br/>
        </div>
    </div>

    <div class="help">Seleccione la Instrucción que desea enviar</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_redireccion_observaciones">
    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'redireccion_observaciones')) ?>

    <div>
        <label for="redireccion_observaciones">Observaciones</label>
        <div class="content">
            <textarea rows="4" cols="30" name="correspondencia[formato][redireccion_observaciones]" id="redireccion_observaciones"><?php if(isset($formulario['redireccion_observaciones'])) echo $formulario['redireccion_observaciones']; ?></textarea>
        </div>
    </div>

    <div class="help"></div>
</div>

</fieldset>