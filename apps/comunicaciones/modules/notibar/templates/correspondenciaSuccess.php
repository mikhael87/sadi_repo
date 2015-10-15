<?php
$months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$date = date('d') . " de " . $months[intval(date('m'))] . " de " . date('Y') . " a las " . date('h') . ":" . date('i') . " " . date('A');
$head= '<div style="text-align: center; padding-top: 10px">
            <font style="font-size: 20px; font-weight: bolder; text-shadow: 1px 2px #999">Correspondencia</font><br/>
            <font style="font-size: 10px; color: #666">'. $date .'</font>
        </div>';

$resumen_diario= array(); //metodo 6
$edicion_correspondencia= array(); //metodo 7
$anulacion_correspondencia= array(); //metodo 10
$firmada_correspondencia= array(); //metodo 11
$cont= 0;
$tiempo = new herramientas();

foreach($correspondencia as $value) {
    switch ($value->getMetodoId()) {
        case 6:
            $resumen_diario[$cont]['id_noti']= $value->getId();
            $resumen_diario[$cont]['mensaje']= $value->getMensaje();
            $resumen_diario[$cont]['f_entrega']= $value->getFEntrega();
            break;
        case 7:
            $edicion_correspondencia[$cont]['id_noti']= $value->getId();
            $edicion_correspondencia[$cont]['mensaje']= $value->getMensaje();

            $parametros = sfYaml::load($value->getParametros());
//            echo '<br/>'.date('Y-m-d h:i:s', $parametros['fecha']); exit;

            if(isset($parametros['fecha']))
                $edicion_correspondencia[$cont]['fecha']= $parametros['fecha'];
//            echo $parametros['fecha']; exit;
            break;
        case 10:
            $anulacion_correspondencia[$cont]['id_noti']= $value->getId();
            $anulacion_correspondencia[$cont]['mensaje']= $value->getMensaje();

            $parametros = sfYaml::load($value->getParametros());

            if(isset($parametros['fecha']))
                $anulacion_correspondencia[$cont]['fecha']= $parametros['fecha'];
            break;
        case 11:
            $firmada_correspondencia[$cont]['id_noti']= $value->getId();
            $firmada_correspondencia[$cont]['mensaje']= $value->getMensaje();

            $parametros = sfYaml::load($value->getParametros());

            if(isset($parametros['fecha']))
                $firmada_correspondencia[$cont]['fecha']= $parametros['fecha'];
            break;
        default:
            break;
    }
    $cont++;
}

$marker= FALSE;
$big_string= '';

if(count($resumen_diario)> 0){
    $i= 0;
    foreach($resumen_diario as $value) {

        $big_string .= '<div style="border: solid 1px #5fab58; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #8adb83; border-radius: 10px 10px 10px 10px;" class="mielemento">';
        $big_string .= '<div style="position:relative; text-align: right; height: 15px; padding-right: 10px"><a style="text-decoration: none" href="'. sfConfig::get('sf_app_correspondencia_url').'recibida"><img style="vertical-align: middle" src="/images/icon/reply.png" />&nbsp;Ir a recibidas</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="text-decoration: none" href="'. sfConfig::get('sf_app_correspondencia_url').'enviada/resumenPdf?id='.$value['id_noti']. '"><img style="vertical-align: middle" src="/images/icon/pdf.png" />&nbsp;Imprimir</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'correspondencia\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar resumen</a>';
        $big_string .= '<div style="z-index: 2; border-top: 1px solid #5fab58; border-left: 1px solid #5fab58; border-right: 1px solid #5fab58; position: absolute; top: -19px; left: 18px; background-color: #8adb83; border-radius: 10px 10px 0px 0px; text-align: right; width: 125px; height: 19px; padding-right: 10px; text-align: center"><font style="font-weight: bolder; color:#325e2d">RESUMEN DIARIO</font><br/><font class="f14n">'. date('d', strtotime('-1 day', strtotime($value['f_entrega']))) .' de '. $months[(int)date('m', strtotime($value['f_entrega']))] .' del '. date('Y', strtotime($value['f_entrega'])) .'</font></div>';
        $big_string .= '</div>';
        $big_string .= html_entity_decode($value['mensaje']);
        $big_string .= '</div>';
        $i++;
        $big_string .= (count($resumen_diario)== $i)? '': '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />';
    }

    $marker= TRUE;
}

