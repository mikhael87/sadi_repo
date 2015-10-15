<?php $asignados=Doctrine::getTable('Correspondencia_Receptor')->receptoresPorCorrespondenciaUnidadEstablecido($correspondencia_correspondencia->getId(),$parametros_correspondencia['unidad_recibe_id'],array('A')); ?>

<div id="list_show_d_<?php echo $correspondencia_correspondencia->getId(); ?>" style="z-index: 100; position: relative; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
    <font class="f19b" id="status_font_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>"><?php echo $parametros_correspondencia['texto']; ?></font>
</div>



<div style="width: 200px;"></div>

<?php if(count($asignados)>0){ ?>    
<div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
    <?php 
    foreach ($asignados as $asignado){ 
        $asignado_tarea=Doctrine::getTable('Comunicaciones_Tarea')->tareaDeCorrespondenciaPorFuncionario($correspondencia_correspondencia->getId(), $asignado->getFuncionarioId());
        $asignado_datos=Doctrine::getTable('Funcionarios_Funcionario')->find($asignado->getFuncionarioId());
        
        $asignado_tarea = $asignado_tarea[0];
    ?>
        <div style="position: absolute; top: 20px; left: 0px;"><?php echo image_tag('icon/asignar.png'); ?></div>
        <font class="f10n">Asignado en Fecha: <?php echo date('d-m-Y h:i:s A', strtotime($asignado->getCreatedAt())); ?></font><br/>
        <?php 
            if($asignado->getFRecepcion()=='') { 
                $color_asigando = '#FF0000'; 
                $title_asignado = "<font style='color: #FF0000;'><b>Aun no ha sido leido por el asignado.</b></font><br/>".$asignado_tarea->getDescripcion();
            } else { 
                $color_asigando = '#000000'; 
                $title_asignado = "Leido en Fecha: ".date('d-m-Y h:i:s A', strtotime($asignado->getFRecepcion()))."<br/>".$asignado_tarea->getDescripcion();
            } 
        ?>
        <font class="tooltip" title="<?php echo $title_asignado; ?>" style="color: <?php echo $color_asigando; ?>;">&nbsp;&nbsp;&nbsp;<?php echo $asignado_datos->getPrimerNombre().' '.$asignado_datos->getPrimerApellido(); ?></font>
        <br/>
    <?php } ?>
</div>
<?php } ?>   

<?php if($correspondencia_correspondencia->getLeido()>0) { 

    $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
    $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
?>

<div style="max-width: 200px; width: 200px; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
  
<?php if(count($archivos)>0) { ?>
<div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
    <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/attach.png'); ?></div>
    <font class="f10n">Archivos adjuntos</font><br/>
    <font class="f14n">
        <?php foreach ($archivos as $archivo): ?>
        &nbsp;&nbsp;&nbsp;<a class="tooltip" <?php echo ((strlen($archivo->getNombreOriginal()) > 28) ? "title='". $archivo->getNombreOriginal() . "'" : "" ); ?>  href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo ((strlen($archivo->getNombreOriginal()) > 28) ? substr($archivo->getNombreOriginal(),0,28).'...' : $archivo->getNombreOriginal()); ?></a><br/>
        <?php endforeach; ?>
    </font>&nbsp;
</div>
<?php } else { ?>
    <font class="f10n">Sin archivos adjuntos.</font>
<?php } ?>
    
<?php if(count($fisicos)>0) { ?>
<div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
    <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/package.png'); ?></div>
    <font class="f10n">Fisicos</font><br/>
        <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/hojaRuta'; ?>" class="tooltip" title="Descargar acuse de recibido">
        <?php foreach ($fisicos as $fisico): ?>
        &nbsp;&nbsp;&nbsp;<font class="f14n"><?php echo $fisico->gettafnombre(); ?>: &nbsp;<?php echo $fisico->getobservacion(); ?></font><br/>
        <?php endforeach; ?>
        </a>
        &nbsp;
</div>
<?php } else { ?>
    <br/><font class="f10n">Sin envio fisicos.</font>
<?php } ?>
</div>
<?php } else { ?>
    <div id="detalles_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>"></div>
<?php } ?>
