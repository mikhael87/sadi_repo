<?php use_helper('jQuery'); ?>
<script>
    function procesar_funcionario(button) {
        $('#tipo_button').html('<input type="hidden" name="'+button+'"/>');
        
        if($('#cedula_edit').val()==''){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/chequearCedulaExistente',
                type:'POST',
                dataType:'json',
                data:'ci='+$('#funcionarios_funcionario_ci').val(),
                success:function(json, textStatus){
                    if(json.status == true) {
                        alert('Ya se encuentra registrado este funcionario.');
                        return false;
                    } else {
                        if(asignacion_cargo == true) { 
                            var error='';
                            if($("#asignacion_cargo").val()==''){
                                error = error+'\nFalta seleccionar el cargo que se asignará.';
                            }

                            if($("#asignacion_f_ingreso_jquery_control").val()=='' || $("#asignacion_f_ingreso_jquery_control").val()=='--'){
                                error = error+'\nFalta seleccionar la fecha de asignación en el cargo.';
                            }

                            if(error!=''){
                                if(confirm("Existen los siguientes errores en la asignación del cargo:\n"+error+"\n\nSi desea continuar solo se guardará el funcionario y posteriormente debera asignar el cargo.")) {
                                    $('#div_asignacion_cargo').html('');
                                    $("#form_funcionario").submit();
                                } else {
                                    return false;
                                }
                            } else {
                                $("#asignacion_f_ingreso_jquery_control").prop('disabled', false);
                                $("#form_funcionario").submit();
                            }
                        } else {
                            $("#form_funcionario").submit();
                        }
                    }
                }});
        } else {
            $("#form_funcionario").submit();
        }
    };
</script>

<?php $cedula_edit = ''; if(!$form->isNew()) { $cedula_edit = $form['ci']->getValue(); } ?>
<input type="hidden" id="cedula_edit" value="<?php echo $cedula_edit; ?>"/>