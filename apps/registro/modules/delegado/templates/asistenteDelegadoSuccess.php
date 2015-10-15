<?php include(sfConfig::get("sf_root_dir").'/apps/registro/modules/delegado/lib/validator_asistente_delegado.php'); ?>

<script>
    function guardar_delegado(destination){
        $('#add_or_next').val(destination);
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>delegado/saveDelegado',
            type:'POST',
            dataType:'html',
            data: $('#form_delegado').serialize(),
            beforeSend: function(Obj){
                $('#save_delegado_msj').html('<?php echo image_tag('icon/cargando.gif', array('style'=>'width: 16px; vertical-align: middle')); ?> Guardando delegado...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(destination);
            }});
    }
    
    function buscarDelegado() {
        var cedula= $('#personas_persona_ci').val();
        var registro_id= $('#operaciones_registro_id').val();

        if(cedula === '') {
            alert('Debe introducir número de cédula');
        }else {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>delegado/buscarDelegado',
                type:'POST',
                dataType:'json',
                data:'cedula='+cedula+'&regid='+registro_id,
                beforeSend: function(Obj){
                    $('#buscar_delegado_msj').html('<?php echo image_tag('icon/cargando.gif', array('style'=>'width: 16px; vertical-align: middle')); ?> Buscando delegado ...');
                },
                success:function(json, textStatus){
                    if(json.status === 'ok') {
                        $('#buscar_delegado_msj').html('');

                        $('#personas_persona_id').val(json.content.id);
                        $('#personas_persona_primer_nombre').val(json.content.primerNombre);
                        $('#personas_persona_primer_apellido').val(json.content.primerApellido);
                        $('#personas_persona_f_nacimiento_day option[value='+json.content.fNacimientoDia+']').attr('selected', 'selected');
                        $('#personas_persona_f_nacimiento_month option[value='+json.content.fNacimientoMes+']').attr('selected', 'selected');
                        $('#personas_persona_f_nacimiento_year option[value='+json.content.fNacimientoAno+']').attr('selected', 'selected');
                        if(json.content.sexo === 'F') {
                            $('#personas_persona_sexo_F').attr('checked', true);
                        }else {
                            $('#personas_persona_sexo_M').attr('checked', true);
                        }
                        if(json.content.edoCivil === 'S') {
                            $('#personas_persona_edo_civil_S').attr('checked', true);
                        }else {
                            if(json.content.edoCivil === 'C') {
                                $('#personas_persona_edo_civil_C').attr('checked', true);
                            }else {
                                if(json.content.edoCivil === 'D') {
                                    $('#personas_persona_edo_civil_D').attr('checked', true);
                                }else {
                                    $('#personas_persona_edo_civil_V').attr('checked', true);
                                }
                            }
                        }

                        $('#personas_persona_telf_movil').val(json.content.telfMovil);
                        $('#personas_persona_email_personal').val(json.content.emailPersonal);
                        
                        $('.to_hid_delegado').show('fast');
                        
                        //BLOQUEO DE CAMPOS
                        if(json.content.primerNombre === '') { $('#personas_persona_primer_nombre').attr('disabled', 'disabled') };
                        if(json.content.primerApellido === '') { $('#personas_persona_primer_apellido').attr('disabled', 'disabled') };
                        if(json.content.fNacimientoDia !== '' && json.content.fNacimientoMes !== '' && json.content.fNacimientoAno !== '') {
                            $('.ui-datepicker-trigger').hide();
                            $('#personas_persona_f_nacimiento_day').attr('disabled', 'disabled');
                            $('#personas_persona_f_nacimiento_month').attr('disabled', 'disabled');
                            $('#personas_persona_f_nacimiento_year').attr('disabled', 'disabled');
                        }
                        $('#personas_persona_edo_civil_S').attr('disabled', 'disabled');
                        $('#personas_persona_edo_civil_C').attr('disabled', 'disabled');
                        $('#personas_persona_edo_civil_D').attr('disabled', 'disabled');
                        $('#personas_persona_edo_civil_V').attr('disabled', 'disabled');
                        $('#personas_persona_sexo_M').attr('disabled', 'disabled');
                        $('#personas_persona_sexo_F').attr('disabled', 'disabled');
                    }else {
                        if(json.status === 'existe') {
                            $("#buscar_delegado_msj").html('<x id="inner_msj"><font style="color: red">La persona ya se encuentra inscrita como delegado en esta misma empresa</font></x>');
                            setTimeout(function(){
                                $("#inner_msj").fadeOut(1000);
                            }, 6000);
                            
                            $('.to_hid_delegado').hide('fast');
                        }else {
                            $('.to_hid_delegado').show('fast');
                            //REINICIO DE FORM
                            $('#personas_persona_id').val('');
                            $('#personas_persona_primer_nombre').val('');
                            $('#personas_persona_primer_apellido').val('');
                            $('#personas_persona_primer_nombre').attr('disabled', false);
                            $('#personas_persona_primer_apellido').attr('disabled', false);
                            $('.ui-datepicker-trigger').show();
                            $('#personas_persona_f_nacimiento_day').attr('disabled', false);
                            $('#personas_persona_f_nacimiento_month').attr('disabled', false);
                            $('#personas_persona_f_nacimiento_year').attr('disabled', false);
                            $('#personas_persona_f_nacimiento_day option[value=""]').attr('selected', 'selected');
                            $('#personas_persona_f_nacimiento_month option[value=""]').attr('selected', 'selected');
                            $('#personas_persona_f_nacimiento_year option[value=""]').attr('selected', 'selected');
                            
                            $('#personas_persona_edo_civil_S').attr('checked', true);
                            $('#personas_persona_edo_civil_S').attr('disabled', false);
                            $('#personas_persona_edo_civil_C').attr('disabled', false);
                            $('#personas_persona_edo_civil_D').attr('disabled', false);
                            $('#personas_persona_edo_civil_V').attr('disabled', false);
                            $('#personas_persona_sexo_M').attr('checked', true);
                            $('#personas_persona_sexo_M').attr('disabled', false);
                            $('#personas_persona_sexo_F').attr('disabled', false);
                            
                            $('#personas_persona_telf_movil').val('');
                            $('#personas_persona_email_personal').val('');
                            
                            if(json.status === 'saime') {
                                $('#personas_persona_primer_nombre').val(json.content.primerNombre);
                                $('#personas_persona_primer_apellido').val(json.content.primerApellido);
                                $('#personas_persona_f_nacimiento_day option[value='+json.content.fNacimientoDia+']').attr('selected', 'selected');
                                $('#personas_persona_f_nacimiento_month option[value='+json.content.fNacimientoMes+']').attr('selected', 'selected');
                                $('#personas_persona_f_nacimiento_year option[value='+json.content.fNacimientoAno+']').attr('selected', 'selected');
                                
                                $("#buscar_delegado_msj").html('<x id="inner_msj"><font style="color: green">Por favor complete los datos faltantes</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 3000);
                            }else {
                                $("#buscar_delegado_msj").html('<x id="inner_msj"><font style="color: green">La persona no esta registrada aún</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 3000);
                            }
                        }
                    }
                }
            });
        }
    }