if(count($edicion_correspondencia)> 0){
    $options_all= '';
    if(count($edicion_correspondencia)> 1){
        foreach($edicion_correspondencia as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #eaff4d; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #f6ffb2; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #eaff4d; border-left: 1px solid #eaff4d; border-right: 1px solid #eaff4d; position: absolute; top: -19px; right: -1px; background-color: #f6ffb2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'correspondencia\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($edicion_correspondencia as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="'. sfConfig::get('sf_app_correspondencia_url').'enviada"><img style="vertical-align: middle" src="/images/icon/send.png" />&nbsp;Ir a enviadas</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'correspondencia\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';

        if(isset($value['fecha'])) {
            $time= $tiempo->tiempo_transcurrido(date('Y-m-d H:i:s', $value['fecha']));
            $mensaje= str_replace('#string_to_replace#', $time, $value['mensaje']);
        }else {
            $mensaje= str_replace('#string_to_replace#', '', $value['mensaje']);
        }

        $big_string .= html_entity_decode($mensaje);

        $i++;
        $big_string .= (count($edicion_correspondencia)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($anulacion_correspondencia)> 0){
    $options_all= '';
    if(count($anulacion_correspondencia)> 1){
        foreach($anulacion_correspondencia as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #ff4dda; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #ffb2ef; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #ff4dda; border-left: 1px solid #ff4dda; border-right: 1px solid #ff4dda; position: absolute; top: -19px; right: -1px; background-color: #ffb2ef; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'correspondencia\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($anulacion_correspondencia as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="'. sfConfig::get('sf_app_correspondencia_url').'enviada"><img style="vertical-align: middle" src="/images/icon/send.png" />&nbsp;Ir a enviadas</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'correspondencia\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';

        if(isset($value['fecha'])) {
            $time= $tiempo->tiempo_transcurrido(date('Y-m-d H:i:s', $value['fecha']));
            $mensaje= str_replace('#string_to_replace#', $time, $value['mensaje']);
        }else {
            $mensaje= str_replace('#string_to_replace#', '', $value['mensaje']);
        }

        $big_string .= html_entity_decode($mensaje);

        $i++;
        $big_string .= (count($anulacion_correspondencia)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($firmada_correspondencia)> 0){
    $options_all= '';
    if(count($firmada_correspondencia)> 1){
        foreach($firmada_correspondencia as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #674dff; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #beb2ff; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #674dff; border-left: 1px solid #674dff; border-right: 1px solid #674dff; position: absolute; top: -19px; right: -1px; background-color: #beb2ff; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'correspondencia\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($firmada_correspondencia as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="'. sfConfig::get('sf_app_correspondencia_url').'enviada"><img style="vertical-align: middle" src="/images/icon/send.png" />&nbsp;Ir a enviadas</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'correspondencia\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';

        if(isset($value['fecha'])) {
            $time= $tiempo->tiempo_transcurrido(date('Y-m-d H:i:s', $value['fecha']));
            $mensaje= str_replace('#string_to_replace#', $time, $value['mensaje']);
        }else {
            $mensaje= str_replace('#string_to_replace#', '', $value['mensaje']);
        }

        $big_string .= html_entity_decode($mensaje);

        $i++;
        $big_string .= (count($firmada_correspondencia)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if($big_string != '') {
    include_partial('notibar/assets_correspondencia');
    echo $head.$big_string;
}else
    echo '';
?>