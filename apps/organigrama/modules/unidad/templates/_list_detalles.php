<font class="tooltip" title="Tipo de Unidad"><?php echo $organigrama_unidad->getOrganigrama_UnidadTipo(); ?></font>
<?php if($organigrama_unidad->getAdscripcion() == true) { ?>
    &nbsp;<img class="tooltip" title="Unidad de adscripción" src="/images/icon/tick.png"/>
<?php } ?>