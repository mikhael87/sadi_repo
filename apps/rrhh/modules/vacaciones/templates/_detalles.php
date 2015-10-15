<div style="position: relative; width: 180px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Fecha de Cumplimiento
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo date('d-m-Y', strtotime($rrhh_vacaciones->getFCumplimiento())); ?>
    </div>
    
    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Años Laborales
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo $rrhh_vacaciones->getAniosLaborales(); ?>
    </div>
</div>
