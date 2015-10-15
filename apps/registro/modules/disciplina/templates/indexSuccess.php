<?php use_helper('I18N', 'Date') ?>
<?php include_partial('disciplina/assets') ?>
<?php $empresa_datos= Doctrine::getTable('Empresas_Empresa')->empresaPorRegistro($registro_id); ?>

<div id="sf_admin_container">
  <h1><?php echo __('Listado de Equipos para la empresa: '.$empresa_datos[0]->getNombre(), array(), 'messages') ?></h1>

  <?php include_partial('disciplina/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('disciplina/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('disciplina/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('operaciones_registro_disciplina_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('disciplina/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('disciplina/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('disciplina/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('disciplina/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
