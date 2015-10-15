<?php // echo $helper->linkToNew(array(  'label' => 'Nuevo',  'params' =>   array(  ),  'class_suffix' => 'new',)) ?>
<li class="sf_admin_action_new">
    <a href="#" onclick="open_window_right(); nuevo_vehiculo(); return false;">Nuevo</a>    
</li>
<li class="sf_admin_action_tipo_vehiculo">
  <?php echo link_to(__('Tipos de Vehiculos', array(), 'messages'), 'vehiculo/tipoVehiculo', array()) ?>
</li>
<li class="sf_admin_action_tipo_vehiculo_usos">
  <?php echo link_to(__('Tipos de Usos', array(), 'messages'), 'vehiculo/tipoVehiculoUso', array()) ?>
</li>
<li class="sf_admin_action_tipo_servicio">
  <?php echo link_to(__('Tipos de Servicios', array(), 'messages'), 'vehiculo/tipoServicio', array()) ?>
</li>
<li class="sf_admin_action_estadisticas">
  <?php echo link_to(__('Estadisticas', array(), 'messages'), 'vehiculo/estadisticas', array()) ?>
</li>
