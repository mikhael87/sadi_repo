<div style="max-width: 200px; width: 200px; max-height: 250px; overflow-y: auto; overflow-x: hidden;">
<?php if(count($archivos)>0) { ?>
    <div class="sf_admin_form_row sf_admin_text" style="position: relative; max-width: 200px;">
        <div style="position: absolute; top: 22px; left: 0px;"><?php echo image_tag('icon/attach.png'); ?></div>
        <font class="f10n">Archivos adjuntos</font><br/>
        <font class="f14n">
            <?php foreach ($archivos as $archivo): ?>
            &nbsp;&nbsp;&nbsp;<a href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo $archivo->getNombreOriginal(); ?></a><br/>
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
            <?php foreach ($fisicos as $fisico): ?>
            &nbsp;&nbsp;&nbsp;<font class="f14n"><?php echo $fisico->gettafnombre(); ?>: &nbsp;<?php echo $fisico->getobservacion(); ?></font><br/>
            <?php endforeach; ?>
            &nbsp;
    </div>
<?php } else { ?>
    <br/><font class="f10n">Sin envio fisicos.</font>
<?php } ?>
</div>
