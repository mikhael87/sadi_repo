<td>
  <ul class="sf_admin_td_actions">
    <li class="sf_admin_action_nomina">
      <?php echo link_to(__('Gestionar Nómina', array(), 'messages'), 'disciplina/nomina?id='.$operaciones_registro_disciplina->getId(), array()) ?>
    </li>
    <li class="sf_admin_action_director">
      <?php echo link_to(__('Gestionar Director', array(), 'messages'), 'disciplina/directores?id='.$operaciones_registro_disciplina->getId(), array()) ?>
    </li>
    <li class="sf_admin_action_nomina_pdf">
      <?php echo link_to(__('Imprimir nomina', array(), 'messages'), 'disciplina/nominaPdf?id='.$operaciones_registro_disciplina->getId(), array('target'=>'_blank')) ?>
    </li>
    <?php
    $participantes= Doctrine::getTable('Operaciones_RegistroPersona')->cantidadParticipantePorEquipo($operaciones_registro_disciplina->getId());
    
    if($participantes[0][0] !== 0) :
    ?>
    <li class="sf_admin_action_grupo">
      <a href="#" onclick="javascript: conmutar_grupo('<?php echo $operaciones_registro_disciplina->getId() ?>'); return false;" >Asignar grupo</a>
    </li>
    <?php endif; ?>
  </ul>
</td>
