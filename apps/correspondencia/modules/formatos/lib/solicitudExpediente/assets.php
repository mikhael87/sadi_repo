<script>
    $(document).ready(function()
    {
        //CAMPOS A VALIDAR
        $('#solicitud_expediente_motivo').rules("add", 'required');
        $('#val_tipo_prestamo_expediente').rules("add", { tipos_prestamos: true });
        $('#val_serie_documental').rules("add", { serie_documental : true });
        $('#div_descriptores_serie_expediente').rules("add", { descriptores_series: true });
        
        //VALIDATORS
        jQuery.validator.addMethod("tipos_prestamos", function(value, element) {
            var alguno= false;
            $('.val_tipos_prestamos').each(function() {
                if($(this).attr('checked')) {
                    alguno= true;
                }
            });
            if(alguno)
                return true;
            else
                return false;
        }, "Seleccione un tipo de prestamo");

        jQuery.validator.addMethod("serie_documental", function(value, element) {
            //El jQuery validate no toma este combo por el name, por eso se hace a traves del input hidden
            if($('#select_serie').val()== '' || $('#select_serie').val()== 0)
                return false;
            else
                return true;
        }, "Este campo es requerido");

        jQuery.validator.addMethod("descriptores_series", function(value, element) {
            if($('#div_descriptores_serie input').length > 0) {
                var alguno= false;
                $('#div_descriptores_serie input').each(function() {
                    if($(this).val() != '') {
                        alguno= true;
                    }
                });
                if($('#solicitud_expediente_numero').val() != '') {
                    alguno= true;
                }
                if(alguno)
                    return true;
                else
                    return false;    
            }else {
                if($('#select_serie').val()== '' || $('#select_serie').val()== 0)
                    return true;
                else
                    return false;
            }
        }, "Llene alguno de los campos mostrados");
    });
</script>
