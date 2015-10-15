<?php

if(count($sf_user->getAttribute('vehiculo.filters', array(), 'admin_module')) != 0) {
    $ext= '_active';
    $status= 'Activo';
} else {
    $ext= '';
    $status= 'Inactivo';
}
?>
<li>
    &nbsp;<?php echo image_tag('icon/find24'.$ext, array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Enviadas: <b>'. $status .'</b>', 'class' => 'tooltip')); ?>&nbsp;
</li>