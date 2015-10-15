 
<?php use_stylesheet('../css/global.css', 'first') ?> 
<?php use_stylesheet('../css/default.css', 'first') ?> 

<script>
    $(document).ready(function()
    {
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido",
            digits: "Por favor ingrese solo números",
            maxlength: "Introdujo muchos caracteres",
        });

       $('#form_pago').validate({
           rules: {
                        'operaciones_registro_pago[monto]' : { required: true, maxlength: 15, number: true },
                        'operaciones_registro_pago[banco]' : { required: false, maxlength: 70 },
                        'operaciones_registro_pago[referencia]' : { required: false, maxlength: 70 },
           },
           messages: {
                        'operaciones_registro_pago[monto]' : { maxlength: 'Disculpe, la cifra es exorbitante', number: 'Introduzca un monto valido, use punto (.) para indicar decimales' },
                        'empresas_empresa[telf_uno]' : { maxlength: 'Disculpe, demasiados caracteres' },
                        'empresas_empresa[telf_dos]' : { maxlength: 'Disculpe, demasiados caracteres' }
            },
            errorElement: "span",
//            submitHandler: function () {
//                $('#guardar_empresa').attr('disabled','disabled');
//                guardar_empresa();
//            }
        });
//
//        jQuery.validator.addMethod("validate_email", function(value, element) {
//                    if($("#empresas_empresa_email_principal").val() !== '') {
//                        var email= $("#empresas_empresa_email_principal").val();
//                        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
//                        return regex.test(email);
//                    }else {
//                        return true;
//                    }
//        }, "Por favor, verifique el email.");
    });
</script>