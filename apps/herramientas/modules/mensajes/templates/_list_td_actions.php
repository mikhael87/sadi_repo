<td>
    <ul class="sf_admin_td_actions">
        <?php if ($public_mensajes->getStatus() == 'A') { ?>
            <?php if ($public_mensajes->getFuncionarioRecibeId() == $sf_user->getAttribute('funcionario_id')) { ?>
                <li class="sf_admin_action_leido">
                    <?php // echo link_to(__('Marcar como leido', array(), 'messages'), 'mensajes/leido?id=' . $public_mensajes->getId(), array()) ?>
                </li>
            <?php } ?>
        <?php } ?>
         <li class="sf_admin_action_eliminar">
              <?php 
              echo link_to(__('Eliminar', array(), 'messages'), 'mensajes/eliminar?id_uno=' . $public_mensajes->getFuncionarioRecibeId() .'&id_dos=' . $public_mensajes->getFuncionarioEnviaId(), array()) ?>
         </li>
    </ul>
</td>


