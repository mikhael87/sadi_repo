<?php use_helper('I18N', 'Date') ?>
<?php include_partial('usuario/assets') ?>

<script>
    $( document ).ready(function() {
        var inac= '<?php echo $sf_user->hasAttribute('func_inactivo'); ?>';
        if(!inac) {
            $('#link_active').css( "color", "black" );
            $('#link_inactive').css( "color", "#cacaca" );
        }else {
            $('#link_active').css( "color", "#cacaca" );
            $('#link_inactive').css( "color", "black" );
        }
    });
    
    function mostrar_cambio(id){ $("#foto_cambio_"+id).show(); };
    function ocultar_cambio(id){ 
        if ($("#foto_cambio_"+id).is(":visible"))
            $("#foto_cambio_"+id).hide();
    };
</script>

<div id="sf_admin_container">
  <h1>
    <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Funcionarios')); ?>&nbsp;
    <?php // echo __('Listado de Funcionarios y Personal', array(), 'messages') ?>
    <a id="link_active" style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_personas_url').'usuario/inactivos?inac=false' ?>">Listado de Usuarios activos</a>&nbsp;&nbsp;<font style="font-weight: lighter; color: #cacaca">|</font>&nbsp;&nbsp;<a id="link_inactive" style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_personas_url').'usuario/inactivos?inac=true' ?>">Listado de Usuarios inactivos</a>
  </h1>

  <?php include_partial('usuario/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('usuario/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('usuario/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('personas_persona_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('usuario/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('usuario/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('usuario/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('usuario/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
