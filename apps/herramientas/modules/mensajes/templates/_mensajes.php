<?php $session_funcionario = $sf_user->getAttribute('session_funcionario');?>
<script>
    var id_global;
    var cargado = false;
    
    function first(){
        var alt = $(".sf_admin_list").height();
        var alt2 = $("#mensajes").height();
        var wid = parseInt($("#sf_admin_container").width())-500;
        $(".f16n").css("width","300px");
        $(".f16n").css("max-width","300px");
        titulo = '<th id="titulo_mensaje" style="text-align: center;">No ha seleccionado ningun mensaje</th>'
        $(".sf_admin_list >table > thead > tr").append(titulo);
        $(".sf_admin_list >table > tfoot > tr > th").attr("colspan","4");
        $(".sf_admin_list >table > tbody > tr:first").append(add);

        cadena =  '<div style="width: '+wid+'px;">'
        cadena += '    <div id="contenido_mensaje" align="left" style="font-size: 12px;padding: 7px;max-height: '+alt+'px;overflow-x: hidden; min-height: '+alt2+'px">';
        cadena += '         <div id="mensaje" style="padding: 20px; word-wrap: break-word; min-height: '+alt2+'px">';
        cadena += '         </div>';
        cadena += '    </div><br/>';
        cadena += '    <div id="envio" align="center">';
        cadena += '    </div>';
        cadena += '</div>';
        $(".sf_admin_list_td_contenido > div").css("min-width", "300px");
        $('#mensajes').append(cadena);
    };
    
    function mensaje(id1, id2, titulo)
    {
        var wid = parseInt($("#sf_admin_container").width())-500;
        $("#mensaje").html("");
        $("#mensaje").html("<img src='/images/icon/cargando.gif' id='img_cargando' align='center'></img>");
        $(this).css("background","#000");
        text = '<textarea id="texto_mensaje" style="width: '+wid+'px; height: 40px; resize: none;" onkeypress="send_mensaje(this.value,event,'+id1+','+id2+');" placeholder="Escriba su mensaje aqui.."/><br/>'
        $('#titulo_mensaje').html(titulo);
        $('#envio').html(text);
        $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>mensajes/cargarMensajes',
                    type:'POST',
                    dataType:'html',
                    data:'id_uno='+id1+'&id_dos='+id2,
                    success:function(data, textStatus) {
                    $("#mensaje").append(data);
                    $("#img_cargando").remove();
                    $("#mensaje").parent().scrollTop($("#mensaje").get(0).scrollHeight);
                }
        });
        id_global = id1;
        cargado = true;
    };
    
    function leidoCambio(id_sms, id_autenticado, id_receptor)
    {
        if(id_autenticado=== id_receptor) {
            $('#sms_div_'+id_sms).parent().parent().css('background-color', '#FFFFFC');
            $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>mensajes/leido?id='+id_sms,
                        type:'POST',
                        dataType:'html',
                        success:function(data, textStatus) {
                        $("#leido_div_"+id_sms).html('');
                        $("#leido_div_"+id_sms).append(data);
                    }
                });
        }
    }
    
    function mostrarMensaje()
    {
        if(cargado != false){
            $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>mensajes/mostrarMensajes',
                        type:'POST',
                        dataType:'html',
                        data:'id='+id_global,
                        success:function(data, textStatus) {
                        $("#mensaje").append(data);
                        $("#mensaje").parent().scrollTop($("#mensaje").get(0).scrollHeight);
                    }
            });
        }
    }
    setInterval("mostrarMensaje()", 6000);
    function send_mensaje(mensaje,tecla,chat_id_uno, chat_id_dos){
        tecla = (tecla.keyCode ? tecla.keyCode : tecla.which);

        if ( tecla == 13 ){
            if($.trim(mensaje)!=''){
                
                marcacion = new Date();
                hora = marcacion.getHours();
                minutos = marcacion.getMinutes();
                segundos = marcacion.getSeconds();

                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>chat/saveChat',
                    type:'POST',
                    dataType:'html',
                    data:'chat='+$.trim(mensaje)+
                        '&user_1='+chat_id_uno+'&user_2='+chat_id_dos,
                    success:function(data, textStatus) {
                            $('#mensaje').append(data);
                
//                        if (!$('#mensaje_'+hora+'-'+minutos).length){
//                            
//                            meridiem = 'AM';
//                            horaf = hora;
//                            if (hora > 12) { horaf -= 12; meridiem = 'PM';} 
//                            else if (hora == 0) { horaf = 12; }
//                            else if (hora == 12) { meridiem = 'PM'; }
//                            
//                            $('div[id^=mensaje_]').each(function (){
//                                $(this).attr("id",'');
//                            });
//                            
//                            cadena =  '<hr>';
//                            cadena += '<div style="position: relative;">';
//                            cadena += '    <div style="position: absolute;">';
//                            cadena += '        <img width="30" src="/images/fotos_personal/<?php echo $session_funcionario['cedula']; ?>.jpg"/>';
//                            cadena += '    </div>';
//                            cadena += '    <div style="position: absolute; right: 10px; top:-5px" class="f10n">';
//                            cadena += '        '+horaf+':'+minutos+' '+meridiem;
//                            cadena += '    </div>';
//                            cadena += '</div>';
//                            cadena += '<div id="mensaje_'+hora+'-'+minutos+'" style="padding-left: 40px; min-height: 40px;">';
//                            cadena += '    '+data;
//                            cadena += '</div>';
//                        
//                            $('#mensaje').append(cadena);
//                        } else {
//                            $('#mensaje_'+hora+'-'+minutos).append(data);
//                        }
                        
                        $("#mensaje").parent().scrollTop($("#mensaje").get(0).scrollHeight);
                        $("#texto_mensaje").val("");
                    }
                })
            }
        }
    }
</script>