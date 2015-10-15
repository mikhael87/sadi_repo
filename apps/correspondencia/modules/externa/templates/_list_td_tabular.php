<?php
$reasignados=Doctrine::getTable('Correspondencia_Correspondencia')->findByPadreIdAndEmisorUnidadId($correspondencia_correspondencia->getId(),$sf_user->getAttribute('funcionario_unidad_id'));
$c_reasignado=0; foreach ($reasignados as $reasignado) $c_reasignado++;

if ($c_reasignado == 0) {
    if ($correspondencia_correspondencia->getStatus() == 'E') {
        $parametros_correspondencia['color'] = '#04B404'; $parametros_correspondencia['texto'] = 'ENVIADA';
    } elseif ($correspondencia_correspondencia->getStatus() == 'D') {
        $parametros_correspondencia['color'] = '#B40404'; $parametros_correspondencia['texto'] = 'DEVUELTA';
    } elseif ($correspondencia_correspondencia->getStatus() == 'L') {
        $parametros_correspondencia['color'] = '#2E9AFE'; $parametros_correspondencia['texto'] = 'RECIBIDO';
    }
} else {
    $parametros_correspondencia['color'] = '#04B404'; $parametros_correspondencia['texto'] = 'PROCESADA';
}  

$autorizacion=Doctrine::getTable('Correspondencia_Correspondencia')->autorizacionCaso($correspondencia_correspondencia->getGrupoCorrespondencia());
?>

<td class="sf_admin_text sf_admin_list_td_identificacion">
  <?php echo get_partial('externa/identificacion', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_documento">
  <?php echo get_partial('externa/documento', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia, 'autorizacion' => $autorizacion)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_detalles">
  <?php echo get_partial('externa/detalles', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia, 'parametros_correspondencia' => $parametros_correspondencia, 'autorizacion' => $autorizacion)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_acciones">
  <?php echo get_partial('externa/acciones', array('type' => 'list', 'correspondencia_correspondencia' => $correspondencia_correspondencia)) ?>
</td>
