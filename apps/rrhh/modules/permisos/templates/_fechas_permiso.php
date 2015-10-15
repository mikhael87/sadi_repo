<div style="position: relative; font-size: 13px; width: 240px;">
    <div style="position: relative;">
        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
            Fecha de inicio
        </div>
        <div style="position: relative; padding-top: 10px; left: 0px;">
            <?php echo date('d-m-Y', strtotime($rrhh_permisos->getFInicioPermiso())); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 80px;">
            Fecha final
        </div>
        <div style="position: absolute; top: 10px; left: 80px;">
            <?php echo date('d-m-Y', strtotime($rrhh_permisos->getFFinPermiso())); ?>
        </div>
        
        <div style="position: absolute; font-size: 8px; top: 0px; left: 160px;">
            Fecha de retorno
        </div>
        <div style="position: absolute; top: 10px; left: 160px;">
            <?php echo date('d-m-Y', strtotime($rrhh_permisos->getFRetornoPermiso())); ?>
        </div>
    </div>
</div>