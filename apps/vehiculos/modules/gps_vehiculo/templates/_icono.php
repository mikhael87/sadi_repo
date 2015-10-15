<?php
if($vehiculos_gps_vehiculo->getIcono() == 'default.png')
    echo image_tag('icon/tracker/'.$vehiculos_gps_vehiculo->getIcono());
else
    echo image_tag('icon/tracker/'.$vehiculos_gps_vehiculo->getColorIcon().'/'.$vehiculos_gps_vehiculo->getIcono());
?>
