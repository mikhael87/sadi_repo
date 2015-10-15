<script>
$(document).ready(function(){
    jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        });

    $('#form_enviada').validate({
           rules: {
                        'val_funcionario_interno' : { validate_receptor_interno : true },
           },
           messages: { },
            errorElement: "span",
            submitHandler: function () {
                $('#guardar_documento').attr('disabled','disabled');
                document.form_enviada.submit();
            }
        });


      //VALIDATORS
     jQuery.validator.addMethod("validate_receptor_interno", function(value, element) {
            if($('#grilla >tbody >tr').length == 0) {
                if($('#correspondencia_receptor_funcionario_id').val()== '' || $('#correspondencia_receptor_funcionario_id').val()== 0){
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

});
</script>
