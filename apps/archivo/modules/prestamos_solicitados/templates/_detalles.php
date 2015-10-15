<?php
$archivo_prestamo_archivo = Doctrine::getTable('Archivo_PrestamoArchivo')->findByExpedienteIdAndFuncionarioId($archivo_expediente->getId(),$sf_user->getAttribute('funcionario_id'));
$prestamista = Doctrine::getTable('Acceso_Usuario')->find($archivo_prestamo_archivo[0]->getIdUpdate());
?>

<div style="position: relative; width: 100px;">
    <div style="position: relative; font-size: 8px; left: 0px;">
        Prestamo realizado por:
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo $prestamista->getNombre(); ?>
    </div>
    
    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Fecha de prestamo
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php echo date('d-m-Y h:i:s A', strtotime($archivo_prestamo_archivo[0]->getCreatedAt())); ?>
    </div>
    
    <?php 
    $dias_faltantes = (strtotime(date('Y-m-d'))-strtotime($archivo_prestamo_archivo[0]->getFExpiracion()))/86400;
    $dias_faltantes = abs($dias_faltantes); 
    $dias_faltantes = floor($dias_faltantes);
    if(date('Y-m-d')>$archivo_prestamo_archivo[0]->getFExpiracion()) $dias_faltantes *= -1;

    $rayado = 'underline';
    if($dias_faltantes>14)
        $color = '#00FF00';
    elseif ($dias_faltantes>7) 
        $color = '#D7DF01';
    elseif($dias_faltantes>0)
        $color = '#FF0000';
    else {
        $color = '#FF0000';
        $rayado = 'line-through';
    }
    ?>
    
    <div style="position: relative; font-size: 8px; left: 0px;">
        <br/>Fecha de expiración
    </div>
    <div style="position: relative; font-size: 13px; font-weight: bold; left: 0px; color: <?php echo $color; ?>; text-decoration: <?php echo $rayado; ?>;">
        <?php echo date('d-m-Y', strtotime($archivo_prestamo_archivo[0]->getFExpiracion())); ?>
    </div>    
</div>