<?php
$status= $vehiculos_gps_vehiculo->getStatus();

switch ($status) {
    case 'A':
        echo '<font style="color: green">Activo</font>';
        break;
    case 'I':
        echo '<font style="color: red">Inactivo</font>';
        break;
    default:
        break;
}
?>
