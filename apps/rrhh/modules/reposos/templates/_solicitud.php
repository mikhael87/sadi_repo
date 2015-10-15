<?php 
$correspondencia_solicitud = Doctrine::getTable('Correspondencia_Correspondencia')->find($rrhh_reposos->getCorrespondenciaSolicitudId());

echo "<a href='".sfConfig::get('sf_app_rrhh_url')."reposos/seguimientoSolicitud?id=".$rrhh_reposos->getCorrespondenciaSolicitudId()."'>".
        image_tag('icon/goto.png')."&nbsp;&nbsp;".$correspondencia_solicitud->getNCorrespondenciaEmisor();
     "</a>"; 
?>

