<?php use_helper('I18N', 'Date') ?>
<?php include_partial('ingresa/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<script>   
    function nuevo_ingreso(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/new',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
    
    function registrar_egreso(id) {
        $('#f_salida_'+id).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'20x20')); ?> Registrando egreso...');
        $.ajax({
                type: 'get',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/registrarSalida',
                data: {valor: id},
                success:function(json){
                    $("#f_salida_"+json.id).html(json.f_egreso);
                    $("#egreso_"+id).remove()
                }
            })
    }
    
    function registrar_egreso_equipo(id) {
        $('#f_salida_equipo_'+id).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'20x20')); ?> Registrando egreso...');
        $.ajax({
                type: 'get',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/registrarSalidaEquipo',
                data: {valor: id},
                success:function(json){
                    $("#f_salida_equipo_"+json.id).html(json.f_egreso);
                    $("#egreso_equipo_"+id).remove();
                }
            })
    }
</script>
<style type="text/css">
   #sf_admin_container ul li a {
        background-image:url(/images/icon/egreso.png);
        background-position:0 0;
        background-repeat:no-repeat no-repeat;
        padding-bottom:17px;
        padding-left:17px;
} 
</style>
<div id="sf_admin_container">
  <h1>
      <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
      <?php echo __('Listado de Visitantes', array(), 'messages') ?>
  </h1>

  <?php include_partial('ingresa/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('ingresa/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('ingresa/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('seguridad_ingreso_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('ingresa/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('ingresa/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('ingresa/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('ingresa/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
