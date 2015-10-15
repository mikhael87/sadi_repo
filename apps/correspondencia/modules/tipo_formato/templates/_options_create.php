<?php use_helper('jQuery'); ?>
<style>
    .div_priv_noshow{
        padding: 10px;
        background-color: #e9e9e9;
        border-radius: 9px;
        border: 2px #cfcfcf solid;
        width: 240px;
        position: relative
    }
</style>

<script>
    $(document).ready(function(){
        fn_dar_eliminar();
        fn_dar_eliminar_vb();
    });
    
    function show_select_funcionario(){
        $("#div_unidad_funcionario").show();
    }
    
    function conmutar(visto_bueno_general_config_id){
        div = document.getElementById('tab_ruta');
        if($('#tab_ruta').is (':visible')) {
            $('#tab_ruta').hide();
        }else
            $('#tab_ruta').show();

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/vistobuenoWindow',
            type:'POST',
            dataType:'html',
            data:'visto_bueno_general_config_id='+visto_bueno_general_config_id,
            beforeSend: function(Obj){
                              $('#div_vb_actualizable').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Espere ...');
                        },
            success:function(data, textStatus){
                    jQuery('#div_vb_actualizable').html(data);
                    jQuery('#edit_vb_route').val(visto_bueno_general_config_id);
                    fn_dar_eliminar_vb();
                    fn_counter_order();
        }});
    }
    
    function fn_dar_eliminar(visto_bueno_general_config_id){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/eliminaRuta',
            type:'POST',
            dataType:'html',
            data:'vb_general_config_id='+visto_bueno_general_config_id,
            success:function(html, textStatus){
                    var boton= $("#boton_vb_ruta_delete_"+visto_bueno_general_config_id);
                    boton.parent().parent().fadeOut("normal", function(){
                        $(this).remove();
                      });
                      if($('#table_rutas >tbody >tr').length === 1)
                          $('#table_rutas >tbody').append('<tr><td class="index" style="font-weight: bolder; font-size: 13px; color: #666; text-align: center; vertical-align: middle">Sin ruta</td></tr>');
        }});
    };
    
    function fn_dar_eliminar_vb(){
        $("a.elimina_vb").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                  $(this).remove();
                  fn_counter_order();
                });
        });
    };
    
    function fn_counter_order(){
        count= $('#table_vistobuenos >tbody >tr').length;
        $('#table_vistobuenos > tbody > tr').each(function() {
            $(this).children('td:first').html(count);
            count= parseInt(count) -1;
        });
    }
    
    function vistobueno_show(op) {
        if (op) {
            if ($('#vb_general_div').is (':visible')) {
                $('#vb_general_div').hide('fast');
            }else {
                $('#vb_general_div').show('fast');
            }
        }else {
            $('#vb_general_div').hide('fast');
        }
    }
    
    function fn_agregar_vistobueno(){
        order= $('#table_vistobuenos >tbody >tr').length;
        cadena = "<tr>";
        cadena = cadena + "<td class='index' style='font-weight: bolder; font-size: 20px; text-align: center; vertical-align: middle'>1</td>";
        cadena = cadena + "<td>" + jQuery.trim($("#vistobueno_unidad option:selected").text()) + "<br/>" + jQuery.trim($("#vistobueno_funcionario option:selected").text()) + "</td>";
        cadena = cadena + "<td style='text-align: center; vertical-align: middle'>Visto bueno</td>";
        cadena = cadena + "<td style='text-align: center; vertical-align: middle'><a class='elimina_vb' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
        cadena = cadena + "<input type='hidden' name='funcionarios_vb[]' value='" + $("#vistobueno_funcionario option:selected").val() + "#A'/>";
        cadena = cadena + "</tr>";
        $("#table_vistobuenos > tbody").append(cadena);
        fn_dar_eliminar_vb();

        <?php echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                        'url' => 'grupos/vistobuenoUnidades',
                                                        'with' => "'unidad_id= 0'",)); ?>

        document.getElementById("vistobueno_unidad").selectedIndex = "";

        $("#div_unidad_funcionario").hide();

        fn_counter_order();
    }
    
    function save_route(cadena_vb, formato_id) {
        var vb_ruta_nombre= $('#vb_ruta_nombre').val();
        var vb_ruta_descripcion= $('#vb_ruta_descripcion').val();
        var edit_vb_route= $('#edit_vb_route').val();
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>tipo_formato/preSaveRuta',
            type:'POST',
            dataType:'html',
            data:'nombre='+vb_ruta_nombre+'&descripcion='+vb_ruta_descripcion+'&cadena_vb='+cadena_vb+'&formato_id='+formato_id,
            success:function(data, textStatus){
                    if($('#table_rutas >tbody >tr >td').html() === 'Sin ruta')
                        $('#table_rutas >tbody >tr').remove();
                    $('#tab_ruta').hide();
                    if(edit_vb_route !== 'empty')
                        fn_dar_eliminar(edit_vb_route);
                    jQuery('#table_rutas tbody').append(data);
        }});
    }
    
    function check_form(formato_id) {
        if($('#vb_ruta_nombre').val() !== '') {
            if($('#table_vistobuenos >tbody >tr').length) {
                var repetido= 0; var cadena_vb= '';
                $('#table_vistobuenos input').each(function() {
                    var dato= $(this).val();
                    cadena_vb = cadena_vb+dato+'$$';

                    $('#table_vistobuenos input').each(function() {
                        if($(this).val()=== dato){
                            repetido++;
                        }
                    });
                });
                if(repetido > $('#table_vistobuenos >tbody >tr').length)
                    alert("Por favor, no repita los visto buenos.");
                else
                    save_route(cadena_vb, formato_id);
          }else {
                alert("Por favor, agregue algun funcionario");
          }
        }else {
            alert("Por favor, llene los campos requeridos");
        }
    }
    
    function toggle_div_priv(valor) {
        if(valor=== 'true') {
            $('#div_priv_noshow').hide();
        }else {
            $('#div_priv_noshow').show();
        }
    }
