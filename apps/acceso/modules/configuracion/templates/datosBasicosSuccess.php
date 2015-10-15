<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function (){
        $('.div_img_sys .prev').imgPreview({
            containerID: 'imgPreviewWithStyles',
            imgCSS: {
                width: '500'
            },
            preloadImages: false,
            distanceFromCursor: {top: -100, left:50}
        });
    });
    
    function establecer_foto(from){
        if($('#'+from+'_img').val()!==''){
            
            var data = new FormData();
            data.append('archivo',$('#'+from+'_img').get(0).files[0]);
            data.append("from", from);
            
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/establecerImg',
                type:'POST',
                dataType:'json',
                contentType:false,
                data:data,
                processData:false,
                cache:false,
                success:function(json, textStatus){
                    if(json.error=== 0) {
                        $('#td_img_'+from).html(json.valor);
                        $('#'+from+'_img').val('');
                    }else {
                        $('#'+from+'_img').val('');
                        alert(json.valor);
                    }
            }});
        } else {
            alert('Seleccione una imagen para continuar');
        }
    }
    
    function mostrarHistorial(from, op){
        tab = $('#tab_history_'+from);
        if(!tab.is(':visible')){
            tab.show();
            tab.attr("position", "absolute");
        }else{
            tab.hide();
        }

        if(op === undefined) {
            $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/historialImg',
                    type:'POST',
                    dataType:'json',
                    data:'from='+from,
                    success:function(json, textStatus){
                        $('#content_history_'+from).html(json.valor);
                }});    
        }
    }
    
    function conmutar_img_sys(){
        div = $('#div_img_sys');
        if(!div.is(':visible')){
            $('#div_img_sys_plus').attr('src', '/images/icon/minus.gif');
            div.show('fast');
        }else{
            $('#div_img_sys_plus').attr('src', '/images/icon/plus.gif');
            div.hide('fast');
        }
    }
    
    function reuseImg(i, from){
        var route= $('#img_'+from+'_'+i).attr('src');
        
        $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/reuseImg',
                type:'POST',
                dataType:'json',
                data:'from='+from+'&route='+route,
                success:function(json, textStatus){
                    $('#td_img_'+from).html(json.valor);
                    mostrarHistorial(from, true);
            }});
    }
</script>

<style>
    #imgPreviewWithStyles {
        background: #222;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        padding: 10px;
        z-index: 999;
        border: none;
    }
</style>

<form method="post" action="<?php echo sfConfig::get('sf_app_acceso_url').'configuracion/saveDatosBasicos'; ?>"> 
<fieldset id="sf_fieldset_oficinas_clave">
    <h2>Datos Básicos del Sistema</h2>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Nombre del Organismo</label>
            <div class="content">
                <input type="text" size="100" name="datos[organismo][nombre]" value="<?php echo $sf_datosBasicos['organismo']['nombre']; ?>"/>
            </div>

            <div class="help">Escriba el nombre completo del Organismo donde esta instalando el sistema</div>
        </div>
    </div>
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Siglas del Organismo</label>
            <div class="content">
                <input type="text" name="datos[organismo][siglas]" value="<?php echo $sf_datosBasicos['organismo']['siglas']; ?>"/>
            </div>

            <div class="help">Escriba las siglas del nombre del Organismo donde esta instalando el sistema</div>
        </div>
    </div>
</fieldset>
    
<fieldset id="sf_fieldset_oficinas_clave">
    <h2>Conectividad</h2>
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Dominio de internet</label>
            <div class="content">
                <input type="text" name="datos[conectividad][dominio]" value="<?php echo $sf_datosBasicos['conectividad']['dominio']; ?>"/>
            </div>

            <div class="help">Escriba el dominio de internet del organismo.</div>
        </div>
    </div>
    
    
    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Rango de IPs</label>
            <div class="content">
                <input type="text" name="datos[conectividad][rangos_ip]" value="<?php echo $sf_datosBasicos['conectividad']['rangos_ip']; ?>"/>
            </div>

            <div class="help">Escriba los dos o tres primeros bloques de las IPs usadas internamente. Si tiene mas de un bloque de IPs separelas por punto y coma(;).</div>
        </div>
    </div>
</fieldset>
    
