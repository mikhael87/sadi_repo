<?php use_helper('I18N', 'Date') ?>
<?php include_partial('mantenimiento/assets') ?>

<div id="sf_admin_container">
  <h1><?php $vehiculo_id= $sf_user->getAttribute('vehiculo_id');
  $vehiculo_datos= Doctrine::getTable('Vehiculos_Vehiculo')->find($vehiculo_id);
  echo __('Servicios para el vehículo "'. $vehiculo_datos->getMarca() .' '. $vehiculo_datos->getModelo() .' '. $vehiculo_datos->getPlaca() .'"', array(), 'messages') ?></h1>

  <?php include_partial('mantenimiento/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('mantenimiento/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('mantenimiento/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('mantenimiento/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('mantenimiento/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('mantenimiento/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('mantenimiento/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
