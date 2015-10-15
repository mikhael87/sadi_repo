<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/oficio/assets.php'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>
<?php include_partial('formatos/plantillas'); ?>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'oficio_contenido')) ?>

    <div>
        <label for="oficio_contenido">Contenido</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][oficio_contenido]" id="oficio_contenido_<?php echo $semilla; ?>"><?php if(isset($formulario['oficio_contenido'])) echo $formulario['oficio_contenido']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue el contenido del memorandum.</div>

</div>

<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('oficio_contenido_<?php echo $semilla; ?>');
</script>
</fieldset>