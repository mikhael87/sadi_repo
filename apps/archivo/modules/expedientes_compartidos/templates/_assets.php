<script>
    $(document).ready(function()
    {
       jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        });

       $('#form_enviada').validate({
           rules: {
                        'val_funcionario_interno' : { validate_receptor_interno : true },
                        'correspondencia[emisor][0][funcionario_id]' : 'required',
                        'val_receptor_interno_repetido' : { validate_repeat_interno : true }
           },
           messages: {
                        'correspondencia[emisor][0][funcionario_id]' : 'Seleccione un firmante'
            },
            errorElement: "span"
        });

    jQuery.validator.addMethod("validate_receptor_interno", function(value, element) {
            if($('#grilla >tbody >tr').length == 0) {
                if($('#correspondencia_receptor_funcionario_id').val()== ''){
                    if($('#select_organismo').length) {
                        if($('#select_organismo').val()== '0') {
                            if($('#grilla_receptor_externo >tbody >tr').length == 0) {
                                return false;
                            }else {
                                return true;
                            }
                        }else {
                            return true;
                        }
                    }else {
                        return false;
                    }
                }else{
                    return true;
                }
            }else {
                return true;
            }
    }, "Seleccione una unidad y luego un funcionario");

    jQuery.validator.addMethod("validate_repeat_interno", function(value, element) {
          if($('#grilla >tbody >tr').length) {
                var repetido= 0;
                $('#grilla input').each(function() {
                    var dato= $(this).val();

                    $('#grilla input').each(function() {
                        if($(this).val()== dato){
                            repetido++;
                        }
                    });
                    if(dato== $('#correspondencia_receptor_unidad_id').val()+'#'+$('#correspondencia_receptor_funcionario_id').val()+'#N' || dato== $('#correspondencia_receptor_unidad_id').val()+'#'+$('#correspondencia_receptor_funcionario_id').val()+'#S') {
                        repetido++;
                    }
                });
                if(repetido > $('#grilla >tbody >tr').length)
                    return false;
                else
                    return true;
          }else {
                return true;
          }
    }, "Por favor, no repita los receptores.");

    });
</script>