<?php

if($vehiculos_mantenimiento->getFecha() != '') {
    $fecha= date('Y-m-d', strtotime($vehiculos_mantenimiento->getFecha()));

    if(strtotime($fecha) > strtotime(date('Y-m-d')) && $vehiculos_mantenimiento->getStatus() == 'A') {
        echo '<font style="color: green">Programado</font>';
    }elseif(strtotime($fecha) < strtotime(date('Y-m-d')) && $vehiculos_mantenimiento->getStatus() == 'A') {
        echo '<font style="color: red">Pendiente</font>';
    }elseif(strtotime($fecha) < strtotime(date('Y-m-d')) && $vehiculos_mantenimiento->getStatus() == 'C') {
        echo '<font style="color: grey">Procesado</font>';
    }
}else {
    $vehiculo= Doctrine::getTable('Vehiculos_Vehiculo')->find($vehiculos_mantenimiento->getVehiculoId());
    $kilometraje= $vehiculos_mantenimiento->getKilometraje();

    if($kilometraje > $vehiculo->getKilometrajeActual() && $vehiculos_mantenimiento->getStatus() == 'A') {
        echo '<font style="color: green">Programado</font>';
    }elseif($kilometraje < $vehiculo->getKilometrajeActual() && $vehiculos_mantenimiento->getStatus() == 'A') {
        echo '<font style="color: red">Pendiente</font>';
    }elseif($kilometraje < $vehiculo->getKilometrajeActual() && $vehiculos_mantenimiento->getStatus() == 'C') {
        echo '<font style="color: grey">Procesado</font>';
    }
}

?>
