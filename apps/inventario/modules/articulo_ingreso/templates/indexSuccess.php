<?php use_helper('I18N', 'Date') ?>
<?php include_partial('articulo_ingreso/assets') ?>

<div id="sf_admin_container">
  <h1>
    <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
    <?php echo __('Compras realizadas', array(), 'messages') ?>
  </h1>

  <?php include_partial('articulo_ingreso/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('articulo_ingreso/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('articulo_ingreso/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('inventario_articulo_ingreso_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('articulo_ingreso/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('articulo_ingreso/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('articulo_ingreso/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('articulo_ingreso/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
