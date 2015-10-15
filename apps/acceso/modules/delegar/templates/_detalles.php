<?php
    $detalles = '';
    switch ($acceso_accion_delegada->getAccion()) {
        case "firmar_correspondencia":
            $accion = "Firmar Correspondencia";
            $detalles = "Con esta acción delegas la firma de la correspondencia a otra persona, 
                    es decir la persona que selecciones podra firmar todas aquellas correspondencias que salgan a tu nombre.<br/>
                    ¡Cerciorece de que la persona seleccionada sea de su gran confianza!.";
            break;
        case "administrar_rrhh":
            $accion = "Administracion de RRHH";
            $detalles = "Con esta acción delegas la visualizacion y edicion de los modulos de RRHH a otra persona, 
                    es decir la persona que selecciones podra visualizar los reportes de RRHH que permitas, asi como editar informacion.<br/>
                    ¡Cerciorece de que la persona seleccionada sea de su equipo de trabajo!.";
            break;
        case "gestionar_calendario":
            $accion = "Gestion del calendario";
            $detalles = "Con esta acción delegas la visualizacion o edicion de tu calendario a otra persona.<br/> 
                    Es decir la persona que selecciones podra visualizar o editar los eventos de tu calendario, asi como invitar funcionarios y personas a los mismos.<br/>
                    ¡Cerciorece de que la persona seleccionada sea de su equipo de trabajo!.";
            break;
        default:
            $accion = "Opción no valida";
    }
?>

<div style="position: relative; width: 180px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Acción
    </div>
    <div style="position: relative; font-size: 13px; left: 0px; font-size:14px; color: #00008B; font-weight: bold;" title="<?php echo $detalles; ?>">
        <?php echo $accion; ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Fecha de Expiración
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo date('d-m-Y', strtotime($acceso_accion_delegada->getFExpiracion())); ?>
    </div>

    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Estatus
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php 
            if($acceso_accion_delegada->getStatus()=='A'){
                if($acceso_accion_delegada->getFExpiracion()<date('Y-m-d')){
                    $acceso_accion_delegada->setStatus('E');
                    $acceso_accion_delegada->save();
                    
                    echo image_tag('icon/offline.png')." Expirado";
                } else {
                    echo image_tag('icon/online.png')." Activo";
                }
            }
            elseif($acceso_accion_delegada->getStatus()=='D')
                echo image_tag('icon/offline.png')." Deshabilitado"; 
            elseif($acceso_accion_delegada->getStatus()=='E')
                echo image_tag('icon/offline.png')." Expirado"; 
        ?>
        <br/>
    </div>
</div>
