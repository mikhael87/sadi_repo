
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
        $archivo_serie_documental_unidad = Doctrine_Core::getTable('Archivo_SerieDocumental')->serieUnidad($funcionario_unidad);

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
                    Tipolog&iacute;as Documentales</th>
                <th id="sf_admin_list_th_actions">Acciones</th>
            </tr>




            <?php
            $count_result= 0;
            foreach ($archivo_serie_documental_unidad as $archivo_serie_documental) { ?>
                <tr class="sf_admin_row odd" id="tr_serie_documental_<?php echo $archivo_serie_documental->getId(); ?>">
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
                            <?php echo $helper->linkToEdit($archivo_serie_documental, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
                            <li class="sf_admin_action_delete">
                            <?php 
                                $notice_delete = '';
                                if($archivo_serie_documental->getTipologias()>0){
                                    $notice_delete = ' actualmente la misma tiene asociadas '.$archivo_serie_documental->getTipologias().' tipologias documentales';
                                    
                                    if($archivo_serie_documental->getExpedientes()>0){
                                        $expedientes = Doctrine::getTable('Archivo_Expediente')->findBySerieDocumentalIdAndStatus($archivo_serie_documental->getId(),'A');
                                        
                                        $notice_delete .= ' y se han creado con la misma los siguientes expediente: ';
                                        foreach ($expedientes as $expediente) {
                                            $notice_delete .= $expediente->getCorrelativo().', ';
                                        }
                                    }
                                }
                                
                                
                                echo link_to(__('Borrar', array(), 'messages'), 
                                                'serie_documental/inactivarSerieDocumental?id='.$archivo_serie_documental->getId(), 
                                                    array('confirm' => 
                                                          '¿Esta usted seguro de eliminar esta Serie Documental?. '.$notice_delete)); 
                                
                            ?>
                            </li>
                            <li class="sf_admin_action_tipologia_documental">
                                <?php echo link_to(__('Tipologia Documental', array(), 'messages'), 'serie_documental/tipologiaDocumental?id='.$archivo_serie_documental->getId(), array()) ?>
                            </li>
                            <li class="sf_admin_action_transferir_serie">
                                <?php //echo link_to(__('Transferir Serie Documental', array(), 'messages'), 'serie_documental/transferirSerie?id='.$archivo_serie_documental->getId(), array()) ?>
                                <a href="#" onclick="open_window_right(); transferir_serie(<?php echo $archivo_serie_documental->getId(); ?>); return false;">Transferir Serie Documental</a>
                            </li>
                            <li class="sf_admin_action_duplicar_serie">
                                <a href="#" onclick="open_window_right(); duplicar_serie(<?php echo $archivo_serie_documental->getId(); ?>); return false;">Duplicar Serie Documental</a>
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