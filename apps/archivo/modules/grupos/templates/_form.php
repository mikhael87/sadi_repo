<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@archivo_funcionario_unidad') ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('grupos/form_fieldset', array('archivo_funcionario_unidad' => $archivo_funcionario_unidad, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php
    if (!$form->isNew()) {
        echo '<input type="hidden" name="archivo_funcionario_unidad[dependencia_unidad_id]" value="' . $archivo_funcionario_unidad->getDependenciaUnidadId() . '">';
        echo '<input type="hidden" name="archivo_funcionario_unidad[funcionario_id]" value="' . $archivo_funcionario_unidad->getFuncionarioId() . '">';
        echo '<input type="hidden" name="archivo_funcionario_unidad[autorizada_unidad_id]" value="' . $archivo_funcionario_unidad->getAutorizadaUnidadId() . '">';
    } ?>
    
    <?php include_partial('grupos/form_actions', array('archivo_funcionario_unidad' => $archivo_funcionario_unidad, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
