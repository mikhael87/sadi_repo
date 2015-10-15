<?php use_helper('jQuery'); ?>


<?php
$datos_iniciales = Doctrine::getTable('Funcionarios_Funcionario')->find($sf_user->getAttribute('funcionario_id'));
$estados = Doctrine::getTable('Public_Estado')->findAll();

$session_funcionario = $sf_user->getAttribute('session_funcionario');
?>


<script>   
    $(document).ready(function(){   
        $('.caja').css({
           position:'absolute',
           left: ($(window).width() - $('.caja').outerWidth())/2,
           top: ($(window).height() - $('.caja').outerHeight())/2
        });

        $("#actualizar_datos_div").slideDown();
        
    });
    
    function saveEmail() {
        var filter= /^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/i;
        email= $.trim($('#email').val());
        if(filter.test(email)){
            $('#js_alert').html('<?php echo image_tag('icon/cargando.gif'); ?>');
            $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/changeEmailorTelf',
                    type:'POST',
                    dataType:'html',
                    data:'email='+email+'&cedula='+<?php echo $session_funcionario['cedula']; ?>+'&act=email',
                    success:function(data, textStatus){

                    $.ajax({
                            url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/enviarCodigoValidador',
                            type:'POST',
                            dataType:'html',
                            data:'email='+email,
                            success:function(data, textStatus){ 
                                jQuery('#js_alert').html(data);
                                $('#codigo_validador').removeAttr('disabled');
                                $('#submit_validador').removeAttr('disabled');
                                
                                $('#email').attr('disabled','disabled');
                                $('#button_email').attr('disabled','disabled');
                                $('#flash_notice').show();
                                $('#flash_error').hide();
                        }});
                }});
        }else {
            alert('El email no es correcto');
            return false;
        }
    }

</script>

<style>
    label.error {
        margin-left: 10px;
        color: #DD3333;
    }
</style>

<div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: black; opacity: 0.4; filter:alpha(opacity=40); z-index: 2000;"></div>

<div id="actualizar_datos_div" class="caja" style="padding: 4px; border-radius: 10px 10px 10px 10px; background-color: #000; z-index: 2001; position: absolute; width: 550px; height:450px; display: none;">
    <div class="inner" style="border-radius: 8px 8px 8px 8px; background-color: #ebebeb; z-index: 2001; height:450px;">
<!--        <div style="top: -15px; left: -15px; position: absolute;">
            <a href="#" onclick="javascript:cerrar_actualizar_datos(); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>
        </div>-->
        <div id="actualizar_datos_ver" style="overflow: auto; height:450px; width: 540px; top: 10px; left: 10px; position: absolute;">
            <h2>¡¡¡UPSS!!! todavia no has validado tu correo electrónico.</h2>
            <div style="position: relative; width: 540px; text-align: justify;">
                Muchos de los procesos del SIGLAS hacen notificaciones a tu correo electrónico, como por ejemplo cuando tratas de recuperar tu contraseña 
                la cual se envia al mismo y asi otro tipo de notificaciondes dependiendo de tus funciones en el sistema, por ello realiza los siguientes pasos:<br/><br/>
                1.- Escribe o actualiza tu correo electrónico.<br/>
                2.- Presiona el boton de "Enviar código validador".<br/>
                3.- Revisa tu correo electrónico, busca el código validador que se te ha enviado y agregalo en el campo "Código validador".<br/>
                4.- Presiona el boton "Validar".
            </div><br/>
            <div style="position: relative; width: 540px;">
                <form id="formactu" name="formactu" method="post" action="<?php echo sfConfig::get('sf_app_acceso_url'); ?>usuario/confirmarCodigoValidador">
                    <div id="sf_admin_container">
                        <div class="notice" id="flash_notice" style="display: none;">En su correo electrónico encontrará el Codigo Validador para ingresarlo y poder continuar. Si no encuentra el correo por favor busquelo en la carpeta spam.</div>
                        <?php if ($sf_user->hasFlash('error_validacion')): ?>
                        <div class="error" id="flash_error"><?php echo $sf_user->getFlash('error_validacion'); ?></div>
                        <?php endif; ?>                          
                    </div>    
                    <table border="0" align="center" width="540">
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>                        
                        <tr>
                            <td align="right">Correo electrónico:</td>
                            <td><input id="email" name="email_personal" type="text" value="<?php echo $datos_iniciales->getEmailPersonal(); ?>"/></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td valign="top"><input id="button_email" onclick="saveEmail();" type = "button" value="Enviar código validador"/><div id="js_alert"></div></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr/></td>
                        </tr>
                        <tr>
                            <td align="right">Código validador:</td>
                            <td><input id="codigo_validador" name="codigo_validador" type="text" disabled="disabled"/></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td valign="top"><input id="submit_validador" name="submit" type = "submit" value="Validar"  disabled="disabled"/></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>


<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo html_entity_decode($sf_user->hasFlash('notice')) ?></div>
<?php endif; ?>

