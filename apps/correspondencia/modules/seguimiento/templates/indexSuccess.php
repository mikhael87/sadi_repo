<link rel="stylesheet" type="text/css" media="screen" href="css/global.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/default.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />

<?php use_helper('jQuery'); ?>
<script>
    function ver_doc(id){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url')?>formatos/show',
            type:'POST',
            dataType:'html',
            data:'idc='+id+'&op=TRUE',
            success:function(data, textStatus){
                jQuery('#documento_'+id).html(data);
            }})

        $("#close_"+id).slideDown();
        $("#documento_"+id).slideDown();
    };

    function cerrar_doc(id){
        $("#close_"+id).slideUp();
        $("#documento_"+id).slideUp();

        $("#documento_"+id).html('')
    };

    function ver_adjunto(id){
        $("#ruta_vb_"+id).slideUp();
        $("#fisico_"+id).slideUp();
        $("#adjunto_"+id).toggle( 'slide', { direction: 'down' } );
    };

    function ver_fisico(id){
        $("#ruta_vb_"+id).slideUp();
        $("#adjunto_"+id).slideUp();
        $("#fisico_"+id).toggle( 'slide', { direction: 'down' } );
    };

    function ver_vb(id){
        $("#fisico_"+id).slideUp();
        $("#adjunto_"+id).slideUp();
        $("#ruta_vb_"+id).toggle( 'slide', { direction: 'down' } );
    };

    function cerrar_adjunto_fisico(id){
//        $("#adjunto_"+id).slideUp();
//        $("#fisico_"+id).slideUp();
//        $("#ruta_vb_"+id).slideUp();
    };

    function notificar() {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url')?>seguimiento/notificarList',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                            $('#list_funcio_noti').html('<font style="color: #666; font-size: 12px">Cargando funcionarios para notificar...</font>');
                    },
            success:function(data, textStatus){
                $('#list_funcio_noti').html('');
                jQuery('#list_funcio_noti').append(data);
//                $('#icono_carga').hide();
        }});
    }

    function enviar_form_miniforo() {
        var texto= $('#contenido_miniforo').val();
        if(texto!= '') {
            $('#button_form_miniforo').attr('disabled','disabled');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url')?>seguimiento/microForo',
                type:'POST',
                dataType:'html',
                data:'contenido='+texto,
                success:function(data, textStatus){
                    $('#div_miniforo').html('');
                    jQuery('#div_miniforo').append(data);
                    $('#text_comment').val(texto);
                    $('#button_form_miniforo').removeAttr('disabled');
                    notificar();
            }});
        }else {
            alert('Por favor, escriba un comentario.')
        }
    }

    function enviar_notificaciones() {
        var text = $('#text_comment').val();
        var ids = new Array();
        var cont= 0;
        $('#list_funcio_noti input').each(function() {
            if($(this).is(':checked')) {
                ids[cont] = $(this).val();
            }
            cont= cont+ 1;
        });

        if(cont> 0) {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url')?>seguimiento/notificar',
                type:'POST',
                dataType:'html',
                data:'f_ids='+ids+'&comment='+text,
                success:function(data, textStatus){
                    $('#list_funcio_noti').html('');
                    jQuery('#list_funcio_noti').append(data);
            }});
        }else {
            alert('Nadie a quien notificar.');
        }
    }
</script>

<div id="sf_admin_container">
  <h1>Seguimiento de Correspondencia</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
  <?php endif; ?>

<br/>
<a href="<?php echo $anterior ?>" class="vbs"><?php echo image_tag('icon/mail_list.png'); ?>Regresar a la Correspondencia</a>
<br/><br/>

