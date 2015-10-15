<?php use_helper('I18N', 'Date') ?>
<?php include_partial('correlativos/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Correlativos de la Unidad', array(), 'messages') ?></h1>

  <?php include_partial('correlativos/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('correlativos/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <form action="<?php echo url_for('correspondencia_unidad_correlativo_collection', array('action' => 'batch')) ?>" method="post">

    <?php include_partial('correlativos/correlativos_list'); ?>
        
    <ul class="sf_admin_actions">
      <?php include_partial('correlativos/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('correlativos/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('correlativos/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
