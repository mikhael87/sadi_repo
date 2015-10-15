<script>
    function saveAsignacionCorrespondencia() {
        var error = false;
        $('#error_asignacion_funcionario').hide();
        $('#error_asignacion_instruccion').hide();
        
        if($('#asignacion_funcionario').val()==''){
            $('#error_asignacion_funcionario').show();
            error = true;
        }
        
        if($('#asignacion_instruccion').val()==''){
            $('#error_asignacion_instruccion').show();
            error = true;
        }
        
        if(error==false){
            $('#button_guardar_asignacion').attr('disabled','disabled');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>recibida/asignado',
                type:'POST',
                dataType:'html',
                data: $('#form_asignar_correspondencia').serialize(),

                success:function(data, textStatus){
                    close_window_right_update_father();
            }});
        }
    };            
</script>

<div id="sf_admin_container">
    <h1>Asignación de la Correspondencia o Caso</h1>

    <div id="sf_admin_content">
        <form id="form_asignar_correspondencia" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida/asignado'; ?>">
        <input type="hidden" name="asignacion[unidad_id]" id="tipo_envio" value="<?php echo $unidad_asigna_id; ?>"/>
        <fieldset id="sf_fieldset_asignacion_correspondencia">            
            <div class="sf_admin_form_row sf_admin_text">
                <div class="error" id="error_asignacion_funcionario" style="display: none;">Seleccione al funcionario a quien asiganara la correspondencia.</div>
                <div>
                    <label for="">Asignar a:</label>
                    <div class="content">
                        <select name="asignacion[funcionario_id]" id="asignacion_funcionario">
                            <option value=""></option>
                            <?php foreach ($funcionarios_directos as $funcionario_directo) { ?>
                                <option value="<?php echo $funcionario_directo->getId(); ?>"><?php echo $funcionario_directo->getCtnombre().' / '.$funcionario_directo->getPrimerNombre().' '.$funcionario_directo->getPrimerApellido(); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="help">Unicamente puede asignar una correspondencia a funcionarios directos de la unidad a la que usted pertenece</div>
            </div>
        </fieldset>
            
        <fieldset id="sf_fieldset_observaciones_asignacion">
            <h2>&nbsp;</h2>

            <div class="sf_admin_form_row sf_admin_text">
                <div class="error" id="error_asignacion_instruccion" style="display: none;">Seleccione la instruccion que asignara a esta correspondencia.</div>
                <div>
                    <label for="redireccion_instruccion">Instrucción</label>
                    <div class="content">
                        <select name="asignacion[instruccion]" id="asignacion_instruccion">
                            <option value=""></option>
                            <?php foreach ($instrucciones as $instruccion) { ?>
                                <option value="<?php echo trim($instruccion); ?>"><?php echo trim($instruccion); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label for="asignacion_descripcion">Descripcion</label>
                    <div class="content">
                        <textarea rows="4" cols="60" name="asignacion[descripcion]" id="asignacion_descripcion"></textarea>
                    </div>
                </div>
                <div class="help"></div>
            </div>
            
            <div class="sf_admin_form_row sf_admin_text">
                <div class="error" id="error_asignacion_instruccion" style="display: none;">Seleccione la instruccion que asignara a esta correspondencia.</div>
                <div>
                    <label for="redireccion_instruccion">Instrucción</label>
                    <div class="content">
                        <select name="asignacion[prioridad]" id="asignacion_instruccion">
                            <option value="1">Normal</option>
                            <option value="2">Urgente</option>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>
        <hr/>
        <li class="sf_admin_action_save">
            <input id="button_guardar_asignacion" type="button" value="Guardar" onclick="saveAsignacionCorrespondencia(); return false;">
        </li>
        </form>
        <br/>
    </div>

</div>