<div id="div_miniforo" style="position: relative; width: 295px;">
    <div style="position: absolute; left: 0px; width: 295px; padding: 3px" id="tweet">
        <?php $ban=0; foreach ($micro_foro as $tweet) { ?>
            <?php if($ban>0){ ?>
                <hr>
            <?php } ?>
                <font class="f11n gris_oscuro" ><?php echo date('d-m-Y h:m a', strtotime($tweet->getCreatedAt())); ?></font>
                <br/>
                <font class="f14n gris_oscuro"><?php echo $tweet->getUnidad().' / '.$tweet->getFuncionario(); ?></font>
                <br/>
                <?php echo $tweet->getContenido(); ?>
        <?php $ban++; } ?>

        <br/><hr/><br/>
        <textarea id="contenido_miniforo" name="contenido" cols="32"></textarea><br/>
        <input id="button_form_miniforo" onClick="javascript: enviar_form_miniforo()" name="publicar" value="Publicar" type="button"/>
        <div style="position:absolute"><div id="div_icon_notify" style="position: absolute; left: 65px; bottom: 3px"><?php echo image_tag('icon/notify_i.png'); ?></div></div>
        <div id="list_funcio_noti"></div>
    </div>
</div>
<input id="text_comment" type="hidden" value="" />
<?php for($i=0;$i<=count($correspondencias);$i++) $tab[$i]=0; ?>


<?php $c=0; $cg=0; ?>
<?php foreach( $correspondencias as $valor ) { ?>

    <?php
    $emisor_externo = 'N';
        $t = substr_count($valor, '--'); // $t numero de tabulaciones
        $w = 58 * $t; // $w numero de px de left
    ?>



    <div style="position: relative; height: 150px; left: 305px;">
        <div style="position: absolute; left: <?php echo $w; ?>px; width: 550px; height: 150px;">

            <?php if($t != 0) { ?>
            <div style="position: absolute; left: -30px; top: 8px;">
                <?php echo image_tag('other/line_arrow_end.png'); ?>
            </div>

            <div style="position: absolute; left: -51px; top: 17px; width: 38px; height: 1px; background-color: #000000; z-index: -100;"></div>

            <?php if($t == $c) {  ?>
            <div style="position: absolute; left: -51px; top: -13px; width: 1px; height: 30px; background-color: #000000; z-index: -100;"></div>
            <?php } else
                {

                ?>


            <div style="position: absolute; left: -51px; top: -<?php echo 149+(($cg-$tab[$t]-1)*167); ?>px; width: 1px; height: <?php echo ($cg-$tab[$t])*167; ?>px; background-color: #000000; z-index: -100;"></div>

            <div style="position: absolute; left: -40px; top: 10px;">

            </div>

            <?php $c = $t; } ?>
            <?php } ?>
            <div style="position: absolute; left: -100px; top: -10px;">
                <font class="f11n">
            <?php
            //$d = $cg-$tab[$t];
            /*echo 'contador parcial: '.$c.
                       '<br/>contador general: '.$cg.
                       '<br/>tabulacion actual: '.$t.
                       '<br/>Ultima tabulacion "'.$t.'": '.$tab[$t].
                       '<br/>Diferencia: '.$d; */ ?>
                </font>
            </div>
<!------------------- -->

            <?php

            if(preg_match("/.==./",$valor))
            {
                $cadena_receptor = explode( ".==.", $valor);
                $correspondencia_id = trim(str_replace("-", "", $cadena_receptor[0]));

                $tiempo_envio=Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);

                if($cadena_receptor[5])
                    $tiempo_inicio = $cadena_receptor[5];
                else
                    $tiempo_inicio = $tiempo_envio->get("f_envio");

                $segundos  = time()-strtotime($tiempo_inicio);
                $dias      = intval($segundos/86400);
                $segundos -= $dias*86400;
                $horas     = intval($segundos/3600);
                $segundos -= $horas*3600;
                $minutos   = intval($segundos/60);
                $segundos -= $minutos*60;

                $tiempo_pasado = $dias . " dias, ". $horas . " horas, " . $minutos . " minutos y " . $segundos ." segundos";
                ?>
                    <?php if($cadena_receptor[5]) { ?>
                        <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_azul_blanco.png); z-index: -1;"></div>

                        <div style="position: absolute; left: 25px; top: 2px; width: 545px;">
                            <font class="f11b">Fecha de Recepción:</font> <font class="f11n"><?php echo date('d-m-Y h:i:s A', strtotime($cadena_receptor[5])); ?></font>
                        </div>

                        <div style="position: absolute; left: 5px; top: 2px;">
                            <?php echo image_tag('icon/open.png'); ?>
                        </div>

                        <div style="position: absolute; left: 30px; top: 90px; width: 545px;">
                            <font class="f16b">Tiempo transcurrido desde la recepción:</font> <font class="f16n"><?php echo $tiempo_pasado; ?></font>
                        </div>

                    <?php } else { ?>
                        <?php if($cadena_receptor[7] == 'C') { ?>
                            <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_gris_blanco.png);"></div>

                            <div style="position: absolute; left: 25px; top: 5px; width: 545px;">
                                <font class="f19b gris_oscuro">NO SE HA ENVIADO</font>
                            </div>

                            <div style="position: absolute; left: 5px; top: 2px;">
                                <?php echo image_tag('icon/newmsg.png'); ?>
                            </div>
                        <?php } else { ?>
                            <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_rosado_blanco.png);"></div>

                            <div style="position: absolute; left: 25px; top: 5px; width: 545px;">
                                <font class="f19b gris_oscuro">Sin Leer</font>
                            </div>

                            <div style="position: absolute; left: 5px; top: 2px;">
                                <?php echo image_tag('icon/newmsg.png'); ?>
                            </div>

                            <div style="position: absolute; left: 100px; top: 5px; width: 545px;">
                                <font class="f15b">Tiempo transcurrido desde el envio:</font> <font class="f15n"><?php echo $tiempo_pasado; ?></font>
                            </div>
                        <?php } ?>


                    <?php } ?>

