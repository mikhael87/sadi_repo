<td>
  <?php if($funcionarios_funcionario_cargo_certificado->getStatus()=='A'){ ?>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_delete">
      <?php echo link_to(__('Anular certificado', array(), 'messages'), 'funcionario_cargo_certificado/anularCertificado?id='.$funcionarios_funcionario_cargo_certificado->getId(), array()) ?>
    </li>
  </ul>
    
  <br/><br/>
  <?php } ?>

  <div class="" style="position: relative; height: 90px; width: 100px;">
    <div class="" style="position: absolute; top: 0px; left: 0px;">
        <font class="f10n">Verificado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $funcionarios_funcionario_cargo_certificado->getUserCreate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($funcionarios_funcionario_cargo_certificado->getCreatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($funcionarios_funcionario_cargo_certificado->getCreatedAt())); ?></font>
    </div>  
  </div>
  <?php if($funcionarios_funcionario_cargo_certificado->getStatus() == 'I') { ?>
  <div class="" style="position: relative; height: 90px; width: 100px;">
    <div class="" style="position: absolute; top: 0px; left: 0px;">
        <font class="f10n">Anulado por:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo $funcionarios_funcionario_cargo_certificado->getUserUpdate(); ?><br/></font>
        <font class="f10n">Fecha:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($funcionarios_funcionario_cargo_certificado->getUpdatedAt())); ?><br/></font>
        <font class="f10n">Hora:</font><br/>
        <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($funcionarios_funcionario_cargo_certificado->getUpdatedAt())); ?></font>
    </div>
  </div>
  <?php } ?>
</td>
