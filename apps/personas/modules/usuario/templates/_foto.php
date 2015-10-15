<div id="td_f_<?php echo $personas_persona->getId(); ?>" style="position: relative; width: 84px; height: 98px;" onmouseout ="javascript:ocultar_cambio(<?php echo $personas_persona->getId(); ?>)">
    <div style="position: absolute; top: 2px; left: 2px; width: 80px; height: 96px;" onmouseover ="mostrar_cambio(<?php echo $personas_persona->getId(); ?>)">
        <?php if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$personas_persona->getCi().'.jpg')){ ?>
            <img src="/images/fotos_personal/<?php echo $personas_persona->getCi(); ?>.jpg" width="80"/><br/>
        <?php } else { ?>
            <img src="/images/other/siglas_photo_small_<?php echo $personas_persona->getSexo().substr($personas_persona->getCi(), -1); ?>.png" width="80"/><br/>
        <?php } ?>
    </div>
    <div style="position: absolute; top: 2px; left: 2px; width: 80px; display: none; background-color: black;" id="foto_cambio_<?php echo $personas_persona->getId(); ?>" onmouseover ="javascript:mostrar_cambio(<?php echo $personas_persona->getId(); ?>)">
        <img src="/images/icon/photo.png" style="vertical-align: middle;"/>
        <a class="" style="color: white;" href="<?php echo sfConfig::get('sf_app_personas_url'); ?>usuario/<?php echo $personas_persona->getId(); ?>/foto">Cambiar</a>        
    </div>
</div>