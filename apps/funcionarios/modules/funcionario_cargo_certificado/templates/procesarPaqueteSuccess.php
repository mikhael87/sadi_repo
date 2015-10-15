<?php if (isset($certificado_existente)){ ?>
  <div class="error"><?php echo $certificado_existente; ?></div>
<?php } ?>
      
<table style="width: 680px;">
    <tr>
        <th>Trama</th>
        <th>Configuracion de carga</th>
    </tr>
    <tr>
        <td>
            <font class="f09n">
                <pre>
<?php echo $certificado; ?>
                </pre>
            </font>
        </td>
        <td>
            
            <?php
            function cortar_text($texto) {
                $nr = 32;
                $mitexto = explode(" ", trim($texto));
                $textonuevo = array();
                foreach ($mitexto as $k => $txt) {
                    if (strlen($txt) > $nr) {
                        $txt = wordwrap($txt, $nr, " ", 1);
                    }
                    $textonuevo[] = $txt;
                }
                return implode(" ", $textonuevo);
            }
            ?>

            <div style="width: 190px; text-align: justify;">
                <?php  echo '<font class="f11n">'.cortar_text($configuracion).'</font>'; ?>
            </div>
            
        </td>
    </tr>
    
    
    
    
    <tr>
        <th colspan="2">Informacion Basica</th>
    </tr>
    <tr>
        <td colspan="2">
            <div style="overflow-x: no-display; overflow-y: auto; width: 680px; height: 150px;">
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

            </div>
        </td>
    </tr>
    
    
    
    
    <tr>
        <th colspan="2">Detalles Tecnicos</th>
    </tr>
    <tr>
        <td colspan="2">
            <div style="overflow-x: no-display; overflow-y: auto; width: 680px; height: 150px;" class="f13n">
                <div id="div_firma_detalles_tecnicos">
                    <hr/>
                    <?php 
                    echo "<table style='width: 100%;'>";

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
        </td>
    </tr>
</table>      
            
            
            
       