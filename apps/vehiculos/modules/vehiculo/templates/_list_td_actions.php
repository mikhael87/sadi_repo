<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($vehiculos_vehiculo, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <li class="sf_admin_action_conductores">
      <?php echo link_to(__('Conductores', array(), 'messages'), 'vehiculo/conductores?id='.$vehiculos_vehiculo->getId(), array()) ?>
    </li>
    <li class="sf_admin_action_asignar_conductor">
      <?php echo image_tag('icon/asignar.png', array('title'=>'Asignar conductores','style'=>'vertical-align: middle; cursor: pointer', 'onClick'=>'conmutar("'. $vehiculos_vehiculo->getId() .'")')) ?>
    </li>
    <li class="sf_admin_action_servicios">
      <?php echo link_to(__('Servicios', array(), 'messages'), 'vehiculo/servicios?id='.$vehiculos_vehiculo->getId(), array()) ?>
    </li>
    <li class="sf_admin_action_gps">
      <?php echo link_to(__('Dispositivo instalado', array(), 'messages'), 'vehiculo/gps?id='.$vehiculos_vehiculo->getId(), array()) ?>
    </li>
    <?php
      $gps_vehiculo= Doctrine::getTable('Vehiculos_GpsVehiculo')->findOneByVehiculoId($vehiculos_vehiculo->getId());
      if($gps_vehiculo) {
          ?>
          <li class="sf_admin_action_track">
              <?php echo link_to(__('Tracker', array(), 'messages'), 'vehiculo/track?id='.$gps_vehiculo->getId(), array()) ?>
          </li>    
          <?php
      } ?>
  </ul>
  <br/>
    <div class="f10n">Registrado por:</div> 
    <div><?php  echo '<font class="f16b">'.$vehiculos_vehiculo->getRegistrador().'</font>'; ?><br/></div>
    <?php  echo '<font class="f11n">'.$vehiculos_vehiculo->getCreatedAt().'</font>'; ?></div>
</td>
