<td>
  <ul class="sf_admin_td_actions">
    <?php
    if($personas_persona->getStatus() !== 'I') {
        echo $helper->linkToEdit($personas_persona, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',));
    } ?>
    <li class="sf_admin_action_passwd">
      <?php
      if($personas_persona->getStatus() !== 'I') {
        echo link_to(__('Reiniciar Contraseña', array(), 'messages'), 'usuario/passwd?id='.$personas_persona->getId(), 'confirm=\'¿Estas seguro de reiniciar la contraseña?\'');
      } ?>
    </li>
<!--  </ul><br/>
  <ul class="sf_admin_td_actions">-->
    <?php
        if($personas_persona->getStatus() == 'I') { ?>
            <li class="sf_admin_action_reactivar">
                <?php echo link_to(__('Reactivar', array(), 'endif;messages'), 'usuario/reactivar?id='.$personas_persona->getId(), array()) ?>
            </li>
    <?php }elseif($personas_persona->getStatus() == 'A') { ?>
            <li class="sf_admin_action_anular">
              <?php echo link_to(__('Anular', array(), 'messages'), 'usuario/anular?id='.$personas_persona->getId(), array()) ?>
            </li>
    <?php } ?>
  </ul>
</td>