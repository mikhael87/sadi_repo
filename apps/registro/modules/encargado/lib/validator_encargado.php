<script>
    $(document).ready(function()
    {
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido",
            digits: "Por favor ingrese solo números",
            maxlength: "Introdujo muchos caracteres",
        });

       $('#form_encargado').validate({
           rules: {
                        'personas_persona[ci]' : { required: true, maxlength: 70 },
                        'personas_persona[primer_nombre]' : { required: true, maxlength: 40 },
                        'personas_persona[primer_apellido]' : { required: true, maxlength: 40 },
                        'personas_persona[f_nacimiento][day]' : { required: true },
                        'personas_persona[f_nacimiento][month]' : { required: true },
                        'personas_persona[f_nacimiento][year]' : { required: true },
                        'personas_persona[telf_movil]' : { digits: true, minlength: 11, maxlength: 11 },
                        'personas_persona[email_personal]' : { validate_email: true },
           },
           messages: {
                        'personas_persona[telf_movil]' : { minlength: 'El número debe incluir codigo de área', maxlength: 'Disculpe, no deberían haber mas de 11 números' },
                        'personas_persona[f_nacimiento][day]' : { required: '' },
                        'personas_persona[f_nacimiento][month]' : { required: '' },
                        'personas_persona[f_nacimiento][year]' : { required: '' }
            },
            errorElement: "span",
            submitHandler: function () {
                $('.sf_admin_action_save').children().attr('disabled','disabled');
                $('.sf_admin_action_save_and_add').children().attr('disabled','disabled');
                document.form_delegado.submit();
            }
        });

        jQuery.validator.addMethod("validate_email", function(value, element) {
                    if($("#personas_persona_email_personal").val() !== '') {
                        var email= $("#personas_persona_email_personal").val();
                        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        return regex.test(email);
                    }else {
                        return true;
                    }
        }, "Por favor, verifique el email.");
    });
</script>