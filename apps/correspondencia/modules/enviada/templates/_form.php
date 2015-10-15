<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<script>
    function transferir_correlativo(tipo)
    {
        if(document.getElementById("correspondencia_correspondencia_tipo_correlativo_S").checked == true)
        {
            valor = document.getElementById("correlativo").value;
            document.getElementById("correspondencia_correspondencia_n_correspondencia_emisor").value = valor;
        }
        else
        {
            document.getElementById("correspondencia_correspondencia_n_correspondencia_emisor").value = '';
        }
    }
</script>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@correspondencia_correspondencia') ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('enviada/form_fieldset', array('correspondencia_correspondencia' => $correspondencia_correspondencia, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('enviada/form_actions', array('correspondencia_correspondencia' => $correspondencia_correspondencia, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
