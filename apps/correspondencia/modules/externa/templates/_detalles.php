<div id="list_show_d_<?php echo $correspondencia_correspondencia->getId(); ?>" style="z-index: 100; position: relative; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
    <font class="f19b" id="status_font_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>"><?php echo $parametros_correspondencia['texto']; ?></font>
</div>

<div style="width: 200px;"></div>


<?php if ($sf_user->hasCredential(array('Archivo'), false)) { ?>
    <?php
    $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
    $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
    ?>

    <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
        <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/attach.png'); ?></div>
        <font class="f10n">Adjuntos</font><br/>
        <font class="f16n">
    <?php foreach ($archivos as $archivo): ?>
            &nbsp;&nbsp;&nbsp;<a href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo $archivo->getNombreOriginal(); ?></a><br/>
    <?php endforeach; ?>
        </font>&nbsp;
    </div>


    <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
        <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/package.png'); ?></div>
        <font class="f10n">Fisicos</font><br/>
    <?php foreach ($fisicos as $fisico): ?>
            &nbsp;&nbsp;&nbsp;<?php echo $fisico->getTafnombre(); ?>: &nbsp;<font class="f16n"><?php echo $fisico->getObservacion(); ?></font><br/>
    <?php endforeach; ?>
        &nbsp;
    </div>
<?php } else {
    $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
    if ($autorizacion[0]['id']) { ?>
        <?php if ($autorizacion[0]['leido'] > 0) { ?>
            <?php
            $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
            ?>

            <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
                <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/attach.png'); ?></div>
                <font class="f10n">Adjuntos</font><br/>
                <font class="f16n">
            <?php foreach ($archivos as $archivo): ?>
                    &nbsp;&nbsp;&nbsp;<a href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo $archivo->getNombreOriginal(); ?></a><br/>
            <?php endforeach; ?>
                </font>&nbsp;
            </div>


            <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
                <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/package.png'); ?></div>
                <font class="f10n">Fisicos</font><br/>
                <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'externa/'.$correspondencia_correspondencia->getId().'/hojaRuta'; ?>" class="tooltip" title="Descargar acuse de recibido">
            <?php foreach ($fisicos as $fisico): ?>
                    &nbsp;&nbsp;&nbsp;<?php echo $fisico->getTafnombre(); ?>: &nbsp;<font class="f16n"><?php echo $fisico->getObservacion(); ?></font><br/>
            <?php endforeach; ?>
                </a>
                &nbsp;
            </div>

        <?php } else { ?>
            <div id="detalles_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>"></div>
        <?php } ?>
    <?php }else {
            if($sf_user->getAttribute('funcionario_id') == $correspondencia_correspondencia->getIdCreate()) { ?>
                <div class="sf_admin_form_row sf_admin_text" style="position: relative; width: 200px;">
                    <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/package.png'); ?></div>
                    <font class="f10n">Fisicos</font><br/>
                    <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'externa/'.$correspondencia_correspondencia->getId().'/hojaRuta'; ?>" class="tooltip" title="Descargar acuse de recibido">
                <?php foreach ($fisicos as $fisico): ?>
                        &nbsp;&nbsp;&nbsp;<?php echo $fisico->getTafnombre(); ?>: &nbsp;<font class="f16n"><?php echo $fisico->getObservacion(); ?></font><br/>
                <?php endforeach; ?>
                    </a>
                    &nbsp;
                </div>
                <?php
            }
    } ?>
<?php } ?>
