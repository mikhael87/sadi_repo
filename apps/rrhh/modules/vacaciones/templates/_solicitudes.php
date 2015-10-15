<?php
if($rrhh_vacaciones->getSolicitudes()==0)
    echo "Sin Solicitudes";
else {
    $solicitudes = Doctrine::getTable('Rrhh_VacacionesDisfrutadas')->findByVacacionesId($rrhh_vacaciones->getId());

    $status_solicitud['I']='Creada';
    $status_solicitud['E']='Enviada';
    $status_solicitud['R']='Anulada';
    $status_solicitud['A']='Aprobada';
    $status_solicitud['N']='Negada';
    $status_solicitud['P']='Pausada';
    $status_solicitud['C']='Canceladas';
    
    echo "<table>";
    echo "<tr><th class='f10b'></th><th class='f10b'>Correspondencia</th><th class='f10b'>Dias Solicitados</th><th class='f10b'>Fecha Inicio</th><th class='f10b'>Fecha Final</th><th class='f10b'>Estatus</th></tr>";
    foreach ($solicitudes as $solicitud) {
        
        $correspondencia_solicitud = Doctrine::getTable('Correspondencia_Correspondencia')->find($solicitud->getCorrespondenciaSolicitudId());
        
        echo "<tr>";
        
        echo "<td><a href='".sfConfig::get('sf_app_rrhh_url')."vacaciones/seguimientoSolicitud?id=".$correspondencia_solicitud->getId()."'>".image_tag('icon/goto.png')."</a></td>";
        echo "<td>".$correspondencia_solicitud->getNCorrespondenciaEmisor()."</td>";
        echo "<td style='text-align: center;'>".$solicitud->getDiasSolicitados()."</td>";
        echo "<td>".date('d-m-Y', strtotime($solicitud->getFInicioDisfrute()))."</td>";
        echo "<td>".date('d-m-Y', strtotime($solicitud->getFFinDisfrute()))."</td>";
        echo "<td>".$status_solicitud[$solicitud->getStatus()]."</td>";
        
        echo "</tr>";
    }
    echo "</table>";
}
?>

