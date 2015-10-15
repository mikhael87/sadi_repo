<?php use_helper('I18N', 'Date') ?>
<?php include_partial('serie_documental/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<?php use_helper('jQuery'); ?>

<script>   
    function transferir_serie(id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Preparando para transferir...');
    
        $.ajax({
            url:'serie_documental/transferirSerie',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                $("#content_window_right").html(data);
            }})
    }

    function duplicar_serie(id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Preparando duplicación...');
    
        $.ajax({
            url:'serie_documental/duplicarSerie',
            type:'POST',
            dataType:'html',
            data:'id='+id,
            success:function(data, textStatus){
                $("#content_window_right").html(data);
            }})
    }
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Series Documentales', array(), 'messages') ?></h1>

  <div id="div_flashes"><?php include_partial('serie_documental/flashes') ?></div>

  <div id="sf_admin_header">
    <?php include_partial('serie_documental/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('serie_documental/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('archivo_serie_documental_collection', array('action' => 'batch')) ?>" method="post">
    <?php // include_partial('serie_documental/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
        
    <?php include_partial('serie_documental/series_list'); ?>

    <ul class="sf_admin_actions">
      <?php include_partial('serie_documental/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('serie_documental/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('serie_documental/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
