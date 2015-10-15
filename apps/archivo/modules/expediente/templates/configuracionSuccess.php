<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        fn_dar_eliminar();
    });
    
    function fn_conmutar(id){
        if ($('input[id=compartir_unidades_especificas_'+id+']').attr('checked')){
            $("#compartir_unidades_especificas_select_"+id).show();
        }else{
            $("#compartir_unidades_especificas_select_"+id).hide();
            $("#compartir_unidades_especificas_select_unidad_"+id).val('');
            $("#button_add_"+id).hide();
            $("#div_error_"+id).html('');
            $('#grilla_compartir_unidad_'+id+' input').each(function() {
                $(this).parent().parent().remove();
            });
        }
    };
    
    function show_button_compartir(id){
        if($('#compartir_unidades_especificas_select_unidad_'+id).val() != '') {
            $("#button_add_"+id).show();
        }else {
            $("#button_add_"+id).hide();
        }
    };
    
    function fn_agregar_unidad_compartir(id){
        if($("#compartir_unidades_especificas_select_unidad_"+id).val())
        {
            cadena = "<tr>";
            cadena = cadena + "<td><font class='f16b'>" + jQuery.trim($("#compartir_unidades_especificas_select_unidad_"+id+" option:selected").text()) + "</font><br/>";
            cadena = cadena + "<input name='configuracion_archivo[compartir][unidades][]' type='hidden' value='" + $("#compartir_unidades_especificas_select_unidad_"+id).val() + "'/>" + "</td>";
            cadena = cadena + "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            $("#grilla_compartir_unidad_"+id+" tbody").append(cadena);
            fn_dar_eliminar();
        }
        else
        { alert('Debe seleccionar un cargo para poder agregar otro'); }
    };
    
    function fn_dar_eliminar(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function validator(id) {
        var cadena= '';
        if(!validate_combo_unidad(id)) {
            cadena= '<span class="error" htmlfor="val_repetido" generated="true">Por favor, seleccione una o varias unidades con las cuales compartir su archivo.</span>';
            $("#div_error_"+id).append(cadena);
            return false;
        }else {
            if(!validate_repeat(id)) {
                cadena= '<span class="error" htmlfor="val_repetido" generated="true">Por favor, no repita las unidades autorizadas.</span>';
                $("#div_error_"+id).append(cadena);
                return false;
            }else
                return true;
        }
    }
    
    function validate_repeat(id) {
        if($('#grilla_compartir_unidad_'+id+' >tbody >tr').length) {
                var repetido= 0;
                $('#grilla_compartir_unidad_'+id+' input').each(function() {
                    var dato= $(this).val();

                    $('#grilla_compartir_unidad_'+id+' input').each(function() {
                        if($(this).val()== dato){
                            repetido++;
                        }
                    });
                    if(dato== $('#compartir_unidades_especificas_select_unidad_'+id).val()) {
                        repetido++;
                    }
                });
                if(repetido > $('#grilla_compartir_unidad_'+id+' >tbody >tr').length)
                    return false;
                else
                    return true;
          }else {
                return true;
          }
    }
    
    function validate_combo_unidad(id) {
        if ( $('#compartir_unidades_especificas_'+id).attr('checked')) {
            if($('#grilla_compartir_unidad_'+id+' >tbody >tr').length == 0) {
                    if($('#compartir_unidades_especificas_select_unidad_'+id).val()== ''){
                        return false;
                    }else{
                        return true;
                    }
                }else {
                    return true;
                }    
        }else
            return true;
    }
</script>
<style>
    #sf_admin_content .helpy {
        color: #aaa;
        padding-left: 17px;
        height: 20px;
        font-style: italic
    }
    
    .compartir_unidades_especificas_select {
        padding-left: 35px;
    }
</style>

