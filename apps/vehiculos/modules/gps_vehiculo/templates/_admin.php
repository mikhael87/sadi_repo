<div style="position: relative; min-height: 100px; width: 200px;">
    <?php
    echo ($vehiculos_gps_vehiculo->getNumeroUno() != ''? $vehiculos_gps_vehiculo->getNumeroUno().'<br/>' : '');
    echo ($vehiculos_gps_vehiculo->getNumeroDos() != ''? $vehiculos_gps_vehiculo->getNumeroDos().'<br/>' : '');
    echo ($vehiculos_gps_vehiculo->getNumeroTres() != ''? $vehiculos_gps_vehiculo->getNumeroTres().'<br/>' : '');
    echo ($vehiculos_gps_vehiculo->getNumeroCuatro() != ''? $vehiculos_gps_vehiculo->getNumeroCuatro().'<br/>' : '');
    echo ($vehiculos_gps_vehiculo->getNumeroCinco() != ''? $vehiculos_gps_vehiculo->getNumeroCinco().'<br/>' : '');
    ?>
</div>
