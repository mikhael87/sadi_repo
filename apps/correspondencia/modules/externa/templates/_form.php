<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('jQuery'); ?>
<?php include_partial('externa/assets') ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@correspondencia_correspondencia_externa',array('id'=>'form_correspondencia_externa','enctype'=>'multipart/form-data')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('externa/form_fieldset', array('correspondencia_correspondencia' => $correspondencia_correspondencia, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('externa/form_actions', array('correspondencia_correspondencia' => $correspondencia_correspondencia, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
