<script>
    $(document).ready(function()
    {
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido",
            digits: "Por favor ingrese solo números",
            maxlength: "Introdujo muchos caracteres",
        });

       $('#form_empresa').validate({
           rules: {
                        'empresas_empresa[nombre]' : { required: true, maxlength: 70 },
                        'empresas_empresa[siglas]' : { required: true, maxlength: 40 },
                        'empresas_empresa[empresa_tipo_id]' : { required: true },
                        'empresas_empresa[telf_uno]' : { digits: true, minlength: 11, maxlength: 11 },
                        'empresas_empresa[telf_dos]' : { digits: true, minlength: 11, maxlength: 11 },
                        'empresas_empresa[dir_av_calle_esq]' : { maxlength: 100 },
                        'empresas_empresa[dir_edf_torre_anexo]' : { maxlength: 100 },
                        'empresas_empresa[dir_piso]' : { maxlength: 10 },
                        'empresas_empresa[dir_urbanizacion]' : { maxlength: 100 },
                        'empresas_empresa[dir_ciudad]' : { maxlength: 100 },
                        'empresas_empresa[email_principal]' : { validate_email: true },
           },
           messages: {
                        'empresas_empresa[siglas]' : { maxlength: 'Debe ser un texto corto de maximo 40 caracteres' },
                        'empresas_empresa[telf_uno]' : { minlength: 'El número debe incluir codigo de área', maxlength: 'Disculpe, no deberían haber mas de 11 números' },
                        'empresas_empresa[telf_dos]' : { minlength: 'El número debe incluir codigo de área', maxlength: 'Disculpe, no deberían haber mas de 11 números' }
            },
            errorElement: "span",
            submitHandler: function () {
                $('#guardar_empresa').attr('disabled','disabled');
                guardar_empresa();
            }
        });

        jQuery.validator.addMethod("validate_email", function(value, element) {
                    if($("#empresas_empresa_email_principal").val() !== '') {
                        var email= $("#empresas_empresa_email_principal").val();
                        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        return regex.test(email);
                    }else {
                        return true;
                    }
        }, "Por favor, verifique el email.");
    });
</script>