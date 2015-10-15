<?php
$parametros = sfYaml::load($parametros);
$imagen_fondo= sfYAML::load($imagen_fondo);
?>

<div style="position: relative; padding: 5px; width: 214px; height: 335px;">
    <div style="position: absolute; top: 0px; left: 0px; width: 204px; height: 325px;">
        <img src="/images/carnet/<?php echo $imagen_fondo['frontal'][0]; ?>" style="border: 1px solid; width: 204px; height: 325px"/>
        
        <?php 
            $mensaje = '[!]Foto[/!]';
            $mensaje .= '<b><i><u>Ancho:</u></i></b> '.$parametros['foto']['y'].'<br/>';
            $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['foto']['x'].'<br/>';
            $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['foto']['y'].'<br/>';
        ?>
        <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; top: <?php echo $parametros['foto']['y']; ?>; left: <?php echo $parametros['foto']['x']; ?>;">
            <div style="top: 0px; position: absolute; border: 1px solid;"><img id="img_foto" src="/images/other/siglas_photo_small_M9.png" style="width: <?php echo $parametros['foto']['w']; ?>; height: auto;"></div>
        </div>
        
        <?php if(isset($parametros['cedula'])) { ?>
            <?php if(isset($parametros['cedula']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Cedula[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['cedula']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['cedula']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['cedula']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['cedula']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['cedula']['y'].'<br/>';
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['cedula']['fuente']; ?>; top: <?php echo $parametros['cedula']['y']; ?>; color: <?php echo $parametros['cedula']['color']; ?>; font-weight: <?php echo ($parametros['cedula']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['cedula']['alineacion']; ?>;">Cedula</div>
            <?php } ?>
        <?php } ?>
        
        <?php if(isset($parametros['nombres_bloque_uno'])) { ?>
            <?php if(isset($parametros['nombres_bloque_uno']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Bloque 1 Nombres[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['nombres_bloque_uno']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['nombres_bloque_uno']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['nombres_bloque_uno']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['nombres_bloque_uno']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['nombres_bloque_uno']['y'].'<br/>';
                    $mensaje .= '<b><i><u>Formato:</u></i></b> '.$parametros['nombres_bloque_uno']['formato'].'<br/>';
                    
                    $cadena_nombres = str_replace('primer_nombre', '1º Nombre', $parametros['nombres_bloque_uno']['formato']);
                    $cadena_nombres = str_replace('segundo_nombre', '2º Nombre', $cadena_nombres);
                    $cadena_nombres = str_replace('primer_apellido', '1º Apellido', $cadena_nombres);
                    $cadena_nombres = str_replace('segundo_apellido', '2º Apellido', $cadena_nombres);
                    if(isset($parametros['nombres_bloque_uno']['mayuscula'])){
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> Si<br/>';
                        $cadena_nombres = strtr(strtoupper($cadena_nombres),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                    } else {
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> No<br/>';
                    }
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['nombres_bloque_uno']['fuente']; ?>; top: <?php echo $parametros['nombres_bloque_uno']['y']; ?>; color: <?php echo $parametros['nombres_bloque_uno']['color']; ?>; font-weight: <?php echo ($parametros['nombres_bloque_uno']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['nombres_bloque_uno']['alineacion']; ?>;"><?php echo $cadena_nombres; ?></div>
            <?php } ?>
        <?php } ?>
                
        <?php if(isset($parametros['nombres_bloque_dos'])) { ?>
            <?php if(isset($parametros['nombres_bloque_dos']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Bloque 2 Nombres[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['nombres_bloque_dos']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['nombres_bloque_dos']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['nombres_bloque_dos']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['nombres_bloque_dos']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['nombres_bloque_dos']['y'].'<br/>';
                    $mensaje .= '<b><i><u>Formato:</u></i></b> '.$parametros['nombres_bloque_dos']['formato'].'<br/>';
                    
                    $cadena_nombres = str_replace('primer_nombre', '1º Nombre', $parametros['nombres_bloque_dos']['formato']);
                    $cadena_nombres = str_replace('segundo_nombre', '2º Nombre', $cadena_nombres);
                    $cadena_nombres = str_replace('primer_apellido', '1º Apellido', $cadena_nombres);
                    $cadena_nombres = str_replace('segundo_apellido', '2º Apellido', $cadena_nombres);
                    
                    if(isset($parametros['nombres_bloque_dos']['mayuscula'])){
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> Si<br/>';
                        $cadena_nombres = strtr(strtoupper($cadena_nombres),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                    } else {
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> No<br/>';
                    }
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['nombres_bloque_dos']['fuente']; ?>; top: <?php echo $parametros['nombres_bloque_dos']['y']; ?>; color: <?php echo $parametros['nombres_bloque_dos']['color']; ?>; font-weight: <?php echo ($parametros['nombres_bloque_dos']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['nombres_bloque_dos']['alineacion']; ?>;"><?php echo $cadena_nombres; ?></div>
            <?php } ?>
        <?php } ?>
                
        <?php if(isset($parametros['unidad'])) { ?>
            <?php if(isset($parametros['unidad']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Unidad[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['unidad']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['unidad']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['unidad']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['unidad']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['unidad']['y'].'<br/>';
                    $mensaje .= '<b><i><u>Formato:</u></i></b> '.$parametros['unidad']['formato'].'<br/>';
                    $mensaje .= '<b><i><u>Tipo:</u></i></b> '.$parametros['unidad']['tipo'].'<br/>';
                    
                    $cadena_unidad['completo'] = 'Nombre completo de la unidad '.$parametros['unidad']['tipo'];
                    $cadena_unidad['reducido'] = 'Nombre reducido de la unidad '.$parametros['unidad']['tipo'];
                    $cadena_unidad['siglas'] = 'Siglas de la unidad '.$parametros['unidad']['tipo'];
                    
                    if(isset($parametros['unidad']['mayuscula'])){
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> Si<br/>';
                        $cadena_unidad['completo'] = strtr(strtoupper($cadena_unidad['completo']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                        $cadena_unidad['reducido'] = strtr(strtoupper($cadena_unidad['reducido']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                        $cadena_unidad['siglas'] = strtr(strtoupper($cadena_unidad['siglas']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                    } else {
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> No<br/>';
                    }
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['unidad']['fuente']; ?>; top: <?php echo $parametros['unidad']['y']; ?>; color: <?php echo $parametros['unidad']['color']; ?>; font-weight: <?php echo ($parametros['unidad']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['unidad']['alineacion']; ?>;"><?php echo $cadena_unidad[$parametros['unidad']['formato']]; ?></div>
            <?php } ?>
        <?php } ?>
                
        <?php if(isset($parametros['cargo_condicion'])) { ?>
            <?php if(isset($parametros['cargo_condicion']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Condicion del Cargo[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['cargo_condicion']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['cargo_condicion']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['cargo_condicion']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['cargo_condicion']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['cargo_condicion']['y'].'<br/>';
                    
                    $formato_condicion_cargo = 'Condición de cargo';
                    if(isset($parametros['cargo_condicion']['mayuscula'])){
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> Si<br/>';
                        $formato_condicion_cargo = strtr(strtoupper($formato_condicion_cargo),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                    } else {
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> No<br/>';
                    }                    
                    
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['cargo_condicion']['fuente']; ?>; top: <?php echo $parametros['cargo_condicion']['y']; ?>; color: <?php echo $parametros['cargo_condicion']['color']; ?>; font-weight: <?php echo ($parametros['cargo_condicion']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['cargo_condicion']['alineacion']; ?>;"><?php echo $formato_condicion_cargo; ?></div>
            <?php } ?>
        <?php } ?>
                
        <?php if(isset($parametros['cargo_tipo'])) { ?>
            <?php if(isset($parametros['cargo_tipo']['visible'])) { ?>
                <?php 
                    $mensaje = '[!]Campo Tipo de Cargo[/!]';
                    $mensaje .= '<b><i><u>Fuente:</u></i></b> '.$parametros['cargo_tipo']['fuente'].'<br/>';
                    $mensaje .= '<b><i><u>Color:</u></i></b> '.$parametros['cargo_tipo']['color'].'<br/>';
                    $mensaje .= '<b><i><u>Alineacion:</u></i></b> '.$parametros['cargo_tipo']['alineacion'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Xº:</u></i></b> '.$parametros['cargo_tipo']['x'].'<br/>';
                    $mensaje .= '<b><i><u>Pos. Yº:</u></i></b> '.$parametros['cargo_tipo']['y'].'<br/>';
                    
                    $formato_tipo_cargo = 'Tipo de cargo';
                    if(isset($parametros['cargo_tipo']['mayuscula'])){
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> Si<br/>';
                        $formato_tipo_cargo = strtr(strtoupper($formato_tipo_cargo),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
                    } else {
                        $mensaje .= '<b><i><u>Mayuscula:</u></i></b> No<br/>';
                    }  
                ?>
                <div class="tooltip" title="<?php echo $mensaje; ?>" style="position: absolute; padding: 5px; width: 194px; font-size: <?php echo $parametros['cargo_tipo']['fuente']; ?>; top: <?php echo $parametros['cargo_tipo']['y']; ?>; color: <?php echo $parametros['cargo_tipo']['color']; ?>; font-weight: <?php echo ($parametros['cargo_tipo']['negrita']=='A') ? 'bold' : 'normal'; ?>; text-align: <?php echo $parametros['cargo_tipo']['alineacion']; ?>;"><?php echo $formato_tipo_cargo; ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>