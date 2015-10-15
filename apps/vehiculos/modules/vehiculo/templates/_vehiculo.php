<style>
    .marca { font-size: 10px; font-weight: bold }
    .modelo { font-size: 12px; color: #666 }
    .operador { font-size: 12px; color: #666 }
    .sindispo { color: grey; font-size: 10px }
    .table_gps { margin-top: 4px; margin-bottom: 0px }
</style>
<div style="position: relative; min-height: 100px; width: 150px;">
    <?php echo '<font style="font-size: 15px; font-weight: bold">'.$vehiculos_vehiculo->getMarca().'</font>&nbsp;<font style="font-size: 15px; color: #666">'.$vehiculos_vehiculo->getModelo().'</font>'; ?><br/>
    <font style="font-size: 10px">Color:</font> <?php echo '<font style="font-size: 11px">'.strtoupper($vehiculos_vehiculo->getColor()).'</font>'; ?><br/>
    <font style="font-size: 10px">Placa:</font> <?php echo '<font style="font-size: 11px">'.strtoupper($vehiculos_vehiculo->getPlaca()).'</font>'; ?>
    <br/>
    <?php
    $devices= Doctrine::getTable('Vehiculos_GpsVehiculo')->gpsPorVehiculo($vehiculos_vehiculo->getId());
    
    if(count($devices) > 0) {
        echo '<table class="table_gps"><tr><td>';
        foreach($devices as $device) {
            echo '<font class="marca">'. $device->getMarca() .'</font><font class="modelo">'. $device->getModelo() .'</font><br/>';
            echo '<font class="operador">'. $device->getOperador() .'</font>';
        }
        echo '</td></tr></table>';
    }else {
        echo '<br/><font class="sindispo">Sin dispositivo GPS</font>';
    }
    ?>
</div>