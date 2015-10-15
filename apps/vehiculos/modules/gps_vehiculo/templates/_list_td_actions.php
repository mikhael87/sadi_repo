<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($vehiculos_gps_vehiculo, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <li class="sf_admin_action_alerta">
      <?php echo link_to(__('Alertas', array(), 'messages'), 'gps_vehiculo/alertas?id='.$vehiculos_gps_vehiculo->getId(), array()) ?>
    </li>
    <?php echo $helper->linkToDelete($vehiculos_gps_vehiculo, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
  </ul>
  <br/>
    <div class="f10n">Asignado por:</div> 
    <div><?php  echo '<font class="f16b">'.$vehiculos_gps_vehiculo->getAsignador().'</font>'; ?><br/></div>
    <?php  echo '<font class="f11n">'.$vehiculos_gps_vehiculo->getCreatedAt().'</font>'; ?></div>
</td>
