
<?php $helper = new gruposGeneratorHelper(); ?>
<table cellspacing="0">

    <?php
    $funcionario_unidades= array();
    
    if(!$sf_user->getAttribute('pae_funcionario_unidad_id')) {
        $boss= false;
        if($sf_user->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));
        }
        $funcionario_unidades_admin = Doctrine::getTable('Archivo_FuncionarioUnidad')->adminFuncionarioGrupo($sf_user->getAttribute('funcionario_id'));

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
    }else {
        $funcionario_unidades[] = $sf_user->getAttribute('pae_funcionario_unidad_id');
    }

    $i=0;
    foreach ($funcionario_unidades as $funcionario_unidad) {
        $grupo = Doctrine_Core::getTable('Archivo_FuncionarioUnidad')->grupoUnidad($funcionario_unidad);

        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($funcionario_unidad);
        ?>
        <?php if($i>0) { ?><tr style="border-style: none; border-width: inherit"><td colspan="10"><br/></td></tr> <?php } ?>

        <tr>
            <td colspan="10" style="background-color: #CCCCFF;" class="f19b">
                Grupo de la unidad <?php echo $unidad->getNombre(); ?>
            </td>
        </tr>

            <tr>
                <th class="sf_admin_text sf_admin_list_th_unombre">
                    Unidad a la que pertenece</th>
                <th class="sf_admin_text sf_admin_list_th_persona">
                    Funcionario</th>
                <th class="sf_admin_text sf_admin_list_th_ctnombre">
                    Cargo</th>
                <th class="sf_admin_boolean sf_admin_list_th_redactar">
                    Archivar  </th>
                <th class="sf_admin_boolean sf_admin_list_th_leer">
                    Leer  </th>
                <th class="sf_admin_boolean sf_admin_list_th_recibir">
                    Prestar  </th>
                <th class="sf_admin_boolean sf_admin_list_th_firmar">
                    Anular  </th>
                <th class="sf_admin_boolean sf_admin_list_th_administrar">
                    Administrar  </th>
                <th class="sf_admin_text sf_admin_list_th_user_update">
                    Hecho por</th>
                <th id="sf_admin_list_th_actions">Acciones</th>
            </tr>




            <?php
            $count_result= 0;
            foreach ($grupo as $funcionario) { ?>
                <tr class="sf_admin_row odd">
                    <td class="sf_admin_text sf_admin_list_td_unombre">
                        <?php echo $funcionario->getUnombre(); ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_persona">
                        <?php echo $funcionario->getPersona(); ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_ctnombre">
                        <?php echo $funcionario->getCtnombre(); ?>
                    </td>
                    <td class="sf_admin_boolean sf_admin_list_td_redactar" style="text-align: center;">
                        <?php if ($funcionario->getArchivar()) { ?>
                            <img src="/images/icon/tick.png" title="Marcado" alt="Marcado">
                        <?php } else {
                            echo '&nbsp;';
                        } ?>
                    </td>
                    <td class="sf_admin_boolean sf_admin_list_td_leer" style="text-align: center;">
                        <?php if ($funcionario->getLeer()) { ?>
                            <img src="/images/icon/tick.png" title="Marcado" alt="Marcado">
                        <?php } else {
                            echo '&nbsp;';
                        } ?>
                    </td>
                    <td class="sf_admin_boolean sf_admin_list_td_recibir" style="text-align: center;">
                        <?php if ($funcionario->getPrestar()) { ?>
                            <img src="/images/icon/tick.png" title="Marcado" alt="Marcado">
                        <?php } else {
                            echo '&nbsp;';
                        } ?>
                    </td>
                    <td class="sf_admin_boolean sf_admin_list_td_firmar" style="text-align: center;">
                        <?php if ($funcionario->getAnular()) { ?>
                            <img src="/images/icon/tick.png" title="Marcado" alt="Marcado">
                        <?php } else {
                            echo '&nbsp;';
                        } ?>
                    </td>
                    <td class="sf_admin_boolean sf_admin_list_td_administrar" style="text-align: center;">
                        <?php if ($funcionario->getAdministrar()) { ?>
                            <img src="/images/icon/tick.png" title="Marcado" alt="Marcado">
                        <?php } else {
                            echo '&nbsp;';
                        } ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_user_update">
                        <?php echo $funcionario->getUserUpdate(); ?>
                    </td>
                    <td>
                        <ul class="sf_admin_td_actions">
                            <li class="sf_admin_action_delete">
                                <?php echo $helper->linkToDelete($funcionario, array('params' => array(), 'confirm' => 'Are you sure?', 'class_suffix' => 'delete', 'label' => 'Delete',)) ?>
                            </li>
                            <li class="sf_admin_action_edit">
                                <?php echo $helper->linkToEdit($funcionario, array('params' => array(), 'confirm' => 'Are you sure?', 'class_suffix' => 'edit', 'label' => 'Edit',)) ?>
                            </li>
                        </ul>
                    </td>
                </tr>
        <?php $count_result++; } ?>
            <tr>
                <th colspan="11"> <?php echo $count_result.' resultado'; echo ($count_result == 1)? '': 's'?> </th>
            </tr>
    <?php $i++; } ?>
</table>