<script>
    $(document).ready(function()
    {
        //CAMPOS A VALIDAR
        $('#correspondencia_formato_materiales_cantidad').rules("add", { materiales: true });
        
        //VALIDATORS
        jQuery.validator.addMethod("materiales", function(value, element) {
            if($('#grilla_materiales >tbody >tr').length == 0) {
                if ($('#correspondencia_formato_materiales_material_id').val()== '') {
                    return false;
                }else {
                    if ($('#correspondencia_formato_materiales_cantidad').val()== '') {
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
