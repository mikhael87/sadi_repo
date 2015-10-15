<?php use_helper('I18N', 'Date') ?>
<?php include_partial('alerta_visitante/assets') ?>

<div id="sf_admin_container">
  <h1>
      <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right', 'title' => 'Filtrar Unidades')); ?>&nbsp;
      <?php echo __('Visitantes en Alerta', array(), 'messages') ?>
  </h1>

  <?php include_partial('alerta_visitante/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('alerta_visitante/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('alerta_visitante/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('seguridad_alerta_visitante_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('alerta_visitante/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('alerta_visitante/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('alerta_visitante/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('alerta_visitante/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
