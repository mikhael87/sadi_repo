<style>
    #div_instructivo_asignacion_gps {
        font-size: 13px;
    }
    #div_content_proccess {
        padding-left: 10px
    }
    #but_asignar {
        display: none
    }
</style>

<script>
    function programador_rutina(step) {
        //STEP 1: RESETEO DE DISPOSITIVO
        //STEP 2: CAMBIO DE CLAVE DE DSP
        //STEP 3: INICIALIZACION DE DSP
        //STEP 4: ASIGNACION DE ADMINISTRADORES
        //STEP 5: TRACK AUTOMATICO POR INTERVALOS
        //STEP 10: CHECKEO DE TRACKING Y ASIGNACION A VEHICULO
        if(step < 6 || step === 10) {
            var sim= $('#numero_asignacion_gps_input').val();
            var v_id= $('#vehiculo_id').val();
            
            if(step === 5) {
                $('#but_asignar').show();
            }
            
            if(step === 10)
                $('#asignar_vehiculo').attr("disabled", "disabled");

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>gps_vehiculo/programadorGPs',
                type:'POST',
                dataType:'json',
                data: 'op='+step+'&sim='+sim+'&v_id='+v_id,
                success:function(json_p, textStatus){
                    $('#div_numero_sim').hide();

                    var mensajes_step_uno = new Array();
                    mensajes_step_uno= {
//                        0: [{ text: "<br/>Reiciniando dispositivo..." },{ time: 2000 }], 
                        0: [{ text: "<br/>Verificando conexión..." },{ time: 2000 }], 
                        1: [{ text: "Hecho<br/>Verificando estatus de dispositivo..." },{ time: 5000 }], 
                        2: [{ text: "Hecho<br/>Inicializando..." },{ time: 7500 }]
                      };

                    var mensajes_step_dos = new Array();
                    mensajes_step_dos= {
//                        0: [{ text: "Hecho<br/>Cambiando claves..." },{ time: 1000 }], 
                        0: [{ text: "Hecho<br/>Verificando conexión satelital..." },{ time: 1000 }], 
                        1: [{ text: "Hecho<br/>Aperturando puertos..." },{ time: 2000 }]
                      };

                    var mensajes_step_tres = new Array();
                    mensajes_step_tres= {
//                        0: [{ text: "Hecho<br/>Inicializando..." },{ time: 1000 }]
                        0: [{ text: "Hecho<br/>Configurando patrones de posicionamiento..." },{ time: 1000 }]
                      };
                    
                    var mensajes_step_cuatro = new Array();
                    mensajes_step_cuatro= {
                        0: [{ text: "Hecho<br/>Definiendo administradores..." },{ time: 1000 }]
                      };
                    
                    var mensajes_step_cinco = new Array();
                    mensajes_step_cinco= {
                        0: [{ text: "Hecho<br/><font style='color: green'>Track Automatico</font>" },{ time: 1000 }]
                      };
                      
                    var mensajes_step_diez = new Array();
                    mensajes_step_diez= {
                        0: [{ text: "<br/><br/>Espere por favor..." },{ time: 1000 }]
                      };

                    var mensajes_all= new Array();
                    mensajes_all[1] = mensajes_step_uno;
                    mensajes_all[2] = mensajes_step_dos;
                    mensajes_all[3] = mensajes_step_tres;
                    mensajes_all[4] = mensajes_step_cuatro;
                    mensajes_all[5] = mensajes_step_cinco;
                    mensajes_all[10] = mensajes_step_diez;

                    $.each( mensajes_all[step], function( key, value ) {
                        show_msj(value[0]['text'], value[1]['time']);
                    });

                    var count= 0;
                    if(json_p.ans !== 'without') {
                        var reinicio_int= setInterval(function(){
                            $.ajax({
                            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>gps_vehiculo/respuesta',
                            type:'POST',
                            dataType:'json',
                            data: 'hora='+json_p.hora+'&sim='+sim+'&ans='+json_p.ans,
                            success:function(json, textStatus){
                                if(json.data=== 'exito') {
                                    clearInterval(reinicio_int);
                                    if(step=== 10)
                                        window.location='<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>vehiculo/index';
                                    else
                                        programador_rutina(parseInt(step)+parseInt(1));
                                }else{
                                    if(json.data=== 'autenticacion_fallo'){
                                        clearInterval(reinicio_int);
                                        $('#div_proccess').append('<font style="color: red">Error en la autenticacion</font>');
                                        $('#try_again').show();
                                        $('#asignar_vehiculo').attr("disabled", "enable");
                                    }
                                }
                            }});
                            if(count ===  12) {
                                clearInterval(reinicio_int);
                                $('#div_proccess').append('<font style="color: red">Error en proceso</font>');
                                $('#try_again').show();
                                $('#asignar_vehiculo').attr("disabled", "enable");
                            }
                            count++;
                        },5000);
                    }else {
                        programador_rutina(parseInt(step)+parseInt(1));
                    }
                }});
        }
    }
    
    function try_again() {
        $('#div_proccess').html('');
        $('#try_again').hide();
        programador_rutina(1);
    }
    
    function asignar_gps_act(){
        var sim= $('#numero_asignacion_gps_input').val();
        //VALIDACION DE CAMPO VA AQUI
        
        if(confirm('¿Esta seguro de proceder con la asignación de GPS?')){
            programador_rutina(1);
        } else {
            return false;
        }
    }
    
    function show_msj(text, time) {
        setTimeout(function(){ $('#div_proccess').append(text) },time);
    }
    
    function check_asig() {
        var sim= $('#numero_asignacion_gps_input').val();
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>gps_vehiculo/checkGps',
            type:'POST',
            dataType:'json',
            data: 'sim='+sim,
            success:function(json, textStatus){
                if(json.data=== 'exito') {
                    clearInterval(reinicio_int);
                    programador_rutina(parseInt(step)+parseInt(1));
                }
            }});
    }
    
    function active_button(act) {
        var icon= '<?php echo "http://".$_SERVER['SERVER_NAME'] ?>/images/icon/';
        if(act=== 'ON')
          $('.continuar_asignacion_img').attr('src', icon+'ok.png');
        else
          $('.continuar_asignacion_img').attr('src', icon+'ok_wait.png');
    }
