<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_informacion">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'revision_punto_informacion_observaciones')) ?>

    <div>
        <label for="revision_punto_informacion_observaciones">Observaciones de la m&aacute;xima autoridad</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][revision_punto_informacion_observaciones]" id="revision_punto_informacion_observaciones_<?php echo $semilla; ?>"><?php if(isset($formulario['revision_punto_informacion_observaciones'])) echo $formulario['revision_punto_informacion_observaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Observaciones emitidas por la M&aacute;xima autoridad al revisar el Punto de Informacion.</div>

</div>

<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('revision_punto_informacion_observaciones_<?php echo $semilla; ?>');
</script>

</fieldset>

