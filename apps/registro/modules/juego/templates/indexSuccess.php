<?php use_helper('I18N', 'Date') ?>
<?php include_partial('juego/assets') ?>

<div id="sf_admin_container">
  <h1>
  <?php echo image_tag('icon/find24', array('onclick' => '$(".sf_admin_filter").dialog("open")', 'style' => 'cursor:pointer; text-align: right; vertical-align: middle', 'title' => 'Filtrar Juegos')); ?>&nbsp;
  <?php echo __('Juegos registrados', array(), 'messages') ?></h1>

  <?php include_partial('juego/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('juego/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('juego/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('juego/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('juego/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('juego/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('juego/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
