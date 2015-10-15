<?php
if($seguridad_carnet_diseno->getCarnetTipoId()==1000){
    include_partial('carnet_diseno/list_preview_funcionarios', array('parametros' => $seguridad_carnet_diseno->getParametros(), 'imagen_fondo' => $seguridad_carnet_diseno->getImagenFondo()));
} elseif ($seguridad_carnet_diseno->getCarnetTipoId()==1001) {
    include_partial('carnet_diseno/list_preview_visitantes', array('parametros' => $seguridad_carnet_diseno->getParametros(), 'imagen_fondo' => $seguridad_carnet_diseno->getImagenFondo()));
} else {
    echo 'No existe el modelo.';
}
?>