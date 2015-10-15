<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($organigrama_unidad, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <li class="sf_admin_action_cargos">
      <?php echo link_to(__('Cargos', array(), 'messages'), 'unidad/cargos?id='.$organigrama_unidad->getId(), array()) ?>
    </li>
    <?php
    $cargos = Doctrine::getTable('Organigrama_Cargo')->findByUnidadFuncionalId($organigrama_unidad->getId());
    $hijos= Doctrine::getTable('Organigrama_Unidad')->findByPadreIdAndStatus($organigrama_unidad->getId(), 'A');

    $cargos_validos= 0;
    foreach($cargos as $value) {
        if($value->getStatus() == 'V' || $value->getStatus() == 'O') {
            $cargos_validos++;
        }
    }

    if($cargos_validos == 0 && count($hijos) == 0) : ?>
        <li class="sf_admin_action_anular">
          <?php echo link_to(__('Anular', array(), 'messages'), 'unidad/anular?id='.$organigrama_unidad->getId(), array()) ?>
        </li>
    <?php endif; ?>
  </ul>
</td>
