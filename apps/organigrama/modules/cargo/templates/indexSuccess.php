<?php use_helper('I18N', 'Date') ?>
<?php include_partial('cargo/assets') ?>

<div id="sf_admin_container">
  <!--<h1><?php // echo __('Cargos de la Unidad', array(), 'messages') ?></h1>-->
    <h1><a id="link_active" style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_organigrama_url').'cargo/inactivos?inac=false' ?>">Cargos activos de la Unidad</a>&nbsp;&nbsp;<font style="font-weight: lighter; color: #cacaca">|</font>&nbsp;&nbsp;<a id="link_inactive" style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_organigrama_url').'cargo/inactivos?inac=true' ?>">Cargos inactivos de la Unidad</a></h1>

  <?php include_partial('cargo/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('cargo/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <?php include_partial('cargo/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('cargo/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('cargo/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('cargo/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
