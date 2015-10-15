<script src="/js/jqueryTooltip.js" type="text/javascript"></script>
<script>    
    function buscar_funcionario_prueba() {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>carnet_diseno/buscarFuncionarioPrueba',
            data: {cedula: $('#cedula_prueba').val()},
            beforeSend: function(Obj){
                $('#div_espera_funcionario').html('<?php echo image_tag('other/siglas_wait.gif'); ?>');
            },
            success: function(json) {
                $('#div_cedula').html('C.I.: '+json['cedula']);
                
                $('#val_primer_nombre').html(json['primer_nombre']);
                $('#val_segundo_nombre').html(json['segundo_nombre']);
                $('#val_primer_apellido').html(json['primer_apellido']);
                $('#val_segundo_apellido').html(json['segundo_apellido']);

                $('#val_unidad_funcional_completo').html(json['unidad_funcional_completo']);
                $('#val_unidad_funcional_reducido').html(json['unidad_funcional_reducido']);
                $('#val_unidad_funcional_siglas').html(json['unidad_funcional_siglas']);
                
                $('#val_unidad_adscripcion_completo').html(json['unidad_adscripcion_completo']);
                $('#val_unidad_adscripcion_reducido').html(json['unidad_adscripcion_reducido']);
                $('#val_unidad_adscripcion_siglas').html(json['unidad_adscripcion_siglas']);
                
                $('#val_cargo_condicion').html(json['cargo_condicion']);
                $('#val_cargo_tipo').html(json['cargo_tipo']);

                $('#img_foto').attr('src',json['foto']);
                
                formateo_nombres('nombres_bloque_uno');
                formateo_nombres('nombres_bloque_dos');
                
                formateo_unidad($("input[name='parametros[unidad][formato]']:checked").val());
                
                $('#div_cargo_condicion').html(json['cargo_condicion']);
                $('#div_cargo_tipo').html(json['cargo_tipo']);
            },
            complete: function() {
                $('#div_espera_funcionario').html('');
            }, 
            error: function(Obj, err) {
                alert('No se pudo buscar el funcionario!');
            }
        })

    }
    
    function tr_background(id){
        
        $(".tr_form_carnet").each(function(){
            $(this).css('background-color','white');
        });
        
        $('#tr_'+id).css('background-color','#F5F6CE');
    }
    
    function class_text_align(){
        $(".select_text_align").each(function(){
           $(this).css({'background-image':'url(/../images/icon/text_align_center.png',
                        'background-repeat':'no-repeat'});
        });
        
        $(".select_text_align option").each(function(){
           $(this).css({'background-image':'url(/../images/icon/text_align_'+$(this).attr('value')+'.png',
                        'background-repeat':'no-repeat'});
        });
    }

    function class_text_color(){
        $(".select_text_color").each(function(){
           $(this).css({'background-image':'url(/../images/icon/text_color_black.png',
                        'background-repeat':'no-repeat'});
        });
        
        $(".select_text_color option").each(function(){
           $(this).css({'background-image':'url(/../images/icon/text_color_'+$(this).attr('value')+'.png',
                        'background-repeat':'no-repeat'});
        });
    }

    function visible_parametro(id) {
        if($('#visible_'+id).is(':checked')){
            $('#div_'+id).show();
        } else {
            $('#div_'+id).hide();
        }
    }
    
    function fuente_parametro(id) {
        $('#div_'+id).css('font-size',$('#fuente_'+id).val());
    }
    
    function tamano_foto(id) {
        $('#img_'+id).css('width',$('#select_tamano_'+id).val());
    }
    
    function x_parametro(id) {
        $('#div_'+id).css('left',$('#x_'+id).val());
    }
    
    function y_parametro(id) {
        $('#div_'+id).css('top',$('#y_'+id).val());
    }
    
    function negrita_parametro(id) {
        if($('#negrita_'+id).is(':checked')){
            $('#div_'+id).css('font-weight','bold');
        } else {
            $('#div_'+id).css('font-weight','normal');
        }
    }
    
    function mayuscula_parametro(id) {
        if($('#mayuscula_'+id).is(':checked')){
            $('#div_'+id).html($('#div_'+id).html().toUpperCase());
        } else {
            if(id == 'nombres_bloque_uno' || id == 'nombres_bloque_dos'){
                formateo_nombres(id);
            } else if(id == 'unidad'){
                formateo_unidad();
            } else {
                $('#div_'+id).html($('#val_'+id).html());
            }
        }
    }
    
    function color_parametro(id) {
        $('#color_'+id).css('background-image','url(/../images/icon/text_color_'+$('#color_'+id).val()+'.png')
        $('#div_'+id).css('color',$('#color_'+id).val())
    }
    
    function alineacion_parametro(id) {
        $('#alineacion_'+id).css('background-image','url(/../images/icon/text_align_'+$('#alineacion_'+id).val()+'.png')
        $('#div_'+id).css('text-align',$('#alineacion_'+id).val())
    }
    
    function formateo_nombres(id) {
        var formato = $('#select_'+id).val();
        formato = $.trim(formato.replace('primer_nombre',' '+$('#val_primer_nombre').html()+' '));
        formato = $.trim(formato.replace('segundo_nombre',' '+$('#val_segundo_nombre').html()+' '));
        formato = $.trim(formato.replace('primer_apellido',' '+$('#val_primer_apellido').html()+' '));
        formato = $.trim(formato.replace('segundo_apellido',' '+$('#val_segundo_apellido').html()+' '));
        
        
        $('#div_'+id).html(formato);
        
        if($('#mayuscula_'+id).is(':checked')){
            $('#div_'+id).html($('#div_'+id).html().toUpperCase());
        }
    }
    
    function formateo_unidad() {
        $('#div_unidad').html($('#val_unidad_'+$("input[name='parametros[unidad][tipo]']:checked").val()+'_'+$("input[name='parametros[unidad][formato]']:checked").val()).html());
        
        if($('#mayuscula_unidad').is(':checked')){
            $('#div_unidad').html($('#div_unidad').html().toUpperCase());
        }
    }
