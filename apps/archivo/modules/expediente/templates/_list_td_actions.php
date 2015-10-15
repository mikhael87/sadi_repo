<td>
  <ul class="sf_admin_td_actions">
    <?php 
    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->PermisosUnidadFuncionario($archivo_expediente->getUnidadId(), $sf_user->getAttribute('funcionario_id'));
    
    if(count($unidades_autorizadas) > 0) {
        if($unidades_autorizadas[0]['archivar'] && $unidades_autorizadas[0]['autorizada_unidad_id']== $archivo_expediente->getUnidadId()) {?>
            <?php echo $helper->linkToEdit($archivo_expediente, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
            <li class="sf_admin_action_adjuntar_documento">
              <?php echo link_to(__('Agregar Documento', array(), 'messages'), 'expediente/adjuntarDocumento?id='.$archivo_expediente->getId(), array('style' => 'text-decoration: none')) ?>
            </li>
    <?php } 
        if($unidades_autorizadas[0]['prestar'] && $unidades_autorizadas[0]['autorizada_unidad_id']== $archivo_expediente->getUnidadId()) {?>
            <li class="sf_admin_action_prestar_documento">
              <?php echo link_to(__('Prestamos del Expediente', array(), 'messages'), 'expediente/prestarDocumento?id='.$archivo_expediente->getId(), array('style' => 'text-decoration: none')) ?>
            </li>
    <?php } 
        if($unidades_autorizadas[0]['anular'] && $unidades_autorizadas[0]['autorizada_unidad_id']== $archivo_expediente->getUnidadId()) { ?>
            <li class="sf_admin_action_anular">
              <?php echo link_to(__('Anular Expediente', array(), 'messages'), 'expediente/anular?id='.$archivo_expediente->getId(), array('style' => 'text-decoration: none')) ?>
            </li>
    <?php } } ?>
  </ul>
<br/>
<div class="" style="position: relative;">
    <font class="f10n">Archivado por:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo $archivo_expediente->getUserUpdate(); ?><br/></font>
    <font class="f10n">Fecha:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($archivo_expediente->getCreatedAt())); ?><br/></font>
    <font class="f10n">Hora:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($archivo_expediente->getCreatedAt())); ?></font>
</div>
</td>
