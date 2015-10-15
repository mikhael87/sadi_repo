<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>
<?php use_helper('jQuery'); ?>

<script>
    function ver_unidad_reposos()
    {
        if($('#reposos_unidad_id').val()=='todas'){
            $('.tabla_reposos_reporte').show();
        } else {
            $('.tabla_reposos_reporte').hide();
            $('#tabla_unidad_'+$('#reposos_unidad_id').val()).show();
        }
    }
    
    function solicitar_reposo_tercero(unidad_tercero_id,cargo_tercero_id,funcionario_tercero_id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>reposos/solicitar',
            type:'POST',
            dataType:'html',
            data:'tercerizado[unidad_id]='+unidad_tercero_id+
                 '&tercerizado[cargo_id]='+cargo_tercero_id+
                 '&tercerizado[funcionario_id]='+funcionario_tercero_id+
                 '&unidad_seleccionada_id='+$('#reposos_unidad_id').val(),
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
</script>

<div id="sf_admin_container">
    <h1>Reporte global de Reposos</h1>
    
    <div id="sf_admin_header"></div>
    
    <div id="sf_admin_content">
        
        <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id trans">
            <div>
                <label for="reposos_unidad_id">Unidad</label>
                <div class="content">
                    <select name="reposos_unidad_id" id="reposos_unidad_id" onchange="ver_unidad_reposos(); return false;">
                        <option value=""></option>
                        <?php if(count($unidades)>1){ ?>
                        <option value="todas">Ver Todas</option>
                        <?php } ?>
                        
                        <?php foreach( $unidades as $clave=>$valor ) { 
                            if($clave != '') { ?>
                            <option value="<?php echo $clave; ?>">
                                <?php echo html_entity_decode($valor); ?>
                            </option>
                        <?php }} ?>
                    </select>

                </div>
                <div class="help">Seleccione la unidad que desea ver.</div>
            </div>
        </div>
        
        <hr/>
        
        <div class="sf_admin_list">
            <?php foreach ($unidades as $unidad_id => $nombre) { 
                    if($unidad_id != ''){
            ?>
            <div id="tabla_unidad_<?php echo $unidad_id; ?>" class="tabla_reposos_reporte" style="display: none;">
                <table style="width: 1000px;" class="trans">
                    <tr>
                        <th style="text-align: left;"><?php echo html_entity_decode($nombre); ?></th>
                        <th>Reposos Registrados</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($funcionarios[$unidad_id] as $funcionario) { ?>
                        <tr>
                            <td style="width: 300px;">
                                <font class='f16n'>
                                <?php echo $funcionario->getPrimer_nombre(); ?>
                                <?php echo $funcionario->getSegundo_nombre(); ?>,
                                <?php echo $funcionario->getPrimer_apellido(); ?>
                                <?php echo $funcionario->getSegundo_apellido(); ?>
                                </font>
                                <br/>
                                <?php echo "<font class='f16b'>" . $funcionario->getCtnombre() . "</font>"; ?>
                                <br/>
                                <?php
                                $cargo= Doctrine::getTable('Funcionarios_FuncionarioCargo')->historicoCargosFuncionario($funcionario->getId());
                                echo "<font class='f11n gris_oscuro'>C&eacute;dula: </font><font class='f13b'>".$funcionario->getCi()."</font><br/>";
                                echo "<font class='f11n gris_oscuro'>Ingreso: </font><font class='f13b'>".date('d-m-Y', strtotime($cargo[0]->getFIngreso()))."</font>";
                                ?>
                            </td>
                            <td>
                                <div style="position: relative; font-size: 13px; width: 550px;">
                                    <div style="position: relative;">
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 0px;">
                                            Dias de reposo
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 70px;">
                                            Dias habiles
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 130px;">
                                            Fin de Semana
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 200px;">
                                            No laborales
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 260px;">
                                            Continuos
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 330px;">
                                            Fecha de inicio
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 410px;">
                                            Fecha final
                                        </div>
                                        <div style="position: absolute; font-size: 8px; top: 0px; left: 490px;">
                                            Fecha retorno
                                        </div>
                                    </div>
                                </div>
                                <div id="periodos_funcionario_<?php echo $funcionario->getId(); ?>">
                                    <?php foreach ($reposos[$funcionario->getId()] as $reposo) { ?>
                                        <div style="position: relative; font-size: 13px; width: 550px;">
                                            <div style="position: relative;">
                                                <div style="position: absolute; top: 10px; left: 0px;">
                                                    <?php echo $reposo->getDiasSolicitados(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 70px;">
                                                    <?php echo $reposo->getDiasReposoHabiles(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 130px;">
                                                    <?php echo $reposo->getDiasReposoFinSemana(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 200px;">
                                                    <?php echo $reposo->getDiasReposoNoLaborales(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 260px;">
                                                    <?php echo $reposo->getDiasReposoContinuo(); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 330px;">
                                                    <?php echo date('d-m-Y', strtotime($reposo->getFInicioReposo())); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 410px;">
                                                    <?php echo date('d-m-Y', strtotime($reposo->getFFinReposo())); ?>
                                                </div>
                                                <div style="position: absolute; top: 10px; left: 490px;">
                                                    <?php echo date('d-m-Y', strtotime($reposo->getFRetornoReposo())); ?>
                                                </div>
                                                <br/>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <br/>
                            </td>
                            <td>
                                <ul class="sf_admin_td_actions">
                                    <?php if($funcionario->getCargoSupervisor()!='') { ?>
                                        <li class="sf_admin_action_solicitar_reposo">
                                            <a href="#" onclick="open_window_right(); solicitar_reposo_tercero(<?php echo $unidad_id; ?>,<?php echo $funcionario->getCid(); ?>,<?php echo $funcionario->getId(); ?>); return false;">Registrar Reposo</a>    
                                        </li>
                                    <?php } else { ?>
                                        <li class="sf_admin_action_solicitar_reposo_bloqueado">
                                            <a href="#">El funcionario no tiene un supervisor inmediato asignado, Ingrese con el mismo para que el SIGLAS le solicite uno.</a>  
                                        </li>
                                    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <?php }} ?>
        </div>
    </div>

    <div id="sf_admin_footer">
        <ul class="sf_admin_actions trans">
            <li class="sf_admin_action_regresar_modulo">
                <a href="<?php echo sfConfig::get('sf_app_rrhh_url'); ?>reposos/index">Regresar a reposos personales</a>
            </li>
        </ul>
    </div>
    

    <?php if($sf_user->getAttribute('call_module_master_reporteGlobal_reposo_unidad')) { ?>
        <script>
            $("#reposos_unidad_id option[value='<?php echo $sf_user->getAttribute('call_module_master_reporteGlobal_reposo_unidad'); ?>']").attr("selected", "selected");
            ver_unidad_reposos();
        </script>
    <?php } ?>

</div>