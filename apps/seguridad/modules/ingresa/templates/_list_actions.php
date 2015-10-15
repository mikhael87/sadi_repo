<?php if ($sf_user->hasCredential(array('Root','Seguridad y Recepción'),false)) { ?>
<li class="sf_admin_action_new">
    <a href="#" onclick="open_window_right(); nuevo_ingreso(); return false;">Nuevo</a>    
</li>
<?php } ?>

<li class="sf_admin_action_preingreso_visitantes">
  <?php echo link_to(__('Preingreso de visitantes', array(), 'messages'), 'ingresa/preingreso', array()) ?>
</li>
<li class="sf_admin_action_alerta_visitante">
  <?php echo link_to(__('Visitantes en alerta', array(), 'messages'), 'ingresa/alertaVisitante', array()) ?>
</li>

<?php if ($sf_user->hasCredential(array('Root','Administrador'),false)) { ?>
<li class="sf_admin_action_pases_ingreso">
  <?php echo link_to(__('Pases de ingreso', array(), 'messages'), 'ingresa/pasesIngreso', array()) ?>
</li>

<li class="sf_admin_action_graficos">
  <?php echo link_to(__('Estadisticas', array(), 'messages'), 'ingresa/estadisticas', array()) ?>  
</li>
<?php } ?>
