<div style="position: relative; width: 100px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Tipo de Prestamo
    </div>
    <div style="position: relative; left: 0px;">
        <?php 
        if($archivo_prestamo_archivo->getFisico()==TRUE && $archivo_prestamo_archivo->getDigital()==TRUE)
            echo "Fisico y Digital";
        elseif ($archivo_prestamo_archivo->getFisico()==TRUE)
            echo "Fisico";
        elseif ($archivo_prestamo_archivo->getDigital()==TRUE)
            echo "Digital";
        ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Fecha de Expiración
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo date('d-m-Y', strtotime($archivo_prestamo_archivo->getFExpiracion())); ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Estatus
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php 
            if($archivo_prestamo_archivo->getStatus()=='A'){
                if($archivo_prestamo_archivo->getFExpiracion()<date('Y-m-d')){
                    $archivo_prestamo_archivo->setStatus('E');
                    $archivo_prestamo_archivo->save();
                    
                    echo image_tag('icon/offline.png')." Expirado";
                } else {
                    echo image_tag('icon/online.png')." Activo";
                }
            }
            elseif($archivo_prestamo_archivo->getStatus()=='D')
                echo image_tag('icon/offline.png')." Deshabilitado"; 
            elseif($archivo_prestamo_archivo->getStatus()=='E')
                echo image_tag('icon/offline.png')." Expirado"; 
        ?>
        <br/>
    </div>
</div>
