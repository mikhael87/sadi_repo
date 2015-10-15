<?php 
if($signature_verify=='true') {

    $color = '#E6E6E6';
    $mensaje = 'La veracidad de este documento esta protegida por el uso de FIRMA ELECTRONICA';
} else {
    $color = '#ff4700';
    $mensaje = 'LA VERACIDAD DE ESTE DOCUMENTO FUE VIOLENTADA';
    ?>
    <script> 
        $("#div_firma_emisor_<?php echo $firma_id; ?>").css("background-color","#ff4700");

        $("#tr_<?php echo $correspondencia_id; ?>").css("background-color","#ff4700");
        $("#tr_<?php echo $correspondencia_id; ?>").removeClass("sf_admin_row");
    </script>
<?php } ?>
    
<div id="div_firma_<?php echo $firma_emisor->getId(); ?>" style="display: none; position: absolute; padding: 5px; left: -60px; top: 20px; width: 650px; background-color: <?php echo $color; ?>; height: 200px; z-index: 300; border: solid;">
    <div style="top: -15px; right: -15px; position: absolute;" onclick="javascript:cerrar_firma(<?php echo $firma_emisor->getId(); ?>);">
        <img src="/images/icon/icon_close.png">
    </div>
    <?php // echo $signature_verify; ?>
    <b><?php echo $mensaje; ?></b><br/>
    Informacion de la firma electronica utilizada:<br/>

    <hr/>
    <div id="div_firma_detalles_<?php echo $firma_emisor->getId(); ?>" style="overflow-x: no-display; overflow-y: auto; width: 650px; height: 150px;">
        <table>
            <tr>
                <td>
                    <?php
                    if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $funcionario_emisor->getCi() .".jpg")) {
                        echo '<img src="/images/firma_digital/'. $funcionario_emisor->getCi() .'.jpg" width="150"/>';
                    }
                    ?>
                </td>
                <td class="f12n"><pre><?php echo $sign; ?></pre></td>
            </tr>
        </table>
        <hr/>
        <b class="azul"><u>Tiempo de validez</u></b><br/>
        <b>desde</b> <?php echo date('d-m-Y g:i a', $ssl_open['validFrom_time_t']); ?> <b>hasta</b> <?php echo date('d-m-Y g:i a', $ssl_open['validTo_time_t']); ?> <br/>
        <hr/>
        <b class="azul"><u>Propietario de la Firma</u></b><br/>
        <b>- Nombre:</b> <?php echo $ssl_open['subject']['CN']; ?><br/>
        <b>- Correo electronico:</b> <?php echo $ssl_open['subject']['emailAddress']; ?><br/>
        <b>- Ubicacion:</b> <?php echo $ssl_open['subject']['C'].' - '.$ssl_open['subject']['ST'].' - '.$ssl_open['subject']['L']; ?><br/>
        <hr/>
        <b class="azul"><u>Emisor de la Firma</u></b><br/>
        <b>- Nombre:</b> <?php echo $ssl_open['issuer']['CN']; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $ssl_open['issuer']['OU']; ?><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $ssl_open['issuer']['O']; ?><br/>
        <b>- Correo electronico:</b> <?php echo $ssl_open['issuer']['emailAddress']; ?><br/>
        <b>- Ubicacion:</b> <?php echo $ssl_open['issuer']['C'].' - '.$ssl_open['issuer']['ST'].' - '.$ssl_open['issuer']['L']; ?><br/>
        <hr/>
        <a href="#" onclick="ver_firma_tecnica(<?php echo $firma_emisor->getId(); ?>); return false;">Ver detalles tecnicos</a>&nbsp;&nbsp;
        <?php if($signature_verify=='true') { ?>
        <a href="#">Respaldar mediante correo electronico</a>
        <?php } ?>
        
        <div id="div_firma_detalles_tecnicos_<?php echo $firma_emisor->getId(); ?>" style="display: none;">
            <hr/>
            <?php 
            echo "<table><tr><th>Propiedad</th><th>Valores</th></tr>";

            echo "<tr><td><b>NAME</b></td>";
            echo "<td>".str_replace("/", "<br/>", $ssl_open['name'])."</td></tr>";

            echo "<tr><td><b>SUBJECT</b></td>";
            echo "<td>";
                foreach ($ssl_open['subject'] as $key => $value) {
                    echo "<b>".$key.": </b>";
                    if(is_array($value)){
                        echo "<pre>"; print_r($value); echo "</pre>"; 
                    } else {
                        echo str_replace("/", "-", $value);
                    }
                    echo '<br/>';
                }
            echo "</td></tr>";

            echo "<tr><td><b>HASH</b></td>";
            echo "<td>".$ssl_open['hash']."</tr>";

            echo "<tr><td><b>ISSUER</b></td>";
            echo "<td>";
                foreach ($ssl_open['issuer'] as $key => $value) {
                    echo "<b>".$key.": </b>";
                    if(is_array($value)){
                        echo "<pre>"; print_r($value); echo "</pre>"; 
                    } else {
                        echo str_replace("/", "-", $value);
                    }
                    echo '<br/>';
                }
            echo "</td></tr>";

            echo "<tr><td><b>VERSION</b></td>";
            echo "<td>".$ssl_open['hash']."</td></tr>";

            echo "<tr><td><b>SERIAL NUMBER</b></td>";
            echo "<td>".$ssl_open['serialNumber']."</td></tr>";

            echo "<tr><td><b>VALID FROM</b></td>";
            echo "<td>".$ssl_open['validFrom']."</td></tr>";

            echo "<tr><td><b>VALID TO</b></td>";
            echo "<td>".$ssl_open['validTo']."</td></tr>";

            echo "<tr><td><b>VALID FROM<br/>TIME T</b></td>";
            echo "<td>".$ssl_open['validFrom_time_t']."</td></tr>";

            echo "<tr><td><b>VALID TO<br/>TIME T</b></td>";
            echo "<td>".$ssl_open['validTo_time_t']."</td></tr>";

            echo "<tr><td><b>PURPOSES</b></td>";
            echo "<td>";
                foreach ($ssl_open['purposes'] as $key => $value) {
                    echo "<b>".$key.": </b>";
                    if(is_object($value)){
                        foreach ($value as $key2 => $value2) {
                            echo "<br/>---<i>".$key2.": </i>".$value2;
                        }
                    } else {
                        echo $value;
                    }
                    echo '<br/>';
                }
            echo "</td></tr>";

            echo "<tr><td><b>EXTENSIONS</b></td>";
            echo "<td>";
                foreach ($ssl_open['extensions'] as $key => $value) {
                    echo "<b>".$key.": </b>";
                    if(is_object($value)){
                        echo '<br/>';
                        foreach ($value as $key2 => $value2) {
                            echo "<br/>---<i>".$key2.": </i>".$value2."<br/>";
                        }
                    } else {
                        echo $value;
                    }
                    echo '<br/>';
                }
            echo "</td></tr>";

            echo "</table>";
            ?>
        </div>
    </div>
</div> 

