
<?php $helper = new serie_documentalGeneratorHelper(); ?>
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
        $archivo_serie_documental_unidad = Doctrine_Core::getTable('Archivo_SerieDocumental')->serieUnidad($funcionario_unidad,'I');

        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($funcionario_unidad);
        ?>
        <?php if($i>0) { ?><tr style="border-style: none; border-width: inherit"><td colspan="6"><br/></td></tr> <?php } ?>

        <tr>
            <td colspan="4" style="background-color: #CCCCFF;" class="f19b">
                Series de la unidad <?php echo $unidad->getNombre(); ?>
            </td>
        </tr>

            <tr>
                <th class="sf_admin_text sf_admin_list_th_unombre">
                    Nombre</th>
                <th class="sf_admin_text sf_admin_list_th_persona">
                    Descriptores</th>
                <th class="sf_admin_text sf_admin_list_th_ctnombre">
                    Tipolog&iacute;s Documentales</th>
                <th id="sf_admin_list_th_actions">Acciones</th>
            </tr>

            <?php
            $count_result= 0;
            foreach ($archivo_serie_documental_unidad as $archivo_serie_documental) { ?>
                <tr class="sf_admin_row odd">
                    <td class="sf_admin_text sf_admin_list_td_nombre">
                        <?php echo $archivo_serie_documental->getNombre(); ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_clasificadores_list">
                        <?php echo get_partial('serie_documental/clasificadores_list', array('type' => 'list', 'archivo_serie_documental' => $archivo_serie_documental)) ?>
                    </td>
                    <td class="sf_admin_text sf_admin_list_td_tipologias_documentales">
                        <?php echo get_partial('serie_documental/tipologias_documentales', array('type' => 'list', 'archivo_serie_documental' => $archivo_serie_documental)) ?>
                    </td>
                    <td>
                        <ul class="sf_admin_td_actions">
                            <li class="sf_admin_action_serie_activa">
                            <?php echo link_to(__('Activar Serie Documental', array(), 'messages'), 'serie_documental/activarSerieDocumental?id='.$archivo_serie_documental->getId(), array('confirm' => '¿Esta usted seguro de activar esta Serie Documental?')) ?>
                            </li>
                        </ul>
                    </td>
                </tr>
        <?php $count_result++; } ?>
            <tr>
                <th colspan="6"> <?php echo $count_result.' resultado'; echo ($count_result == 1)? '': 's'?> </th>
            </tr>
    <?php $i++; } ?>
</table>