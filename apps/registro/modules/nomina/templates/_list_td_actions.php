<td>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_anular">
      <?php echo link_to(__('Anular', array(), 'messages'), 'nomina/inactivar?id='.$personas_persona->getRpersona(), array()) ?>
    </li>
    <li class="sf_admin_action_marcar">
      <?php echo link_to(__('Marcar', array(), 'messages'), 'nomina/marcar?id='.$personas_persona->getRpersona(), array()) ?>
    </li>
  </ul>
</td>
