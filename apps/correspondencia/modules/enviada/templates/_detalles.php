<div style="max-width: 200px; width: 200px;">

    <div style="position: relative; text-align: center; background-color: <?php echo $parametros_correspondencia['color'];?>; color: white;">
        <font class="f19b"><?php echo $parametros_correspondencia['texto']; ?></font>
    </div>

    <?php if($parametros_correspondencia['accesible'] == 'S') { ?>
        <div style="max-width: 200px; width: 200px; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
            <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
                <?php if($correspondencia_correspondencia->getFEnvio()) { ?>
                <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/cal_good.png'); ?></div>
                <font class="f10n">Fecha de Envio</font><br/>
                &nbsp;&nbsp;&nbsp;<?php echo date('d-m-Y h:i:s A', strtotime($correspondencia_correspondencia->getFEnvio())); ?>
                <?php } else { ?>
                <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/cal_bad.png'); ?></div>
                <font class="f10n">Fecha de Envio</font><br/>    
                &nbsp;&nbsp;&nbsp;<font style='color: #B40404;'>Aún sin enviar</font>
                <?php } ?>
            </div>

            <?php
                $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
                $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
            ?>

            <?php if(count($archivos)>0) { ?>
                <div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
                    <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/attach.png'); ?></div>
                    <font class="f10n">Adjuntos</font><br/>
                    <font class="f14n">
                        <?php foreach ($archivos as $archivo): ?>
                        &nbsp;&nbsp;&nbsp;&bull;<a class="tooltip" <?php echo ((strlen($archivo->getNombreOriginal()) > 28) ? "title='". $archivo->getNombreOriginal() . "'" : "" ); ?>  href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo ((strlen($archivo->getNombreOriginal()) > 28) ? substr($archivo->getNombreOriginal(),0,28).'...' : $archivo->getNombreOriginal()); ?></a><br/>
                        <?php endforeach; ?>
                    </font>&nbsp;
                </div>
            <?php } ?>

            <?php if(count($fisicos)>0) { ?>
                <div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
                    <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/package.png'); ?></div>
                    <font class="f10n">Fisicos</font><br/>
                        <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_correspondencia->getId().'/hojaRuta'; ?>" class="tooltip" title="Descargar acuse de recibido">
                        <?php foreach ($fisicos as $fisico): ?>
                        &nbsp;&nbsp;&nbsp;&bull;<font class="f14n"><?php echo $fisico->getTafnombre(); ?>: &nbsp;<?php echo $fisico->getObservacion(); ?></font><br/>
                        <?php endforeach; ?>
                        </a>
                        &nbsp;
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>