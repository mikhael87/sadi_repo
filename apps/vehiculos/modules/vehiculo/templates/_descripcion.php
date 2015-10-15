<style>
    .label_v { font-size: 10px; font-weight: bold }
    .content_v { font-size: 11px }
</style>
<div style="position: relative; min-height: 100px; width: 300px;">
    <div style="min-height: 100px; width: 150px; float: left">
        <?php
        $cadena1= '<font class="label_v">Tipo:</font>&nbsp;<font class="content_v">'. $vehiculos_vehiculo->getTipoNombre() .'</font><br/>';
        $cadena1 .= '<font class="label_v">Uso:</font>&nbsp;<font class="content_v">'. $vehiculos_vehiculo->getTipoUsoNombre() .'</font><br/>';
        $cadena1 .= '<font class="label_v">Kilometraje:</font>&nbsp;<font class="content_v">'. number_format($vehiculos_vehiculo->getKilometrajeActual(), 0, '', '.') .'&nbsp;Km</font><br/>';
        $cadena1 .= '<font class="label_v">A&ntildeo:</font>&nbsp;<font class="content_l">'. $vehiculos_vehiculo->getAno() .'</font>';
        echo $cadena1;
        ?>
    </div>
    <div style="min-height: 100px; width: 150px; float: left">
        <?php
        $cadena2 = '<font class="label_v">Vel. Max:</font>&nbsp;<font class="content_v">'. $vehiculos_vehiculo->getVelMax() .'&nbsp;Km/h</font><br/>';
        $cadena2 .= '<font class="label_v">Seriales:</font><br/>';
        $cadena2 .= '&nbsp;&nbsp;&nbsp;<font class="label_v">Motor:</font>&nbsp;<font class="content_v">'. $vehiculos_vehiculo->getSerialMotor() .'</font><br/>';
        $cadena2 .= '&nbsp;&nbsp;&nbsp;<font class="label_v">Carroceria:</font>&nbsp;<font class="content_v">'. $vehiculos_vehiculo->getSerialMotor() .'</font>';
        echo $cadena2;
        ?>
    </div>
</div>
