<div>
    <table>
        <tr style='border: none'>
            <td style="min-width: 250px; border: none">
                <font class='label_p'>Nombres: </font>
                <font class='dato_p'><?php echo $personas_persona->getPrimerNombre().' '.$personas_persona->getSegundoNombre(); ?></font>
            </td>
            <td style="min-width: 100px; border: none">
                <font class='label_p'>Edad: </font><?php
                list($Y,$m,$d) = explode("-",$personas_persona->getFNacimiento());
                $edad = (date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y);
                echo (($edad > 1)? '<font class="dato_p">'.$edad.' años</font>' : '');  ?>
            </td>
        </tr>
        <tr style='border: none'>
            <td style="min-width: 250px; border: none">
                <font class='label_p'>Apellidos: </font>
                <font class='dato_p'><?php echo $personas_persona->getPrimerApellido().' '.$personas_persona->getSegundoApellido(); ?></font>
            </td>
            <td style="min-width: 100px; border: none">
                <font class='label_p'>Sexo: </font>
                <font class='dato_p'><?php echo $personas_persona->getSexo(); ?></font>
            </td>
        </tr>
        <tr style='border: none'>
            <td style="min-width: 250px; border: none">
                <font class='label_p'>C&eacute;dula: </font>
                <font class='dato_p'><?php echo $personas_persona->getCi(); ?></font>
            </td>
            <td style="min-width: 100px; border: none"></td>
        </tr>
    </table>
</div>
