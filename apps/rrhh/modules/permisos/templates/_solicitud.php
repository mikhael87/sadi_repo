<?php 
$correspondencia_solicitud = Doctrine::getTable('Correspondencia_Correspondencia')->find($rrhh_permisos->getCorrespondenciaSolicitudId());

echo "<a href='".sfConfig::get('sf_app_rrhh_url')."permisos/seguimientoSolicitud?id=".$rrhh_permisos->getCorrespondenciaSolicitudId()."'>".
        image_tag('icon/goto.png')."&nbsp;&nbsp;".$correspondencia_solicitud->getNCorrespondenciaEmisor();
     "</a>"; 
?>

