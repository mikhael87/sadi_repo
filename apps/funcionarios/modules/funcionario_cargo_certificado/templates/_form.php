<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <form id="form_certificado" action="/funcionarios_dev.php/funcionario_cargo_certificado" method="post">
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('funcionario_cargo_certificado/form_fieldset', array('funcionarios_funcionario_cargo_certificado' => $funcionarios_funcionario_cargo_certificado, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('funcionario_cargo_certificado/form_actions', array('funcionarios_funcionario_cargo_certificado' => $funcionarios_funcionario_cargo_certificado, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
