<style>
    .red { color: red }
    .green { color: green }
</style>

<?php
$parametros= sfYaml::load($vehiculos_gps_vehiculo->getAlertaParametro());

$cadena= '';
foreach($parametros['alertas'] as $key => $value) {
    $cadena.= '<font class="f15n">';
    $cadena.= $value['label'];
//    $cadena.= str_replace("'", "", $value['label']);
    $cadena.= ':&nbsp;&nbsp;</font>';
    $cadena.= ($value['status'])? '<font class="green">ON</font>' : '<font class="red">OFF</font>';
    $cadena.= '<br/>';
}
echo $cadena;
?>
