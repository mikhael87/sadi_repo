<?php
$status= $vehiculos_mantenimiento_tipo->getStatus();

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
