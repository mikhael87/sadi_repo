<script>
    $(document).ready(function()
    {

        //CAMPOS A VALIDAR
        $('#memorandum_asunto').rules("add", { required: true, nonstring: true });
        $('#memorandum_contenido_'+<?php echo $semilla ?>).rules("add", 'ckeditor_content');

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

         jQuery.validator.addMethod("nonstring", function(value, element) {
              cadena= $('#memorandum_asunto').val().toLowerCase();
              cadena= trim(cadena);
              cadena = cadena.replace(/\s/g,'');
            if (/eneltexto/.test(cadena))
                return false;
            else
                return true;
         }, "Por favor, no use la frase: \"En el texto\"");
    });

    function trim (myString) {
        return myString.replace(/^\s+/g,'').replace(/\s+$/g,'');
    }

    function setear_plantilla(pantilla_id) {
        $('#memorandum_asunto').val($('#plantilla_memorandum_asunto_'+pantilla_id).html());
        $('#cke_contents_memorandum_contenido_<?php echo $semilla; ?> iframe').contents().find('body').html($('#plantilla_memorandum_contenido_'+pantilla_id).html());
        
        $('#plantillas_listado').slideToggle();
    }
    
    function guardar_plantilla() {
        error = false;
        
        if($.trim($('#input_plantilla_nueva').val())=='' || $.trim($('#input_plantilla_nueva').val())=='nombre de la plantilla'){
            error = true;
            alert('Agregue el nombre con el cual desea identificar la plantilla.');            
        } else {
            asunto= trim($.trim($('#memorandum_asunto').val().toLowerCase()));
            asunto = asunto.replace(/\s/g,'');
        
            if(asunto=='' || /eneltexto/.test(asunto)){
                error = true;
                alert('Agregue el asunto del memorando para poder guardarlo como plantilla (NO use la frase "En el texto").');
            } else {
                var contenido = $('#cke_contents_memorandum_contenido_<?php echo $semilla; ?> iframe').contents().find('body').html();
                var contenido_ex= contenido;
                contenido = contenido.replace(/(<([^>]+)>)/gi,'');
                contenido_ex = contenido_ex.replace(/&nbsp;/gi,' ');
                if (contenido==''){
                    error = true;
                    alert('Agregue el contenido del memorando para poder guardarlo como plantilla.');
                }
            }
        }
        
        if(error==false){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/guardarPlantilla',
                type:'POST',
                dataType:'html',
                data:'tipo_formato_id='+$('#formato_tipo_formato_id').val()+'&plantilla[nombre_plantilla]='+$.trim($('#input_plantilla_nueva').val())+'&plantilla[asunto]='+$.trim($('#memorandum_asunto').val())+'&plantilla[contenido]='+contenido_ex,
                success:function(data, textStatus){
                    $("#plantillas").html('');
                    $("#plantilla_creada").val(1);
            }})  
        }
    }
</script>
