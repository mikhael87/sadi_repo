<?php use_helper('jQuery'); ?>
<?php use_helper('I18N', 'Date') ?>
<?php include_partial('expediente/assets') ?>
<script type="text/javascript" src="/js/imgpreview.0.23.js"></script>

<script>
    $(document).ready(function (){
        $("#clon").change(function(){
            $("#quickfilter").submit();
        });

        $('.documents .prev').imgPreview({
            containerID: 'imgPreviewWithStyles',
            imgCSS: {
                width: 'auto'
            },
            preloadImages: false,
            distanceFromCursor: {top: 20, left:-300}
        });
    });
    
    function faltantes(id){
        $(".faltante_"+id).each(function() {
            if ($(this).is(":hidden")) {
                $(this).show("slow");
            } else {
                $(this).slideUp();
            }
        });
    };

    function accion_mostrar(id){
        $("#edit_"+id).each(function() {
            $(this).show();
        });
        $("#delete_"+id).each(function() {
            $(this).show();
        });
        $("#preview_"+id).each(function() {
            $(this).show();
        });
    };

    function accion_ocultar(id){
        $("#edit_"+id).each(function() {
            $(this).hide();
        });
        $("#delete_"+id).each(function() {
            $(this).hide();
        });
        $("#preview_"+id).each(function() {
            $(this).hide();
        });
    };
</script>

<style>
    #imgPreviewWithStyles {
        background: #222;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        padding: 10px;
        z-index: 999;
        border: none;
    }
</style>

<div id="sf_admin_container">
  <h1>
      <?php echo __('Expedientes Archivados', array(), 'messages') ?>
  </h1>

  <?php include_partial('expediente/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('expediente/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('expediente/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('archivo_expediente_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('expediente/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('expediente/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('expediente/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('expediente/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
