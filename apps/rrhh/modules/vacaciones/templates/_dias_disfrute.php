<div style="position: relative; font-size: 13px; width: 300px;">
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
            Establecidos
        </div>
        <div style="position: relative; padding-top: 10px; left: 0px;">
            <?php echo $rrhh_vacaciones->getDiasDisfruteEstablecidos(); ?>
        </div>
        
        
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 80px;">
            Adicionales
        </div>
        <div style="position: absolute; top: 10px; left: 80px;">
            <?php echo $rrhh_vacaciones->getDiasDisfruteAdicionales(); ?>
        </div>
        
        
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 160px;">
            Totales
        </div>
        <div style="position: absolute; top: 10px; left: 160px;">
            <?php echo $rrhh_vacaciones->getDiasDisfruteTotales(); ?>
        </div>
        
        
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 240px;">
            Pendientes
        </div>
        <div style="position: absolute; top: 10px; left: 240px;">
            <?php echo $rrhh_vacaciones->getDiasDisfrutePendientes(); ?>
        </div>
    </div>
</div>