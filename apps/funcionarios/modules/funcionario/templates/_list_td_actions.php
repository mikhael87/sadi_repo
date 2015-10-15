<?php $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByUsuarioEnlaceIdAndEnlaceId($funcionarios_funcionario->getId(),1); ?>

<td>
  <ul class="sf_admin_td_actions">
    <?php
    if($funcionarios_funcionario->getStatus() !== 'I') {
        echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? $helper->linkToEdit($funcionarios_funcionario, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) : '');
    } ?>
    <li class="sf_admin_action_cargosf">
      <?php
      if($funcionarios_funcionario->getStatus() !== 'I') {
        echo link_to(__('Cargo', array(), 'messages'), 'funcionario/cargosf?id='.$funcionarios_funcionario->getId(), array());
      } ?>
    </li>
    <li class="sf_admin_action_passwd">
      <?php
      if($funcionarios_funcionario->getStatus() !== 'I') {
        echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Reiniciar Contraseña', array(), 'messages'), 'funcionario/passwd?id='.$funcionarios_funcionario->getId(), 'confirm=\'¿Estas seguro de reiniciar la contraseña?\'') : '');
      } ?>
    </li>
  </ul><br/>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_digitalizar">
      <?php
      if($funcionarios_funcionario->getStatus() !== 'I') {
        echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Firma digitalizada', array(), 'messages'), 'funcionario/digiFirma?id='.$funcionarios_funcionario->getId(), array()) : '');
      } ?>
    </li>
  
    <?php
    if($funcionarios_funcionario->getStatus() !== 'I') :
        if(!$sf_user->hasCredential(array('Carnetizador'), false)) :
            if($usuario->getAccesoGlobal()==FALSE) { ?>
                <li class="sf_admin_action_global_enable">
                  <?php echo link_to(__('Activar acceso global', array(), 'messages'), 'funcionario/globalEnable?id='.$funcionarios_funcionario->getId(), 'confirm=\'¿Estas seguro de activar el acceso global?\'') ?>
                </li>
        <?php } else { ?>
            <li class="sf_admin_action_global_disable">
              <?php echo link_to(__('Desactivar acceso global', array(), 'messages'), 'funcionario/globalDisable?id='.$funcionarios_funcionario->getId(), 'confirm=\'¿Estas seguro de desactivar el acceso global?\'') ?>
            </li>
    <?php } endif; endif;?>
        
    <?php
    $funcionario_cargo=Doctrine::getTable('Funcionarios_FuncionarioCargo')->findByFuncionarioIdAndStatus($funcionarios_funcionario->getId(), 'A');
    if(count($funcionario_cargo) == 0) :
        if($funcionarios_funcionario->getStatus() == 'I') { ?>
            <li class="sf_admin_action_reactivar">
                <?php echo link_to(__('Reactivar', array(), 'endif;messages'), 'funcionario/reactivar?id='.$funcionarios_funcionario->getId(), array()) ?>
            </li>
    <?php }elseif($funcionarios_funcionario->getStatus() == 'A') { ?>
            <li class="sf_admin_action_anular">
              <?php echo link_to(__('Anular', array(), 'messages'), 'funcionario/anular?id='.$funcionarios_funcionario->getId(), array()) ?>
            </li>
    <?php }
    endif;?>
  </ul>
</td>