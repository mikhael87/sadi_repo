<?php use_helper('I18N', 'Date') ?>
<?php include_partial('externa/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<?php use_helper('jQuery'); ?>

<script>   
    function fn_abrir(id,idr){
        $("#formato_sin_leer_"+id).hide();
        
        $.ajax({
            url:'externa/abrirFormato',
            type:'POST',
            dataType:'html',
            data:'id='+id+'&idr='+idr,
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
        
        if ($("#status_font_sin_leer_"+id).text() == "ENVIADA")
        { 
            $("#status_font_sin_leer_"+id).text("RECIBIDO");   
            $("#list_show_i_"+id).css("background-color", "#2E9AFE");
            $("#formato_leido_titulo_"+id).css("background-color", "#2E9AFE");
            $("#list_show_d_"+id).css("background-color", "#2E9AFE");
        }
        
        $("#formato_sin_leer_gif_"+id).show();
        $("#abrir_"+id).hide();
        $("#acciones_"+id).fadeIn(1000);
    };
    
    function archivar_correspondencia(id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Preparando para archivar...');
    
        $.ajax({
            url:'externa/archivar',
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
      <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
      <?php echo __('Correspondencia y Solicitudes Externas', array(), 'messages') ?>
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

  <div id="div_flashes"><?php include_partial('externa/flashes') ?></div>

  <div id="sf_admin_header">
    <?php include_partial('externa/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('externa/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('externa/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('externa/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('externa/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('externa/list_footer', array('pager' => $pager)) ?>
  </div>
</div>