<fieldset id="sf_fieldset_oficinas_clave">
    <h2><a style="text-decoration: none" href="#" onClick="javascript: conmutar_img_sys()"><img id="div_img_sys_plus" src="/images/icon/plus.gif"/>&nbsp;Im&aacute;genes del Sistema</a></h2>
    <div class="div_img_sys" id="div_img_sys" style="display: none">
        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Encabezado de pantalla de autenticaci&oacute;n lateral <b>izquierdo</b><br/><a href="/images/manuales/auth_left.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&nbsp;Min. 120px / Max. 660px&nbsp;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_auth_left" style="border: none"><?php echo image_tag('organismo/banner_izquierdo.png?'.time(), array('style'=>'height: 57px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>57px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="auth_left_img" type="file" name="auth_left" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('auth_left'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('auth_left'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Im&aacute;gen
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_auth_left" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('auth_left', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_auth_left"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Esta im&aacute;gen debe ser extension <b>PNG</b> con un ancho m&iacute;nimo de <b>120px</b> y m&aacute;ximo de <b>660px</b>, con una altura de <b>57px</b></div>
            </div>
        </div>

        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Encabezado de pantalla de autenticaci&oacute;n lateral <b>derecho</b><br/><a href="/images/manuales/auth_right.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&nbsp;Min. 120px / Max. 280px&nbsp;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_auth_right" style="border: none"><?php echo image_tag('organismo/banner_derecho.png?'.time(), array('style'=>'height: 57px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>57px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="auth_right_img" type="file" name="auth_right" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('auth_right'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('auth_right'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Im&aacute;gen
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_auth_right" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('auth_right', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_auth_right"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Esta im&aacute;gen debe ser extension <b>PNG</b> con un ancho m&iacute;nimo de <b>120px</b> y m&aacute;ximo de <b>280px</b>, con una altura de <b>57px</b></div>
            </div>
        </div>
        
        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Logo institucional para pantalla de inicio<br/><a href="/images/manuales/main_inst.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&nbsp;338px&nbsp;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_main_session" style="border: none"><?php echo image_tag('organismo/logo_session.png?'.time(), array('style'=>'height: 120px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>304px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="main_session_img" type="file" name="main_session" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('main_session'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('main_session'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Im&aacute;gen
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_main_session" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('main_session', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_main_session"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Esta im&aacute;gen debe ser extension <b>PNG</b> con un taman&tilde;o de <b>338px</b> de ancho y <b>304px</b> de alto.</div>
            </div>
        </div>

        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Encabezado de imprimibles<br/><a href="/images/manuales/header_pdf.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&mdash;&nbsp;665px&nbsp;&mdash;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_header" style="border: none"><?php echo image_tag('organismo/pdf/gob_pdf.png?'.time(), array('style'=>'width: 500px; height: 68px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>Min. 90px / Max. 110px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="header_img" type="file" name="header_img" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('header'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('header'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Encabezado
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_header" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em; max-height: 320px; overflow:scroll;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('header', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_header"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Seleccione una imagen para reemplazar el encabezado. Esta imagen debe ser extension <b>PNG</b> con un tama&ntilde;o de <b>665 x 90</b> px.</div>
            </div>
        </div>
        
        
        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Encabezado de imprimibles <b>(Auditoria interna)</b><br/><a href="/images/manuales/header_pdf_au.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&mdash;&nbsp;665px&nbsp;&mdash;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_header_cont" style="border: none"><?php echo image_tag('organismo/pdf/gob_pdf_contraloria.png?'.time(), array('style'=>'width: 500px; height: 68px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>Min. 90px / Max. 110px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="header_cont_img" type="file" name="header_cont_img" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('header_cont'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('header_cont'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Encabezado
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_header_cont" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em; max-height: 320px; overflow:scroll;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('header_cont', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_header_cont"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Seleccione una imagen para reemplazar el encabezado. Esta imagen debe ser extension <b>PNG</b> con un tama&ntilde;o de <b>665 x 90</b> px.</div>
            </div>
        </div>


        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">P&iacute;e de p&aacute;gina de imprimibles<br/><a href="/images/manuales/footer_pdf.png" class="prev" target="_blank"><img src="/images/icon/info.png"/></a></label>
                <div class="content">
                    <table>
                        <tr style="border: none">
                            <td style='text-align: center; border: none'>&mdash;&mdash;&nbsp;665px&nbsp;&mdash;&mdash;</td>
                            <td style="border: none"></td>
                        </tr>
                        <tr style="border: none">
                            <td id="td_img_footer" style="border: none"><?php echo image_tag('organismo/pdf/gob_footer_pdf.png?'.time(), array('style'=>'width: 500px; height: 68px')); ?></td>
                            <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>90px<br/>&nbsp;&nbsp;|</td>
                        </tr>
                    </table>
                    <input id="footer_img" type="file" name="footer_img" size="30" />
                    <a href="#" onClick="javascrtrip: mostrarHistorial('footer'); return false;"><img src="/images/icon/historial.png" style="vertical-align: middle">&nbsp;Ver historial</a>

                    <div>
                        <a href="#" onclick="establecer_foto('footer'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Pi&eacute; de P&aacute;gina
                        </a>
                    </div>

                    <div style="position: relative">
                        <div id="tab_history_footer" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; min-width: 150px; min-height:50px; left: 200px; top: -100px; display: none">
                                <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:50px; padding: 5px; box-shadow: #777 0.3em 0.3em 0.8em;">
                                <div style="top: -15px; left: -15px; position: absolute;">
                                        <a href="#" onclick="javascrtrip: mostrarHistorial('footer', true); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
                                </div>
                                <div id="content_history_footer"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help">Seleccione una imagen para reemplazar el pi&eacute; de p&aacute;gina. Esta imagen debe ser extension <b>PNG</b> con un tama&ntilde;o de <b>665 x 90</b> px.</div>
            </div>
        </div>
    </div>
    
    
    <ul class="sf_admin_actions">
        <li class="sf_admin_action_save">
            <button id="guardar_documento" onClick="javascript: this.form.submit();" style="height: 35px; margin-left: 130px">
                <?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')) ?>&nbsp;<strong>Guardar cambios</strong>
            </button>
        </li>
    </ul>
</fieldset>
    
</form>  