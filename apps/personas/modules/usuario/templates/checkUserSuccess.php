<?php use_helper('jQuery'); ?>

<?php if($tipo_user=='siglas'){ ?>

    <a href="#" onClick="checkuserSiglas(<?php echo $id_usr; ?>); return false;" >Comprobar</a>
    &nbsp;&nbsp;<?php
    if ($usuario != 0)
        echo image_tag('icon/error.png', array('class' => 'icon_check'));
    else
        echo image_tag('icon/ok.png', array('class' => 'icon_check'));
    ?>&nbsp;
    <input name="aceptar" type="button" value="Aceptar" <?php echo ($usuario != 0)? 'disabled="disabled"' : '' ?> onClick="saveuser(<?php echo $id_usr; ?>,'<?php echo $nombre; ?>','siglas'); return false;" />

<?php }