<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->

                        <?php //$autorizados=Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaIdAndUnidadIdAndEstablecido($correspondencia_id,$cadena_receptor[8],'A'); ?>
                        <?php $autorizados=Doctrine::getTable('Correspondencia_Receptor')->receptoresPorCorrespondenciaUnidadEstablecido($correspondencia_id,$cadena_receptor[8],array('N','A')); ?>

                        <div style="position: absolute; top:5px; left:560px; width: 550px; height: 150px; ">
                            <table class="trans">
                                <?php foreach ($autorizados as $autorizado) { ?>
                                <?php $autorizado_datos=Doctrine::getTable('Funcionarios_Funcionario')->find($autorizado['funcionario_id']); ?>
                                <tr>
                                    <td>
                                        <?php
                                            $cadena_recepcion = '';
                                            $mensaje_recepcion = '';
                                            //echo image_tag('icon/open.png', array('title' => 'Leido en Fecha '.date('d-m-Y h:i:s A', strtotime($autorizado['f_recepcion']))));
                                            if($autorizado->getEstablecido()=='A'){
                                                $mensaje_recepcion .= 'Asignado en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getCreatedAt()));
                                                if($autorizado->getFRecepcion()==''){
                                                    $cadena_recepcion .= image_tag('icon/mail.png');
                                                    $color_recepcion = '#FF0000';
                                                    $mensaje_recepcion .= "<br/><font style='color: #FF0000;'><b>Aun no ha sido leido por el asignado.</b></font>";
                                                } else {
                                                    $cadena_recepcion .= image_tag('icon/open.png');
                                                    $color_recepcion = '#1c94c4';
                                                    $mensaje_recepcion .= '<br/>Leido en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getFRecepcion()));
                                                }
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= image_tag('icon/asignar.png');
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= '<font style="color:'.$color_recepcion.'"><b>'.$autorizado_datos['primer_nombre'].', '.$autorizado_datos['primer_apellido'].'</b></font>';
                                            } else {
                                                $cadena_recepcion .= image_tag('icon/open.png');
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= $autorizado_datos['primer_nombre'].', '.$autorizado_datos['primer_apellido'];
                                                $mensaje_recepcion .= 'Leido en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getFRecepcion()));
                                            }

                                            echo '<div class="tooltip" title="'.$mensaje_recepcion.'">'.$cadena_recepcion.'</div>';
                                        ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </table>

                        </div>



<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->


                    <div style="position: absolute; left: 5px; top: 22px; width: 545px;">
                        <font class="f16b">Unidad:</font>
                        <font class="f16n" title="<?php echo ucwords(strtolower($cadena_receptor[2])); ?>">
                            <?php echo ucwords(strtolower($cadena_receptor[1])); ?><br/>
                        </font>
                    </div>

                    <div style="position: absolute; left: 5px; top: 36px; width: 545px;">
                        <font class="f16b">Funcionario Receptor:</font>
                        <font class="f16n" title="<?php echo ucwords(strtolower($cadena_receptor[4])); ?>">
                            <?php echo ucwords(strtolower($cadena_receptor[3])); ?>
                        </font>; &nbsp;
                    </div>
                <?php
            }
            else
            {
                $numeros = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
                $correspondencia_tab = str_replace($numeros, "", $valor);

                $correspondencia_id = trim(str_replace("-", "", $valor));

                if($correspondencia_id)
                {
                    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);


                    $emisor_externo = 'N';
                    if($correspondencia->getEmisorUnidadId()!=null) {
                        $emisor=Doctrine::getTable('Correspondencia_Correspondencia')->unidadEmisor($correspondencia_id);
                    } else {
                        $emisor_externo = 'S';
                        $emisor=Doctrine::getTable('Correspondencia_Correspondencia')->unidadEmisorOrganismo($correspondencia_id);
                    }

                    $status_final=Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);

                    if($status_final->get("status")=='F') {
                    ?>
                        <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_verde.png);"></div>
                    <?php } elseif($status_final->get("status")=='C') { ?>
                        <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_azul_gris.png);"></div>
                    <?php } else { ?>
                        <div style="position: absolute; left:0px; width: 550px; height: 150px; background-image: url(/images/other/correspondencia_azul.png);"></div>
                    <?php } ?>








