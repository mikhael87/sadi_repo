<div style="position: relative; font-size: 13px; width: 360px;">
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
            <?php echo ($rrhh_reposos->getClasificacion() == 'D') ? 'D&iacute;as de reposo' : 'Semanas de reposo' ?>
        </div>
        <div style="position: relative; padding-top: 10px; left: 0px;">
            <?php
            if($rrhh_reposos->getClasificacion() == 'D')
                echo $rrhh_reposos->getDiasSolicitados();
            else
                echo $rrhh_reposos->getDiasSolicitados()/ 5;
            ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 80px;">
            Dias habiles
        </div>
        <div style="position: absolute; top: 10px; left: 80px;">
            <?php echo $rrhh_reposos->getDiasReposoHabiles(); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 160px;">
            Fin de Semana
        </div>
        <div style="position: absolute; top: 10px; left: 160px;">
            <?php echo $rrhh_reposos->getDiasReposoFinSemana(); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 240px;">
            No laborales
        </div>
        <div style="position: absolute; top: 10px; left: 240px;">
            <?php echo $rrhh_reposos->getDiasReposoNoLaborales(); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 310px;">
            Continuos
        </div>
        <div style="position: absolute; top: 10px; left: 310px;">
            <?php echo $rrhh_reposos->getDiasReposoContinuo(); ?>
        </div>
    </div>
</div>