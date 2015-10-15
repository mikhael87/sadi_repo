
<?php $helper = new estanteGeneratorHelper(); ?>
<table cellspacing="0">

    <?php
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

    $funcionario_unidades= array();
    foreach ($nonrepeat as $valor){
        if (!in_array($valor, $funcionario_unidades)){
            $funcionario_unidades[]= $valor;
        }
    }

    $i=0;
    foreach ($funcionario_unidades as $funcionario_unidad) {
        $archivo_estante_unidad = Doctrine_Core::getTable('Archivo_Estante')->estanteUnidad($funcionario_unidad);

        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($funcionario_unidad);
        ?>
        <?php if($i>0) { ?><tr style="border-style: none; border-width: inherit"><td colspan="11"><br/></td></tr> <?php } ?>

        <tr>
            <td colspan="11" style="background-color: #CCCCFF;" class="f19b">
                Estantes de la unidad <?php echo $unidad->getNombre(); ?>
            </td>
        </tr>

            <tr>
                <th></th>
                <th class="sf_admin_text sf_admin_list_th_archivo_estante_modelo">
                    Modelo</th>
                <th class="sf_admin_text sf_admin_list_th_organigrama_unidad_fisica">
                    Ubicaci&oacute;n</th>
                <th class="sf_admin_text sf_admin_list_th_detalles_ubicacion_fisica">
                    Detalles de ubicaci&oacute;n f&iacute;sica</th>
                <th class="sf_admin_text sf_admin_list_th_identificador">
                    C&oacute;digo del mueble</th>
                <th class="sf_admin_text sf_admin_list_th_tramos">
                    Tramos o gavetas</th>
                <th class="sf_admin_text sf_admin_list_th_alto_tramos">
                    Alto</th>
                <th class="sf_admin_text sf_admin_list_th_ancho_tramos">
                    Ancho</th>
                <th class="sf_admin_text sf_admin_list_th_largo_tramos">
                    Largo</th>
                <th class="sf_admin_text sf_admin_list_th_porcentaje_ocupado">
                    Porcentaje ocupado</th>
                <th id="sf_admin_list_th_actions">Acciones</th>
            </tr>




            <?php
            $count_result= 0;
            foreach ($archivo_estante_unidad as $archivo_estante) { ?>
                <tr class="sf_admin_row odd">
                    <td>
                        <input class="sf_admin_batch_checkbox" type="checkbox" value="<?php echo $archivo_estante->getId(); ?>" name="ids[]">
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_archivo_estante_modelo">
                        <?php echo $archivo_estante->getArchivoEstanteModelo() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_organigrama_unidad_fisica">
                        <?php echo $archivo_estante->getOrganigramaUnidadFisica() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_detalles_ubicacion_fisica">
                        <?php echo $archivo_estante->getDetallesUbicacionFisica() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_identificador">
                        <?php echo $archivo_estante->getIdentificador() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_tramos">
                        <?php echo $archivo_estante->getTramos() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_alto_tramos">
                        <?php echo $archivo_estante->getAltoTramos() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_ancho_tramos">
                        <?php echo $archivo_estante->getAnchoTramos() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_largo_tramos">
                        <?php echo $archivo_estante->getLargoTramos() ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_porcentaje_ocupado">
                        <?php echo $archivo_estante->getPorcentajeOcupado() ?>
                    </td>
                    <td>
                        <ul class="sf_admin_td_actions">
                            <?php echo $helper->linkToEdit($archivo_estante, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
                            <?php echo $helper->linkToDelete($archivo_estante, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
                            <li class="sf_admin_action_almacenamiento">
                            <?php echo link_to(__('Modo de Almacenamiento', array(), 'messages'), 'estante/almacenamiento?id='.$archivo_estante->getId(), array()) ?>
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