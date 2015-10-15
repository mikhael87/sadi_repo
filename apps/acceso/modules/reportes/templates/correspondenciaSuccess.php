<div>   
    <fieldset><h2>ESTATUS DE CORRESPONDENCIA POR UNIDADES</h2></fieldset>
        <br/>

        <table style="width: 100%;">
            <tr class="sf_admin_row">
                <th rowspan="2">Unidades</th>
                <th colspan="5" style="text-align: center;">ENVIADAS</th>
                <th>|</th>
                <th colspan="3" style="text-align: center;">RECIBIDAS</th>
            </tr>
            <tr class="sf_admin_row">
                <th style="background-color: #E9E9E9; text-align: center;">Por Firmar</th>
                <th style="background-color: #1CC21C; text-align: center;">Enviadas</th>
                <th style="background-color: #46B2FD; text-align: center;">Entregadas</th>
                <th style="background-color: #F01D1D; text-align: center;">Pausadas</th>
                <th style="background-color: #FFD700; text-align: center;">Devueltas</th>
                <td style="background-color: #000;">-</td>
                <th style="background-color: #E9E9E9; text-align: center;">Sin Leer</th>
                <th style="background-color: #FFD700; text-align: center;">Devueltas</th>
                <th style="background-color: #1CC21C; text-align: center;">Leidas</th>
            </tr>
            <?php  foreach ( $organigrama as $unidad_id=>$unidad_nombre ) { if($unidad_id!='') { ?>
                <tr class="sf_admin_row">
                    <td>
                        <?php echo html_entity_decode($unidad_nombre); ?>
                    </td>
                    <td style="background-color: #E9E9E9; text-align: center;"><?php echo $estadistica_enviada[$unidad_id]['por_firmar']; ?></td>
                    <td style="background-color: #1CC21C; text-align: center;"><?php echo $estadistica_enviada[$unidad_id]['enviadas']; ?></td>
                    <td style="background-color: #46B2FD; text-align: center;"><?php echo $estadistica_enviada[$unidad_id]['entregadas']; ?></td>
                    <td style="background-color: #F01D1D; text-align: center;"><?php echo $estadistica_enviada[$unidad_id]['pausadas']; ?></td>
                    <td style="background-color: #FFD700; text-align: center;"><?php echo $estadistica_enviada[$unidad_id]['devueltas']; ?></td>
                    <td style="background-color: #000;">-</td>
                    <td style="background-color: #E9E9E9; text-align: center;"><?php if(isset($estadistica_recibida[$unidad_id]['E'])) echo $estadistica_recibida[$unidad_id]['E']; else echo '0'; ?></td>
                    <td style="background-color: #FFD700; text-align: center;"><?php if(isset($estadistica_recibida[$unidad_id]['D'])) echo $estadistica_recibida[$unidad_id]['D']; else echo '0'; ?></td>
                    <td style="background-color: #1CC21C; text-align: center;"><?php if(isset($estadistica_recibida[$unidad_id]['L'])) echo $estadistica_recibida[$unidad_id]['L']; else echo '0'; ?></td>
                </tr>

                
            <?php } } ?>
        </table>
    <div id="sf_admin_footer"> </div>
</div>