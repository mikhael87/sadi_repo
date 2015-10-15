<script>
    $(document).ready(function()
    {
        //CAMPOS A VALIDAR
        $('#oficio_contenido_'+<?php echo $semilla ?>).rules("add", 'ckeditor_content');
        
        //VALIDATORS
        jQuery.validator.addMethod("ckeditor_content", function(value, element) {
            var cadena = $('.cke_contents iframe').contents().find('body').html();
            cadena = cadena.replace(/(<([^>]+)>)/gi,'');
            if (cadena== '') {
                return false;
            }else {
                return true;
            }
         }, "Introduzca un contenido");
    });
    

    function setear_plantilla(pantilla_id) {
        $('#cke_contents_oficio_contenido_<?php echo $semilla; ?> iframe').contents().find('body').html($('#plantilla_oficio_contenido_'+pantilla_id).html());
        
        $('#plantillas_listado').slideToggle();
    }
    
    function guardar_plantilla() {
        error = false;
        
        if($.trim($('#input_plantilla_nueva').val())=='' || $.trim($('#input_plantilla_nueva').val())=='nombre de la plantilla'){
            error = true;
            alert('Agregue el nombre con el cual desea identificar la plantilla.');            
        } else {
            var contenido = $('#cke_contents_oficio_contenido_<?php echo $semilla ?> iframe').contents().find('body').html();
            var contenido_ex= contenido;
            contenido = contenido.replace(/(<([^>]+)>)/gi,'');
            contenido_ex = contenido_ex.replace(/&nbsp;/gi,' ');
            if (contenido==''){
                error = true;
                alert('Agregue el contenido del oficio para poder guardarlo como plantilla.');
            }
        }
        
        if(error==false){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/guardarPlantilla',
                type:'POST',
                dataType:'html',
                data:'tipo_formato_id='+$('#formato_tipo_formato_id').val()+'&plantilla[nombre_plantilla]='+$.trim($('#input_plantilla_nueva').val())+'&plantilla[contenido]='+contenido_ex,
                success:function(data, textStatus){
                    $("#plantillas").html('');
                    $("#plantilla_creada").val(1);
            }})  
        }
    }
</script>
