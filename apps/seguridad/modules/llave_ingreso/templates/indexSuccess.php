<?php use_helper('I18N', 'Date') ?>
<?php include_partial('llave_ingreso/assets') ?>

<script>
    function nuevos_pases() {
        $('#div_form_pases').toggle();
    }
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Pases de Ingreso', array(), 'messages') ?></h1>

  <?php include_partial('llave_ingreso/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('llave_ingreso/list_header', array('pager' => $pager)) ?>
      <div style="position: relative;">
          <div id="div_form_pases" style="position: absolute; display: none; background-color: #FCFCFC; border: 1px solid; top: 25px; padding: 5px;">
              <form action="<?php echo sfConfig::get('sf_app_seguridad_url'); ?>llave_ingreso/generarPasesIngreso'">
                Cantidad de pases de ingreso &nbsp;&nbsp;
                <input name="n_pases_nuevos" type="text" size="4" maxlength="4"/>
                <input type="submit" value="Generar"/>
              </form>
          </div>
      </div>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('seguridad_llave_ingreso_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('llave_ingreso/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('llave_ingreso/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('llave_ingreso/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('llave_ingreso/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
