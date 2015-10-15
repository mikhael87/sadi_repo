<?php
    use_helper('jQuery');

    $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
    $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
    $parametros = sfYaml::load($tipo_formato[0]->getParametros());
?>


<div style="position: relative; min-width: 600px; max-width: 600px;">
    <div id="formato_leido_titulo_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: relative; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
        <font class="f19b"><?php  echo strtr(strtoupper($tipo_formato[0]->getNombre()),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></font>
        <?php
        if($correspondencia_correspondencia->getPrioridad()== 'U')
            echo image_tag('icon/stick_urgent.png', array('style'=>'position: absolute; right: 2px; top: 2px'));
        ?>
    </div>

    <?php if ($sf_user->hasCredential(array('Archivo'), false)) { ?>

        <div id="formato_leido_<?php echo $correspondencia_correspondencia->getId(); ?>" style="min-width: 600px; max-width: 600px; z-index: 100;">
            <div style="left: 290px; top: 60px; position: absolute;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?></div>
        </div>
        <?php use_helper('jQuery'); ?>
        <script>
            <?php
            echo jq_remote_function(array('update' => 'formato_leido_'.$correspondencia_correspondencia->getId(),
            'url' => 'formatos/show',
            'with'     => "'idc=".$correspondencia_correspondencia->getId()."'"))
            ?>
        </script>

    <?php } else { ?>

        <?php if ($autorizacion[0]['id']) { ?>

            <?php if ($autorizacion[0]['leido'] > 0) { ?>
                <div id="formato_leido_<?php echo $correspondencia_correspondencia->getId(); ?>" style="min-width: 600px; max-width: 600px; z-index: 100; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                    <div style="left: 290px; top: 60px; position: absolute;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?></div>
                </div>
                <?php use_helper('jQuery'); ?>
                <script>
                    <?php
                    echo jq_remote_function(array('update' => 'formato_leido_'.$correspondencia_correspondencia->getId(),
                    'url' => 'formatos/show',
                    'with'     => "'idc=".$correspondencia_correspondencia->getId()."'"))
                    ?>
                </script>
            <?php } else { ?>

                <div id="formato_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: relative; min-width: 600px; max-width: 600px; min-height: 130px; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                    <div style="position: relative; width: 100%; top: 35px; left: 280px;">
                        <a href="#" onclick="javascript: fn_abrir(<?php echo $correspondencia_correspondencia->getId(); ?>,<?php echo $autorizacion[0]['id']; ?>); return false;" title="Leer"><?php echo image_tag('icon/preview.png'); ?></a>
                    </div>
                </div>
                <div id="formato_sin_leer_gif_<?php echo $correspondencia_correspondencia->getId(); ?>" style="left: 290px; top: 60px; position: absolute; display: none;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?></div>
            <?php } ?>

        <?php } else { ?>

            <div id="formato_sin_autorizacion_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: relative; min-width: 600px; max-width: 600px; min-height: 130px; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                <div style="position: relative; width: 100%; top: 35px; left: 280px;">
                    <?php echo image_tag('icon/file_locked_64.png',"title='Actualmente tiene acceso unicamente a visualizar el seguimiento y la información básica.'"); ?>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>