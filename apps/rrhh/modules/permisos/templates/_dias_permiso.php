<div style="position: relative; font-size: 13px; width: 360px;">
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
            <?php echo ($rrhh_permisos->getClasificacion() == 'D') ? 'D&iacute;as Solicitados' : 'Semanas Solicitadas' ?>
        </div>
        <div style="position: relative; padding-top: 10px; left: 0px;">
            <?php
            $parts_dias_solicitados = explode(".", $rrhh_permisos->getDiasSolicitados());
            if($rrhh_permisos->getClasificacion() == 'D') 
                echo ((isset($parts_dias_solicitados[1])) ? (((int)$rrhh_permisos->getDiasSolicitados() != 0) ? (int)$rrhh_permisos->getDiasSolicitados() : '').' &frac12' : $rrhh_permisos->getDiasSolicitados());
            else
                echo $rrhh_permisos->getDiasSolicitados()/ 5;
            ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 80px;">
            D&iacute;as habiles
        </div>
        <div style="position: absolute; top: 10px; left: 80px;">
            <?php
            $parts_dias_habiles = explode(".", $rrhh_permisos->getDiasPermisoHabiles());
            echo ((isset($parts_dias_habiles[1])) ? (((int)$rrhh_permisos->getDiasPermisoHabiles() != 0) ? (int)$rrhh_permisos->getDiasPermisoHabiles() : '').' &frac12' : $rrhh_permisos->getDiasPermisoHabiles()); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 160px;">
            Fin de Semana
        </div>
        <div style="position: absolute; top: 10px; left: 160px;">
            <?php echo $rrhh_permisos->getDiasPermisoFinSemana(); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 240px;">
            No laborales
        </div>
        <div style="position: absolute; top: 10px; left: 240px;">
            <?php echo $rrhh_permisos->getDiasPermisoNoLaborales(); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 310px;">
            Continuos
        </div>
        <div style="position: absolute; top: 10px; left: 310px;">
            <?php
            $parts_dias_contenuos = explode(".", $rrhh_permisos->getDiasPermisoContinuo());
            echo ((isset($parts_dias_contenuos[1])) ? (((int)$rrhh_permisos->getDiasPermisoContinuo() != 0) ? (int)$rrhh_permisos->getDiasPermisoContinuo() : '').' &frac12' : $rrhh_permisos->getDiasPermisoContinuo()); ?>
        </div>
    </div>
    
    <?php 
        if($rrhh_permisos->getObservacionesAutomaticas()!=''){
            echo '<hr/>'.$rrhh_permisos->getObservacionesAutomaticas(); 
        }
    ?>
</div>