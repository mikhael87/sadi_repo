<div style="text-align: left; width: auto; background-color: #ccf; padding: 5px; font-size: 16px; font-weight: bold">
    <?php
    if(isset($visto_bueno_general_config_id))
        echo 'Edici&oacute;n de ruta de verificaci&oacute;n';
    else
        echo 'Nueva ruta de verificaci&oacute;n';
    ?>
</div>
<input type="hidden" value="" name="edit_vb_route" id="edit_vb_route"/>
<table>
    <tr>
        <td>Nombre de ruta:</td>
        <td><input type="text" value="<?php echo (isset($nombre)? $nombre : '') ?>" name="vb_ruta_nombre" id="vb_ruta_nombre" style="width: 200px"/></td>
    </tr>
    <tr>
        <td>Descripci&oacute;n:</td>
        <td><textarea name="vb_ruta_descripcion" id="vb_ruta_descripcion" rows="4" style="width: 200px"><?php echo (isset($descripcion)? $descripcion : '') ?></textarea></td>
    </tr>
</table>
<table id="table_vistobuenos">
    <thead><tr><th>Orden</th><th>Funcionario</th><th>Funci&oacute;n</th><th>Acci&oacute;n</th></tr></thead>
        <tbody>
            <?php
            if(isset($visto_bueno_general_config_id)) {
                $vb_data= Doctrine::getTable('Correspondencia_VistobuenoGeneral')->vistobuenoGeneral($visto_bueno_general_config_id);

                foreach($vb_data as $value) {
                    $datos_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($value->getFuncionarioId());

                    $cadena = "<tr>"; $cargo= TRUE;
                    $cadena .= "<td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'>" . $value->getOrden() . "</td>";
                    if(!count($datos_fun)> 0 || $value->getStatus()== 'D') {
                        $sin_cargo= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($value->getFuncionarioId());
                        $cadena .= "<td><font style='color: #666'>" . $sin_cargo[0]['primer_nombre'] . " " . $sin_cargo[0]['primer_apellido']  . "</td>";
                        $cargo= FALSE;
                    }else {
                        $cadena .= "<td>" . $datos_fun[0]['unombre'] . "<br/>" . $datos_fun[0]['ctnombre']  . "/ " . $datos_fun[0]['fnombre'] . " " . $datos_fun[0]['fapellido'] . "</td>";
                    }
                    $cadena .= "<td style='text-align: center; vertical-align: middle'>" . (($value->getStatus()== 'A' && $cargo)? 'Visto bueno': '<font style="color: red">Desincorporado(a) del Cargo</font>') . "</td>";
                    $cadena .= "<td style='text-align: center; vertical-align: middle'><a class='elimina_vb' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                    $cadena .= "<input type='hidden' name='funcionarios_vb[]' value='" . $value->getFuncionarioId() . "#" . $value->getFuncionarioCargoId() . "#" . $value->getStatus() . "'/>";
                    $cadena .= "</tr>";

                    echo $cadena;
                }
            } ?>
        </tbody>
</table>
