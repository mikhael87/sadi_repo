<?php
if($seguridad_carnet_diseno->getCarnetTipoId()==1000){
    include_partial('carnet_diseno/list_detalles_funcionarios', array('indices' => $seguridad_carnet_diseno->getIndices()));
} elseif ($seguridad_carnet_diseno->getCarnetTipoId()==1001) {
    include_partial('carnet_diseno/list_detalles_visitantes', array('indices' => $seguridad_carnet_diseno->getIndices()));
} else {
    echo 'Sin indices';
}
?>
<hr/>
<br/>
<div class="f10n">Diseñado por:</div> 
<div><?php  echo $seguridad_carnet_diseno->getDisenador(); ?></div><br/>
<div class="f10n">Fecha de diseño</div>
<?php echo date('d-m-Y h:i A', strtotime($seguridad_carnet_diseno->getCreatedAt())); ?><br/>