<script>
$(document).ready(function(){
    jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        }); 
    
    $('#form_enviada').validate({
           rules: {
                        'val_funcionario_interno' : { validate_receptor_interno : true },
                        'permisos_tipo_permiso' : 'required',
                        'val_permiso' : { validate_permiso : true }
           },
           messages: {
               'permisos_tipo_permiso' : 'Seleccione un tipo de permiso.'
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
    
    jQuery.validator.addMethod("validate_permiso", function(value, element) {
            if($('#dias_solicitados').val()){
                var f_i = $('#permisos_f_inicio_jquery_control').val();
                var f_r = $('#permisos_f_retorno_jquery_control').val();
                var md_i= null;
                var md_r= null;
                
                if($('#tipo_permiso').val() == 1 || $('#tipo_permiso').val()== 2) {
                    var d = $('#dias_solicitados').val();
                }else {
                    md_i= $('#input_turno_inicio').val();
                    md_r= $('#input_turno_retorno').val();
                    var d = diasDiferencia(f_i, f_r, md_i, md_r);
                }
                
                var parametros_dias = $("#permisos_tipo_permiso").val();
                var parametros_dias = parametros_dias.split('-');
                var dias_max = parseInt(parametros_dias[1]);
                
                if(d >= 0) {
                    if(d <= dias_max) {
                        var turn_error= false;
                        if(d== 0 && md_i== md_r)
                            turn_error= true;
                        else {
                            if(d== 0 && md_i== 'T' && md_r== 'M') {
                                turn_error= true;
                            }
                        }
                        if(!turn_error){
                            var pass= 'false';
                            $.ajax({
                                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/librerias',
                                type:'POST',
                                dataType:'html',
                                async: false,
                                data:'forma='+ $('#formato_tipo_formato_id').val() +'&func=LapsoDisponible&var[f_i]='+$('#permisos_f_inicio_jquery_control').val()+'&var[f_r]='+$('#permisos_f_retorno_jquery_control').val(),
                                success:function(data, textStatus){
                                    pass= data;
                                }});
                            if(pass)
                                return true;
                            else {
                                alert('El lapso de tiempo ya esta ocupado por otra solicitud anterior, por favor verifique');
                                return false;
                            }
                        }
                    }else
                        return false;
                }else
                    return false;
            }else
                return false;
    }, "Error en formulario, utilice el <b>Calculo de Retorno</b> para mas informaci&oacute;n");
});
</script>
