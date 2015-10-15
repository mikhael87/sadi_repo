<script>
    $(document).ready(function()
    {
       $('#form_prestamo').validate({
           rules: {
                        'prestamo_archivo[solicitante][funcionario_id]' : 'required',
                        'val_doc' : { documentos_prestamo : true },
                        'archivo_prestamo_archivo[f_expiracion][day]' : 'required',
                        'archivo_prestamo_archivo[f_expiracion][month]' : 'required',
                        'archivo_prestamo_archivo[f_expiracion][year]' : 'required',
                        'val_tipo_prestamo' : { tipo_prestamo : { depends: function(element) { return ( $('#archivo_prestamo_tipo_prestamo_digital').length || $('#archivo_prestamo_tipo_prestamo_fisico').length) } } }
           },
           messages: {
                        'prestamo_archivo[solicitante][funcionario_id]' : 'Seleccione una unidad y luego un funcionario.',
                        'archivo_prestamo_archivo[f_expiracion][day]' : '',
                        'archivo_prestamo_archivo[f_expiracion][month]' : '',
                        'archivo_prestamo_archivo[f_expiracion][year]' : ''
            },
            errorElement: "span"
        });
    });

    jQuery.validator.addMethod("documentos_prestamo", function(value, element) {
        if($('#documentos_a_prestar_table >tbody >tr').length > 0) {
            var alguno= false;
            $(".documentos_prest").each(function(){
                if($(this).attr('checked'))
                    alguno= true;
            });
            if(alguno)
                return true;
            else
                return false;
        }else {
            return false;
        }

    }, "No hay documentos a prestar.");

    jQuery.validator.addMethod("tipo_prestamo", function(value, element) {
        var alguno= false;
        if($("#archivo_prestamo_tipo_prestamo_digital").attr('checked'))
            alguno= true;

        if($("#archivo_prestamo_tipo_prestamo_fisico").attr('checked'))
            alguno= true;

        if(alguno)
            return true;
        else
            return false;
    }, "Por favor, seleccione un tipo de prestamo.");
</script>
