<?php use_helper('I18N', 'Date') ?>
<?php include_partial('nomina/assets') ?>

<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/nomina/lib/validator_nomina.php'); ?>

<div id="sf_admin_container">
  <h1><?php echo __('Nuevo Participante', array(), 'messages') ?></h1>

  <?php include_partial('nomina/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('nomina/form_header', array('personas_persona' => $personas_persona, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('nomina/form', array('personas_persona' => $personas_persona, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('nomina/form_footer', array('personas_persona' => $personas_persona, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>
</div>
