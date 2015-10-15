<?php use_helper('I18N', 'Date') ?>
<?php include_partial('pagoDetalle/assets') ?>
<?php $empresa_datos= Doctrine::getTable('Empresas_Empresa')->empresaPorRegistro(sfContext::getInstance()->getUser()->getAttribute('pae_registro_id')); ?>

<div id="sf_admin_container">
  <h1><?php echo __('Pagos recaudados de: '.$empresa_datos[0]->getNombre(), array(), 'messages') ?></h1>

  <?php include_partial('pagoDetalle/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('pagoDetalle/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('pagoDetalle/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('pagoDetalle/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('pagoDetalle/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('pagoDetalle/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('pagoDetalle/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