</script>

<div id="sf_admin_container">
    <h1>Asistente para la asignaci&oacute;n de GPS</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <div id="sf_admin_content">
        
        <div class="sf_admin_list trans">
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label for="correspondencia_n_correspondencia_emisor">
                        <img src="/images/other/siglas_update.png" width="80"/>
                    </label>
                    <div id="div_instructivo_asignacion_gps" style="min-height: 110px;" class="content">
                            <br/>
                            Este asistente le permite la asignaci&oacute;n de un dispositivo receptor GPS a un veh&iacute;culo previamente seleccionado.
                            <br/>
                            Deben cumplirse los siguientes requerimientos antes de ejecutar este asistente:
                            <br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;>><font style="color: #666">Asegurese de haber establecido al menos un (1) numero telef&oacute;nico como administrador SIGLAS (Desde configuraciones GPS).</font>
                            <br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;>><font style="color: #666">El dispositivo deber&aacute; estar instalado en el veh&iacute;culo correctamente preferiblemente por un tecnico electr&oacute;nico.</font>
                            <br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;>><font style="color: #666">Asegurese de que la tarjeta SIM instalada en el dispositivo tiene una linea telef&oacute;nica asignada con saldo positivo.</font>
                    </div>
                </div>
            </div>
            <br/>
            <div id="div_content_proccess">
                <div id="div_numero_sim">
                    <label for="numero_asignacion_gps">N&uacute;mero telef&oacute;nico:</label>
                    <input type="text" name="numero_asignacion_gps_input" id="numero_asignacion_gps_input"/>
                    <input type="hidden" name="vehiculo_id" id="vehiculo_id" value="<?php echo $vehiculo_id; ?>"/>
                    <button id="asignar_gps_act" onClick="javascript: asignar_gps_act();" onMouseover="active_button('ON')" onMouseout="active_button('OFF')" style="height: 23px">
                        <?php echo image_tag('icon/ok_wait.png', array('style' => 'vertical-align: middle', 'class'=>'continuar_asignacion_img')) ?>&nbsp;<strong>Continuar</strong>
                    </button>
                    <div class="help">
                        N&uacute;mero asignado a la tarjeta SIM previamente instalada en el dispositivo receptor GPS
                    </div>
                </div>
                <div id="div_proccess">
                    
                </div>
                <div id="but_asignar">
                    <button onClick="javascript: programador_rutina(10);" onMouseover="active_button('ON')" onMouseout="active_button('OFF')" style="height: 23px" id="asignar_vehiculo">
                        <?php echo image_tag('icon/ok_wait.png', array('style' => 'vertical-align: middle', 'class'=>'continuar_asignacion_img')) ?>&nbsp;<strong>Asignar al Veh&iacute;culo</strong>
                    </button>
                </div>
                <div style="display: none" id="try_again">
                    <button onClick="javascript: try_again();" onMouseover="active_button('ON')" onMouseout="active_button('OFF')" style="height: 23px">
                        <?php echo image_tag('icon/ok_wait.png', array('style' => 'vertical-align: middle', 'class'=>'continuar_asignacion_img')) ?>&nbsp;<strong>Reintentar</strong>
                    </button>
                </div>
            </div>
            <br/>
        </div>
    </div>
</div>
