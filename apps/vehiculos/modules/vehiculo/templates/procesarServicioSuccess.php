<?php
if(count($servicios) > 0) {
    $fecha_actual= strtotime(date('Y-m-d'));
    $servicios_icon= array();
    $k= 0;
    foreach($servicios as $servicio) {
        if($servicio->getFecha() != '') {
            if(strtotime($servicio->getFecha()) < $fecha_actual && $servicio->getStatus() == 'A') {
                $servicios_icon[$k]['id'] = $servicio->getId();
                $servicios_icon[$k]['icono'] = $servicio->getIcono();
                $servicios_icon[$k]['observacion'] = $servicio->getObservacion();
                $servicios_icon[$k]['nombre'] = $servicio->getNombre();
                $servicios_icon[$k]['fecha'] = $servicio->getFecha();
                $servicios_icon[$k]['kilometraje'] = $servicio->getKilometraje();
                $k++;
            }
        }else {
            if($servicio->getKilometraje() < $servicio->getKilometrajeActual() && $servicio->getStatus()== 'A') {
                $servicios_icon[$k]['id'] = $servicio->getId();
                $servicios_icon[$k]['icono'] = $servicio->getIcono();
                $servicios_icon[$k]['observacion'] = $servicio->getObservacion();
                $servicios_icon[$k]['nombre'] = $servicio->getNombre();
                $servicios_icon[$k]['fecha'] = $servicio->getFecha();
                $servicios_icon[$k]['kilometraje'] = $servicio->getKilometraje();
                $k++;
            }
        }
    }

    $cadena= '';
    if(count($servicios_icon) > 0) {
        foreach($servicios_icon as $value) {
            $cadena.= image_tag('icon/tracker/al_'.$value['icono'], array('style'=>'cursor: pointer', 'class'=>'tooltip', 'title'=>'[!]'. $value['nombre'] .'[/!]'.(($value['observacion']!= '')? $value['observacion'] : '') . '<br/>Desde: '.(($value['fecha'] != '')? date('d-m-Y', strtotime($value['fecha'])) : $value['Kilometraje']).'<br/><br/>Click si ya realizo este servicio...', 'onClick'=>'procesar("'. $vehiculo_id .'", "'. $value['id'] .'")')).'&nbsp;';
        }
    }else {
        $cadena.= '<div style="text-align: center; padding-top: 10px; padding-bottom: 5px"><font style="color: grey; font-size: 10">Servicios al dia</font></div>';
    }
    echo $cadena;
}
?>
