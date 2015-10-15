<?php
$counter= 0;
$cadena = "<tr>";
$cadena .= "<td class='index' style='font-weight: bolder; font-size: 12px; text-align: left; vertical-align: middle'>" . $nombre . "</td>";
$cadena .= "<td style='text-align: left; vertical-align: middle'>". $descripcion ."</td>";
$cadena .= "<td style='text-align: left; vertical-align: middle'>";

$rutas_div= ''; $orden= count($parts_cadena);
foreach($parts_cadena as $values) {
    $value= explode('#', $values);

    $cadena2= ''; $cargo= TRUE;
    $datos_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($value[0]);

    $cadena2 .= "<font style='font-weight: bolder'>". $orden ."</font> - ";
    if(!count($datos_fun)> 0 || $value[2]== 'D') {
        $sin_cargo= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($value[0]);
        $cadena2 .= $sin_cargo[0]['primer_nombre'] . " " . $sin_cargo[0]['primer_apellido']  . " - ";
        $cargo= FALSE;
    }else {
        $cadena2 .= $datos_fun[0]['fnombre'] . " " . $datos_fun[0]['fapellido'] . " - ";
    }
    $cadena2 .= (($value[2]== 'A' && $cargo)? 'Visto bueno': '<font style="color: #666">Desincorporado(a) del Cargo</font>');
    $cadena2 .= "</br>";

    $orden--;
    $rutas_div .= $cadena2;
}

if($rutas_div != '') {
    $cadena .= '<div style="border: 1px solid #d0d0d0; padding: 5px">'. $rutas_div. '</div>';
}

$cadena .= "</td>";

if(isset($vb_general_config)) {
    $cadena .= "<td style='text-align: center; vertical-align: middle'><a href='#' onClick='javascript: conmutar(". $vb_general_config ."); return false;' style='cursor: pointer;'><img src='/images/icon/edit.png'/></a>&nbsp;";
    $cadena .= "<a id='boton_vb_ruta_delete_". $vb_general_config ."' href='#' onClick='javascript: fn_dar_eliminar(". $vb_general_config ."); return false;' class='elimina_ruta' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
}
    

$counter++;
echo $cadena;
?>
