<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Solicitante: </font>
        <font class="f16n"><?php if(isset($valores['vacaciones_solicitante'])) echo $valores['vacaciones_solicitante']; ?></font>
    </div>
    <hr>
    <div>
        <font class="f16b">Dias Solicitados: </font>
        <font class="f16n"><?php if(isset($valores['vacaciones_dias_solicitados'])) echo $valores['vacaciones_dias_solicitados']; ?></font>
    </div>
    <div>
        <font class="f16b">Periodo Vacacional: </font>
        <font class="f16n"><?php if(isset($valores['vacaciones_periodo_vacacional_show'])) echo $valores['vacaciones_periodo_vacacional_show']; ?></font>
    </div>
    <table width="540">
        <tr>
            <td>
                <font class="f16b">Fecha Inicio: </font><br/>
                <font class="f16n"><?php if(isset($valores['vacaciones_f_inicio_show'])) echo $valores['vacaciones_f_inicio_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Final: </font><br/>
                <font class="f16n"><?php if(isset($valores['vacaciones_f_final_show'])) echo $valores['vacaciones_f_final_show']; ?></font>
            </td>
            <td>
                <font class="f16b">Fecha Retorno: </font><br/>
                <font class="f16n"><?php if(isset($valores['vacaciones_f_retorno_show'])) echo $valores['vacaciones_f_retorno_show']; ?></font>
            </td>
            <td>
                <font class="f16b">-</font><br/>
                <font class="f16n"></font>
            </td>
        </tr>
        <tr>
            <td>
                <font class="f16b">Dias continuos: </font>
                <font class="f16n"><?php if(isset($valores['vacaciones_dias_totales'])) echo $valores['vacaciones_dias_totales']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias habiles: </font>
                <font class="f16n"><?php if(isset($valores['vacaciones_laborables'])) echo $valores['vacaciones_laborables']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias de fines de semana: </font>
                <font class="f16n"><?php if(isset($valores['vacaciones_fines_semana'])) echo $valores['vacaciones_fines_semana']; ?></font>
            </td>
            <td>
                <font class="f16b">Dias no laborables: </font>
                <font class="f16n"><?php if(isset($valores['vacaciones_no_laborables'])) echo $valores['vacaciones_no_laborables']; ?></font>
            </td>
        </tr>
    </table>
    <hr>
    <div>
        <font class="f16b">Observaciones: </font>
        <font class="f16n"><?php if(isset($valores['vacaciones_observacion'])) echo $valores['vacaciones_observacion']; ?></font>
    </div>
</div>