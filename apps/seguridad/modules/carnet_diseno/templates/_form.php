<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <form id="form_diseno_carnet" enctype="multipart/form-data" action="/seguridad_dev.php/carnet_diseno" method="post">
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('carnet_diseno/form_fieldset', array('seguridad_carnet_diseno' => $seguridad_carnet_diseno, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('carnet_diseno/form_actions', array('seguridad_carnet_diseno' => $seguridad_carnet_diseno, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
