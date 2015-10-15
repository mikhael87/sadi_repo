<?php

$conductores= Doctrine::getTable('Vehiculos_ConductorVehiculo')->conductorPorVehiculo($vehiculo_id);

if(count($conductores) > 0) {
    foreach($conductores as $conductor) {
        echo '<font class="label_v">Asignaci&oacute;n:</font> '.$conductor->getCondicion().'<br/>';
        echo '<font class="name_c">'.$conductor->getNombre().' '.$conductor->getApellido().'</font><br/>';

        $unidad= Doctrine::getTable('Organigrama_Unidad')->datosCargoUnidad($conductor->getCargoId());
        foreach($unidad as $unidad) {
            echo '<font class="unidad_c">'.$unidad->getUnidadNombre().'</font><br/>';
        }
        echo '<hr/>';
    }
}else {
    echo '<div style="text-align: center; padding-top: 20px"><font style="color: grey; font-size: 10">Sin conductores</font></div>';
}
?>
