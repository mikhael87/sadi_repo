<td>
  <?php if($seguridad_alerta_visitante->getStatus()=='A'){ ?>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($seguridad_alerta_visitante, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <li class="sf_admin_action_anular_alerta">
      <?php echo link_to(__('Anular alerta', array(), 'messages'), 'alerta_visitante/anularAlerta?id='.$seguridad_alerta_visitante->getId(), array()) ?>
    </li>
  </ul>
  <br/><br/>
  <?php } ?>

  <div class="" style="position: relative; height: 90px; width: 200px;">
    <div class="" style="position: absolute; top: 0px; left: 0px;">
        <font class="f10n">Alertado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $seguridad_alerta_visitante->getUserCreate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($seguridad_alerta_visitante->getCreatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($seguridad_alerta_visitante->getCreatedAt())); ?></font>
    </div>  
    <?php if($seguridad_alerta_visitante->getStatus() != 'A') { ?>
    <div class="" style="position: absolute; top: 0px; left: 100px;">
        <font class="f10n">Anulado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $seguridad_alerta_visitante->getUserUpdate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($seguridad_alerta_visitante->getUpdatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($seguridad_alerta_visitante->getUpdatedAt())); ?></font>
    </div>
    <?php } ?>
  </div>
</td>

