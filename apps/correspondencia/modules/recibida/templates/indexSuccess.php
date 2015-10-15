<?php use_helper('I18N', 'Date') ?>
<?php include_partial('recibida/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<?php use_helper('jQuery'); ?>

<script>
    function verCorrespondencia(id)
    {
        valor = document.getElementById("nrecepcion_"+id).value;
        document.location = "<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida/'; ?>"+id+"/ver?nr="+valor;
    }
    
    function fn_abrir(id){
        $.ajax({
            url:'recibida/abrirFormato',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                $("#formato_sin_leer_gif_"+id).hide();
                jQuery('#formato_sin_leer_'+id).hide().html(data).fadeIn(1000);
            }})
        
        $.ajax({
            url:'recibida/abrirDetalles',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                jQuery('#detalles_sin_leer_'+id).hide().html(data).fadeIn(1000);
            }})
        
        if ($("#status_font_sin_leer_"+id).text() == "SIN LEER")
        { 
            $("#status_font_sin_leer_"+id).text("LEÍDA");   
            $("#list_show_i_"+id).css("background-color", "#2E9AFE");
            $("#formato_leido_titulo_"+id).css("background-color", "#2E9AFE");
            $("#list_show_d_"+id).css("background-color", "#2E9AFE");
        }
        
        $("#formato_leido_descargas_"+id).show();
        $("#formato_sin_leer_gif_"+id).show();
        $("#abrir_"+id).hide();
        $("#acciones_"+id).fadeIn(1000);
    };
    
    $(document).ready(function (){
        $("#correspondencia_correspondencia_filters_formato option").clone().appendTo("#clon");
        $('#clon option:first').html('Todos los documentos');
        $("#correspondencia_correspondencia_filters__csrf_token2").val($("#correspondencia_correspondencia_filters__csrf_token").val())

        $("#clon").change(function(){
            if($('#clon').val() === '')
                    location.href= '<?php echo sfConfig::get('sf_app_correspondencia_url') ?>recibida/filter/action?_reset&_csrf_token?='+$("#correspondencia_correspondencia_filters__csrf_token").val();
                else
            $("#quickfilter").submit();
        });

        $("#correspondencia_correspondencia_filters_statusRecepcion option").clone().appendTo("#clon_status_recepcion");
            $('#clon_status_recepcion option:first').html('Todos los documentos');

                $("#clon_status_recepcion").change(function(){
                    if($('#clon_status_recepcion').val() === '')
                        location.href= '<?php echo sfConfig::get('sf_app_correspondencia_url') ?>recibida/filter/action?_reset&_csrf_token?='+$("#correspondencia_correspondencia_filters__csrf_token").val();
                    else
                    $("#quickfilter").submit();
                });
    });
    
    function asignar_correspondencia(id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando...');
    
        $.ajax({
            url:'recibida/asignar',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                $("#content_window_right").html(data);
            }})
    }
</script>

<div id="sf_admin_container">
  <h1>
      <?php echo __('Correspondencia y Solicitudes recibidas', array(), 'messages') ?>
  </h1>

  <div style="position: relative;">  
    <div style="position: absolute; top: -45px; right: 0px; text-align: right; padding: 0px;" class="trans">
        <?php
            $unidades_autorizadas = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($sf_user->getAttribute('funcionario_id'));

            foreach ($unidades_autorizadas as $unidad_autorizada){
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_autorizada->getAutorizadaUnidadId());
                echo $unidad->getNombre().'. ';
                echo ' Leer: ';
                if($unidad_autorizada->getLeer()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo ' Redactar: ';
                if($unidad_autorizada->getRedactar()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo ' Firmar: ';
                if($unidad_autorizada->getFirmar()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo '<br/>';
            } 
            
            if(count($unidades_autorizadas)==0)
                echo '<fond class="rojo">NO esta autorizado en ningún grupo de correspondencia.</fond>';
        ?>
    </div>
  </div>
    
  <?php include_partial('recibida/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('recibida/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('recibida/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('correspondencia_correspondencia_recibida_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('recibida/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('recibida/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('recibida/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('recibida/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
