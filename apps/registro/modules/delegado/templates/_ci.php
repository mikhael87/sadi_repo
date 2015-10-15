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
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>delegado/BuscarDelegado',
                type:'POST',
                dataType:'json',
                data:'cedula='+$("#personas_persona_ci").val()+'&regid='+$('#pae_registro_id').val(),
                success:function(json){
                    $('#div_espera_verificar_cedula').html('');
                    if(json['status']=='ok') {
                        $('#personas_persona_primer_nombre').val(json['content']['primerNombre']);
                        $('#personas_persona_primer_apellido').val(json['content']['primerApellido']);
                        
                        $('#personas_persona_f_nacimiento_day').val(json['content']['fNacimientoDia']);
                        $('#personas_persona_f_nacimiento_month').val(json['content']['fNacimientoMes']);
                        $('#personas_persona_f_nacimiento_year').val(json['content']['fNacimientoAno']);
                        
                        $('#personas_persona_f_nacimiento_jquery_control').val(json['content']['fNacimiento']);
                        //DETERMINA SI SE DEBE CREAR EL REGISTRO EN PERSONAS O NO
                        $('#persona_id').val(json['content']['id']);
                    }else {
                        if(json['status']=='saime'){
                            $('#personas_persona_primer_nombre').val(json['content']['primerNombre']);
                            $('#personas_persona_primer_apellido').val(json['content']['primerApellido']);

                            $('#personas_persona_f_nacimiento_day').val(json['content']['fNacimientoDia']);
                            $('#personas_persona_f_nacimiento_month').val(json['content']['fNacimientoMes']);
                            $('#personas_persona_f_nacimiento_year').val(json['content']['fNacimientoAno']);

                            $('#personas_persona_f_nacimiento_jquery_control').val(json['content']['fNacimiento']);

                            //DETERMINA SI SE DEBE CREAR EL REGISTRO EN PERSONAS O NO
                            $('#persona_id').val('');
                        } else {
                            $('#personas_persona_primer_nombre').val('');
                            $('#personas_persona_primer_apellido').val('');

                            $('#personas_persona_f_nacimiento_day').val('');
                            $('#personas_persona_f_nacimiento_month').val('');
                            $('#personas_persona_f_nacimiento_year').val('');

                            $('#personas_persona_f_nacimiento_jquery_control').val('');

                            //DETERMINA SI SE DEBE CREAR EL REGISTRO EN PERSONAS O NO
                            $('#persona_id').val('');

                            if(json['status']=='empty') {
                                $('#div_error_verificar_cedula').html('Cedula no encontrada');
                            }else {
                                $('#div_error_verificar_cedula').html('El usuario ya se encuentra registrado');
                            }
                        }
                    }
                }})
        } else {
            alert(error);
        }
    }
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_ci">
    <div>
        <label for="personas_persona_ci">Cédula</label>
        <div class="content" style="position: relative;">
            <?php $cedula_edit = ''; if(!$form->isNew()) { $cedula_edit = $form['ci']->getValue(); } ?>
            <input type="hidden" id="cedula_edit" value="<?php echo $cedula_edit; ?>"/>
            <input type="text" id="personas_persona_ci" name="personas_persona[ci]" value="<?php if(!$form->isNew()) { echo $form['ci']->getValue(); } ?>"/>
            <input type="hidden" id="pae_registro_id" value="<?php echo sfContext::getInstance()->getUser()->getAttribute('pae_registro_id') ?>">
            <input type="hidden" id="persona_id" name="persona_id" value=""/>
            <?php
                $sf_seguridad = sfYaml::load(sfConfig::get('sf_root_dir') . "/config/siglas/seguridad.yml");
                if($sf_seguridad['conexion_saime']['activo']==true){
            ?>
            <div style="position: absolute; left: 300px; top: 2px; display: block; cursor: pointer;" id="div_persona_button_validate" title="Verificar cedula" onclick="verificarCedula(); return false;"><?php echo image_tag('icon/2execute.png'); ?></div>
            <div style="position: absolute; top: -5px; left: 295px; display: block; width: 200px; z-index: 100;" id="div_espera_verificar_cedula"></div>
            <div style="position: absolute; top: 2px; left: 320px; display: block; width: 200px; z-index: 101;" id="div_error_verificar_cedula"></div>
            <?php } ?>
        </div>

        <div class="help">Documento de identificación de la persona</div>
    </div>
</div>