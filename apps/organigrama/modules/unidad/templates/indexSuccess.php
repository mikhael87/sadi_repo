<?php use_helper('I18N', 'Date') ?>
<script src="/js/jqueryTooltip.js" type="text/javascript"></script>
<?php include_partial('unidad/assets') ?>

<div id="sf_admin_container">
  <h1>
    <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
    <?php echo __('Estructura Organizativa', array(), 'messages') ?>
  </h1>

  <?php include_partial('unidad/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('unidad/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('unidad/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('organigrama_unidad_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('unidad/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper, 'unidades_orden' => $unidades_orden)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('unidad/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('unidad/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('unidad/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
