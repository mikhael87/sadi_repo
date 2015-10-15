<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_informacion">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_informacion_asunto')) ?>

    <div>
        <label for="punto_informacion_asunto">Asunto</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_informacion_asunto]" id="punto_informacion_asunto_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_informacion_asunto'])) echo $formulario['punto_informacion_asunto']; ?></textarea>
        </div>
    </div>

    <div class="help">Asunto del punto de información.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_informacion">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_informacion_sintesis')) ?>

    <div>
        <label for="punto_informacion_sintesis">Sintesis</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_informacion_sintesis]" id="punto_informacion_sintesis_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_informacion_sintesis'])) echo $formulario['punto_informacion_sintesis']; ?></textarea>
        </div>
    </div>

    <div class="help">Sintesis del punto de información.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_informacion">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'punto_informacion_recomendaciones')) ?>

    <div>
        <label for="punto_informacion_recomendaciones">Recomendaciones</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][punto_informacion_recomendaciones]" id="punto_informacion_recomendaciones_<?php echo $semilla; ?>"><?php if(isset($formulario['punto_informacion_recomendaciones'])) echo $formulario['punto_informacion_recomendaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue las recomendaciones.</div>

</div>


<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('punto_informacion_asunto_<?php echo $semilla; ?>');
        CKEDITOR.replace('punto_informacion_sintesis_<?php echo $semilla; ?>');
        CKEDITOR.replace('punto_informacion_recomendaciones_<?php echo $semilla; ?>');
</script>

</fieldset>