<div id="sf_admin_container">
    <h1>M&oacute;dulo Archivo: Configuraci&oacute;n General</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <div id="sf_admin_content">

        <div class="sf_admin_form">
            <div id="contenido_opciones"><br/>
            <?php foreach($sf_config as $unidad_id => $sf_configs):
                $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidad_id); ?>
                <fieldset id="sf_fieldset_email">
                    <form id="form_compartir_<?php echo $unidad_id ?>" method="post" name="form_sms_config" onSubmit="javascript: return validator('<?php echo $unidad_id ?>');" action="<?php echo sfConfig::get('sf_app_archivo_url').'expediente/saveConfiguracion'; ?>">
                    <h2><font style="font-size: 16px"><?php echo $unidad->getNombre(); ?></font><br/>Compartir Expedientes <?php echo image_tag('icon/info', array('style' => 'vertical-align: middle' , 'class'=> 'tooltip', 'title'=> '[!]Compartir expedientes de archivo[/!]Le permite habilitar todo su archivo en forma digital::
                                                                                                                                                             a unidades espec&iacute;ficas  o a todas las  unidades::
                                                                                                                                                             subalternas a su unidad actual. Use esta  opci&oacute;n::
                                                                                                                                                             para evitar el prestamo recurrente de expedientes.::
                                                                                                                                                             Su contraparte podr&aacute; ver todos sus expediente::
                                                                                                                                                             SOLO PARA LECTURA.'));?></h2>
                        <div class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label for="correspondencia_n_correspondencia_emisor"></label>
                            <div class="content">
                                <input type="hidden" name="unidad_autorizada" id="unidad_autorizada" value="<?php echo $unidad_id ?>">
                                <input type="radio" name="configuracion_archivo[compartir][modo]" value="privado" onClick="javascript: fn_conmutar('<?php echo $unidad_id ?>')" <?php echo (($sf_configs['compartir']['privado'])? 'checked' : '') ?>/> <b>No</b> compartir mis expedientes<br/>
                                <div class="helpy">Los expedientes ser&aacute;n vistos solo por miembros del grupo que usted preside.</div>

                                <input type="radio" name="configuracion_archivo[compartir][modo]" value="unidades" id="compartir_unidades_especificas_<?php echo $unidad_id ?>" onClick="javascript: fn_conmutar('<?php echo $unidad_id ?>')" <?php echo ((count($sf_configs['compartir']['unidades']) > 0 && !$sf_configs['compartir']['sub_unidades'])? 'checked' : '') ?>/> A unidades <b>espec&iacute;ficas</b><br/>
                                <div class="helpy">Le permite seleccionar con que unidades compartir su archivo.</div>
                                <div class="compartir_unidades_especificas_select" id="compartir_unidades_especificas_select_<?php echo $unidad_id ?>" style="display: <?php echo ((count($sf_configs['compartir']['unidades']) > 0 && !$sf_configs['compartir']['sub_unidades']) ? 'block' : 'none') ?>">
                                    <select name="configuracion_archivo[compartir][unidades][unico]" id="compartir_unidades_especificas_select_unidad_<?php echo $unidad_id ?>" onchange="javascript: show_button_compartir('<?php echo $unidad_id ?>')">

                                        <?php
                                        foreach ($unidades as $clave => $valor) {
                                            $list_id = explode("&&", $clave); ?>
                                            <option value="<?php echo $list_id[0]; ?>">
                                            <?php echo html_entity_decode($valor); ?>
                                            </option>
                                            <?php } ?>
                                    </select>
                                    <a class='partial_new_view partial' id="button_add_<?php echo $unidad_id ?>" href="#" onclick="javascript: fn_agregar_unidad_compartir('<?php echo $unidad_id ?>'); return false;" style="display: none">Agregar otro</a>
                                    <br/><br/>
                                    <div>
                                        <table id="grilla_compartir_unidad_<?php echo $unidad_id ?>" class="lista">
                                            <tbody>
                                                <?php
                                                if(count($sf_configs['compartir']['unidades']) > 0 && !$sf_configs['compartir']['sub_unidades']) {
                                                    $cadena= '';
                                                    foreach($sf_configs['compartir']['unidades'] as $values) {
                                                        $unidad= Doctrine::getTable('Organigrama_Unidad')->find($values);
                                                        $cadena .= "<tr>";
                                                        $cadena .= "<td><font class='f16b'>". $unidad->getNombre() ."</font><br/>";
                                                        $cadena .= "<input name='configuracion_archivo[compartir][unidades][]' type='hidden' value=" . $unidad->getId() . " /></td>";
                                                        $cadena .= "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";    
                                                        $cadena .= "</tr>";
                                                    }
                                                    echo $cadena;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <input type="radio" name="configuracion_archivo[compartir][modo]" value="sub_unidades" onClick="javascript: fn_conmutar('<?php echo $unidad_id ?>')" <?php echo (($sf_configs['compartir']['sub_unidades'])? 'checked' : '') ?>/> A todas mis <b>sub-unidades</b><br/>
                                <div class="helpy">Comparte todos sus expedientes con unidades subordinadas seg&uacute;n organigrama.</div>
                            </div>
                            <div id="div_error_<?php echo $unidad_id ?>"></div>
                        </div>
                    </div>

                    <ul class="sf_admin_actions">
                        <li class="sf_admin_action_save">
                            <button id="guardar_documento" style="height: 35px; margin-left: 130px">
                                <?php echo image_tag('icon/filesave.png', array('style'=> 'vertical-align: middle')) ?>&nbsp;<strong>Guardar cambios</strong>
                            </button>
                        </li>
                    </ul>

                    </form>
                </fieldset>
            <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>


