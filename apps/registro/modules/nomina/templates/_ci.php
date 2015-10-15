<script>
    function verificarCedula()
    {
        var error = null;
        if(!$("#personas_persona_ci").val()) {
            error = 'Escriba una cedula para realizar la verificacion ante el SAIME.';
        }
        
        if(error==null) {
            $('#div_error_verificar_cedula').html('');
            $('#div_espera_verificar_cedula').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Verificando cedula...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>nomina/buscarParticipante',
                type:'POST',
                dataType:'json',
                data:'cedula='+$("#personas_persona_ci").val(),
                success:function(json){
                    if(json.status === 'owndb') {
                        $('#buscar_participante_msj').html('');

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
                        
                        //BLOQUEO DE CAMPOS
//                        if(json.content.primerNombre === '') { $('#personas_persona_primer_nombre').attr('disabled', 'disabled') };
//                        if(json.content.primerApellido === '') { $('#personas_persona_primer_apellido').attr('disabled', 'disabled') };
//                        if(json.content.fNacimientoDia !== '' && json.content.fNacimientoMes !== '' && json.content.fNacimientoAno !== '') {
//                            $('.ui-datepicker-trigger').hide();
//                            $('#personas_persona_f_nacimiento_day').attr('disabled', 'disabled');
//                            $('#personas_persona_f_nacimiento_month').attr('disabled', 'disabled');
//                            $('#personas_persona_f_nacimiento_year').attr('disabled', 'disabled');
//                        }
//                        $('#personas_persona_edo_civil_S').attr('disabled', 'disabled');
//                        $('#personas_persona_edo_civil_C').attr('disabled', 'disabled');
//                        $('#personas_persona_edo_civil_D').attr('disabled', 'disabled');
//                        $('#personas_persona_edo_civil_V').attr('disabled', 'disabled');
//                        $('#personas_persona_sexo_M').attr('disabled', 'disabled');
//                        $('#personas_persona_sexo_F').attr('disabled', 'disabled');
                        
                        $('li.sf_admin_action_save > input').attr('disabled', false);
                        $('li.sf_admin_action_save_and_add > input').attr('disabled', false);
                    }else {
                        $('.to_hid_participante').show('fast');
                        $('#personas_persona_id').val('');
                        //REINICIO DE FORM
                        $('#personas_persona_primer_nombre').val('');
                        $('#personas_persona_primer_apellido').val('');
//                        $('#personas_persona_primer_nombre').attr('disabled', false);
//                        $('#personas_persona_primer_apellido').attr('disabled', false);
                        $('.ui-datepicker-trigger').show();
//                        $('#personas_persona_f_nacimiento_day').attr('disabled', false);
//                        $('#personas_persona_f_nacimiento_month').attr('disabled', false);
//                        $('#personas_persona_f_nacimiento_year').attr('disabled', false);
                        $('#personas_persona_f_nacimiento_day option[value=""]').attr('selected', 'selected');
                        $('#personas_persona_f_nacimiento_month option[value=""]').attr('selected', 'selected');
                        $('#personas_persona_f_nacimiento_year option[value=""]').attr('selected', 'selected');

                        $('#personas_persona_edo_civil_S').attr('checked', true);
//                        $('#personas_persona_edo_civil_S').attr('disabled', false);
//                        $('#personas_persona_edo_civil_C').attr('disabled', false);
//                        $('#personas_persona_edo_civil_D').attr('disabled', false);
//                        $('#personas_persona_edo_civil_V').attr('disabled', false);
                        $('#personas_persona_sexo_M').attr('checked', true);
//                        $('#personas_persona_sexo_M').attr('disabled', false);
//                        $('#personas_persona_sexo_F').attr('disabled', false);

                        $('#personas_persona_telf_movil').val('');
                        $('#personas_persona_email_personal').val('');
                        
                        if(json.status === 'empresa' || json.status === 'equipo') {
                            if(json.status === 'empresa') {
                                $("#buscar_participante_msj").html('<x id="inner_msj"><font style="color: red">La persona ya se encuentra inscrita en 3 disciplinas</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 6000);

                                $('li.sf_admin_action_save > input').attr('disabled', true);
                                $('li.sf_admin_action_save_and_add > input').attr('disabled', true);
                            }else {
                                $("#buscar_participante_msj").html('<x id="inner_msj"><font style="color: red">La persona ya se encuentra inscrita en este mismo equipo</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 6000);

                                $('li.sf_admin_action_save > input').attr('disabled', true);
                                $('li.sf_admin_action_save_and_add > input').attr('disabled', true);
                            }
                        }else {
                            $('li.sf_admin_action_save > input').attr('disabled', false);
                            $('li.sf_admin_action_save_and_add > input').attr('disabled', false);
                        
                            if(json.status === 'saime') {
                                $('#personas_persona_primer_nombre').val(json.content.primerNombre);
                                $('#personas_persona_primer_apellido').val(json.content.primerApellido);
                                $('#personas_persona_f_nacimiento_day option[value='+json.content.fNacimientoDia+']').attr('selected', 'selected');
                                $('#personas_persona_f_nacimiento_month option[value='+json.content.fNacimientoMes+']').attr('selected', 'selected');
                                $('#personas_persona_f_nacimiento_year option[value='+json.content.fNacimientoAno+']').attr('selected', 'selected');
                                
                                $("#buscar_participante_msj").html('<x id="inner_msj"><font style="color: green">Por favor complete los datos faltantes</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 3000);
                            }else {
                                $("#buscar_participante_msj").html('<x id="inner_msj"><font style="color: green">La persona no esta registrada aún</font></x>');
                                setTimeout(function(){
                                    $("#inner_msj").fadeOut(1000);
                                }, 3000);
                            }
                        }
                    }
                }})
        } else {
            alert(error);
        }
    }
</script>

<div class="sf_admin_form_row">
    <div>
        <label>C&eacute;dula</label>
        <div class="content">
            <input type="text" maxlength="8" id="personas_persona_ci" name="personas_persona[ci]"/>&nbsp;&nbsp;&nbsp;
            <a href="#" onClick="javascript: verificarCedula(); return false;"><?php echo image_tag('icon/2execute.png', array('style'=>'vertical-align: middle')) ?></a>
            <x id="buscar_participante_msj"></x>
        </div>
        <div class="help">Indique n&uacute;mero de documento de identidad</div>
    </div>
</div>