</script>

<link rel="stylesheet" type="text/css" media="screen" href="/css/carnet.css" />

<?php 
    $fuentes='';
    for($i=6;$i<=28;$i++){
        if($i == 13) $select = ' selected = "selected" '; else $select = '';
        $fuentes .= '<option value="'.$i.'px" '.$select.'>'.$i.' px</option>';
    }
    
    $tamano='';
    for($i=20;$i<=204;$i++){
        if($i == 90) $select = ' selected = "selected" '; else $select = '';
        $tamano .= '<option value="'.$i.'px" '.$select.'>'.$i.'px</option>';
    }
    
    $x='<option value="0px" selected="selected">0</option>';
    for($i=1;$i<=204;$i++){
        $x .= '<option value="'.$i.'px">'.$i.'</option>';
    }
    
    $y='';
    for($i=0;$i<=325;$i++){
        $y .= '<option value="'.$i.'px">'.$i.'</option>';
    }
    
    $color = '<option value="black" selected="selected">&nbsp;&nbsp;</option>';
    $color .= '<option value="gray">&nbsp;&nbsp;</option>';
    $color .= '<option value="white">&nbsp;&nbsp;</option>';
    $color .= '<option value="gold">&nbsp;&nbsp;</option>';
    $color .= '<option value="blue">&nbsp;&nbsp;</option>';
    $color .= '<option value="red">&nbsp;&nbsp;</option>';
    $color .= '<option value="green">&nbsp;&nbsp;</option>';
    
    $alineacion = '<option value="justify">&nbsp;&nbsp;</option>';
    $alineacion .= '<option value="center" selected="selected">&nbsp;&nbsp;</option>';
    $alineacion .= '<option value="left">&nbsp;&nbsp;</option>';
    $alineacion .= '<option value="right">&nbsp;&nbsp;</option>';
    
?>

