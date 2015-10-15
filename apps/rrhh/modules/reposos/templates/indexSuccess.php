<?php use_helper('I18N', 'Date') ?>
<?php include_partial('reposos/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<script>   
    function solicitar_reposos(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>reposos/solicitar',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Reposos Registrados', array(), 'messages') ?></h1>

  <?php include_partial('reposos/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('reposos/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <?php include_partial('reposos/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('reposos/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('reposos/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('reposos/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
