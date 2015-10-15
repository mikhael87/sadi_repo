<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/encargado/lib/validator_encargado.php'); ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@personas_persona', array('id'=>'form_encargado')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('encargado/form_fieldset', array('personas_persona' => $personas_persona, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('encargado/form_actions', array('personas_persona' => $personas_persona, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