<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->

                        <?php
                        $correspondencia_padre = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);

                        if($correspondencia_padre['padre_id'] && $emisor[0]['emisor_unidad_id'])
                            {

                             //$autorizados=Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaIdAndUnidadIdAndEstablecido($correspondencia_padre['padre_id'],$emisor[0]['emisor_unidad_id'],'N');
                             $autorizados=Doctrine::getTable('Correspondencia_Receptor')->receptoresPorCorrespondenciaUnidadEstablecido($correspondencia_padre['padre_id'],$emisor[0]['emisor_unidad_id'],array('N','A')); ?>

                            <div style="position: absolute; top:5px; left:560px; width: 550px; height: 150px;">
                                <table class="trans">
                                    <?php foreach ($autorizados as $autorizado) { ?>
                                    <?php $autorizado_datos=Doctrine::getTable('Funcionarios_Funcionario')->find($autorizado['funcionario_id']); ?>
                                    <tr>
                                        <td>
                                        <?php
                                            $cadena_recepcion = '';
                                            $mensaje_recepcion = '';
                                            //echo image_tag('icon/open.png', array('title' => 'Leido en Fecha '.date('d-m-Y h:i:s A', strtotime($autorizado['f_recepcion']))));
                                            if($autorizado->getEstablecido()=='A'){
                                                $mensaje_recepcion .= 'Asignado en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getCreatedAt()));
                                                if($autorizado->getFRecepcion()==''){
                                                    $cadena_recepcion .= image_tag('icon/mail.png');
                                                    $color_recepcion = '#FF0000';
                                                    $mensaje_recepcion .= "<br/><font style='color: #FF0000;'><b>Aun no ha sido leido por el asignado.</b></font>";
                                                } else {
                                                    $cadena_recepcion .= image_tag('icon/open.png');
                                                    $color_recepcion = '#1c94c4';
                                                    $mensaje_recepcion .= '<br/>Leido en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getFRecepcion()));
                                                }
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= image_tag('icon/asignar.png');
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= '<font style="color:'.$color_recepcion.'"><b>'.$autorizado_datos['primer_nombre'].', '.$autorizado_datos['primer_apellido'].'</b></font>';
                                            } else {
                                                $cadena_recepcion .= image_tag('icon/open.png');
                                                $cadena_recepcion .= "&nbsp;";
                                                $cadena_recepcion .= $autorizado_datos['primer_nombre'].', '.$autorizado_datos['primer_apellido'];
                                                $mensaje_recepcion .= 'Leido en Fecha: '.date('d-m-Y h:i:s A', strtotime($autorizado->getFRecepcion()));
                                            }

                                            echo '<div class="tooltip" title="'.$mensaje_recepcion.'">'.$cadena_recepcion.'</div>';
                                        ?>
                                        </td>
                                    </tr>
                                    <?php } ?>


                                </table>

                        </div>
                        <?php } ?>


