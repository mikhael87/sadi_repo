<?php use_helper('I18N', 'Date') ?>
<?php include_partial('serie_documental/assets') ?>

<div id="sf_admin_container">
  <h1>Series Documentales Inactivas</h1>

  <?php include_partial('serie_documental/flashes') ?>

  <div id="sf_admin_header"></div>

  <div id="sf_admin_bar"></div>

  <div id="sf_admin_content">
        
    <?php include_partial('serie_documental/series_inactivas_list'); ?>

    <ul class="sf_admin_actions">
        <li class="sf_admin_action_regresar_modulo">
            <a href="<?php echo sfConfig::get('sf_app_archivo_url'); ?>serie_documental/index">Regresar a Series Documentales</a>
        </li>
    </ul>
  </div>

  <div id="sf_admin_footer"></div>
</div>
