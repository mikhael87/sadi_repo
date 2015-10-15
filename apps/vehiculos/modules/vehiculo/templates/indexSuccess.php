<?php use_helper('I18N', 'Date') ?>
<?php include_partial('vehiculo/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<script>   
    function nuevo_vehiculo(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>vehiculo/new',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }}); 
    }
    
    function conmutar(id){
        div = $('#div_dinamico_'+id);
        if(div.css('display') === 'none') {
            conductores_activos(id);
            $('#div_dinamico_'+id).show();
        }else
            $('#div_dinamico_'+id).hide();
    }
    
    function conductores_activos (id) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>vehiculo/conductoresAct',
            type:'POST',
            dataType:'html',
            data: 'id='+id,
            beforeSend: function(Obj){
                $('#content_conductores_'+id).html('<img src="/images/icon/cargando.gif" />&nbsp;<font style="color: #666; font-size: 12px">Cargando funcionarios...</font>');
            },
            success:function(data, textStatus){
                $('#content_conductores_'+id).html(data)
            }});
    }
    
    function guardar_conductores(id) {
            var cadena_select= '';
            $(".conductores_list_"+id).each(function () {
                if ($(this).is(':checked')) {
                    cadena_select= cadena_select+ $(this).val()+'#';
                }
            });
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>vehiculo/guardarConductores',
                type:'POST',
                dataType:'html',
                data: 'cadena='+cadena_select+'&vehiculo_id='+id,
                success:function(data, textStatus){
                    $('#list_conductores_refresh_'+id).html(data);
                    conmutar(id);
                }});
    }
</script>

<div id="sf_admin_container">
  <h1>
      <?php // echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
      <?php echo __('Lista de Vehículos', array(), 'messages') ?>
  </h1>

  <?php include_partial('vehiculo/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('vehiculo/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('vehiculo/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('vehiculo/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('vehiculo/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('vehiculo/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('vehiculo/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
