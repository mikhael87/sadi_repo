<li class="sf_admin_action_solicitar_reposo">
    <a href="#" onclick="open_window_right(); solicitar_reposos(); return false;">Registrar Reposos</a>
</li>


<?php 
  $session_cargos = $sf_user->getAttribute('session_cargos');
  $ver_unidad = FALSE; 
  foreach ($session_cargos as $session_cargo) {
      if($session_cargo['cargo_grado_id']==99) $ver_unidad = TRUE; 
  }

  $ver_global = FALSE;
  $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
  foreach ($session_cargos as $session_cargo) {
      if($session_cargo['cargo_grado_id']==99 && $session_cargo['unidad_id']==$sf_oficinasClave['recursos_humanos']['seleccion']) {
        $ver_global = TRUE; 
      }
  }
  
  if($ver_global == FALSE || $ver_unidad == FALSE)
      $rrhh_delegados = Doctrine::getTable('Acceso_AccionDelegada')->findByUsuarioDelegadoIdAndAccionAndStatus($sf_user->getAttribute('usuario_id'), 'administrar_rrhh', 'A');
  
  if($ver_global == FALSE){
    foreach ($rrhh_delegados as $rrhh_delegado) {
        $parametros = sfYaml::load($rrhh_delegado->getParametros());
        if(isset($parametros['ver_reposos_global'])) $ver_global = TRUE;
    }
  }
  
  if($ver_unidad == FALSE){
    foreach ($rrhh_delegados as $rrhh_delegado) {
        $parametros = sfYaml::load($rrhh_delegado->getParametros());
        if(isset($parametros['ver_reposos_unidad'])) $ver_unidad = TRUE;
    }
  }
  
  if($ver_unidad == TRUE) { ?>
      <li class="sf_admin_action_reporte_unidad_reposos">
        <?php echo link_to(__('Reporte de la Unidad', array(), 'messages'), 'reposos/reporteGlobal?tipo=unidad', array()) ?>
      </li>
  <?php }

  if($ver_global == TRUE) { ?>
        <li class="sf_admin_action_reporte_global_reposos">
          <?php echo link_to(__('Reporte de Global', array(), 'messages'), 'reposos/reporteGlobal?tipo=global', array()) ?>
        </li>
<?php } ?>