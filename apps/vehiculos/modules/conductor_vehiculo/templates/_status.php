<?php

if($vehiculos_conductor_vehiculo->getStatus() == 'A') {
    echo '<font style="color: green">Asignado</font>';
}else {
    echo '<font style="color: red">Desincorporado</font>';
}
?>
