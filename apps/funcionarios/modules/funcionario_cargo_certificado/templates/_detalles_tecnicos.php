<?php $ssl_open = sfYaml::load($funcionarios_funcionario_cargo_certificado->getDetallesTecnicos()); ?>

<div style="overflow-x: no-display; overflow-y: auto; width: 300px; height: 240px;" class="f11n">
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
                    print_r($value);
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
