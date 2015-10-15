<td>
  <ul class="sf_admin_td_actions">
    <?php
    if($organigrama_cargo->getStatus() == 'O' || $organigrama_cargo->getStatus() == 'V') :
        echo $helper->linkToEdit($organigrama_cargo, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
        <li class="sf_admin_action_mover">
          <?php echo link_to(__('Mover de Unidad', array(), 'messages'), 'cargo/mover?id='.$organigrama_cargo->getId(), array()) ?>
        </li>
        <?php
        $funcionario=Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDelCargo($organigrama_cargo->getId());
        if(count($funcionario) == 0) :
        ?>
            <li class="sf_admin_action_anular">
              <?php echo link_to(__('Anular', array(), 'messages'), 'cargo/anular?id='.$organigrama_cargo->getId(), array()) ?>
            </li>
        <?php
        endif;
    endif;
    
    if($organigrama_cargo->getStatus() == 'I') : ?>
        <li class="sf_admin_action_reactivar">
            <?php echo link_to(__('Reactivar', array(), 'messages'), 'cargo/reactivar?id='.$organigrama_cargo->getId(), array()) ?>
        </li>
    <?php endif; ?>
  </ul>
</td>
