<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $options_object= $parametros_all['options_object'];
}else
    $options_object= Array('devolver'=> 'true', 'email_externo'=> 'false', 'responder'=> 'true', 'descargas'=> array('pdf'=> 'false', 'odt'=> 'false', 'doc'=> 'false'));
?>
<style>
    .div_odt_up{
        display: none;
        padding: 10px;
        background-color: #e9e9e9;
        border-radius: 9px;
        border: 2px #cfcfcf solid;
        width: 410px;
        position: relative
    }
    
    .div_pdf_up{
        display: none;
        padding: 10px;
        background-color: #e9e9e9;
        border-radius: 9px;
        border: 2px #cfcfcf solid;
        width: 570px;
        position: relative
    }
</style>
<script>
    $(document).ready(function(){
        conmutarOdtAsk('odt');
        conmutarOdtAsk('pdf');
    });
    
    function establecer_foto(from, classe){
        if($('#'+from+'_img').val()!==''){
            
            var data = new FormData();
            data.append('archivo',$('#'+from+'_img').get(0).files[0]);
            data.append("from", from);
            data.append("classe", classe);
            
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

    function conmutarOdtAsk(type) {
        if($("#"+type+"UpBox").is(':checked')) {
            $('#div_'+type+'_ask').show();
        }else {
            $('#div_'+type+'_ask').hide();
            $('#div_'+type+'_up').hide();
        }
    }
    
    function cargarPlantilla(classe) {
        
        if($('#plantilla_odt').val()!==''){
            var data = new FormData();
            data.append('archivo',$('#plantilla_odt').get(0).files[0]);
            data.append("class", classe);
            data.append("consult", $('#consult_val').val());
            
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/plantillaUp',
                type:'POST',
                dataType:'json',
                contentType:false,
                data:data,
                processData:false,
                cache:false,
                success:function(json, textStatus){
                    $('#plantilla_odt').val('');
                    $('#div_odt_up').toggle();
                    alert(json.valor);
            }});
        } else {
            alert('Seleccione un archivo para continuar');
        }
    }
    
    function chanceConsultAsk(element) {
        $('#consult_val').val(element.value);
    }
    
    function downloadRef(classe) {
        var consult= $('#consult_val').val();

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/checkOdtDown',
            type:'POST',
            dataType:'html',
            data:'ft='+classe+'&cl='+consult,
            success:function(html, textStatus){
                if(html === '') {
                    $(location).attr('href', '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/odtDown?ft='+classe+'&cl='+consult);
                }else {
                    alert(html);
                }
        }});
    }
    
    function borrarIndependiente(from, classe) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>configuracion/borrarIndependiente',
            type:'POST',
            dataType:'html',
            data:'from='+from+'&classe='+classe,
            success:function(html, textStatus){
//                alert(html);
                if(html === '') {
                    javascript: $('#div_pdf_up').toggle();
                }else {
                    alert(html);
                }
        }});
    }
