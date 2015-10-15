<?php if($parametros_correspondencia['accesible'] == 'S') { ?>

<?php
$formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
$tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
$parametros = sfYaml::load($tipo_formato[0]->getParametros());
?>

<?php if($correspondencia_correspondencia->getLeido()==0 && $correspondencia_correspondencia->getStatus()!='D') { ?>
<div id="abrir_<?php echo $correspondencia_correspondencia->getId(); ?>" style="text-align: center; vertical-align: middle;">
    <br/>
    <a href="#" onclick="javascript: fn_abrir(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;" title="Leer"><?php echo image_tag('icon/preview.png'); ?></a>
</div>

<div id="acciones_<?php echo $correspondencia_correspondencia->getId(); ?>" style="display: none;">
<?php } ?>

<a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida/'.$correspondencia_correspondencia->getId().'/seguimiento'; ?>" style="text-decoration: none" title="Seguimiento">
    <?php echo image_tag('icon/goto.png'); ?>
</a>

<?php if ($correspondencia_correspondencia->getStatus()!='D') { ?>
    
    <?php if($parametros['options_object']['responder']=='true'){ ?>
        <?php if ($parametros_correspondencia['reasignado'] == 0) { ?>
            <?php if ($correspondencia_correspondencia->getStatus()!='A') { ?>
                <a href="#" onclick="open_window_right(); asignar_correspondencia(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;" title="Asignar">
                    <?php echo image_tag('icon/asignar.png'); ?>
                </a>
            <?php } ?>
    
            <a href="#" onclick="open_window_right(); responder_correspondencia(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;" title="Responder">
                <?php echo image_tag('icon/mail_responder.png'); ?>
            </a>
<!--            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida/'.$correspondencia_correspondencia->getId().'/responder'; ?>" style="text-decoration: none" title="Responder">
                <?php echo image_tag('icon/mail_responder.png'); ?>
            </a>-->
        <?php } ?>
    <?php } ?>
    
    <?php
    
        if($parametros['options_object']['devolver']=='true'){

            if ($parametros_correspondencia['reasignado'] == 0) { ?>
                <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida/'.$correspondencia_correspondencia->getId().'/devolver'; ?>" style="text-decoration: none" title="Devolver">
                    <?php echo image_tag('icon/return.png'); ?>
                </a>
        <?php }}} ?>

<?php if($correspondencia_correspondencia->getLeido()==0 && $correspondencia_correspondencia->getStatus()!='D') { ?></div><?php } ?>
<?php } ?>


