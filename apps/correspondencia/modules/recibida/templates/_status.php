<?php
$reasignados=Doctrine::getTable('Correspondencia_Correspondencia')->findByPadreIdAndEmisorUnidadId($correspondencia_correspondencia->getId(),$sf_user->getAttribute('funcionario_unidad_id'));

$c_reasignado=0;
foreach ($reasignados as $reasignado)
    $c_reasignado++;

if($c_reasignado==0){ ?>

<?php if($correspondencia_correspondencia->getStatus() == 'E') { ?>
    <div class="status_etiqueta" style="background-color: silver; color: white;"><b>&nbsp;&nbsp;Sin Leer&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'D') { ?>
    <div class="status_etiqueta" style="background-color: #B40404; color: white;"><b>&nbsp;&nbsp;Devuelta&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'L') { ?>
    <div class="status_etiqueta" style="background-color: #2E9AFE; color: white;"><b>&nbsp;&nbsp;Leído&nbsp;&nbsp;</b></div>
<?php } ?>


<?php } else { ?>
<div class="status_etiqueta" style="background-color: #04B404; color: white;"><b>&nbsp;&nbsp;Procesada&nbsp;&nbsp;</b></div>
<?php } ?>
