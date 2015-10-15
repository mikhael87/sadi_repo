<a class="vbsn" href="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>unidad">
    <?php echo image_tag('icon/mail_list.png'); ?>&nbsp;Volver a Unidades
</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a class="vbs" href="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo_tipo">
    <?php echo image_tag('icon/tipo.png'); ?>&nbsp;Tipos
</a>
&nbsp;&nbsp;
<a class="vbs" href="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo_condicion">
    <?php echo image_tag('icon/condicion.png'); ?>&nbsp;Condiciones
</a>
&nbsp;&nbsp;
<a class="vbs" href="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo_grado">
    <?php echo image_tag('icon/grado.png'); ?>&nbsp;Grados
</a>
&nbsp;&nbsp;
<a class="vbs" href="<?php echo sfConfig::get('sf_app_organigrama_url'); ?>cargo/moverMasivo">
    <?php echo image_tag('icon/group.png'); ?>&nbsp;Mover todos
</a>
&nbsp;&nbsp;
<br/><br/>

<script>
    $( document ).ready(function() {
        var inac= '<?php echo $sf_user->hasAttribute('inactivo'); ?>';
        if(!inac) {
            $('#link_active').css( "color", "black" );
            $('#link_inactive').css( "color", "#cacaca" );
        }else {
            $('#link_active').css( "color", "#cacaca" );
            $('#link_inactive').css( "color", "black" );
        }
    });
</script>