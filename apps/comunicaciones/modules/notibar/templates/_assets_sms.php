<script>
    function borrar_individual(id, action){
        var old_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
        var up= '';
        var new_alto= '';
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_comunicaciones_url'); ?>notibar/borraIndividual',
            type:'POST',
            dataType:'html',
            data:'id_n='+id,
            success:function(data, textStatus){
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_comunicaciones_url'); ?>notibar/'+action,
                    type:'POST',
                    dataType:'html',
                    beforeSend: function(Obj){
                            jQuery('#content_notificacion_arriba_inner').html('<div style="text-align: center; padding-top: 40px">Cargando notificacioens...</div>');
                        },
                    success:function(data, textStatus){
                        data= $.trim(data);
                        if(data != '') {
                            jQuery('#content_notificacion_arriba_inner').html(data);
                            new_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
                            up= parseInt(old_alto) - parseInt(new_alto);
                            $('#content_notificacion_arriba').animate({ top: '-='+up },200);
                        }else
                            cerrar_notibar();
                        notibar_count();
                }});
        }});
    }

    function borrar_todas(ids, action){
        var old_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
        var up= '';
        var new_alto= '';
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_comunicaciones_url'); ?>notibar/borraTodas',
            type:'POST',
            dataType:'html',
            data:'ids_n='+ids,
            success:function(data, textStatus){
                notibar_count();
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_comunicaciones_url'); ?>notibar/'+action,
                    type:'POST',
                    dataType:'html',
                    beforeSend: function(Obj){
                            jQuery('#content_notificacion_arriba_inner').html('<div style="text-align: center; padding-top: 40px">Cargando notificacioens...</div>');
                        },
                    success:function(data, textStatus){
                        data= $.trim(data);
                        if(data != '') {
                            jQuery('#content_notificacion_arriba_inner').html(data);
                            new_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
                            up= parseInt(old_alto) - parseInt(new_alto);
                            $('#content_notificacion_arriba').animate({ top: '-='+up },200);
                        }else
                            cerrar_notibar();
                }});
        }});
    }

    function fn_responder(id, noti_id){
        var old_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
        var up= '';
        var new_alto= '';
        var obj= document.getElementById('table_respuesta_'+id);
        if(obj) {
            $('#table_respuesta_'+id).remove();

            if(parseInt($("#content_notificacion_arriba_inner").css('height')) < 500) {
                new_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
                up= parseInt(old_alto) - parseInt(new_alto);
                $('#content_notificacion_arriba').animate({ top: '-='+up },200);
            }
        }else {
            cadena = cadena = "<table id='table_respuesta_"+id+"'>";
            cadena = cadena + "<tr>";
            cadena = cadena + "<td colspan='4'>";
            cadena = cadena + "<textarea style='max-width: 500px; min-width: 500px; max-height: 20px; min-height: 20px; border: 3px solid #cccccc; padding: 3px; background-image: url(/images/other/mensajes.png); background-position: bottom right; background-repeat: no-repeat;' name='mensaje' id='mensaje_"+id+"'/>";
            cadena = cadena + "&nbsp;<input id='boton_submit_"+id+"' style='height: 31px; vertical-align: top' type='button' value='Enviar' name='enviar' onClick='javascript: enviarForm("+ id +", "+ noti_id +")' /></td>";
            cadena = cadena + "</td>";
            cadena = cadena + "</tr>";
            cadena = cadena + "<tr>";
            cadena = cadena + "<td style='padding-left: 360px'><input style='vertical-align: middle' type='checkbox' checked name='mensajes_archivar' id='mensajes_archivar_"+id+"'><font class='f14n'>Archivar</font>&nbsp;&nbsp;";
            cadena = cadena + "<input style='vertical-align: middle' type='checkbox' name='mensajes_email' id='mensajes_email_"+id+"'><font class='f14n'>Email</font>&nbsp;&nbsp;";
            cadena = cadena + "<input style='vertical-align: middle' type='checkbox' name='mensajes_sms' id='mensajes_sms_"+id+"'><font class='f14n'>SMS</font>&nbsp;&nbsp;";
            cadena = cadena + "</td></tr>";
            cadena = cadena + "</table>";

            $("#div_respuesta_"+id).html('');
            $("#div_respuesta_"+id).append(cadena);

            if(parseInt($("#content_notificacion_arriba_inner").css('height')) < 500) {
                alto= parseInt($("#div_respuesta_"+id).css('height')) + 15;
                $('#content_notificacion_arriba').animate({ top: '+='+alto },200);
            }
        }
    };

    function enviarForm(id, noti_id) {
        if($('#mensaje_'+id).val() != '') {
            $('#boton_submit_'+id).attr("disabled", true);
            $('#boton_submit_'+id).val('Espere');
            var old_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
            var up= '';
            var new_alto= '';

            var mensaje= $('#mensaje_'+id).val();
            var mensajes_archivar= ($('#mensajes_archivar_'+id).is(':checked'))? 't' : 'f';
            var mensajes_email= ($('#mensajes_email_'+id).is(':checked'))? 't' : 'f';
            var mensajes_sms= ($('#mensajes_sms_'+id).is(':checked'))? 't' : 'f';

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>mensajes/responder',
                type:'POST',
                dataType:'html',
                async:'false',
                data:'id='+id+'&mensajes_archivar='+mensajes_archivar+'&mensajes_email='+mensajes_email+'&mensajes_sms='+mensajes_sms+'&mensaje='+mensaje,
                success:function(data, textStatus){
                    alert(data);
                    if(mensajes_archivar == 't')
                        borrar_individual(noti_id, 'sms');
                    else {
                        $("#div_respuesta_"+id).html('');
                        if(parseInt($("#content_notificacion_arriba_inner").css('height')) < 500) {
                            new_alto= parseInt($("#content_notificacion_arriba_inner").css('height')) + 80;
                            up= parseInt(old_alto) - parseInt(new_alto);
                            $('#content_notificacion_arriba').animate({ top: '-='+up },200);
                        }
                    }
            }});
        }else {
            alert('Por favor, escriba la respuesta.');
        }
    }

    function fn_archivar(id, noti_id) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>mensajes/archivar',
            type:'POST',
            dataType:'html',
            async:'false',
            data:'id='+id,
            success:function(data, textStatus){
                borrar_individual(noti_id, 'sms');
        }});
    }
</script>
