<?php use_helper('I18N', 'Date') ?>
<?php include_partial('prestamos_solicitados/assets') ?>

<script>
    $(document).ready(function (){
        $("#clon").change(function(){
            $("#quickfilter").submit();
        });
});
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Expedientes Solicitados', array(), 'messages') ?></h1>

  <?php include_partial('prestamos_solicitados/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('prestamos_solicitados/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('prestamos_solicitados/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('prestamos_solicitados/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('prestamos_solicitados/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('prestamos_solicitados/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('prestamos_solicitados/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
