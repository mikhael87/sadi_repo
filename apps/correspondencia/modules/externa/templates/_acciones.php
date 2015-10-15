<a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'externa/'.$correspondencia_correspondencia->getId().'/seguimiento'; ?>" title="Seguimiento">
    <?php echo image_tag('icon/goto.png'); ?>
</a>

<?php
    if ($correspondencia_correspondencia->getIdCreate()==$sf_user->getAttribute('usuario_id') &&
          ($correspondencia_correspondencia->getStatus()=='E' || 
          $correspondencia_correspondencia->getStatus()=='D')) { ?>
            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'externa/'.$correspondencia_correspondencia->getId().'/edit'; ?>" title="Editar">
                <?php echo image_tag('icon/edit.png'); ?>
            </a>  
            <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'externa/'.$correspondencia_correspondencia->getId().'/anular'; ?>" title="Anular" onclick="return confirm('¿Estas seguro de anular la correspondencia?');">
                <?php echo image_tag('icon/delete.png'); ?>
            </a> 
<?php } ?>

<br/><br/>

<div class="" style="position: relative;">
    <font class="f10n">Recibido por:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo $correspondencia_correspondencia->getUserUpdate(); ?><br/></font>
    <font class="f10n">Fecha:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($correspondencia_correspondencia->getCreatedAt())); ?><br/></font>
    <font class="f10n">Hora:</font><br/>
    <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($correspondencia_correspondencia->getCreatedAt())); ?></font>
</div>

