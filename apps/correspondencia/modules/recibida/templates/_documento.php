<div style="position: relative; min-width: 600px; max-width: 600px;">

    <?php
        $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
        $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
        $parametros = sfYaml::load($tipo_formato[0]->getParametros());
        $oficinas_clave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
    ?>

    <div id="formato_leido_titulo_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: relative; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
        <font class="f19b"><?php  echo strtr(strtoupper($tipo_formato[0]->getNombre()),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"); ?></font>
        <?php
        if($correspondencia_correspondencia->getPrioridad()== 'U')
            echo image_tag('icon/stick_urgent.png', array('style'=>'position: absolute; right: 2px; top: 2px'));
        
        $unidades_hijas = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,array($correspondencia_correspondencia->getEmisorUnidadId()));
    
        if($oficinas_clave['auditoria_interna']['seleccion'] === $correspondencia_correspondencia->getEmisorUnidadId() || in_array($oficinas_clave['auditoria_interna']['seleccion'], $unidades_hijas))
            echo image_tag('icon/sncf.png', array('style'=>'position: absolute; right: 10px; top: 20px; opacity: 0.3'));
        ?>
    </div>

    <?php if($correspondencia_correspondencia->getLeido()>0) $display = 'block;'; else $display = 'none;'; ?>

    <?php if($parametros_correspondencia['accesible'] == 'S') { ?>
        <div style="position: absolute; left: 5px; top: 0px; z-index: 100; display: <?php echo $display ?>" id="formato_leido_descargas_<?php echo $correspondencia_correspondencia->getId(); ?>">
             <?php if($parametros['options_object']['descargas']['pdf']=='true') { ?>
             <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$formato[0]->getId(); ?>" title="Descargar en PDF">
                <?php echo image_tag('icon/pdf.png'); ?>
             </a>
            <?php } if($parametros['options_object']['descargas']['odt']=='true') { ?>
             <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/odt?id='.$formato[0]->getId(); ?>" title="Descargar en ODT (Linux - Canaima - Writer)">
                <?php echo image_tag('icon/odt.png'); ?>
             </a>
            <?php } if($parametros['options_object']['descargas']['doc']=='true') { ?>
             <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/doc?id='.$formato[0]->getId(); ?>" title="Descargar en DOC (Windows - Word)">
                <?php echo image_tag('icon/doc.png'); ?>
             </a>
            <?php } ?>
        </div>
    
        <?php if($correspondencia_correspondencia->getLeido()>0 || $correspondencia_correspondencia->getStatus()=='D') { ?>
            <div id="formato_leido_<?php echo $correspondencia_correspondencia->getId(); ?>" style="min-width: 600px; max-width: 600px; max-height: 250px; overflow-y: auto; overflow-x: hidden; z-index: 100">
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
            <div id="formato_sin_leer_<?php echo $correspondencia_correspondencia->getId(); ?>" style="min-width: 600px;"><br/>
                <?php
                if($correspondencia_correspondencia->getPrioridad()== 'U'):
                    echo image_tag('icon/urgent.png', array('style'=>'padding-left: 50%; opacity: 0.7; filter:alpha(opacity=70)'));
                endif;
                ?>
            </div>
            <div id="formato_sin_leer_gif_<?php echo $correspondencia_correspondencia->getId(); ?>" style="left: 290px; top: 60px; position: absolute; display: none;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?></div>
        <?php } ?>
    <?php } else { ?>
            <div style="left: 280px; top: 35px; position: absolute;"><?php echo image_tag('icon/file_locked_64_confidencial.png',"title='Este documento es confidencial.'"); ?></div>
    <?php } ?>
</div>


