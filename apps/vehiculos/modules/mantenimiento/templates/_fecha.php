<?php
if($vehiculos_mantenimiento->getFecha() != '') {
    $fecha= strtotime($vehiculos_mantenimiento->getFecha());
    $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
    $date = date('d', $fecha) . " de " . $months[(int)date('m', $fecha)] . " de " . date('Y', $fecha);
    
    echo $date;
}
?>
