<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/informe/assets.php'); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>
<?php include_partial('formatos/plantillas'); ?>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_informe_asunto">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'informe_asunto')) ?>

    <div>
        <label for="informe_asunto">Asunto</label>
        <div class="content">
            <input name="correspondencia[formato][informe_asunto]" id="informe_asunto" size="70" value="<?php if(isset($formulario['informe_asunto'])) echo $formulario['informe_asunto']; ?>"/>
        </div>
    </div>

    <div class="help">Agregue el asunto.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_informe_contenido">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'informe_contenido')) ?>

    <div>
        <label for="informe_contenido">Contenido</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][informe_contenido]" id="informe_contenido_<?php echo $semilla; ?>"><?php if(isset($formulario['informe_contenido'])) echo $formulario['informe_contenido']; ?></textarea>
        </div>
    </div>

    <div class="help">Agregue el contenido del informe.</div>

</div>

<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('informe_contenido_<?php echo $semilla; ?>');
</script>
</fieldset>
