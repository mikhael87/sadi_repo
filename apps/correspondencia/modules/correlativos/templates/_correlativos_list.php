<?php $helper = new correlativosGeneratorHelper(); ?>
<table cellspacing="0">

    <?php
    $funcionario_unidades= array();
    
    if($sf_user->getAttribute('pae_funcionario_unidad_id')) {
        $funcionario_unidad_id= $sf_user->getAttribute('pae_funcionario_unidad_id');
        
        $funcionario_unidades[]= $funcionario_unidad_id;
    }else {
        $boss= false;
        if($sf_user->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($sf_user->getAttribute('funcionario_id'));

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $admin_array= array();
        for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
            $admin_array[]= $funcionario_unidades_admin[$i][0];
        }

        $nonrepeat= array_merge($cargo_array, $admin_array);

        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
    }
    
    $i=0;
    foreach ($funcionario_unidades as $funcionario_unidad) {
        $correlativos = Doctrine_Core::getTable('Correspondencia_UnidadCorrelativo')->correlativosUnidad($funcionario_unidad);

        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($funcionario_unidad);
        ?>
        <?php if($i>0) { ?><tr style="border-style: none; border-width: inherit"><td colspan="7"><br/></td></tr> <?php } ?>

        <tr>
            <td colspan="8" style="background-color: #CCCCFF;" class="f19b">
                Correlativos de la unidad <?php echo $unidad->getNombre(); ?>
            </td>
        </tr>

        <tr>
            <th class="sf_admin_text sf_admin_list_th_unombre">
                Descripción</th>
            <th class="sf_admin_text sf_admin_list_th_persona">
                Nomenclador</th>
            <th class="sf_admin_text sf_admin_list_th_ctnombre">
                Letra</th>
            <th class="sf_admin_text sf_admin_list_th_ctnombre">
                Secuencia</th>
            <th class="sf_admin_text sf_admin_list_th_ctnombre">
                Compartido</th>
            <th class="sf_admin_text sf_admin_list_th_ctnombre">
                Formatos asociados</th>
            <th class="sf_admin_text sf_admin_list_th_user_update">
                Hecho por</th>
            <th id="sf_admin_list_th_actions">Acciones</th>
        </tr>




        <?php
        $count_result= 0;
        foreach ($correlativos as $correlativo) { ?>
            <tr class="sf_admin_row odd">
                <td class="sf_admin_text sf_admin_list_td_unombre">
                    <?php echo $correlativo->getDescripcion(); ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_persona">
                    <?php echo $correlativo->getNomenclador(); ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_ctnombre">
                    <?php echo $correlativo->getLetra(); ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_ctnombre">
                    <?php echo $correlativo->getSecuencia(); ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_ctnombre" style="text-align: center;">
                    <?php if ($correlativo->getCompartido()): ?>
                      <?php echo image_tag(sfConfig::get('sf_admin_module_web_dir').'/images/tick.png', array('alt' => __('Checked', array(), 'sf_admin'), 'title' => __('Checked', array(), 'sf_admin'))) ?>
                    <?php else: ?>
                      &nbsp;
                    <?php endif; ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_ctnombre">
                    <?php
                    if($correlativo->getTipo()!='R') {
                        $correlativos_formatos = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->findByUnidadCorrelativoId($correlativo->getId());

                        foreach ($correlativos_formatos as $correlativo_formato) {
                            $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($correlativo_formato->getTipoFormatoId());

                            echo $tipo_formato->getNombre().'<br/>';
                        }
                    } else {
                        echo "Recepción Externa";
                    }
                    ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_user_update">
                    <?php echo $correlativo->getUserUpdate(); ?>
                </td>
                <td>
                    <ul class="sf_admin_td_actions">
                        <?php if($correlativo->getTipo()!='R') { ?>
                        <li class="sf_admin_action_edit">
                            <?php echo $helper->linkToEdit($correlativo, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
                        </li>  
                        <li class="sf_admin_action_delete">
                            <?php echo $helper->linkToDelete($correlativo, array('params' => array(), 'confirm' => 'Are you sure?', 'class_suffix' => 'delete', 'label' => 'Delete',)) ?>
                        </li>
                        <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php $count_result++; } ?>
                <tr>
                    <th colspan="8"> <?php echo $count_result.' resultado'; echo ($count_result == 1)? '': 's'?> </th>
                </tr>
        <?php $i++; } ?>

</table>