<script>
    $(document).ready(function() {
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        }); 

    $('#form_correspondencia_externa').validate({
            rules: {
//                'correspondencia_correspondencia[f_envio][day]': { required: { depends: function(element) { if($('#fecha_actual_externo').is(':checked')) return false; else return true; } } },
                'val_correspondencia_externa_funcionario': { receptores_grilla: true },
                'correspondencia_correspondencia[emisor_organismo_id]': { validate_receptor_externo : true },
                'correspondencia_correspondencia[emisor_persona_id]' : { required: { depends: function(element) { return ( $("#select_organismo").val() != '0') } } },
                'correspondencia_correspondencia[emisor_persona_cargo_id]' : { required: { depends: function(element) { return ( $("#select_organismo").val() != '0') } } },
                'val_adjunto_externo' : { adjuntos: true },
                'resumen_externo' : 'required',
                'correspondencia_correspondencia[email_externo]': { validate_email_externo: true },
                'correspondencia_correspondencia[telf_local_externo]' : { maxlength: 11, minlength: 11, digits: true },
                'correspondencia_correspondencia[telf_movil_externo]' : { maxlength: 11, minlength: 11, digits: true }
            },
            messages: {
                'correspondencia_correspondencia[emisor_persona_id]': 'Seleccione o cree una persona',
                'correspondencia_correspondencia[emisor_persona_cargo_id]': 'Seleccione o cree un cargo',
                'correspondencia_correspondencia[telf_local_externo]' : { maxlength: 'Por favor, no ingrese mas de 11 digitos', minlength: 'Por favor, no ingrese menos de 7 digitos', digits: 'Por favor, ingrese solo números' },
                'correspondencia_correspondencia[telf_movil_externo]' : { maxlength: 'Por favor, no ingrese mas de 11 digitos', minlength: 'Por favor, no ingrese menos de 7 digitos', digits: 'Por favor, ingrese solo números' }
            },
            errorElement: "span"
        });
    });
    
    jQuery.validator.addMethod("receptores_grilla", function(value, element) {
            if($('#grilla >tbody >tr').length == 0) {
                if($('#receptor_interno_funcionario_id').val()== ''){
                    return false;
                }else{
                    return true;
                }
            }else {
                return true;
            }
    }, "Seleccione una unidad y luego un funcionario");
    
    jQuery.validator.addMethod("adjuntos", function(value, element) {
            if($('#div_adjunto_listos >x').length == 0) {
                if($('#correspondencia_externa_adjunto').val()== ''){
                    //SE HABILITA PARA PERMITIR GUARDAR DOCS SIN ARCHIVO DIGITALIZADO (CASO DE CLIENTES SIN SCANER)
                    return true;
                }else{
                    return true;
                }
            }else {
                return true;
            }
    }, "Adjunte los documentos digitalizados.");
    
    jQuery.validator.addMethod("validate_receptor_externo", function(value, element) {
                if($('#select_organismo').val()== '0') {
                    return false;
                }else {
                    return true;
                }
    }, "Campo requerido");
    
    jQuery.validator.addMethod("validate_email_externo", function(value, element) {
                if($("#correspondencia_correspondencia_email_externo").val() != '') {
                    var email= $("#correspondencia_correspondencia_email_externo").val();
                    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    return regex.test(email);
                }else {
                    return true;
                }
    }, "Por favor, verifique el email.");
</script>