<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->
<!--                        #################################################################-->

                        <div style="position: absolute; left: 5px; top: 22px; width: 545px;">
                            <font class="f16b"><?php if($emisor_externo != 'S') echo "Unidad:"; else echo "Organismo:"; ?></font>
                            <font class="f16n" title="<?php
                                                        if($emisor_externo != 'S')
                                                            echo ucwords(strtolower($emisor[0]['padre_unidad']));
                                                      ?>">

                                <?php
                                    if($emisor_externo != 'S'){
                                        if(isset($emisor[0]['unombre']))
                                            echo ucwords(strtolower($emisor[0]['unombre']));
                                    } else {
                                        if(isset($emisor[0]['onombre']))
                                            echo ucwords(strtolower($emisor[0]['onombre']));
                                    }

                                ?><br/>
                            </font>
                        </div>

                        <div style="position: absolute; left: 25px; bottom: 3px; width: 545px;">
                            <font class="f11b">N° de Envio:</font> <font class="f12b azul"><?php echo $emisor[0]['n_correspondencia_emisor']; ?></font>&nbsp;&nbsp;
                            <?php if($status_final->get("status")=='C') { ?>
                                <font class="f11b">--- NO SE HA ENVIADO ---</font>
                            <?php } else { ?>
                                <font class="f11b">Fecha de Envio:</font> <font class="f11n"><?php echo date('d-m-Y h:i:s A', strtotime($emisor[0]['f_envio'])); ?></font>
                            <?php } ?>
                        </div>
                        <div style="position: absolute; left: 5px; bottom: 2px;">
                            <?php echo image_tag('icon/send.png'); ?>
                        </div>

                        <?php if($emisor[0]['tadjuntos']>0 || $emisor[0]['tfisicos']>0) {
                            if($emisor[0]['tadjuntos']>0) { ?>
                                <div style="position: absolute; left: 380px; top: 125px; min-width: 40px; padding-left: 10px; padding-top: 5px;" onclick="javascript:ver_adjunto(<?php echo $correspondencia_id; ?>);">
                                    <?php if (!$sf_user->getAttribute("seguimiento_externa") || $sf_user->hasCredential(array('Archivo'), false)) { ?>
                                        <div id="adjunto_<?php echo $correspondencia_id; ?>" style="padding: 5px; position: absolute; border: solid 1px; background-color: #F2F1F0; min-width: 225px; max-height: 225px; left: -50px; bottom: 20px; display: none; z-index: 800;">
                                            <div style="position: relative; overflow-y: auto; overflow-x: hidden; max-height: 225px;">
                                                <?php $archivos = Doctrine::getTable('Correspondencia_AnexoArchivo')->filtrarPorCorrespondencia($correspondencia_id); ?>
                                                <?php foreach ($archivos as $archivo) { ?>
                                                    <!--&bull;<a href="/uploads/correspondencia/<?php //echo $archivo->getruta(); ?>"><?php //echo $archivo->getNombreOriginal(); ?></a><br/>-->
                                                    &bull;<a class="tooltip" <?php echo ((strlen($archivo->getNombreOriginal()) > 30) ? "title='". $archivo->getNombreOriginal() . "'" : "" ); ?>  href="/uploads/correspondencia/<?php echo $archivo->getruta(); ?>"><?php echo ((strlen($archivo->getNombreOriginal()) > 30) ? substr($archivo->getNombreOriginal(),0,30).'...' : $archivo->getNombreOriginal()); ?></a><br/>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <a href="#"><?php echo image_tag('icon/attach.png'); ?></a>
                                    <div style="cursor: pointer; opacity: 0.8; position:absolute; right: 20px; top: 12px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%"><div style="position: absolute; left: 4px; top: 1px"><font style="font-size: 9px; color: white"><?php echo $emisor[0]['tadjuntos']; ?></font></div></div>
                                </div>
                            <?php } ?>

                            <?php if($emisor[0]['tfisicos']>0) { ?>
                                <div style="position: absolute; left: 420px; top: 125px; min-width: 40px; padding-left: 10px; padding-top: 5px;" onclick="javascript:ver_fisico(<?php echo $correspondencia_id; ?>);">
                                    <?php if (!$sf_user->getAttribute("seguimiento_externa") || $sf_user->hasCredential(array('Archivo'), false)) { ?>
                                        <div id="fisico_<?php echo $correspondencia_id; ?>" style="padding: 5px; position: absolute; border: solid 1px; background-color: #F2F1F0; min-width: 225px; max-height: 225px; left: -115px; bottom: 20px; display: none; z-index: 800;">
                                            <div style="position: relative; overflow-y: auto; overflow-x: hidden; max-height: 225px;">
                                                <?php $fisicos = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($correspondencia_id); ?>
                                                <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_id.'/hojaRuta'; ?>" title="Descargar acuse de recibido">
                                                <?php foreach ($fisicos as $fisico) { ?>
                                                    &bull;<?php echo $fisico->getTafnombre(); ?>: &nbsp;<font class="f16n"><?php echo $fisico->getObservacion(); ?></font><br/>
                                                <?php } ?>
                                            </div>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <a href="#"><?php echo image_tag('icon/package.png'); ?></a>
                                    <div style="cursor: pointer; opacity: 0.8; position:absolute; right: 20px; top: 12px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%"><div style="position: absolute; left: 4px; top: 1px"><font style="font-size: 9px; color: white"><?php echo $emisor[0]['tfisicos']; ?></font></div></div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div style="position: absolute; left: 470px; top: 132px;">
                            <?php
                                $modulo = "";
                                for($t=0;$t<count($recibidas_ids);$t++)
                                    if($correspondencia_id == $recibidas_ids[$t])
                                    {
                                        $modulo = "recibida";
                                        $t = count($recibidas_ids);
                                    }

                                if($modulo == "")
                                    $modulo = "enviada";
                            ?>
                            <?php if (!$sf_user->getAttribute("seguimiento_externa") || $sf_user->hasCredential(array('Archivo'), false)) { ?>
                                <a href="#" onclick="javascript:ver_doc(<?php echo $correspondencia_id; ?>); return false;" class="vbs"><?php echo image_tag('icon/view.png'); ?></a>
                                <div id ="close_<?php echo $correspondencia_id; ?>" style="z-index: 991; position: absolute; left: -500px; top: -140px; display: none;">
                                    <a href="#" class="vbs" onclick="javascript:cerrar_doc(<?php echo $correspondencia_id; ?>); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>
                                </div>
                                <div id ="documento_<?php echo $correspondencia_id; ?>" style="z-index: 990; position: absolute; border: solid 1px; background-color: #F2F2F2; left: -490px; top: -130px; width: 550px; min-height:150px; display: none;"></div>
                            <?php } ?>
                        </div>

                        <?php $config_vb = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->funcionarios_vistobueno($correspondencia_id);
                        $count= 0; ?>
                        <div style="position: absolute; left: 495px; top: 132px; min-width: 40px" <?php echo ((count($config_vb) > 0)? 'onclick="javascript:ver_vb('.$correspondencia_id.')"' : ''); ?>>
                            <?php
                            $cadena='';
                            if(count($config_vb) > 0) :
                                $cadena.= '<table style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th colspan="2">
                                                <font style="font-size: 10px; color: #007fc1">'.(($config_vb[0]['nonfirm'] == 0) ? 'Vistos bueno' : 'Visto bueno: Incompleto').'</font>
                                            </th>
                                            <th style="text-align: right">
                                                '.image_tag('icon/'.(($config_vb[0]['nonfirm'] == 0)? 'tick' : 'error').'.png').'
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                                    $tiempo_trans= new herramientas();
                                    foreach($config_vb as $value) {
                                        $icon= '';
                                        $cadena .= '<tr>';
                                        if($value->getFuncionarioId()== $sf_user->getAttribute('funcionario_id'))
                                            $cadena .= '<td><font style="font-size: 10px"><b>'.$value->getPnombre().' '.$value->getPapellido().'</b></font></td>';
                                        else
                                            $cadena .= '<td><font style="font-size: 10px">'.$value->getPnombre().' '.$value->getPapellido().'</font></td>';
                                        $cadena .= '<td style="text-align: right"><font style="font-size: 9px">';
                                        if($value->getStatus() == 'V') {
                                            $icon = image_tag('icon/tick.png', array('class' => 'tooltip', 'title' => 'Ya fue verificado'));
                                            $cadena .= '<font style="color: #666">'.date('d-m-y g:i a', strtotime($value->getUpdatedAt())).'</font>';
                                            $count++;
                                        } else {
                                            if($value->getStatus() == 'E') {
                                                if($value->getTurno()) {
                                                    $icon = image_tag('icon/clock.png', array('class' => 'tooltip', 'title' => 'A&uacute;n en espera por visto bueno'));
                                                    if($value->getOrden() == 1) {
                                                        $cadena .= '<font style="color: red">'.$tiempo_trans->tiempo_transcurrido($value->getUpdatedAt(), TRUE). '</font>';
                                                    }else {
                                                        $prev_fecha= '';
                                                        foreach($config_vb as $val) {
                                                            if($val->getOrden() == $value->getOrden()- 1) {
                                                                $prev_fecha= $val->getUpdatedAt();
                                                            }
                                                        }
                                                        $cadena .= '<font style="color: red">'.$tiempo_trans->tiempo_transcurrido($prev_fecha, TRUE). '</font>';
                                                    }
                                                }
                                                $count++;
                                            } else {
                                                if($value->getStatus() == 'D')
                                                    $icon= image_tag('icon/delete.png', array('class' => 'tooltip', 'title' => 'Cargo desincorporado'));
                                            }
                                        }
                                        $cadena .= '</font></td>';
                                        $cadena .= '<td>' . $icon . '</td>';
                                        $cadena .= '</tr>';
                                    }
                                    $cadena.='</tbody></table><div id="div_vistobueno_<?php echo $correspondencia_id; ?>" style="display: none; background-color: #e1e1e1; padding: 2px; width: auto">
                                                </div>';
                            endif;

                            $botom_vb='&nbsp;&nbsp;&nbsp;'.image_tag("icon/ruta_vb_inactive.png");
                            if(count($config_vb) > 0) {
                                $botom_vb= '&nbsp;&nbsp;&nbsp;<a href="#">'.image_tag("icon/ruta_vb.png").'</a>';
                            }
                            if($count > 0) {
                                $botom_vb.= '<div style="cursor: pointer; opacity: 0.8; position:absolute; right: 8px; top: 5px; bottom: 12px; background-color: red; width: 13px; height: 13px; border-radius: 50%"><div style="position: absolute; left: 4px; top: 1px"><font style="font-size: 9px; color: white">'.$count.'</font></div></div>';
                            }
                            echo $botom_vb;
                            ?>
                            <!--VISTOS BUENO-->
                            <div id="ruta_vb_<?php echo $correspondencia_id; ?>" style="padding: 5px; position: absolute; border: solid 1px; background-color: #F2F1F0; min-width: 225px; max-height: 225px; left: -120px; bottom: 26px; display: none; z-index: 800;">
                                <div style="position: relative; overflow-y: auto; overflow-x: hidden; max-height: 225px;">
                                    <?php echo $cadena; ?>
                                </div>
                                </a>
                            </div>
                        </div>


                        <?php if($cg != 0)
                            {
                                $receptor_unico=Doctrine::getTable('Correspondencia_Receptor')->funcionarioReceptor($emisor[0]['padre_id'],$emisor[0]['emisor_unidad_id'],$correspondencia_id);

                        ?>


                            <div style="position: absolute; left: 25px; top: 3px; width: 545px;">
                                <font class="f11b">Fecha de Recepción:</font> <font class="f11n"><?php echo date('d-m-Y h:i:s A', strtotime($receptor_unico[0]['f_recepcion'])); ?></font>
                            </div>

                            <div style="position: absolute; left: 5px; top: 2px;">
                                <?php echo image_tag('icon/open.png'); ?>
                            </div>

                            <div style="position: absolute; left: 5px; top: 36px; width: 545px;">
                                <font class="f16b">Funcionario Receptor:</font>
                                <font class="f16n" title="<?php if(isset($receptor_unico[0]['ctnombre'])) echo $receptor_unico[0]['ctnombre']; ?>">
                                    <?php if(isset($receptor_unico[0]['pn']) && isset($receptor_unico[0]['sa']) && isset($receptor_unico[0]['pn']) && isset($receptor_unico[0]['sa'])) echo ucwords(strtolower($receptor_unico[0]['pn'].' '.$receptor_unico[0]['sn'].', '.$receptor_unico[0]['pa'].' '.$receptor_unico[0]['sa'])); ?>
                                </font>; &nbsp;
                            </div>

                        <?php } else { ?>
                            <div style="position: absolute; left: 25px; top: 5px; width: 545px;">
                                <font class="f19b gris_oscuro">Correspondencia Inicial</font>
                            </div>

                            <div style="position: absolute; left: 5px; top: 2px;">
                                <?php echo image_tag('icon/mail_new.png'); ?>
                            </div>
                        <?php } ?>

                    <?php

                    $firman_list=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondenciaSeguimiento($correspondencia_id);

                    ?>
                    <div style="position: absolute; left: 5px; top: 63px; width: 545px;">
                        <font class="f16b">Funcionarios Firmantes para el envio:</font>
                        <?php
                        if(count($firman_list)>0)
                        {
                            foreach ($firman_list as $firman) { ?>
                            <font class="f15n" title="<?php if($emisor_externo != 'S') echo $firman->getctnombre(); ?>">
                                <?php  echo ucwords(strtolower($firman->getpn())).' '.ucwords(strtolower($firman->getpa())); ?>
                            </font>; &nbsp;
                        <?php } } else { ?>
                            <font class="f15n" title="<?php if($emisor_externo != 'S') echo $firman->getctnombre(); ?>">
                                <?php echo $emisor[0]['emisor_persona_cargo']." / ".$emisor[0]['emisor_persona']; ?>
                            </font>; &nbsp;
                        <?php } ?>
                    </div>
                    <?php

                    $formatos_list=Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);

                    ?>
                    <div style="position: absolute; left: 5px; top: 97px; width: 545px;">
                        <font class="f16b">Documento enviado:</font>
                        <?php foreach ($formatos_list as $formatos) { ?>
                            <font class="f15n">
                                <?php echo $formatos->getTadnombre() ?>
                            </font>; &nbsp;
                        <?php } ?>
                    </div>
                    <?php
                }
            }
            ?>
<!------------------- -->

        </div>
    </div>&nbsp;

<?php

$c++; $tab[$t]=$cg; $cg++; }

$sf_user->getAttributeHolder()->remove('correspondencia_id');
?>
</div>