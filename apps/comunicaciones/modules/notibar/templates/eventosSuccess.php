<?php
$months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$date = date('d') . " de " . $months[intval(date('m'))] . " de " . date('Y') . " a las " . date('h') . ":" . date('i') . " " . date('A');
$head= '<div style="text-align: center; padding-top: 10px">
            <font style="font-size: 20px; font-weight: bolder; text-shadow: 1px 2px #999">Eventos</font><br/>
            <font style="font-size: 10px; color: #666">'. $date .'</font>
        </div>';

$cambio_grupo_correspondencia= array(); //metodo 1
$cambio_grupo_archivo= array(); //metodo 2
$cambio_grupo_unidad= array(); //metodo 3
$miniforo_correspondencia= array(); //metodo 4
$cumpleano= array(); //metodo 5
$cont= 0;

foreach($eventos as $evento) {
    switch ($evento->getMetodoId()) {
        case 1:
            $cambio_grupo_correspondencia[$cont]['id_noti']= $evento->getId();
            $cambio_grupo_correspondencia[$cont]['mensaje']= $evento->getMensaje();
            break;
        case 2:
            $cambio_grupo_archivo[$cont]['id_noti']= $evento->getId();
            $cambio_grupo_archivo[$cont]['mensaje']= $evento->getMensaje();
            break;
        case 3:
            $cambio_grupo_unidad[$cont]['id_noti']= $evento->getId();
            $cambio_grupo_unidad[$cont]['mensaje']= $evento->getMensaje();
            break;
        case 4:
            $miniforo_correspondencia[$cont]['id_noti']= $evento->getId();
            $miniforo_correspondencia[$cont]['mensaje']= $evento->getMensaje();
            break;
        case 5:
            $cumpleano[$cont]['id_noti']= $evento->getId();
            $cumpleano[$cont]['mensaje']= $evento->getMensaje();
            break;
        default:
            break;
    }
    $cont++;
}

$marker= FALSE;
$big_string= '';

if(count($cambio_grupo_correspondencia)> 0){
    $options_all= '';
    if(count($cambio_grupo_correspondencia)> 1){
        foreach($cambio_grupo_correspondencia as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= '<div style="border: solid 1px #ffb54d; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #ffdfb2; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #ffb54d; border-left: 1px solid #ffb54d; border-right: 1px solid #ffb54d; position: absolute; top: -19px; right: -1px; background-color: #ffdfb2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'eventos\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($cambio_grupo_correspondencia as $value) {
        $big_string .= '<div><div id="'. $value['id_noti'] .'" style="text-align: right; height: 15px; padding-right: 10px"><a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'eventos\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';
        $big_string .= html_entity_decode($value['mensaje']);
        $big_string .= '</div>';

        $i++;
        $big_string .= (count($cambio_grupo_correspondencia)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($cambio_grupo_archivo)> 0){
    $options_all= '';
    if(count($cambio_grupo_archivo)> 1){
        foreach($cambio_grupo_archivo as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #eaff4d; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #f6ffb2; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #eaff4d; border-left: 1px solid #eaff4d; border-right: 1px solid #eaff4d; position: absolute; top: -19px; right: -1px; background-color: #f6ffb2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'eventos\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($cambio_grupo_archivo as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'eventos\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';
        $big_string .= html_entity_decode($value['mensaje']);

        $i++;
        $big_string .= (count($cambio_grupo_archivo)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($cambio_grupo_unidad)> 0){
    $options_all= '';
    if(count($cambio_grupo_unidad)> 1){
        foreach($cambio_grupo_unidad as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #7dff4d; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #c7ffb2; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #7dff4d; border-left: 1px solid #7dff4d; border-right: 1px solid #7dff4d; position: absolute; top: -19px; right: -1px; background-color: #c7ffb2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'eventos\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($cambio_grupo_unidad as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'eventos\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';
        $big_string .= html_entity_decode($value['mensaje']);

        $i++;
        $big_string .= (count($cambio_grupo_unidad)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($miniforo_correspondencia)> 0){
    $options_all= '';
    if(count($miniforo_correspondencia)> 1){
        foreach($miniforo_correspondencia as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #4d58ff; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-color: #b2b7ff; border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #4d58ff; border-left: 1px solid #4d58ff; border-right: 1px solid #4d58ff; position: absolute; top: -19px; right: -1px; background-color: #b2b7ff; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'eventos\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($miniforo_correspondencia as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'eventos\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';
        $big_string .= html_entity_decode($value['mensaje']);

        $i++;
        $big_string .= (count($miniforo_correspondencia)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if(count($cumpleano)> 0){
    $options_all= '';
    if(count($cumpleano)> 1){
        foreach($cumpleano as $value) {
            $options_all .= $value['id_noti'].',';
        }
    }
    $big_string .= ($marker)? '<hr style="margin-left: 0px; margin-top:5px; margin-bottom: 30px; color: black; background-color: black; height: 3px; width: 880px" />': '';
    $big_string .= '<div style="border: solid 1px #ff4d72; position: relative; max-width: 880px; min-width: 880px; top: 0px; background-image: url(/images/other/background_birthday.jpg); border-radius: 10px '. (($options_all != 0)? "0px" : "10px") .' 10px 10px;" class="mielemento">';
    $big_string .= ($options_all != 0) ? '<div style="position: relative"><div style="z-index: 2; border-top: 1px solid #ff4d72; border-left: 1px solid #ff4d72; border-right: 1px solid #ff4d72; position: absolute; top: -19px; right: -1px; background-color: #ffb2c2; border-radius: 10px 10px 0px 0px; text-align: right; width: 90px; height: 19px; padding-right: 18px"><a style="text-decoration: none" href="javascript: borrar_todas(\''. $options_all. '\', \'eventos\')">Borrar todas</a></div></div>' : '';
    $i= 0;
    foreach($cumpleano as $value) {
        $big_string .= '<div style="text-align: right; height: 0px; padding-right: 10px"><a style="text-decoration: none" href="javascript: borrar_individual(\''. $value['id_noti']. '\', \'eventos\')"><img style="vertical-align: middle" src="/images/icon/clear.png" />&nbsp;Borrar</a></div>';
        $big_string .= html_entity_decode($value['mensaje']);

        $i++;
        $big_string .= (count($cumpleano)== $i)? '': '<hr style="width: 845px"/>';
    }
    $big_string .= '</div>';
    $marker= TRUE;
}

if($big_string != '') {
    include_partial('notibar/assets_eventos');
    echo $head.$big_string;
}else
    echo '';
?>