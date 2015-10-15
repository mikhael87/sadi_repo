<?php use_helper('jQuery'); ?>

<script>
    function programador_rutina(step, id_switch, condi) {
        //STEP 11: SUPRESS
        //STEP 12: LOWBATTERY
        //STEP 13: EXTPOWER
        //STEP 14: GPS_SIGNAL
        //STEP 15: MOVE
        //STEP 16: SPEED
        //STEP 17: ACC
        var sim= $('#numero_asignacion_gps_input').val();
        var param= $("#param_"+id_switch).val();

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>gps_vehiculo/programadorGPs',
            type:'POST',
            dataType:'json',
            data: 'op='+step+'&sim='+sim+'&condi='+condi+'&param='+param,
            beforeSend: function(Obj){
                $('#noti_command_'+id_switch).html('<div style="position: absolute; left: 250px"><img src="/images/icon/cargando.gif" />&nbsp;<font style="color: #666; font-size: 12px; vertical-align: super">Esto tardara solo un momento...</font></div>');
            },
            success:function(json_p, textStatus){
                if(json_p.stat === '') {
                    var count= 0;
                    var reinicio_int= setInterval(function(){
                        $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>gps_vehiculo/respuesta',
                        type:'POST',
                        dataType:'json',
                        data: 'hora='+json_p.hora+'&sim='+sim+'&ans='+json_p.ans+'&case='+json_p.case+'&condi='+json_p.condi,
                        success:function(json, textStatus){
                            var posit= $('#state_'+id_switch).val();
                            if(json.data=== 'exito') {
                                clearInterval(reinicio_int);
                                $("#div_wait_"+id_switch).hide();
                                $('#noti_command_'+id_switch).html('');
                                if(id_switch === 16 && posit=== 'off')
                                    $('#noti_command_'+id_switch).html('<input style="width: 60px; height: 20px" type="text" id="param_'+ id_switch +'" value=""/>&nbsp; Km/h&nbsp;<font style="color: #666">(Vel. m&iacute;nima 30 Km/h)</font>');
                            }
                        }});
                        if(count ===  10) {
                            clearInterval(reinicio_int);
                            var posit= $('#state_'+id_switch).val();

                            $("#div_wait_"+id_switch).hide();
                            $('#noti_command_'+id_switch).html('');
                            if(id_switch === 16 && posit === 'on')
                                $('#noti_command_'+id_switch).html('<input style="width: 60px; height: 20px" type="text" id="param_'+ id_switch +'" value=""/>&nbsp; Km/h&nbsp;<font style="color: #666">(Vel. m&iacute;nima 30 Km/h)</font>');
                            $('#noti_command_'+id_switch).append('<div style="position: absolute; left: 250px; padding-top: 8px">&nbsp;&nbsp;<font class="error" style="color: red">Error al cambiar estatus.</font></div>');
                            $(".error").delay(3000).fadeOut("fast");

                            if(condi === 'on') {
                                jQuery(".iphone_switch_"+id_switch).animate({backgroundPosition: -53}, "slow", function() {
                                    jQuery(this).attr("src", "/images/icon/tracker/iphone_switch_container_off.png");
                                });
                            }else {
                                jQuery(".iphone_switch_"+id_switch).animate({backgroundPosition: 0}, "slow", function() {
                                    jQuery(this).attr("src", "/images/icon/tracker/iphone_switch_container_on.png");
                                });
                            }
                            if(posit=== 'on')
                                $('#state_'+id_switch).val('off');
                            else
                                $('#state_'+id_switch).val('on');
                        }
                        count++;
                    },3000);
                }else {
                    var posit= $('#state_'+id_switch).val();

                    $("#div_wait_"+id_switch).hide();
                    $('#noti_command_'+id_switch).html('');
                    $('#noti_command_'+id_switch).append('<div style="position: absolute; left: 250px; padding-top: 8px">&nbsp;&nbsp;<font class="error" style="color: red">'+ json_p.stat +'</font></div>');
                    $(".error").delay(3000).fadeOut("fast");

                    if(condi === 'on') {
                        jQuery(".iphone_switch_"+id_switch).animate({backgroundPosition: -53}, "slow", function() {
                            jQuery(this).attr("src", "/images/icon/tracker/iphone_switch_container_off.png");
                        });
                    }else {
                        jQuery(".iphone_switch_"+id_switch).animate({backgroundPosition: 0}, "slow", function() {
                            jQuery(this).attr("src", "/images/icon/tracker/iphone_switch_container_on.png");
                        });
                    }
                    if(posit=== 'on')
                        $('#state_'+id_switch).val('off');
                    else
                        $('#state_'+id_switch).val('on');
                }
            }});
    }

    function change_status(cont) {
        var state= $('#state_'+cont).val();
        var switch_on_container_path= '/images/icon/tracker/iphone_switch_container_on.png';
        var switch_off_container_path= '/images/icon/tracker/iphone_switch_container_off.png';
        if(state === 'on') {
                $('.iphone_switch_'+cont).animate({backgroundPosition: -53}, "slow", function() {
                        $('.iphone_switch_'+cont).attr('src', switch_off_container_path);

                        $("#div_wait_"+cont).show();
                        $('#state_'+cont).val('off');
                        programador_rutina(cont, cont, "off");
                });
        }
        else {
                $('.iphone_switch_'+cont).animate({backgroundPosition: 0}, "slow", function() {
                        $('.iphone_switch_'+cont).attr('src', switch_on_container_path);

                        if(cont === 16) {
                            var param= $("#param_"+cont).val();

                            $("#div_wait_"+cont).show();
                            if (param.length > 3 || param.length < 2 || isNaN(param) || param < 30) {

                                $("#noti_command_"+cont).append("<font class=\"error\">&nbsp;&nbsp;Verifique la velocidad máxima</font>");
                                $(".error").delay(3000).fadeOut("fast");

                                jQuery(".iphone_switch_"+cont).animate({backgroundPosition: -53}, "slow", function() {
                                        jQuery(this).attr("src", "/images/icon/tracker/iphone_switch_container_off.png");
                                });
                                $("#div_wait_"+cont).hide();
                            }else {
                                $('#state_'+cont).val('on');
                                programador_rutina(cont, cont, "on");
                            }
                        }else {
                            $("#div_wait_"+cont).show();
                            $('#state_'+cont).val('on');
                            programador_rutina(cont, cont, "on");
                        }
                });
        }
    }

