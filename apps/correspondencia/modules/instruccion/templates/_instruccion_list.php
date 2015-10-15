<?php $helper = new instruccionGeneratorHelper(); ?>
<table cellspacing="0">

    <?php
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

    $funcionario_unidades= array();
    foreach ($nonrepeat as $valor){
        if (!in_array($valor, $funcionario_unidades)){
            $funcionario_unidades[]= $valor;
        }
    }
    
    $i=0;
    foreach ($funcionario_unidades as $funcionario_unidad) {
        $instrucciones = Doctrine_Core::getTable('Correspondencia_Instruccion')->instruccionesUnidad($funcionario_unidad);

        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($funcionario_unidad);
        ?>
        <?php if($i>0) { ?><tr style="border-style: none; border-width: inherit"><td colspan="7"><br/></td></tr> <?php } ?>

        <tr>
            <td colspan="8" style="background-color: #CCCCFF;" class="f19b">
                Instrucciones de la unidad <?php echo $unidad->getNombre(); ?>
            </td>
        </tr>

        <tr>
            <th class="sf_admin_text sf_admin_list_th_descripcion">Descripción</th>
            <th class="sf_admin_text sf_admin_list_th_user_update">Hecho por</th>
            <th id="sf_admin_list_th_actions">Acciones</th>
        </tr>

        <?php
        $count_result= 0;
        foreach ($instrucciones as $instruccion) { ?>
            <tr class="sf_admin_row odd">
                <td class="sf_admin_text sf_admin_list_td_unombre">
                    <?php echo $instruccion->getDescripcion(); ?>
                </td>
                <td class="sf_admin_text sf_admin_list_td_user_update">
                    <?php echo $instruccion->getUserUpdate(); ?>
                </td>
                <td>
                    <ul class="sf_admin_td_actions">
                        <li class="sf_admin_action_edit">
                            <?php echo $helper->linkToEdit($instruccion, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
                        </li>  
                        <li class="sf_admin_action_delete">
                            <?php echo $helper->linkToDelete($instruccion, array('params' => array(), 'confirm' => 'Are you sure?', 'class_suffix' => 'delete', 'label' => 'Delete',)) ?>
                        </li>
                    </ul>
                </td>
            </tr>
            <?php $count_result++; } ?>
                <tr>
                    <th colspan="8"> <?php echo $count_result.' resultado'; echo ($count_result == 1)? '': 's'?> </th>
                </tr>
        <?php } ?>

</table>