</script>

<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
<?php endif; ?>
    
<?php if ($sf_user->hasFlash('error')): ?>
    <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
<form id="form_delegado">
    <fieldset>
        <h2>Datos Personales</h2>
        <div class="sf_admin_form_row">
            <div>
                <label>C&eacute;dula</label>
                <div class="content">
                    <input type="text" maxlength="8" id="personas_persona_ci" name="personas_persona[ci]"/>&nbsp;&nbsp;&nbsp;
                    <a href="#" onClick="javascript: buscarDelegado(); return false;"><?php echo image_tag('icon/2execute.png', array('style'=>'vertical-align: middle')) ?></a>
                    <input type='hidden' name='operaciones_registro[id]' id='operaciones_registro_id' value='<?php echo $registro_id ?>' />
                    <input type="hidden" id="personas_persona_id" name="personas_persona[id]" value=""/>
                    <x id="buscar_delegado_msj"></x>
                </div>
                <div class="help">Indique n&uacute;mero de documento de identidad</div>
            </div>
        </div>
        <div class='to_hid_delegado' style='display: none'>
            <div class="sf_admin_form_row">
                <div>
                    <label>Nombre</label>
                    <div class="content">
                        <?php echo $form['primer_nombre'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Apellido</label>
                    <div class="content">
                        <?php echo $form['primer_apellido'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>F. Nacimiento</label>
                    <div class="content">
                        <?php echo $form['f_nacimiento'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Sexo</label>
                    <div class="content">
                        <?php echo $form['sexo'] ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Edo. Civil</label>
                    <div class="content">
                        <?php echo $form['edo_civil'] ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div class='to_hid_delegado' style='display: none'>
        <fieldset>
            <h2>Contacto</h2>
            <div class="sf_admin_form_row">
                <div>
                    <label>Tel&eacute;fono</label>
                    <div class="content">
                        <?php echo $form['telf_movil']; ?>
                    </div>
                </div>
            </div>
            <div class="sf_admin_form_row">
                <div>
                    <label>Correo</label>
                    <div class="content">
                        <?php echo $form['email_personal']; ?>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <div class="sf_admin_form_row">
                <button id="guardar_delegado_add" onClick='javascript: $("#add_or_next").val("3")' style="height: 35px">
                    <strong>Guardar y crear otro</strong>&nbsp;<?php echo image_tag('icon/add.png', array('style' => 'vertical-align: middle')) ?>
                </button>&nbsp;&nbsp;
                <button id="guardar_delegado_next" onClick='javascript: $("#add_or_next").val("4")' style="height: 35px">
                    <strong>Guardar y continuar</strong>&nbsp;<?php echo image_tag('icon/execute.png', array('style' => 'vertical-align: middle')) ?>
                </button>
                <input type='hidden' name='destination' id='add_or_next' value='4' />
                <x id="save_delegado_msj"></x>
            </div>
        </fieldset>
    </div>
</form>