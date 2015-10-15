<script>
    function verificarCedula()
    {
        var error = null;
        if(!$("#funcionarios_funcionario_ci").val()) {
            error = 'Escriba una cedula para realizar la verificacion ante el SAIME.';
        }
        
        if(error==null) {
            $('#div_error_verificar_cedula').html('');
            $('#div_espera_verificar_cedula').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Verificando cedula...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/verificarCedula',
                type:'POST',
                dataType:'json',
                data:'cedula_verificar='+$("#funcionarios_funcionario_ci").val(),
                success:function(json){
                    $('#div_espera_verificar_cedula').html('');
                    if(json['persona_saime']==true){
                        $('#funcionarios_funcionario_primer_nombre').val(json['primer_nombre']);
                        $('#funcionarios_funcionario_segundo_nombre').val(json['segundo_nombre']);
                        $('#funcionarios_funcionario_primer_apellido').val(json['primer_apellido']);
                        $('#funcionarios_funcionario_segundo_apellido').val(json['segundo_apellido']);
                        
                        $('#funcionarios_funcionario_f_nacimiento_day').val(json['f_nacimiento_day']);
                        $('#funcionarios_funcionario_f_nacimiento_month').val(json['f_nacimiento_month']);
                        $('#funcionarios_funcionario_f_nacimiento_year').val(json['f_nacimiento_year']);
                        
                        $('#funcionarios_funcionario_f_nacimiento_jquery_control').val(json['f_nacimiento']);
                    } else {
                        $('#funcionarios_funcionario_primer_nombre').val('');
                        $('#funcionarios_funcionario_segundo_nombre').val('');
                        $('#funcionarios_funcionario_primer_apellido').val('');
                        $('#funcionarios_funcionario_segundo_apellido').val('');
                        
                        $('#funcionarios_funcionario_f_nacimiento_day').val('');
                        $('#funcionarios_funcionario_f_nacimiento_month').val('');
                        $('#funcionarios_funcionario_f_nacimiento_year').val('');
                        
                        $('#funcionarios_funcionario_f_nacimiento_jquery_control').val('');
                        
                        $('#div_error_verificar_cedula').html('Cedula no encontrada');
                    }
                }})
        } else {
            alert(error);
        }
    }
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_ci">
    <div>
        <label for="funcionarios_funcionario_ci">Cédula</label>
        <div class="content" style="position: relative;">
            <?php $cedula_edit = ''; if(!$form->isNew()) { $cedula_edit = $form['ci']->getValue(); } ?>
            <input type="hidden" id="cedula_edit" value="<?php echo $cedula_edit; ?>"/>
            <input type="text" id="funcionarios_funcionario_ci" name="funcionarios_funcionario[ci]" value="<?php if(!$form->isNew()) { echo $form['ci']->getValue(); } ?>"/>
            
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