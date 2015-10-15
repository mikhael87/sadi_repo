<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($vehiculos_mantenimiento, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <?php echo $helper->linkToDelete($vehiculos_mantenimiento, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
  </ul>
  <br/>
    <div class="f10n">Registrado por:</div> 
    <div><?php  echo '<font class="f16b">'.$vehiculos_mantenimiento->getRegistrador().'</font>'; ?><br/></div>
    <?php  echo '<font class="f11n">'.$vehiculos_mantenimiento->getCreatedAt().'</font>'; ?></div>
</td>
