<?php use_helper('I18N', 'Date') ?>
<?php include_partial('grupos/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Grupo autorizado de archivo para la unidad', array(), 'messages') ?></h1>

  <?php include_partial('grupos/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('grupos/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <form action="<?php echo url_for('archivo_funcionario_unidad_collection', array('action' => 'batch')) ?>" method="post">
    <?php //include_partial('grupos/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
        
    <?php include_partial('grupos/grupos_list'); ?>
        
    <ul class="sf_admin_actions">
      <?php include_partial('grupos/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('grupos/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('grupos/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
