<?php
list($Y,$m,$d) = explode("-",$personas_persona->getFNacimiento());
$edad = (date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y);
echo (($edad > 1)? $edad : '');
?>