<?php 

  $session_cargos = sfContext::getInstance()->getUser()->getAttribute('session_cargos');
  foreach ($session_cargos as $session_cargo) {
      $unidad_ids[] = $session_cargo['unidad_id'];
  }

  if(!in_array($seguridad_preingreso->getUnidadId(),$unidad_ids)) {
    $unidad='<font class="tooltip" title="'.$seguridad_preingreso->getUserUpdate().' cargo el preingreso para una unidad a la que no pertenece">
                <img src="/images/icon/error.png"/>&nbsp;&nbsp;<i>'.$seguridad_preingreso->getUnidad().'</i></font>';
  } else {
    $unidad=$seguridad_preingreso->getUnidad();
  }
?>

<div style="position: relative; min-height: 100px; width: 400px;">
    <?php echo $unidad; ?><br/>
    <b>Funcionario</b>: 
        <?php
            if($seguridad_preingreso->getFuncionarioId()){
                echo $seguridad_preingreso->getFuncionarioPrimerNombre().' '.$seguridad_preingreso->getFuncionarioPrimerApellido(); 
            } else {
                echo 'Sin especificar.';
            }
        ?>
    <hr/>
    <b>Motivo</b>: <?php echo $seguridad_preingreso->getMotivoClasificado(); ?><br/>
    <?php echo $seguridad_preingreso->getMotivoVisita(); ?>
</div>