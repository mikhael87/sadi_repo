<td>
  <ul class="sf_admin_td_actions">
    <?php if($archivo_prestamo_archivo->getStatus()=='A'){ ?>   
        <?php if($archivo_prestamo_archivo->getFEntregaFisico()=='' || $archivo_prestamo_archivo->getFDevolucionFisico()!=''){ ?>
            <li class="sf_admin_action_deshabilitar">
              <?php echo link_to(__('Deshabilitar', array(), 'messages'), 'prestamo/deshabilitar?id='.$archivo_prestamo_archivo->getId(), 'confirm=\'¿Estas seguro de deshabilitar el prestamo?\'') ?>
            </li>
        <?php } ?>
        <?php if($archivo_prestamo_archivo->getFEntregaFisico()=='' && $archivo_prestamo_archivo->getFisico()==TRUE){ ?>
            <li class="sf_admin_action_registro_retiro_fisico">
              <a href="#" onclick="javascript:open_registro_retiro(<?php echo $archivo_prestamo_archivo->getId(); ?>);">Registrar entrega del fisico</a>
            </li>
        <?php } ?>
    <?php } ?>
    <?php if($archivo_prestamo_archivo->getFEntregaFisico()!='' && $archivo_prestamo_archivo->getFDevolucionFisico()=='' && $archivo_prestamo_archivo->getFisico()==TRUE){ ?>
        <li class="sf_admin_action_registro_devolucion_fisico">
          <?php echo link_to(__('Registrar devolución del fisico', array(), 'messages'), 'prestamo/registroDevolucionFisico?id='.$archivo_prestamo_archivo->getId(), 'confirm=\'¿Estas seguro de que recibiste todos los documentos prestados?\'') ?>
        </li>
    <?php } ?>
  </ul>

<br/>    
<div style="position: relative; width: 100px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Realizado por:
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo $archivo_prestamo_archivo->getUserUpdate(); ?>
    </div>
    
    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Fecha de prestamo
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo date('d-m-Y h:i:s A', strtotime($archivo_prestamo_archivo->getCreatedAt())); ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>PC de donde se presto
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo $archivo_prestamo_archivo->getIpUpdate(); ?>
        <br/>
    </div>
</div>

</td>
