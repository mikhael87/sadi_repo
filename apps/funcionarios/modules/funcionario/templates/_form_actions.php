<ul class="sf_admin_actions">
<?php if ($form->isNew()): ?>
  <?php echo $helper->linkToDelete($form->getObject(), array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
  <?php echo $helper->linkToList(array(  'params' =>   array(  ),  'class_suffix' => 'list',  'label' => 'Back to list',)) ?>
  <li class="sf_admin_action_save">
      <input type="button" value="Guardar" onclick="procesar_funcionario('');">
  </li>
  <li class="sf_admin_action_save_and_add">
      <input type="button" name="_save_and_add" value="Guardar y crear otro" onclick="procesar_funcionario('_save_and_add');">
  </li>

<?php else: ?>
<?php echo $helper->linkToDelete($form->getObject(), array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
  <?php echo $helper->linkToList(array(  'params' =>   array(  ),  'class_suffix' => 'list',  'label' => 'Back to list',)) ?>
  <li class="sf_admin_action_save">
      <input type="button" value="Guardar" onclick="procesar_funcionario('');">
  </li>
<?php endif; ?>
  
</ul>
<div id="tipo_button"></div>
