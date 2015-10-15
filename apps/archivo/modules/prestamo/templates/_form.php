<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include_partial('prestamo/assets') ?>
<?php use_helper('jQuery'); ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@archivo_prestamo_archivo', array('id' => 'form_prestamo')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('prestamo/form_fieldset', array('archivo_prestamo_archivo' => $archivo_prestamo_archivo, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('prestamo/form_actions', array('archivo_prestamo_archivo' => $archivo_prestamo_archivo, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
