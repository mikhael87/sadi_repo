<script>
$(document).ready(function(){
    jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        }); 
    
    $('#form_enviada').validate({
           rules: {
                        'val_funcionario_interno' : { validate_receptor_interno : true },
                        'reposos_tipo_reposo' : 'required',
                        'val_reposo' : { validate_reposo : true }
           },
           messages: {
               'reposos_tipo_reposo' : 'Seleccione un tipo de reposo.'
            },
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
    
    jQuery.validator.addMethod("validate_reposo", function(value, element) {
        var pass= 'false';
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/librerias',
            type:'POST',
            dataType:'html',
            async: false,
            data:'forma=0&func=LapsoDisponible&var[f_i]='+$('#reposos_f_inicio_jquery_control').val()+'&var[d_s]='+$('#dias_solicitados').val(),
            success:function(data, textStatus){
                pass= data;
            }});
        if(pass)
            return true;
        else {
            alert('El lapso de tiempo ya esta ocupado por otra solicitud anterior, por favor verifique');
            return false;
        }
    }, "Error en formulario, utilice el <b>Calculo de Retorno</b> para mas informaci&oacute;n");
});
</script>
