<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/memorandum/assets.php'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>
<?php include_partial('formatos/plantillas'); ?>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'memorandum_asunto')) ?>

    <div>
        <label for="memorandum_asunto">Asunto</label>
        <div class="content">
            <input name="correspondencia[formato][memorandum_asunto]" id="memorandum_asunto" size="70" value="<?php if(isset($formulario['memorandum_asunto'])) echo $formulario['memorandum_asunto']; ?>"/>
        </div>
    </div>

    <div class="help">Agregue el asunto.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_memorandum_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'memorandum_contenido')) ?>

    <div>
        <label for="memorandum_contenido">Contenido</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][memorandum_contenido]" id="memorandum_contenido_<?php echo $semilla; ?>"><?php if(isset($formulario['memorandum_contenido'])) echo $formulario['memorandum_contenido']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue el contenido del memorandum.</div>

</div>

<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('memorandum_contenido_<?php echo $semilla; ?>');
</script>
</fieldset>
