<td>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($empresas_empresa, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
    <li class="sf_admin_action_encargado">
      <?php echo link_to(__('Registrar encargado', array(), 'messages'), 'empresa/encargados?id='.$empresas_empresa->getRegistro(), array()) ?>
    </li>
    <li class="sf_admin_action_delegado">
      <?php echo link_to(__('Registrar delegado', array(), 'messages'), 'empresa/delegados?id='.$empresas_empresa->getRegistro(), array()) ?>
    </li>
    <br/><br/>
    <li class="sf_admin_action_disciplina">
      <?php echo link_to(__('Inscribir disciplinas', array(), 'messages'), 'empresa/disciplinas?id='.$empresas_empresa->getRegistro(), array()) ?>
    </li>
    <li class="sf_admin_action_planillainscripcion">
      <?php echo link_to(__('Planilla de Inscripción', array(), 'messages'), 'empresa/planillaInscripcion?id='.$empresas_empresa->getRegistro(), array('target'=>'_blank')) ?>
    </li>
    <li class="sf_admin_action_pago">
        <a href="#" onclick="javascript: conmutar_pago('<?php echo $empresas_empresa->getRegistro() ?>'); return false;" ></a>
    </li>
    <br/><br/>
    <li class="sf_admin_action_equipos">
      <?php echo link_to(__('Equipos', array(), 'messages'), 'empresa/equipos?id='.$empresas_empresa->getRegistro(), array()) ?>
    </li>
  </ul>
</td>
