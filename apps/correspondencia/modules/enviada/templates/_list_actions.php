<li class="sf_admin_action_new">
  <a href="#" onclick="open_window_right(); crear_correspondencia(); return false;">Nuevo</a>
</li>

<li class="sf_admin_action_excel">
  <?php echo link_to(__('Exportar', array(), 'messages'), 'enviada/excel', array()) ?>
</li>

<li class="sf_admin_action_graficos">
  <?php echo link_to(__('Estadisticas', array(), 'messages'), 'enviada/estadisticas', array()) ?>  
</li>