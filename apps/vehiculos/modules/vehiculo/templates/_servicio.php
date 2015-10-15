<?php use_helper('jQuery'); ?>
<script>
    function procesar(vehiculo_id, servicio_id) {
        if(confirm('¿Esta seguro que este Servicio fue realizado?')){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>vehiculo/procesarServicio',
            type:'POST',
            dataType:'html',
            data: 'vehiculo_id='+vehiculo_id+'&servicio_id='+servicio_id,
            beforeSend: function(Obj){
                $('#servicios_actualizable_'+vehiculo_id).html('<img src="/images/icon/cargando.gif" />&nbsp;<font style="color: #666; font-size: 12px">Cargando...</font>');
            },
            success:function(data, textStatus){
                $('#servicios_actualizable_'+vehiculo_id).html(data);
                $('#tooltip').hide();
            }});
        }else {
            return false;
        }
    }
</script>
<style>
    .label_s { font-size: 10px; font-weight: bold; color: #666 }
    .content_s { font-size: 11px; color: #999}
</style>
<div style="position: relative; min-height: 100px; width: 150px;">
    <?php
    $servicios= Doctrine::getTable('Vehiculos_Mantenimiento')->servicioPorVehiculo($vehiculos_vehiculo->getId());
    $cadena= '';
    if(count($servicios) > 0) {
        $kilometraje_actual= $vehiculos_vehiculo->getKilometrajeActual();
        $fecha_actual= strtotime(date('Y-m-d'));
        $prox_kilometraje= array();
        $prox_fecha= array();
        $servicios_icon= array();
        $i= 0; $j= 0; $k=0;
        foreach($servicios as $servicio) {
            //PROX KILOMOTRAJE
            if($servicio->getKilometraje() > $kilometraje_actual) {
                $i++;
                if($i== 1)
                    $prox_kilometraje['kilometraje']= $servicio->getKilometraje();
                    $prox_kilometraje['icono']= $servicio->getIcono();
                    $prox_kilometraje['observacion']= $servicio->getObservacion();
                    $prox_kilometraje['nombre']= $servicio->getNombre();
                if($servicio->getKilometraje() < $prox_kilometraje)
                    $prox_kilometraje['kilometraje']= $servicio->getKilometraje();
                    $prox_kilometraje['icono']= $servicio->getIcono();
                    $prox_kilometraje['observacion']= $servicio->getObservacion();
                    $prox_kilometraje['nombre']= $servicio->getNombre();
            }
            //PROX FECHA
            if(strtotime($servicio->getFecha()) > $fecha_actual) {
                $j++;
                if($j== 1) {
                    $prox_fecha['fecha']= strtotime($servicio->getFecha());
                    $prox_fecha['icono']= $servicio->getIcono();
                    $prox_fecha['observacion']= $servicio->getObservacion();
                    $prox_fecha['nombre']= $servicio->getNombre();
                }
                if(strtotime($servicio->getFecha()) < $prox_fecha) {
                    $prox_fecha['fecha']= strtotime($servicio->getFecha());
                    $prox_fecha['icono']= $servicio->getIcono();
                    $prox_fecha['observacion']= $servicio->getObservacion();
                    $prox_fecha['nombre']= $servicio->getNombre();
                }
            }
            
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

        $cadena.= '<font class="label_s">Servicios pendientes:</font><br/>';
        if(count($servicios_icon) > 0) {
            $cadena.= '<div id="servicios_actualizable_'. $vehiculos_vehiculo->getId() .'" style="padding-top: 10px; padding-bottom: 5px">';
            foreach($servicios_icon as $value) {
                $cadena.= image_tag('icon/tracker/al_'.$value['icono'], array('style'=>'cursor: pointer', 'class'=>'tooltip', 'title'=>'[!]'. $value['nombre'] .'[/!]'.(($value['observacion']!= '')? $value['observacion'] : '') . '<br/>Desde: '.(($value['fecha'] != '')? date('d-m-Y', strtotime($value['fecha'])) : number_format($value['kilometraje']).' Km').'<br/><br/>Click si ya realizo este servicio...', 'onClick'=>'procesar("'. $vehiculos_vehiculo->getId() .'", "'. $value['id'] .'")')).'&nbsp;';
            }
            $cadena.= '</div>';
        }else {
            $cadena.= '<div style="text-align: center; padding-top: 10px; padding-bottom: 5px"><font style="color: grey; font-size: 10">Servicios al dia</font></div>';
        }
        
        $cadena.= '<font class="label_s">Prox. Servicio (Tiempo):</font><br/>';

        if(count($prox_fecha) > 0)
            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s tooltip" title="[!]'. $prox_fecha['nombre'] .'[/!]'. (($prox_fecha['observacion']!= '')? $prox_fecha['observacion'] : '') .'"">'. image_tag('icon/tracker/'.$prox_fecha['icono'], array('style'=>'cursor: pointer; vertical-align: middle')) . '&nbsp;' . date('d-m-Y', $prox_fecha['fecha']) .'</font>';
        else
            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">Sin servicios</font>';
        
        $cadena .= '<br/><font class="label_s">Prox. Servicio (Km):</font><br/>';
        
        if(count($prox_kilometraje) > 0)
            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s tooltip" title="[!]'. $prox_kilometraje['nombre'] .'[/!]'. (($prox_kilometraje['observacion']!= '')? $prox_kilometraje['observacion'] : '') .'">'. image_tag('icon/tracker/'.$prox_kilometraje['icono'], array('style'=>'cursor: pointer; vertical-align: middle')) . '&nbsp;' . number_format($prox_kilometraje['kilometraje']) .' Km</font>';
        else
            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">Sin servicios</font>';
        
        echo $cadena;
        
//        $cadena= '<font class="label_s">Prox. Servicio (Kilometraje):</font><br/>';
//        if($prox_kilometraje)
//            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">'. $prox_kilometraje .' Km</font>';
//        else
//            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">N/A</font>';
//        if($prox_fecha)
//            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">'. date('d-m-Y', $prox_fecha) .'</font>';
//        else
//            $cadena .= '&nbsp;&nbsp;&nbsp;<font class="content_s">N/A</font>';
    }else {
        echo '<div style="text-align: center; padding-top: 20px"><font style="color: grey; font-size: 10">Sin servicios</font></div>';
    }
    ?>
</div>