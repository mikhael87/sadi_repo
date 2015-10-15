<td class="sf_admin_text sf_admin_list_td_foto">
  <?php echo get_partial('funcionario/foto', array('type' => 'list', 'funcionarios_funcionario' => $funcionarios_funcionario)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_cargo">
  <?php echo get_partial('funcionario/cargo', array('type' => 'list', 'funcionarios_funcionario' => $funcionarios_funcionario)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_ci">
  <?php echo $funcionarios_funcionario->getCi() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_primer_nombre">
  <?php echo $funcionarios_funcionario->getPrimerNombre() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_segundo_nombre">
  <?php echo $funcionarios_funcionario->getSegundoNombre() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_primer_apellido">
  <?php echo $funcionarios_funcionario->getPrimerApellido() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_segundo_apellido">
  <?php echo $funcionarios_funcionario->getSegundoApellido() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_sexo">
  <?php echo $funcionarios_funcionario->getSexo() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_contacto">
  <?php echo get_partial('funcionario/contacto', array('type' => 'list', 'funcionarios_funcionario' => $funcionarios_funcionario)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_usuario">
  <?php echo get_partial('funcionario/usuario', array('type' => 'list', 'funcionarios_funcionario' => $funcionarios_funcionario, 'sf_autenticacion' => $sf_autenticacion)) ?>
</td>