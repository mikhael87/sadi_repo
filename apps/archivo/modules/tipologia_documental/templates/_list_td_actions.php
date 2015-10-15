<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($archivo_tipologia_documental, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <?php echo $helper->linkToDelete($archivo_tipologia_documental, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
      
    <input type="hidden" class="tipologias_<?php echo $archivo_tipologia_documental->getCuerpoDocumentalId(); ?>" value="<?php echo $archivo_tipologia_documental->getId(); ?>" name="tipologias[]"/>
    <li class="sf_admin_action_up">
        <a class='up cuerpo_<?php echo $archivo_tipologia_documental->getCuerpoDocumentalId(); ?>' style='cursor: pointer;'>Subir un nivel</a>
    </li>
    <li class="sf_admin_action_down">
        <a class='down cuerpo_<?php echo $archivo_tipologia_documental->getCuerpoDocumentalId(); ?>' style='cursor: pointer;'>Bajar un nivel</a>
    </li>
  </ul>
</td>
