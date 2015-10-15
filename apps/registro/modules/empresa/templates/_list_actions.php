<li class="sf_admin_action_new">
  <?php echo link_to(__('Registrar nueva empresa', array(), 'messages'), 'empresa/asistente?paso=1', array()) ?>
  <?php // echo link_to(__('Registrar nueva empresa', array(), 'messages'), 'empresa/asistente?paso=3&regid=1', array()) ?>
</li>

<li class="sf_admin_action_excel">
  <?php echo link_to(__('Exportar', array(), 'messages'), 'empresa/excel', array()) ?>
</li>

<li class="sf_admin_action_pdf">
  <?php echo link_to(__('Todas las planillas', array(), 'messages'), 'empresa/planillaInscripcionTodas', array('target'=>'_blank', 'confirm'=>'¿Estás seguro?, esto tardará algunos minutos, sea paciente.')) ?>
</li>
