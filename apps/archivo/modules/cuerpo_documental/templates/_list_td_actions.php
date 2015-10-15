<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($archivo_cuerpo_documental, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <?php echo $helper->linkToDelete($archivo_cuerpo_documental, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
      
    <input type="hidden" class="cuerpos" value="<?php echo $archivo_cuerpo_documental->getId(); ?>" name="cuerpos[]"/>
    <li class="sf_admin_action_up">
        <a class='up' style='cursor: pointer;'>Subir un nivel</a>
    </li>
    <li class="sf_admin_action_down">
        <a class='down' style='cursor: pointer;'>Bajar un nivel</a>
    </li>
  </ul>
</td>
