<?php use_helper('I18N', 'Date') ?>
<?php include_partial('grupos/assets') ?>

<?php use_helper('jQuery') ?>

<?php $nombre_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($archivo_funcionario_unidad->getFuncionarioId()); ?>

<div id="sf_admin_container">
  <h1><?php echo __('Edición de permiso del funcionario %%funcionario_id%%', array('%%funcionario_id%%' => $nombre_fun[0]['fnombre'].' '.$nombre_fun[0]['fapellido']), 'messages') ?></h1>

  <?php include_partial('grupos/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('grupos/form_header', array('archivo_funcionario_unidad' => $archivo_funcionario_unidad, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('grupos/form', array('archivo_funcionario_unidad' => $archivo_funcionario_unidad, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('grupos/form_footer', array('archivo_funcionario_unidad' => $archivo_funcionario_unidad, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>
</div>
