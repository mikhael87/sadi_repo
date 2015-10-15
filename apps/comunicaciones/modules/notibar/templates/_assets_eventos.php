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

    function group_archivo_eliminar(grupo_id, id, action) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_archivo_url'); ?>grupos/eliminarFunGrupo',
            type:'POST',
            dataType:'html',
            data: 'id='+grupo_id,
            success:function(data, textStatus){
                data= $.trim(data);
                if(data)
                    borrar_individual(id, action);
                else
                    alert('Error al eliminar notificacion.')
            }
        });
    }

    function group_correspondencia_eliminar(grupo_id, id, action) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>grupos/eliminarFunGrupo',
            type:'POST',
            dataType:'html',
            data: 'id='+grupo_id,
            success:function(data, textStatus){
                data= $.trim(data);
                if(data)
                    borrar_individual(id, action);
                else
                    alert('Error al eliminar notificacion.')
            }
        });
    }
</script>
