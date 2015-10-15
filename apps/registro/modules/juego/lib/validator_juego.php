<script>
    $(document).ready(function()
    {
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido",
            digits: "Por favor ingrese solo números",
            maxlength: "Introdujo muchos caracteres",
        });

       $('#form_juego').validate({
           rules: {
                        'disciplina' : { required: true },
                        'operaciones_juego[disciplina_variante_id]' : { required: true, maxlength: 40 },
                        'operaciones_juego[fecha][day]' : { required: true },
                        'operaciones_juego[fecha][month]' : { required: true },
                        'operaciones_juego[fecha][year]' : { required: true },
                        'tipo[equipos][0][id]' : { required: { depends: function(element) { return ( $("select[name='tipo[equipos][1][id]']").val() != '') } } },
                        'tipo[equipos][1][id]' : { required: { depends: function(element) {
                            if($("select[name='tipo[equipos][0][id]']").length == 0) {
                                return false;
                            }else {
                                if($("select[name='tipo[equipos][0][id]']").val() != '') {
                                    return true;
                                }else {
                                    return false;
                                }
                            }
                        } } }
           },
           messages: {
                        'disciplina' : { required: 'Por favor, seleccione una opción' },
                        'operaciones_juego[disciplina_variante_id]' : { required: 'Por favor, seleccione una opción' },
                        'operaciones_juego[fecha][day]' : { required: '' },
                        'operaciones_juego[fecha][month]' : { required: '' },
                        'operaciones_juego[fecha][year]' : { required: '' },
                        'tipo[equipos][0][id]' : { required: '' },
                        'tipo[equipos][1][id]' : { required: '' }
            },
            errorElement: "span",
            submitHandler: function () {
                $('.sf_admin_action_save').children().attr('disabled','disabled');
                $('.sf_admin_action_save_and_add').children().attr('disabled','disabled');
                document.form_juego.submit();
                
//                guardar_delegado($("#add_or_next").val());
            }
        });
    });
</script>