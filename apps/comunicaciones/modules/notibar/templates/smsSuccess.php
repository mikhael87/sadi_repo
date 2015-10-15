<?php
$months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$date = date('d') . " de " . $months[intval(date('m'))] . " de " . date('Y') . " a las " . date('h') . ":" . date('i') . " " . date('A');
$head= '<div style="text-align: center; padding-top: 10px">
            <font style="font-size: 20px; font-weight: bolder; text-shadow: 1px 2px #999">Mensajes</font><br/>
            <font style="font-size: 10px; color: #666">'. $date .'</font>
        </div>';

$sms_interno= array(); //metodo 8
//$sms_externo= array(); //metodo 9
$cont= 0;
$tiempo = new herramientas();

foreach($sms as $value) {
    switch ($value->getMetodoId()) {
        case 8:
            $sms_interno[$cont]['id_noti']= $value->getId();
            $sms_interno[$cont]['mensaje']= $value->getMensaje();

            $parametros = sfYaml::load($value->getParametros());

            if(isset($parametros['fecha']))
                $sms_interno[$cont]['fecha']= $parametros['fecha'];
            if(isset($parametros['mensaje_id']))
                $sms_interno[$cont]['mensaje_id']= $parametros['mensaje_id'];

            break;
        case 9:
//            $sms_externo[$cont]['id_noti']= $value->getId();
//            $sms_externo[$cont]['mensaje']= $value->getMensaje();
//            $sms_externo[$cont]['f_entrega']= $value->getFEntrega();
//            break;
        default:
            break;
    }
    $cont++;
}

$marker= FALSE;
$big_string= '';

if(count($sms_interno)> 0){
    $options_all= '';
    if(count($sms_interno)> 1){
        foreach($sms_interno as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #eaff4d; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #f6ffb2; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #eaff4d; border-left: 1px solid #eaff4d; border-right: 1px solid #eaff4d; position: absolute; top: -19px; right: -1px; background-color: #f6ffb2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'sms\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($sms_interno as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px">';
        $big_string .= '<a style="text-decoration: none" href="'. sfConfig::get('sf_app_herramientas_url').'mensajes"><img style="vertical-align: middle" src="/images/icon/chat.png" />&nbsp;Ir a mensajes</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        $big_string .= (isset($value['mensaje_id'])) ? '<a style="text-decoration: none" href="javascript: fn_responder(\''. $value['mensaje_id'] .'\', \''. $value['id_noti']. '\')"><img style="vertical-align: middle" src="/images/icon/reused.png" />&nbsp;Responder</a>&nbsp;&nbsp;|&nbsp;&nbsp;' : '';
        $big_string .= (isset($value['mensaje_id'])) ? '<a style="text-decoration: none" href="javascript: fn_archivar(\''. $value['mensaje_id'] .'\', \''. $value['id_noti']. '\')"><img style="vertical-align: middle" src="/images/icon/filesave.png" />&nbsp;Archivar</a>' : '';
        $big_string .= '</div>';

        if(isset($value['fecha'])) {
            $time= $tiempo->tiempo_transcurrido(date('Y-m-d H:i:s', $value['fecha']));
            $mensaje= str_replace('#string_to_replace#', $time, $value['mensaje']);
        }else {
            $mensaje= str_replace('#string_to_replace#', '', $value['mensaje']);
        }

        $big_string .= html_entity_decode($mensaje);
        $big_string .= (isset($value['mensaje_id'])) ? '<div id="div_respuesta_'. $value['mensaje_id'] .'"></div>' : '';
        $i++;
        $big_string .= (count($sms_interno)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if($big_string != '') {
    include_partial('notibar/assets_sms');
    echo $head.$big_string;
}else
    echo '';
?>