</script>

<style>
    .left{
        float:left;
        width:120px;
    }

    #ajax{
        float:left;
        width:300px;
        padding-top:5px;
        font-weight:700;
    }

    .clear{clear:both;}

    .error { color: red }

    .div_wait {
        display: none;
        position: absolute;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        background-color: black;
        opacity: 0.4;
        filter:alpha(opacity=40);
        z-index: 999;
    }
</style>

<div id="sf_admin_container">
    <h1><?php
    $vehiculo_datos= Doctrine::getTable('Vehiculos_Vehiculo')->find($vehiculo_id);
    echo 'Alertas para el vehículo "'. $vehiculo_datos->getMarca() .' '. $vehiculo_datos->getModelo() .' '. $vehiculo_datos->getPlaca() .'"' ?></h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <div id="sf_admin_content">

        <div class="sf_admin_form">
            <div class="sf_admin_form_row sf_admin_text"  style="background-image: url('../images/other/td_fond.png');">
                <div>
                    <?php echo link_to(image_tag('icon/back.png', array('style'=>'vertical-align: middle')).'&nbsp;Regresar a Gps asignados', 'gps_vehiculo/index'); ?>
                </div>
            </div>
            <fieldset id="sf_fieldset_alertas">
                <h2>Activaci&oacute;n y desactivaci&oacute;n de alertas del dispositivo GPS</h2>

                <input type="hidden" id="numero_asignacion_gps_input" value="<?php echo $sim ?>" />

                <?php
                $switch_on_container_path= '/images/icon/tracker/iphone_switch_container_on.png';
                $switch_off_container_path= '/images/icon/tracker/iphone_switch_container_off.png';
                $cadena=''; $cont= 11;
                foreach($parametros['alertas'] as $key => $value) {
                    $op= ($value['status']) ? 'on' : 'off';
                    $cadena .= '<input type="hidden" id="state_'. $cont .'" value="'. $op .'"/>';

                    $cadena .= '<div style="position: relative" id="div_'. $cont .'" class="sf_admin_form_row sf_admin_text">';
                    $cadena .= '<div id="div_wait_'. $cont .'" class="div_wait"></div>';
                    $cadena .= '<div><label for="">'. $value['label'] .':</label>';
                    $cadena .= '<div class="content">';

                    $cadena .= '<div class="left" id="'. $cont .'">';

                    $cadena .= '<div class="iphone_switch_container" onClick="javascript: change_status('. $cont .')" style="height:27px; width:94px; position: relative; overflow: hidden; cursor: pointer; background: default">';

                    $cadena .= '<img class="iphone_switch_'. $cont .'" style="height:27px; width:94px; background-image:url(/images/icon/tracker/iphone_switch.png); background-repeat:none; background-position:'.($op == 'on' ? 0 : -53).'px" src="'.($op == 'on' ? $switch_on_container_path : $switch_off_container_path).'" /></div>';

                    $cadena .= '</div>';

                    $cadena .= '</div>';
                    $cadena .= '<div id="noti_command_'. $cont .'">';
                    if($cont == 16 && $op == 'off')
                        $cadena .= '<input style="width: 60px; height: 20px" type="text" id="param_'. $cont .'" value=""/>&nbsp; Km/h&nbsp;<font style="color: #666">(Vel. m&iacute;nima 30 Km/h)</font>';
                    $cadena .= '</div></div>';
                    if($cont == 16)
                        $cadena .= '<br/>';
                    else
                        $cadena .= '<br/><br/>';
                    $cadena .= '<div class="help clear">'. html_entity_decode($value['help']) .'</div>';

                    $cadena .= '</div>';
                    $cont++;
                }
                echo $cadena;
                ?>
            </fieldset>
        </div>
    </div>
</div>
