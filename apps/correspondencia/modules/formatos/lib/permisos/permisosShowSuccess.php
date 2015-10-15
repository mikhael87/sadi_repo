<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Solicitante: </font>
        <font class="f16n"><?php if(isset($valores['permisos_solicitante'])) echo $valores['permisos_solicitante']; ?></font>
    </div>
    <hr>
    <div>
        <font class="f16b">Tipo de Permiso: </font>
        <font class="f16n"><?php if(isset($valores['permisos_tipo_permiso'])) echo $valores['permisos_tipo_permiso']; ?></font>
    </div>
    <div>
        <font class="f16b"><?php echo ($valores['permisos_clasificacion'] == 'D') ? 'D&iacute;as Solicitados: ' : 'Semanas Solicitadas: ' ?></font>
        <font class="f16n"><?php if(isset($valores['permisos_dias_solicitados']))  {
            if($valores['permisos_clasificacion'] == 'D') {
                $parts= explode(".", $valores['permisos_dias_solicitados']);
                echo ((isset($parts[1])) ? (((int)$valores['permisos_dias_solicitados'] != 0) ? (int)$valores['permisos_dias_solicitados'] : '').' &frac12' : $valores['permisos_dias_solicitados']);
            } else {
                echo $valores['permisos_dias_solicitados']/ 5;
            } } ?></font>
    </div>
    <table width="550">
        <tr>
            <td>
                <font class="f16b">Fecha Inicio: </font><br/>
                <font class="f16n"><?php if(isset($valores['permisos_f_inicio_show'])) echo $valores['permisos_f_inicio_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Final: </font><br/>
                <font class="f16n"><?php if(isset($valores['permisos_f_final_show'])) echo $valores['permisos_f_final_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Retorno: </font><br/>
                <font class="f16n"><?php if(isset($valores['permisos_f_retorno_show'])) echo $valores['permisos_f_retorno_show']; ?></font>
            </td>
            <td>
                <font class="f16b">-</font><br/>
                <font class="f16n"></font>
            </td>
        </tr>
        <tr>
            <td>
                <font class="f16b">D&iacute;as continuos: </font>
                <font class="f16n"><?php if(isset($valores['permisos_dias_totales'])) {
                    $parts= explode(".", $valores['permisos_dias_totales']);
                    echo ((isset($parts[1])) ? (((int)$valores['permisos_dias_totales'] != 0) ? (int)$valores['permisos_dias_totales'] : '').' &frac12' : $valores['permisos_dias_totales']);
                    } ?></font>
            </td>
            <td>
                <font class="f16b">D&iacute;as habiles: </font>
                <font class="f16n"><?php if(isset($valores['permisos_laborables'])) {
                    $parts= explode(".", $valores['permisos_laborables']);
                    echo ((isset($parts[1])) ? (((int)$valores['permisos_laborables'] != 0) ? (int)$valores['permisos_laborables'] : '').' &frac12' : $valores['permisos_laborables']);
                    } ?></font>
            </td>
            <td>
                <font class="f16b">D&iacute;as de fines de semana: </font>
                <font class="f16n"><?php if(isset($valores['permisos_fines_semana'])) echo $valores['permisos_fines_semana']; ?></font>
            </td>
            <td>
                <font class="f16b">D&iacute;as no laborables: </font>
                <font class="f16n"><?php if(isset($valores['permisos_no_laborables'])) echo $valores['permisos_no_laborables']; ?></font>
            </td>
        </tr>
    </table>
    <hr>
    <div>
        <font class="f16b">Observaciones: </font>
        <font class="f16n"><?php if(isset($valores['permisos_observacion'])) echo $valores['permisos_observacion']; ?></font>
    </div>
</div>