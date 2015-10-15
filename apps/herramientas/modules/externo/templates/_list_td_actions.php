<td>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_reutilizar">
      <?php echo link_to(__('Reutilizar listado', array(), 'messages'), 'externo/reutilizar?id='.$public_mensajes->getId(), array()) ?>
    </li>
    <?php if($public_mensajes->getStatus()=='A'){ ?>
    <li class="sf_admin_action_cancelar" id="action_cancelar_<?php echo $public_mensajes->getId(); ?>">
      <?php echo link_to(__('Cancelar restantes', array(), 'messages'), 'externo/cancelar?id='.$public_mensajes->getId(), array()) ?>
    </li>
    <?php } ?>
    <?php if($public_mensajes->getStatus()=='A'){ ?>
    <li class="sf_admin_action_pausar" id="action_pausar_<?php echo $public_mensajes->getId(); ?>">
      <?php echo link_to(__('Pausar', array(), 'messages'), 'externo/pausar?id='.$public_mensajes->getId(), array()) ?>
    </li>
    <?php } ?>
    <?php if($public_mensajes->getStatus()=='P'){ ?>
    <li class="sf_admin_action_continuar">
      <?php echo link_to(__('Continuar', array(), 'messages'), 'externo/continuar?id='.$public_mensajes->getId(), array()) ?>
    </li>
    <?php } ?>
  </ul>
</td>
