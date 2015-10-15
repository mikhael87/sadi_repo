<script>
    $(document).ready(function()
    {
       jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        });

       $('#form_enviada').validate({
           rules: {
                        'val_funcionario_interno' : { validate_receptor_interno : true },
                        'correspondencia[emisor][0][funcionario_id]' : 'required',
                        'correspondencia[receptor_externo][organismo_id]' : { validate_receptor_externo : true },
                        'correspondencia[receptor_externo][persona_id]' : { required: { depends: function(element) { return ( $("#select_organismo").val() != '0') } } },
                        'correspondencia[receptor_externo][persona_cargo_id]' : { required: { depends: function(element) { return ( $("#select_organismo").val() != '0') } } },
                        'val_receptor_interno_repetido' : { validate_repeat_interno : true },
                        'val_receptor_externo_repetido' : { validate_repeat_externo : true }
           },
           messages: {
                        'correspondencia[emisor][0][funcionario_id]' : 'Seleccione un firmante'
            },
            errorElement: "span",
            submitHandler: function () {
                $('#guardar_documento_y_agregar').attr('disabled','disabled');
                $('#guardar_documento').attr('disabled','disabled');
                document.form_enviada.submit();
            }
        });

    jQuery.validator.addMethod("validate_receptor_interno", function(value, element) {
            if($('#grilla >tbody >tr').length == 0) {
                if($('#correspondencia_receptor_funcionario_id').val()== '' || $('#correspondencia_receptor_funcionario_id').val()== 0){
                    if($('#select_organismo').length) {
                        if($('#select_organismo').val()== '0') {
                            if($('#grilla_receptor_externo >tbody >tr').length == 0) {
//                                $('#guardar_documento_y_agregar').attr('disabled','disabled');
//                                $('#guardar_documento').attr('disabled','disabled');
                                return false;
                            }else {
                                return true;
                            }
                        }else {
                            return true;
                        }
                    }else {
                        return false;
                    }
                }else{
                    return true;
                }
            }else {
                return true;
            }
    }, "Seleccione una unidad y luego un funcionario");

    jQuery.validator.addMethod("validate_receptor_externo", function(value, element) {
                if($('#grilla_receptor_externo >tbody >tr').length == 0) {
                    if($('#select_organismo').val()== '0') {
                        if($("#correspondencia_receptor_funcionario_id").length) {
                            if($("#correspondencia_receptor_funcionario_id").val() != '') {
                                return true;
                            }else {
                                if($('#grilla >tbody >tr').length == 0) {
                                    return false;
                                }else {
                                    return true;
                                }
                            }
                        }else {
                            return false;
                        }
                    }else {
                        return true;
                    }
                }else {
                    return true;
                }
    }, "Campo requerido");

    jQuery.validator.addMethod("validate_repeat_externo", function(value, element) {
          if($('#grilla_receptor_externo >tbody >tr').length) {
                var repetido= 0;
                $('#grilla_receptor_externo input').each(function() {
                    var dato= $(this).val();

                    $('#grilla_receptor_externo input').each(function() {
                        if($(this).val()== dato){
                            repetido++;
                        }
                    });
                    if(dato== $('#select_organismo').val()+'#'+$('#select_persona').val()+'#'+$('#select_cargos').val()) {
                        repetido++;
                    }
                });
                if(repetido > $('#grilla_receptor_externo >tbody >tr').length)
                    return false;
                else
                    return true;
          }else {
                return true;
          }
    }, "Por favor, no repita los receptores.");

    jQuery.validator.addMethod("validate_repeat_interno", function(value, element) {
          if($('#grilla >tbody >tr').length) {
                var repetido= 0;
                $('#grilla input').each(function() {
                    var dato= $(this).val();

                    $('#grilla input').each(function() {
                        if($(this).val()== dato){
                            repetido++;
                        }
                    });
                    if(dato== $('#correspondencia_receptor_unidad_id').val()+'#'+$('#correspondencia_receptor_funcionario_id').val()+'#N' || dato== $('#correspondencia_receptor_unidad_id').val()+'#'+$('#correspondencia_receptor_funcionario_id').val()+'#S') {
                        repetido++;
                    }
                });
                if($('#grilla >tbody >tr').length > 2)
                    repetido= parseInt(repetido)- 1;
                if(repetido > $('#grilla >tbody >tr').length)
                    return false;
                else
                    return true;
          }else {
                return true;
          }
    }, "Por favor, no repita los receptores.");

    });

    function findVistoBueno(unidad_firmante, emisor_old) {
        var func_emisor= $('#correspondencia_emisor_funcionario_id').val();

        var tipo_formato_id= $('#formato_tipo_formato_id').val();

        if(emisor_old !== 'false' && func_emisor !== emisor_old) {
            $('#trigger_vb').val('N');
            $("#grilla_vb > input").remove();
        }

        if(func_emisor !== '') {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/buscaVistoBueno',
                type:'POST',
                dataType:'json',
                data:'id='+func_emisor+'&ud='+unidad_firmante+'&nuevo='+emisor_old+'&formato='+tipo_formato_id,
                success:function(json, textStatus){
                        var stat= json.exist;
                        var dinamic= json.dinamico;
                        $('#div_icon_vb').empty();
                        $('#div_visto_bueno').hide();
                        $('#div_unidad_funcionario_vb').hide();
                        jQuery('#div_visto_bueno').html(json.data);
                        $.ajax({
                            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/iconVistoBueno',
                            type:'POST',
                            dataType:'html',
                            data:'status='+stat+'&dinamic='+dinamic+'&formato='+tipo_formato_id,
                            success:function(data, textStatus){
                                jQuery('#div_icon_vb').html(data);

                                $('#icon_vb').css({
                                    top: '-10px',
                                    opacity: 0,
                                    display: 'block'
                                }).animate({
                                    opacity: 1,
                                    top: '+=14px'
                                });
                                
                                $('#icon_vb_ab').css({
                                    top: '-10px',
                                    opacity: 0,
                                    display: 'block'
                                }).animate({
                                    opacity: 1,
                                    top: '+=14px'
                                });
                                tooltip();
                            }
                        });
                    tooltip();
            }});
        }else {
            $('#icon_vb').hide();
            $('#div_visto_bueno').hide();
            $('#div_unidad_funcionario_vb').hide();
        }
    }

    function fn_agregar_vistobueno(){

        var valuex= $("#vistobueno_funcionario option:selected").val();
        var parts= valuex.split('-');
        var texto= jQuery.trim($("#vistobueno_funcionario option:selected").text());
        var texto_parts= texto.split('/');
        var nombre= jQuery.trim(texto_parts[1]);

        var repeat= false;
        $('#grilla_vb > tbody > tr').each(function() {
            if($(this).attr('id') === 'tr_'+parts[0]) {
                repeat= true;
            }
        });

        if(repeat=== false) {
            cadena = "<tr id='tr_"+ parts[0] +"'>";
            cadena = cadena + "<td><img style='vertical-align: middle' src='/images/icon/arrow_up.png' > <font class='f17b'>"+ nombre +"</font> <font class='f15b'>("+ jQuery.trim($("#vistobueno_unidad option:selected").text()) +")</font></td>";
            cadena = cadena + "<input type='hidden' name='vistobuenos[funcionarios][]' value='"+ valuex +"-A'/>";
            cadena = cadena + "<td style='text-align: center; vertical-align: middle'><a onClick='javascript: borrame("+ parts[0] +")' style='cursor: pointer;'><img src='/images/icon/delete_old.png'/></a></td>";
            cadena = cadena + "</tr>";
            $("#grilla_vb > tbody").append(cadena);

            <?php echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                            'url' => 'formatos/vistobuenoUnidades',
                                                            'with' => "'unidad_id= 0'",)); ?>

            document.getElementById("vistobueno_unidad").selectedIndex = "";

            $('#vistobueno_act').attr('checked', true);
            change_trigger_vb();
            agregar_vb();    
        }else {
            alert('Visto bueno repetido.');
        }
    }

    function agregar_vb() {
        $('#div_unidad_funcionario_vb').slideToggle('fast');
    }

    function change_trigger_vb() {
        if($('#vistobueno_act').length) {
            if($('#vistobueno_act').is(':checked'))
                $('#trigger_vb').val('N');
            else
                $('#trigger_vb').val('A');
        }
    }

    function toggle_div_vb(){
        $("#div_visto_bueno").slideToggle('fast');
    }
    
    function toggle_div_vb_ab(formato_id){
        if ($('#div_visto_bueno_ab').is (':visible')){
            $("#div_visto_bueno_ab").hide('fast');
        }else {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/buscaVistoBuenoAb',
                type:'POST',
                dataType:'html',
                data:'formato_id='+formato_id,
                success:function(data, textStatus){
                    $("#div_visto_bueno_ab").show('fast');
                    $("#div_visto_bueno_ab_div").html(data);
                    tooltip();
                }
            });
        }
    }
    
    function select_route_vb(vb_general_config_id, formato_id) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/updateVbAbsoluto',
            type:'POST',
            dataType:'html',
            data:'vb_general_config_id='+vb_general_config_id+'&formato_id='+formato_id,
            success:function(data, textStatus){
                $("#div_visto_bueno_ab").hide();
                jQuery('#div_visto_bueno').html(data);
                toggle_div_vb();
                tooltip();
            }
        });
    }

    function borrame(elemento) {
            $("#tr_"+elemento).fadeOut("normal", function(){
                $(this).remove();
              });

            $('#vistobueno_act').attr('checked', true);
            change_trigger_vb();
    }
</script>

