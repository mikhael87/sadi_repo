<?php use_helper('jQuery'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'nombre_etiqueta')) ?>

    <div>
        <label for="correspondencia[formato][nombre_etiqueta]">Nombre de Etiqueta</label>
        <div class="content">
            <input type="text" name="correspondencia[formato][nombre_etiqueta]" value="" checked/>
        </div>
    </div>

    <div class="help">Ayuda.</div>

</div>

</fieldset>