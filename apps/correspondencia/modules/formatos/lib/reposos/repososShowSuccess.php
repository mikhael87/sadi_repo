<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Solicitante: </font>
        <font class="f16n"><?php if(isset($valores['reposos_solicitante'])) echo $valores['reposos_solicitante']; ?></font>
    </div>
    <hr>
    <div>
        <font class="f16b">Tipo de Reposo: </font>
        <font class="f16n"><?php if(isset($valores['reposos_tipo_reposo'])) echo $valores['reposos_tipo_reposo']; ?></font>
    </div>
    <div>
        <font class="f16b"><?php echo ($valores['reposos_clasificacion'] == 'D') ? 'D&iacute;as Registrados: ' : 'Semanas Solicitadas: ' ?></font>
        <font class="f16n"><?php if(isset($valores['reposos_dias_solicitados'])) {
            if($valores['reposos_clasificacion'] == 'D')
                echo $valores['reposos_dias_solicitados'];
            else
                echo $valores['reposos_dias_solicitados']/ 5;
            } ?></font>
    </div>
    <table width="540">
        <tr>
            <td>
                <font class="f16b">Fecha Inicio: </font><br/>
                <font class="f16n"><?php if(isset($valores['reposos_f_inicio_show'])) echo $valores['reposos_f_inicio_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Final: </font><br/>
                <font class="f16n"><?php if(isset($valores['reposos_f_final_show'])) echo $valores['reposos_f_final_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Retorno: </font><br/>
                <font class="f16n"><?php if(isset($valores['reposos_f_retorno_show'])) echo $valores['reposos_f_retorno_show']; ?></font>
            </td>
            <td>
                <font class="f16b">-</font><br/>
                <font class="f16n"></font>
            </td>
        </tr>
        <tr>
            <td>
                <font class="f16b">Dias continuos: </font>
                <font class="f16n"><?php if(isset($valores['reposos_dias_totales'])) echo $valores['reposos_dias_totales']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias habiles: </font>
                <font class="f16n"><?php if(isset($valores['reposos_laborables'])) echo $valores['reposos_laborables']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias de fines de semana: </font>
                <font class="f16n"><?php if(isset($valores['reposos_fines_semana'])) echo $valores['reposos_fines_semana']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias no laborables: </font>
                <font class="f16n"><?php if(isset($valores['reposos_no_laborables'])) echo $valores['reposos_no_laborables']; ?></font>
            </td>
        </tr>
    </table>
    <hr>
    <div>
        <font class="f16b">Observaciones: </font>
        <font class="f16n"><?php if(isset($valores['reposos_observacion'])) echo $valores['reposos_observacion']; ?></font>
    </div>
</div>