<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($seguridad_preingreso, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <?php echo $helper->linkToDelete($seguridad_preingreso, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
    <?php if ($sf_user->hasCredential(array('Root','Seguridad y Recepción'),false)) { ?>
    <li class="sf_admin_action_ingresar">
        <a href="#" onclick="open_window_right(); preparar_ingreso(<?php echo $seguridad_preingreso->getId(); ?>); return false;">Registrar ingreso</a>
    </li>
    <?php } ?>
  </ul>
    
    <br/>

    <div class="" style="position: relative;">
        <font class="f10n">Preingresado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $seguridad_preingreso->getUserUpdate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($seguridad_preingreso->getCreatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($seguridad_preingreso->getCreatedAt())); ?></font>
    </div>
</td>