</script>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_object">
    <div>
        <label>Devolver</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][devolver]" value="true" <?php echo (($options_object['devolver']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][devolver]" value="false" <?php echo (($options_object['devolver']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea permitir que una vez enviado este formato los receptores lo puedan devolver?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_object">
    <div>
        <label>Email externo</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][email_externo]" value="true" <?php echo (($options_object['email_externo']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][email_externo]" value="false" <?php echo (($options_object['email_externo']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea permitir que una vez enviado este formato se pueda enviar una copia por email a receptores externos?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_object">
    <div>
        <label>Responder</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][responder]" value="true" <?php echo (($options_object['responder']== 'true')? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_object][responder]" value="false" <?php echo (($options_object['responder']== 'false')? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea permitir que una vez enviado este formato los receptores lo puedan responder o reenviar o reasignar?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_object">
    <div>
        <label>Descargas fisicas</label>
        <div class="content">
            <input type="checkbox" name="correspondencia_tipo_formato[parametros_contenido][options_object][descargas][]" value="pdf" id="pdfUpBox" onClick="javascript: conmutarOdtAsk('pdf')" <?php echo (($options_object['descargas']['pdf']== 'true')? 'checked' : ''); ?>/> PDF (Portable Document Format)
                <a id="div_pdf_ask" style="display: none" href="#" onclick="javascript: $('#div_pdf_up').toggle(); return false;"><img src="/images/icon/add.png" style="vertical-align: middle"/>Utilizar encabezado independiente</a>
                <div id="div_pdf_up" class="div_pdf_up">
                    <div>
                        <table>
                            <tr style="border: none">
                                <td style='text-align: center; border: none'>&mdash;&mdash;&nbsp;665px&nbsp;&mdash;&mdash;</td>
                                <td style="border: none"></td>
                            </tr>
                            <tr style="border: none">
                                <td id="td_img_header_inde" style="border: none"><?php
                                    $name= 'gob_pdf';
                                    if(file_exists('images/organismo/pdf/'.$classe.'_gob_pdf.png')) {
                                        $name= $classe.'_'.$name;
                                    }
                                    echo image_tag('organismo/pdf/'.$name.'.png?'.time(), array('style'=>'width: 500px; height: 68px'));
                                ?></td>
                                <td style="vertical-align: middle; border: none">&nbsp;&nbsp;|<br/>90px<br/>&nbsp;&nbsp;|</td>
                            </tr>
                        </table>
                        <input id="header_inde_img" type="file" name="header_inde_img" size="30" />
                        <a href="#" onClick="javascrtrip: borrarIndependiente('header_inde', '<?php echo $classe; ?>'); return false;"><img src="/images/icon/clear.png" style="vertical-align: middle">&nbsp;Eliminar y usar encabezado por defecto</a>

                        <div>
                            <a href="#" onclick="establecer_foto('header_inde', '<?php echo $classe; ?>'); return false;">
                                <img src="/images/icon/upload.png"/>&nbsp;Reemplazar Encabezado
                            </a>
                        </div>
                    </div>

                    <div style="font-size: 11px; color: #666; text-align: justify">Seleccione una imagen para reemplazar el encabezado. Esta imagen debe ser extension <b>PNG</b> con un tama&ntilde;o de <b>665 x 90</b> px.</div>
                </div>
                <br/>
            <input type="checkbox" name="correspondencia_tipo_formato[parametros_contenido][options_object][descargas][]" value="odt" id="odtUpBox" onClick="javascript: conmutarOdtAsk('odt')" <?php echo (($options_object['descargas']['odt']== 'true')? 'checked' : ''); ?>/> ODT (Open Document Format)&nbsp;&nbsp;&nbsp;
                <a id="div_odt_ask" style="display: none" href="#" onclick="javascript: $('#div_odt_up').toggle(); return false;"><img src="/images/icon/add.png" style="vertical-align: middle"/>Utilizar otra plantilla</a>
                <div id="div_odt_up" class="div_odt_up">
                    <div style='width: 100%; position: absolute; right: -10px; top: 5px'>
                        <input type="radio" name="consult_ask_but" value="general" onclick="chanceConsultAsk(this)" checked /> General &nbsp;&nbsp;
                        <input type="radio" name="consult_ask_but" value="consultoria" onclick="chanceConsultAsk(this)"/> Auditor&iacute;a Interna<font style="color: red">*</font>
                        <input type="hidden" value="general" id="consult_val"/>
                    </div>
                    <div style='width: 100%; position: absolute; left: 30px; top: 5px'>
                        <font style='display: none; right: 55px; position: absolute; font-style: italic; color: #555' id='font_help_odt_up'>Descarga la plantilla actual</font>
                        <a href="javascript:void(0)" onclick="javascript: downloadRef('<?php echo $classe; ?>')"><img style="cursor: pointer; position: absolute; right: 35px" src="/images/icon/info.png" onmouseover="javascript: $('#font_help_odt_up').toggle( 'slide', { direction: 'right' } );" onmouseout="javascript: $('#font_help_odt_up').toggle( 'slide', { direction: 'right' } );"/></a>
                    </div>
                    <br/>
                    <input id="plantilla_odt" type="file" name="plantilla_odt" size="30" />
                    <div>
                        <a href="#" onclick="cargarPlantilla('<?php echo $classe; ?>'); return false;">
                            <img src="/images/icon/upload.png"/>&nbsp;Cargar plantilla
                        </a>
                    </div>
                    <font style="color: #666; font-style: italic">(</font><font style="color: red; font-style: italic">*</font><font style="color: #666; font-style: italic">)</font>&nbsp;<font style="color: #666; font-style: italic; font-size: 11px">NOTA: Los formatos imprimibles del departamento de Auditor&iacute;a Interna debe&aacute;n poseer en su encabezado, el sello de la Contraloria General de la Rep&uacute;blica</font>
                </div>
            <!--<input type="checkbox" name="correspondencia_tipo_formato[parametros_contenido][options_object][descargas][]" value="doc" <?php // echo (($options_object['descargas']['doc']== 'true')? 'checked' : ''); ?>/> DOC (Document Microsoft)-->
        </div>
        <div class="help">Si desea que este formato pueda descargarse seleccione las extenciones de descarga posibles.</div>
    </div>
</div>