</script>

<style>
    .helpfont {
        font-size: 10px;
        color: #333;
    }
    .icon_check {
        vertical-align: middle
    }
</style>

<?php
if(sfContext::getInstance()->getUser()->hasAttribute('parametros_formato')) {
    $parametros_all= sfContext::getInstance()->getUser()->getAttribute('parametros_formato');
    $options_create= $parametros_all['options_create'];
}else {
    $options_create= Array('privacidad'=> 'true', 'prioridad'=> 'true', 'adjunto_archivo'=> 'true', 'adjunto_fisico'=> 'true', 'vistobueno'=> 'true', 'vistobueno_dinamico'=> 'false');
}

$unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE);
?>
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_create">
    <div>
        <label>Privacidad</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][privacidad][habilitado]" value="true" onclick="javascript: toggle_div_priv(this.value)" <?php echo (($options_create['privacidad']['habilitado']== 'true') ? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][privacidad][habilitado]" value="false" onclick="javascript: toggle_div_priv(this.value)" <?php echo (($options_create['privacidad']['habilitado']== 'false') ? 'checked' : ''); ?>/> NO
            <div id="div_priv_noshow" class="div_priv_noshow" style='display: <?php echo (($options_create['privacidad']['habilitado']=='false')? 'block' : 'none' ) ?>'>
                <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][privacidad][publico]" value='true' <?php echo ((isset($options_create['privacidad']['publico']))? (($options_create['privacidad']['publico']== 'true') ? 'checked' : '') : 'checked'); ?>/>&nbsp;P&uacute;blico&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][privacidad][publico]" value='false' <?php echo ((isset($options_create['privacidad']['publico']))? (($options_create['privacidad']['publico']== 'false') ? 'checked' : '') : ''); ?>/>&nbsp;Privado<br/><br/>

                <font style="color: #666; font-style: italic; font-size: 11px">NOTA: Los usuarios no podr&aacute;n modificar esta opci&oacute;n cuando creen correspondencia</font>
            </div>
        </div>
        <div class="help">¿Desea habilitar la seleccion de la opción de privacidad para este formato?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_create">
    <div>
        <label>Prioridad</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][prioridad]" value="true" <?php echo (($options_create['prioridad']== 'true') ? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][prioridad]" value="false" <?php echo (($options_create['prioridad']== 'false') ? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea habilitar la seleccion de la opción de prioridad para este formato?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_create">
    <div>
        <label>Adjuntar archivos</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][adjunto_archivo]" value="true" <?php echo (($options_create['adjunto_archivo']== 'true') ? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][adjunto_archivo]" value="false" <?php echo (($options_create['adjunto_archivo']== 'false') ? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea que se permita adjuntar archivos para este formato?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_create">
    <div>
        <label>Enunciados fisicos</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][adjunto_fisico]" value="true" <?php echo (($options_create['adjunto_fisico']== 'true') ? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][adjunto_fisico]" value="false" <?php echo (($options_create['adjunto_fisico']== 'false') ? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">¿Desea que se permita enuciar si se realizara un envio fisico con este formato?.</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_vistobueno" style="position: relative">
    <div>
        <label>Rutas de Visto Bueno</label>
        <div class="content">
            <input type="radio" onChange='javascript: vistobueno_show(false)' name="correspondencia_tipo_formato[parametros_contenido][options_create][vistobueno]" value="G" <?php echo (($options_create['vistobueno']== 'G') ? 'checked' : ''); ?>/> Usar vistos bueno de grupos <font style="color: #666; font-size: 11px">(Creados en grupos de correspondencia, pueden ser editados por jefes de unidades)</font><br/>
            <input type="radio" onChange='javascript: vistobueno_show(true)' id="vb_fijo" name="correspondencia_tipo_formato[parametros_contenido][options_create][vistobueno]" value="F" <?php echo (($options_create['vistobueno']== 'F') ? 'checked' : ''); ?>/> Usar vistos bueno fijos <font style="color: #666; font-size: 11px">(Cree una o varias rutas de vistos bueno general)</font><br/>
            <div id='vb_general_div' style='display: <?php echo (($options_create['vistobueno']== 'F')? 'block' : 'none') ?>; margin-left: 20px'>
                <div id="div_table_rutas" style="padding-left: 1em">
                    <table id="table_rutas">
                        <a class='partial_new_view partial' href="#" onclick="conmutar('empty'); return false;" id="ruta">Agregar otra ruta</a>
                        <thead><tr><th>Nombre</th><th>Descripci&oacute;n</th><th>Ruta</th><th>Acci&oacute;n</th></tr></thead>
                            <tbody>
                                <?php
                                $counter= 2;
                                //RECUPERAR EL ID DEL FORMATO QUE SE ESTA EDITANDO
                                if(isset($parametros_all['formato_id'])) {
                                    $vb_config= Doctrine::getTable('Correspondencia_VistobuenoGeneralConfig')->findByTipoFormatoIdAndStatus($parametros_all['formato_id'], 'A');
                                    
                                    foreach($vb_config as $values) {
                                        $vb_data= Doctrine::getTable('Correspondencia_VistobuenoGeneral')->vistobuenoGeneral($values->getId());
                                        
                                        $cadena = "<tr>";
                                        $cadena .= "<td class='index' style='font-weight: bolder; font-size: 12px; text-align: left; vertical-align: middle'>" . $values->getNombre() . "</td>";
                                        $cadena .= "<td style='text-align: left; vertical-align: middle'>". $values->getDescripcion() ."</td>";
                                        $cadena .= "<td style='text-align: left; vertical-align: middle'>";
                                        
                                        $rutas_div= '';
                                        foreach($vb_data as $value) {
                                            $cadena2= ''; $cargo= TRUE;
                                            $datos_fun= Doctrine::getTable('Funcionarios_FuncionarioCargo')->datosFuncionario($value->getFuncionarioId());

                                            $cadena2 .= "<font style='font-weight: bolder'>". $value->getOrden() ."</font> - ";
                                            if(!count($datos_fun)> 0 || $value->getStatus()== 'D') {
                                                $sin_cargo= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($value->getFuncionarioId());
                                                $cadena2 .= '<font style="color: #666">'.$sin_cargo[0]['primer_nombre'] . " " . $sin_cargo[0]['primer_apellido']  . "</font> - ";
                                                $cargo= FALSE;
                                            }else {
                                                $cadena2 .= $datos_fun[0]['fnombre'] . " " . $datos_fun[0]['fapellido'] . " - ";
                                            }
                                            $cadena2 .= (($value->getStatus()== 'A' && $cargo)? 'Visto bueno': '<font style="color: red">Desincorporado(a) del Cargo</font>');
                                            $cadena2 .= "</br>";

                                            $rutas_div .= $cadena2;
                                        }
                                        
                                        if($rutas_div != '') {
                                            $cadena .= '<div style="border: 1px solid #d0d0d0; padding: 5px">'. $rutas_div. '</div>';
                                        }
                                        
                                        $cadena .= "</td>";
                                        
                                        $cadena .= "<td style='text-align: center; vertical-align: middle'><a href='#' onClick='javascript: conmutar(". $values->getId() ."); return false;' style='cursor: pointer;'><img src='/images/icon/edit.png'/></a>&nbsp;";
                                        $cadena .= "<a id='boton_vb_ruta_delete_". $values->getId() ."' href='#' onClick='javascript: fn_dar_eliminar(". $values->getId() ."); return false;' class='elimina_ruta' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                                        $cadena .= "</tr>";

                                        $counter++;
                                        echo $cadena;
                                    }
                                }
                                
                                if($counter == 2) {
                                    echo "<tr><td class='index' style='font-weight: bolder; font-size: 13px; color: #666; text-align: center; vertical-align: middle'>Sin ruta</td></tr>";
                                }
                                ?>
                            </tbody>
                    </table>
                </div>
            </div>
            <input type="radio" onChange='javascript: vistobueno_show(false)' name="correspondencia_tipo_formato[parametros_contenido][options_create][vistobueno]" value="N" <?php echo (($options_create['vistobueno']== 'N') ? 'checked' : ''); ?>/> No usar ninguna ruta de visto bueno pre-configurada
        </div>
    </div>
    
    
    
    <div id="tab_ruta" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: auto; min-height:300px; left: 20%; top: 30%; display: none; box-shadow: #666 0.4em 0.5em 2em;">
        <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:300px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
            <div style="top: -15px; left: -15px; position: absolute;">
                    <a href="#" onclick="javascript: conmutar(); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
            </div>
            <div style="padding: 10px">
                <div id="div_vb_actualizable">
                    <!--CONTENIDO POR AJAX-->
                </div>
                
                <div style="padding-left: 1em; color: #aaa;"><font style="color: #ff5a5a">*</font>&nbsp;El documento deber&aacute; ser verificado por los funcionarios de la lista en orden ascendente</div>
                <div style="text-align: right; padding-top: 5px; font-size: 14px; padding-right: 15px"><a class='partial_new_view partial' href="#" onClick="javascript: show_select_funcionario(); return false;" >Agregar funcionario</a></div>
                
                <div id="div_unidad_funcionario" style="padding-left: 1em; display: none">
                    <div id="div_vistobueno_unidad">
                        <br />Unidad:<br />
                        <select name="vistobueno_unidad" id="vistobueno_unidad" onchange="
                            <?php
                            echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                    'url' => 'grupos/vistobuenoUnidades',
                                                    'with' => "'unidad_id=' +this.value",))
                            ?>"> <?php
                            foreach ($unidades as $clave => $valor) {
                                $list_id = explode("&&", $clave); ?>
                                <option value="<?php echo $list_id[0]; ?>">
                                    <?php echo $valor; ?>
                                </option>
                                <?php } ?>
                        </select>
                    </div>

                    <div id="div_vistobueno_funcionario">
                        <br />Funcionario:<br />
                        <select name="vistobueno_funcionario" id="vistobueno_funcionario">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div style="text-align: right; width: auto; background-color: #B7B7B7; padding-right: 10px" id="renew">
                <input name="aceptar" type="button" value="Aceptar" onClick="check_form('<?php echo (isset($parametros_all['formato_id'])? $parametros_all['formato_id'] : "new") ?>')"/>
            </div>
        </div>
    </div>
    
    
</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_options_vistobueno_dinamico">
    <div>
        <label>Permitir visto bueno din&aacute;mico</label>
        <div class="content">
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][vistobueno_dinamico]" value="true" <?php echo (($options_create['vistobueno_dinamico']== 'true') ? 'checked' : ''); ?>/> SI &nbsp;&nbsp;
            <input type="radio" name="correspondencia_tipo_formato[parametros_contenido][options_create][vistobueno_dinamico]" value="false" <?php echo (($options_create['vistobueno_dinamico']== 'false') ? 'checked' : ''); ?>/> NO
        </div>
        <div class="help">Permite la creaci&oacute;n, edici&oacute;n y eliminaci&oacute;n de ruta de visto bueno directamente al crear documento. Nota: convierte las rutas pre-configuradas en din&aacute;micas</div>
    </div>
</div>