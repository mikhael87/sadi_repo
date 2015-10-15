<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/juego/lib/validator_juego.php'); ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@operaciones_juego', array('id'=>'form_juego')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('juego/form_fieldset', array('operaciones_juego' => $operaciones_juego, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('juego/form_actions', array('operaciones_juego' => $operaciones_juego, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
