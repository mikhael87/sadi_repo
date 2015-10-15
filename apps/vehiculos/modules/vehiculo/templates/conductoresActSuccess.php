<font style='font-weight: bold; font-size: 14px'>Conductores activos</font><br/><hr/>
<?php
$asignado_array= Array();
foreach($asignados as $value) {
    $asignado_array[]= $value->getFuncionarioId();
}

$cadena= '';
foreach($conductores as $conductor) {
    $cadena.= '<input type="checkbox" ';
    $cadena.= (in_array($conductor->getFuncionarioId(), $asignado_array))? 'checked' : '';
    $cadena.= ' class="conductores_list_'. $vehiculo_id .'" name="conductors" value="'. $conductor->getFuncionarioId() .'"/> '.$conductor->getNombre()."<br/>";
}
echo $cadena;
?>
<div style="text-align: right; width: 200px; background-color: #B7B7B7" >
    <input name="aceptar" type="button" value="Aceptar" onClick="guardar_conductores('<?php echo $vehiculo_id ?>')"/>
</div>
