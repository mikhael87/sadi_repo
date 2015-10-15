<?php

$link_uno= '<li><img src="/images/icon/noti_correspondence_i.png" /></li>';
if($groups_count[0][0]> 0) {
    $link_uno= '<li><img style="cursor: pointer" src="/images/icon/noti_correspondence_a.png" onclick="javascript:abrir_notibar(\'correspondencia\')"/>';
    if($groups_count[0][0]< 100)
        $link_uno.= '<div onclick="javascript:abrir_notibar(\'correspondencia\')" style="cursor: pointer; opacity: 0.7; position:absolute; right: 78px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%; text-align: center; vertical-align: baseline"><font style="font-size: 9px; color: white">'. $groups_count[0][0] .'</font></div></li>';
}
$link_dos= '<li><img src="/images/icon/noti_community_i.png" /></li>';
if($groups_count[0][1]> 0) {
    $link_dos= '<li><img style="cursor: pointer" src="/images/icon/noti_community_a.png" onclick="javascript:abrir_notibar(\'eventos\')"/>';
    if($groups_count[0][1]< 100)
        $link_dos.= '<div onclick="javascript:abrir_notibar(\'eventos\')" style="cursor: pointer; opacity: 0.7; position:absolute; right: 40px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%; text-align: center; vertical-align: baseline"><font style="font-size: 9px; color: white">'. $groups_count[0][1] .'</font></div></li>';
}
$link_tres= '<li><img src="/images/icon/noti_chat_i.png" /></li>';
if($groups_count[0][2]> 0) {
    $link_tres= '<li><img style="cursor: pointer" src="/images/icon/noti_chat_a.png" onclick="javascript:abrir_notibar(\'sms\')"/>';
    if($groups_count[0][2]< 100)
        $link_tres.= '<div onclick="javascript:abrir_notibar(\'sms\')" style="cursor: pointer; opacity: 0.7; position:absolute; right: 5px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%; text-align: center; vertical-align: baseline"><font style="font-size: 9px; color: white">'. $groups_count[0][2] .'</font></div></li>';
}
$cadena= $link_uno.$link_dos.$link_tres;
echo $cadena;
?>