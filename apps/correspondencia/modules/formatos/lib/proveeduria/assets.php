<script>
    $(document).ready(function()
    {
        //CAMPOS A VALIDAR
        $('#correspondencia_formato_proveedurias_cantidad').rules("add", { proveedurias: true });
        
        //VALIDATORS
        jQuery.validator.addMethod("proveedurias", function(value, element) {
            if($('#grilla_proveedurias >tbody >tr').length == 0) {
                if ($('#correspondencia_formato_proveedurias_proveeduria_id').val()== '') {
                    return false;
                }else {
                    if ($('#correspondencia_formato_proveedurias_cantidad').val()== '') {
                        return false;
                    }else {
                        return true;
                    }
                }
            }else {
                return true;
            }
        }, "Estos campos son requeridos");
    });
</script>