<script type="text/javascript">
   $(document).ready(function(){
      $(".arrastrame").draggable({
         containment: 'parent',
         drag: function(event, ui){
            var id = ui.helper.attr('id');
            id = id.replace('div_','');
            
            $("#x_"+id+" option[value="+ Math.round(ui.position.left) +"px]").attr("selected",true);       
            $("#y_"+id+" option[value="+ Math.round(ui.position.top) +"px]").attr("selected",true);       
         }
      })
   })
</script>

<div id="div_carnet_parametros" style="position: relative; width: 800px; height: 395px;">
    <div id="contenedor" style="position: absolute; top: 0px; left: 0px; width: 204px; height: 325px;">        
        <div class="arrastrame" id="div_foto" onclick="tr_background('foto');" style="top: 0px; position: absolute; border: 1px solid; cursor: move;"><img id="img_foto" src="/images/other/siglas_photo_small_M9.png" style="width: 90px; height: auto;"></div>
        <div class="arrastrame" id="div_cedula" onclick="tr_background('cedula');" style="top: 120px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">C.I.: 00000000</div>
        <div class="arrastrame" id="div_nombres_bloque_uno" onclick="tr_background('nombres_bloque_uno');" style="top: 140px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">1º Nombre</div>
        <div class="arrastrame" id="div_nombres_bloque_dos" onclick="tr_background('nombres_bloque_dos');" style="top: 160px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">1º Apellido</div>
        <div class="arrastrame" id="div_unidad" onclick="tr_background('unidad');" style="top: 180px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">Nombre completo de la unidad de adscripción</div>
        <div class="arrastrame" id="div_cargo_condicion" onclick="tr_background('cargo_condicion');" style="top: 200px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">Condición de cargo</div>
        <div class="arrastrame" id="div_cargo_tipo" onclick="tr_background('cargo_tipo');" style="top: 220px; position: absolute; padding: 5px; width: 194px; text-align: center; cursor: move;">Tipo de cargo</div>
        <div id="div_espera_funcionario" style="position: absolute; top: 92px; left: 32px;"></div>
    </div>
    
    <div id="val_primer_nombre" style="display: none;">1º Nombre</div>
    <div id="val_segundo_nombre" style="display: none;">2º Nombre</div>
    <div id="val_primer_apellido" style="display: none;">1º Apellido</div>
    <div id="val_segundo_apellido" style="display: none;">2º Apellido</div>
    
    <div id="val_unidad_funcional_completo" style="display: none;">Nombre completo de la unidad funcional</div>
    <div id="val_unidad_funcional_reducido" style="display: none;">Nombre reducido de la unidad funcional</div>
    <div id="val_unidad_funcional_siglas" style="display: none;">Siglas de la unidad funcional</div>
    
    <div id="val_unidad_adscripcion_completo" style="display: none;">Nombre completo de la unidad de adscripción</div>
    <div id="val_unidad_adscripcion_reducido" style="display: none;">Nombre reducido de la unidad de adscripción</div>
    <div id="val_unidad_adscripcion_siglas" style="display: none;">Siglas de la unidad de adscripción</div>
    
    <div id="val_cargo_condicion" style="display: none;">Condición de cargo</div>
    <div id="val_cargo_tipo" style="display: none;">Tipo de cargo</div>
    
    <div id="div_parametros_1000" style="position: absolute; left: 215px; top: 0px; padding: 5px; width: 800px;">
        <div>
            <a href="#" onclick="return false;">
                <img src="/images/icon/show.png"/>&nbsp;Persuavilizar una cedula
            </a>
            <input type="text" id="cedula_prueba" style="size: 15px;"/>
            <a href="#" onclick="buscar_funcionario_prueba(); return false;">
                <img src="/images/icon/find.png"/>
            </a>
        </div>

        <div>
            <img src="/images/icon/config_gray.png"/>&nbsp;Aplicar este diseño a:&nbsp;&nbsp;
            <?php
            $cargo_condiciones = Doctrine::getTable('Organigrama_CargoCondicion')->ordenado();

            foreach ($cargo_condiciones as $cargo_condicion) { 
                
                
                
                $carnet_diseno_activo = Doctrine::getTable('Seguridad_CarnetDiseno')->disenoActivoCargoCondicion($cargo_condicion->getId());

                $info_error = '';
                $color_fuente = 'black';
                $mensaje_title = '';
                if(count($carnet_diseno_activo)>0){                    
                    $mensaje_title = '[!]Condicion activa[/!]';
                    $mensaje_title .= 'Esta condicion de cargos<br/>';
                    $mensaje_title .= 'ya tiene un diseño activo,<br/>';
                    $mensaje_title .= 'Si activa esta casilla se<br/>';
                    $mensaje_title .= 'inactivara el diseño anterior.';
                    
                    $info_error = '<img src="/images/icon/info.png"/>';
                    $color_fuente = 'red';
                }
    
    
                ?>
            <font class="tooltip" title="<?php echo $mensaje_title; ?>" style="color: <?php echo $color_fuente; ?>;"><input class="check_cargo_condicion_aplicada" type="checkbox" name="indices[cargo_condicion_<?php echo $cargo_condicion->getId(); ?>]" value="A"/>&nbsp;<?php echo $info_error; ?>&nbsp;<?php echo $cargo_condicion->getNombre(); ?></font>&nbsp;&nbsp;&nbsp;
            <?php } ?>
        </div>

        <hr/>

        <table style="width: 800px;">
            <tr>
                <th></th>
                <th>Tamaño</th>
                <th>ºX</th>
                <th>ºY</th>
                <th>Color</th>
                <th>Negrita</th>
                <th>Mayuscula</th>
                <th>Alineación</th>
                <th>Campo</th>
            </tr>
            <tr id="tr_foto" class="tr_form_carnet" onclick="tr_background('foto');">
                <td></td>
                <td><select id="select_tamano_foto" name="parametros[foto][w]" onchange="tamano_foto('foto');"><?php echo $tamano; ?></select></td>
                <td><select id="x_foto" name="parametros[foto][x]"><?php echo $x; ?></select></td>
                <td><select id="y_foto" name="parametros[foto][y]"><?php echo $y; ?></select></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>Foto</td>
            </tr>
            <tr id="tr_cedula" class="tr_form_carnet" onclick="tr_background('cedula');">
                <td><input id="visible_cedula" type="checkbox" name="parametros[cedula][visible]" onclick="visible_parametro('cedula');" checked="checked"/></td>
                <td><select id="fuente_cedula" name="parametros[cedula][fuente]" onchange="fuente_parametro('cedula');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_cedula" name="parametros[cedula][x]" onchange="x_parametro('cedula');"><option value="0px">0</option></select></td>
                <td><select id="y_cedula" name="parametros[cedula][y]" onchange="y_parametro('cedula');"><?php echo $y; ?></select></td>
                <td><select id="color_cedula" name="parametros[cedula][color]" class="select_text_color" onchange="color_parametro('cedula');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_cedula" name="parametros[cedula][negrita]" value="A"  type="checkbox" onclick="negrita_parametro('cedula');"/></td>
                <td>-</td>
                <td><select id="alineacion_cedula" class="select_text_align" name="parametros[cedula][alineacion]" onchange="alineacion_parametro('cedula');"><?php echo $alineacion; ?></select></td>
                <td>Cedula</td>
            </tr>
            <tr id="tr_nombres_bloque_uno" class="tr_form_carnet" onclick="tr_background('nombres_bloque_uno');">
                <td><input id="visible_nombres_bloque_uno" type="checkbox" name="parametros[nombres_bloque_uno][visible]" onclick="visible_parametro('nombres_bloque_uno');" checked="checked"/></td>
                <td><select id="fuente_nombres_bloque_uno" name="parametros[nombres_bloque_uno][fuente]" onchange="fuente_parametro('nombres_bloque_uno');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_nombres_bloque_uno" name="parametros[nombres_bloque_uno][x]" onchange="x_parametro('nombres_bloque_uno');"><option value="0px">0</option></select></td>
                <td><select id="y_nombres_bloque_uno" name="parametros[nombres_bloque_uno][y]" onchange="y_parametro('nombres_bloque_uno');"><?php echo $y; ?></select></td>
                <td><select id="color_nombres_bloque_uno" name="parametros[nombres_bloque_uno][color]" class="select_text_color" onchange="color_parametro('nombres_bloque_uno');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_nombres_bloque_uno" name="parametros[nombres_bloque_uno][negrita]" value="A" type="checkbox" onclick="negrita_parametro('nombres_bloque_uno');"/></td>
                <td style="text-align: center;"><input id="mayuscula_nombres_bloque_uno" name="parametros[nombres_bloque_uno][mayuscula]" value="A"  type="checkbox" onclick="mayuscula_parametro('nombres_bloque_uno');"/></td>
                <td><select id="alineacion_nombres_bloque_uno" name="parametros[nombres_bloque_uno][alineacion]" class="select_text_align" onchange="alineacion_parametro('nombres_bloque_uno');"><?php echo $alineacion; ?></select></td>
                <td>
                    Nombres y Apellidos (Bloque uno)<br/>
                    <select id="select_nombres_bloque_uno" name="parametros[nombres_bloque_uno][formato]" onchange="formateo_nombres('nombres_bloque_uno');">
                        <option value="primer_nombre" selected="selected">1º Nombre</option>
                        <option value="primer_apellido">1º Apellido</option>
                        <option value="primer_nombre primer_apellido">1º Nombre - 1º Apellido</option>
                        <option value="primer_nombre segundo_nombre primer_apellido segundo_apellido">1º Nombre - 2º Nombre - 1º Apellido - 2º Apellido</option>
                        <option value="primer_nombre segundo_nombre primer_apellido">1º Nombre - 2º Nombre - 1º Apellido</option>
                        <option value="primer_nombre segundo_nombre">1º Nombre - 2º Nombre</option>
                        <option value="primer_apellido primer_nombre">1º Apellido - 1º Nombre</option>
                        <option value="primer_apellido segundo_apellido primer_nombre segundo_nombre">1º Apellido - 2º Apellido - 1º Nombre - 2º Nombre</option>
                        <option value="primer_apellido segundo_apellido primer_nombre">1º Apellido - 2º Apellido - 1º Nombre</option>
                        <option value="primer_apellido segundo_apellido">1º Apellido - 2º Apellido</option>
                    </select><br/><br/>
                </td>
            </tr>
            <tr id="tr_nombres_bloque_dos" class="tr_form_carnet" onclick="tr_background('nombres_bloque_dos');">
                <td><input id="visible_nombres_bloque_dos" type="checkbox" name="parametros[nombres_bloque_dos][visible]" onclick="visible_parametro('nombres_bloque_dos');" checked="checked"/></td>
                <td><select id="fuente_nombres_bloque_dos" name="parametros[nombres_bloque_dos][fuente]" onchange="fuente_parametro('nombres_bloque_dos');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_nombres_bloque_dos" name="parametros[nombres_bloque_dos][x]" onchange="x_parametro('nombres_bloque_dos');"><option value="0px">0</option></select></td>
                <td><select id="y_nombres_bloque_dos" name="parametros[nombres_bloque_dos][y]" onchange="y_parametro('nombres_bloque_dos');"><?php echo $y; ?></select></td>
                <td><select id="color_nombres_bloque_dos" name="parametros[nombres_bloque_dos][color]" class="select_text_color" onchange="color_parametro('nombres_bloque_dos');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_nombres_bloque_dos" name="parametros[nombres_bloque_dos][negrita]" value="A"  type="checkbox" onclick="negrita_parametro('nombres_bloque_dos');"/></td>
                <td style="text-align: center;"><input id="mayuscula_nombres_bloque_dos" name="parametros[nombres_bloque_dos][mayuscula]" value="A"  type="checkbox" onclick="mayuscula_parametro('nombres_bloque_dos');"/></td>
                <td><select id="alineacion_nombres_bloque_dos" name="parametros[nombres_bloque_dos][alineacion]" class="select_text_align" onchange="alineacion_parametro('nombres_bloque_dos');"><?php echo $alineacion; ?></select></td>
                <td>
                    Nombres y Apellidos (Bloque dos)<br/>
                    <select id="select_nombres_bloque_dos" name="parametros[nombres_bloque_dos][formato]" onchange="formateo_nombres('nombres_bloque_dos');">
                        <option value="primer_nombre" selected="selected">1º Nombre</option>
                        <option value="primer_apellido">1º Apellido</option>
                        <option value="primer_nombre primer_apellido">1º Nombre - 1º Apellido</option>
                        <option value="primer_nombre segundo_nombre primer_apellido segundo_apellido">1º Nombre - 2º Nombre - 1º Apellido - 2º Apellido</option>
                        <option value="primer_nombre segundo_nombre primer_apellido">1º Nombre - 2º Nombre - 1º Apellido</option>
                        <option value="primer_nombre segundo_nombre">1º Nombre - 2º Nombre</option>
                        <option value="primer_apellido primer_nombre">1º Apellido - 1º Nombre</option>
                        <option value="primer_apellido segundo_apellido primer_nombre segundo_nombre">1º Apellido - 2º Apellido - 1º Nombre - 2º Nombre</option>
                        <option value="primer_apellido segundo_apellido primer_nombre">1º Apellido - 2º Apellido - 1º Nombre</option>
                        <option value="primer_apellido segundo_apellido">1º Apellido - 2º Apellido</option>
                    </select><br/><br/>
                </td>
            </tr>
            <tr id="tr_unidad" class="tr_form_carnet" onclick="tr_background('unidad');">
                <td><input id="visible_unidad" type="checkbox" name="parametros[unidad][visible]" onclick="visible_parametro('unidad');" checked="checked"/></td>
                <td><select id="fuente_unidad" name="parametros[unidad][fuente]" onchange="fuente_parametro('unidad');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_unidad" name="parametros[unidad][x]" onchange="x_parametro('unidad');"><option value="0px">0</option></select></td>
                <td><select id="y_unidad" name="parametros[unidad][y]" onchange="y_parametro('unidad');"><?php echo $y; ?></select></td>
                <td><select id="color_unidad" name="parametros[unidad][color]" class="select_text_color" onchange="color_parametro('unidad');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_unidad" name="parametros[unidad][negrita]" value="A"  type="checkbox" onclick="negrita_parametro('unidad');"/></td>
                <td style="text-align: center;"><input id="mayuscula_unidad" name="parametros[unidad][mayuscula]" value="A"  type="checkbox" onclick="mayuscula_parametro('unidad');"/></td>
                <td><select id="alineacion_unidad" name="parametros[unidad][alineacion]" class="select_text_align" onchange="alineacion_parametro('unidad');"><?php echo $alineacion; ?></select></td>
                <td>
                    Nombre de la Unidad<br/>
                    <input type="radio" name="parametros[unidad][formato]" value="completo" onclick="formateo_unidad();" checked="checked"/>&nbsp;Completo&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="parametros[unidad][formato]" value="reducido" onclick="formateo_unidad();"/>&nbsp;Reducido&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="parametros[unidad][formato]" value="siglas" onclick="formateo_unidad();"/>&nbsp;siglas&nbsp;&nbsp;&nbsp;<br/><br/>
                    
                    Tipo de Unidad: 
                    <input type="radio" name="parametros[unidad][tipo]" value="adscripcion" onclick="formateo_unidad();" checked="checked"/>&nbsp;Adscripción&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="parametros[unidad][tipo]" value="funcional" onclick="formateo_unidad();"/>&nbsp;Funcional&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
            <tr id="tr_cargo_condicion" class="tr_form_carnet" onclick="tr_background('cargo_condicion');">
                <td><input id="visible_cargo_condicion" type="checkbox" name="parametros[cargo_condicion][visible]" onclick="visible_parametro('cargo_condicion');" checked="checked"/></td>
                <td><select id="fuente_cargo_condicion" name="parametros[cargo_condicion][fuente]" onchange="fuente_parametro('cargo_condicion');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_cargo_condicion" name="parametros[cargo_condicion][x]" onchange="x_parametro('cargo_condicion');"><option value="0px">0</option></select></td>
                <td><select id="y_cargo_condicion" name="parametros[cargo_condicion][y]" onchange="y_parametro('cargo_condicion');"><?php echo $y; ?></select></td>
                <td><select id="color_cargo_condicion" name="parametros[cargo_condicion][color]" class="select_text_color" onchange="color_parametro('cargo_condicion');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_cargo_condicion" name="parametros[cargo_condicion][negrita]" value="A"  type="checkbox" onclick="negrita_parametro('cargo_condicion');"/></td>
                <td style="text-align: center;"><input id="mayuscula_cargo_condicion" name="parametros[cargo_condicion][mayuscula]" value="A"  type="checkbox" onclick="mayuscula_parametro('cargo_condicion');"/></td>
                <td><select id="alineacion_cargo_condicion" name="parametros[cargo_condicion][alineacion]" class="select_text_align" onchange="alineacion_parametro('cargo_condicion');"><?php echo $alineacion; ?></select></td>
                <td>Condicion del cargo que desempeña</td>
            </tr>   
            <tr id="tr_cargo_tipo" class="tr_form_carnet" onclick="tr_background('cargo_tipo');">
                <td><input id="visible_cargo_tipo" type="checkbox" name="parametros[cargo_tipo][visible]" onclick="visible_parametro('cargo_tipo');" checked="checked"/></td>
                <td><select id="fuente_cargo_tipo" name="parametros[cargo_tipo][fuente]" onchange="fuente_parametro('cargo_tipo');"><?php echo $fuentes; ?></select></td>
                <td><select id="x_cargo_tipo" name="parametros[cargo_tipo][x]" onchange="x_parametro('cargo_tipo');"><option value="0px">0</option></select></td>
                <td><select id="y_cargo_tipo" name="parametros[cargo_tipo][y]" onchange="y_parametro('cargo_tipo');"><?php echo $y; ?></select></td>
                <td><select id="color_cargo_tipo" name="parametros[cargo_tipo][color]" class="select_text_color" onchange="color_parametro('cargo_tipo');"><?php echo $color; ?></select></td>
                <td style="text-align: center;"><input id="negrita_cargo_tipo" name="parametros[cargo_tipo][negrita]" value="A"  type="checkbox" onclick="negrita_parametro('cargo_tipo');"/></td>
                <td style="text-align: center;"><input id="mayuscula_cargo_tipo" name="parametros[cargo_tipo][mayuscula]" value="A"  type="checkbox" onclick="mayuscula_parametro('cargo_tipo');"/></td>
                <td><select id="alineacion_cargo_tipo" name="parametros[cargo_tipo][alineacion]" class="select_text_align" onchange="alineacion_parametro('cargo_tipo');"><?php echo $alineacion; ?></select></td>
                <td>Tipo de cargo que desempeña</td>
            </tr>  
        </table>

        <div class="gris_medio">Selecciones los parametros que desea que aparezcan en el carnet</div>
    </div>
</div>

<script>
    $('#div_carnet_fondo').css('height','470px');
    class_text_align(); 
    class_text_color();
    
    $('#y_cedula option[value=120px]').attr("selected",true);
    $('#y_nombres_bloque_uno option[value=140px]').attr("selected",true);
    $('#y_nombres_bloque_dos option[value=160px]').attr("selected",true);
    $('#y_unidad option[value=180px]').attr("selected",true);
    $('#y_cargo_condicion option[value=200px]').attr("selected",true);
    $('#y_cargo_tipo option[value=220px]').attr("selected",true);
</script>