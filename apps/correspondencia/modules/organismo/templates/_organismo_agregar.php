<?php
$organismos = Doctrine::getTable('Organismos_Organismo')->todosNoHidratados();
$organismos_tipo = Doctrine::getTable('Organismos_OrganismoTipo')->createQuery('a')->orderBy('nombre')->execute();

$grupoReceptoresExternos = Doctrine::getTable('Correspondencia_GrupoReceptor')->getNombres("E");

$cor_externa_organismo= 0;
if($sf_user->hasAttribute('emisor_externo')) {
    $datos_emisor= $sf_user->getAttribute('emisor_externo');
    $parts_ext= explode('#', $datos_emisor);
    $cor_externa_organismo= $parts_ext[0];
}
?>

<style>
    .div_organismo_info{
        display: none;
        padding: 10px;
        background-color: #e9e9e9;
        border-radius: 9px;
        border: 2px #cfcfcf solid;
        width: 410px
    }
    .helpy{
        color: #aaa;
        font-style: italic
    }
</style>

<script>
    modificado = 0;
    agregado = 0;
    var cantidadExterna = 0;
    var agregar = 0;

    $(document).ready(function(){
        fn_dar_eliminar();
        fn_cantidad();
        fn_ajax_inicial();

        $('#grupoReceptorExterno').change(function (){
            grupo_id = $('#grupoReceptorExterno').val();
            $.ajax(
            {
                url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/funcionariosGrupo',
                type:'POST',
                dataType:'html',
                data:'grupo_id='+grupo_id+"&tipo=E",
                success:function(data, textStatus) {
                    modificado = 0;
                    $("#CargarGrupo").html(data);
                }
            });
        });
    });

    function clear_siglas() {
        if($('#organismo_siglas').val()==='<- SIGLAS ->') {
            $('#organismo_siglas').val(null);
            $('#organismo_siglas').html('');
        }
    };

    function verificarOrganismo()
    {
        var error = null;
        if(!$("#organismo_nombre").val()){
            error = 'Escriba el nombre del Organismo';
        } else if(!$("#organismo_siglas").val() || $("#organismo_siglas").val()=== "<- SIGLAS ->") {
            error = 'Escriba las siglas que identifican el Organismo.';
        } else if(!$("#organismo_tipo").val()) {
            error = 'Seleccione el Tipo de Organismo.';
        }

        if(error=== null) {
            datos_organismo_id = $("#select_organismo").val();
            datos_persona = $("#persona_nombre").val();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/verificarOrganismos',
                type:'POST',
                dataType:'html',
                data:'datos[organismo]='+$("#organismo_nombre").val()+
                    '&datos[siglas]='+$("#organismo_siglas").val()+
                    '&datos[tipo]='+$("#organismo_tipo").val()+
                    '&organismo_name=<?php echo $organismo_name; ?>'+
                    '&persona_name=<?php echo $persona_name; ?>'+
                    '&cargo_name=<?php echo $cargo_name; ?>',
                success:function(data, textStatus){
                    jQuery('#organismos').html(data);
                }});

            $("#div_organismo_info").hide();
            $("#organismo_nombre").hide();
            $("#organismo_button_open").hide();
            $("#select_organismo").show();
        } else {
            alert(error);
        }
    }    

    function saveOrganismo(organismo_id) {
        if(organismo_id=== 0) {
            var error = null;
            if(!$("#organismo_nombre").val()){
                error = 'Escriba el nombre del Organismo';
            } else if(!$("#organismo_siglas").val() || $("#organismo_siglas").val()=== "<- SIGLAS ->") {
                error = 'Escriba las siglas que identifican el Organismo.';
            } else if(!$("#organismo_tipo").val()) {
                error = 'Seleccione el Tipo de Organismo.';
            }

            var org_nombre= $("#organismo_nombre").val();
            var org_siglas= $("#organismo_siglas").val();
            var org_tipo= $("#organismo_tipo").val();

            if(error=== null) {
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/saveOrganismo',
                    type:'POST',
                    dataType:'html',
                    data:'organismo='+org_nombre+
                        '&siglas='+org_siglas+
                        '&tipo='+org_tipo+
                        '&organismo_name=<?php echo $organismo_name; ?>'+
                        '&persona_name=<?php echo $persona_name; ?>'+
                        '&cargo_name=<?php echo $cargo_name; ?>',
                    success:function(data, textStatus){
                        jQuery('#organismos').html(data);
                    }});

                $("#div_organismo_info").hide();
                $("#organismo_nombre").hide();
                $("#organismo_button_open").hide();
                $("#verificacion_organismo").hide();
                $("#select_organismo").show();
            } else {
                alert(error);
            }
        }else {

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/listarOrganismos',
                type:'POST',
                dataType:'html',
                data:'id_select='+organismo_id+
                    '&organismo_name=<?php echo $organismo_name; ?>'+
                    '&persona_name=<?php echo $persona_name; ?>'+
                    '&cargo_name=<?php echo $cargo_name; ?>',
                success:function(data, textStatus){
                    jQuery('#organismos').html(data);
                }});
        }
    }

    function fn_ajax_inicial() {
        if(<?php echo $cor_externa_organismo ?> !== '') {
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/listarPersonas',
                type:'POST',
                dataType:'html',
                data:"o_id=<?php echo $cor_externa_organismo ?>&persona_name=<?php echo $persona_name ?>&cargo_name=<?php echo $cargo_name ?>",
                success:function(data, textStatus){
                    jQuery('#lista_personas').html(data);
            }});
        }
    }

    function fn_agregar_externo(){
        if($("#select_organismo").val() && $("#select_persona").val() && $("#select_cargos").val())
        {
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + $("#select_organismo option:selected").text() + "</font><br/>";
            cadena += "<font class='f16n'>" + $("#select_cargos option:selected").text() + " / " + $("#select_persona option:selected").text() + "</font>";
            cadena += "<input class='val_externo_repeat' name='correspondencia[receptor_externo][copias][]' type='hidden' value='" + $("#select_organismo").val() + "#" + $("#select_persona").val() + '#' + $("#select_cargos").val() + "'/></td>";
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td></tr>";
            comp = $("#select_organismo").val() + "#" + $("#select_persona").val() + '#' + $("#select_cargos").val();
            if(cantidadExterna > 0){
                $("#grilla_receptor_externo tbody tr td input[name='correspondencia[receptor_externo][copias][]']").each(function (){
                    if($(this).val() == comp){ agregar = 1; }
                });
                if(agregar == 0)
                {
                    $("#grilla_receptor_externo tbody").append(cadena);
                    fn_dar_eliminar_externo();
                    fn_cantidad_externo();
                    agregar = 0;
                    modificado = 1;
                }
            }
            else
            {
                $("#grilla_receptor_externo tbody").append(cadena);
                fn_dar_eliminar_externo();
                fn_cantidad_externo();
                modificado = 1;
            }

            $("#select_organismo option[value='0']").attr('selected', 'selected');
            $.ajax({
                    url:'organismo/listarPersonas',
                    type:'POST',
                    dataType:'html',
                    data:"o_id=0&persona_name='<?php echo $persona_name ?>'&cargo_name='<?php echo $cargo_name ?>'",
                    success:function(data, textStatus){
                        jQuery('#lista_personas').html(data);
            }});
        }
        else
        { alert('Debe seleccionar un organismo, persona y cargo para poder agregar otro'); }
    };

    function fn_cantidad_externo(){
            cantidadExterna = $("#grilla_receptor_externo tbody").find("tr").length;
            $("#span_cantidad").html(cantidadExterna);
            if(cantidadExterna > 2)
                fn_guardar_grupo_externo();
            else
                $("#guardarGrupoExterno").remove();
    };

    function fn_dar_eliminar_externo(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };

    function keyupFunc() {
        var filter = $('#organismo_nombre').val(), count = 0;
        $("#select_organismo option").each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show();
                count++;
            }
        });

        if(count==0) {
            $("#organismo_button_open").show();
            $("#select_organismo").hide();
        } else {
            $("#organismo_button_open").hide();
            $("#div_organismo_info").hide();
            $("#select_organismo").show();
        }
    }

    function toggleFinder()
    {
        $("#organismo_nombre").toggle("slow");
        $("#organismo_nombre").val(null);
        $("#select_organismo option").show();
        $("#organismo_button_open").hide();
        $("#select_organismo").show();
        $("#div_organismo_info").hide();
        $("#verificacion_organismo").hide();
    }

    function organismo_button_open_func()
    {
        $("#div_organismo_info").toggle("slow");
        $("#organismo_siglas").val('<- SIGLAS ->');
        var left_x = parseFloat(($("#organismo_tipo").css("width")).replace('px','')) + 130;
        $("#div_organismo_button_save").css("left",left_x);
    }

//    FUNCIONES DE PERSONA
//    FUNCIONES DE PERSONA
//    FUNCIONES DE PERSONA
//    FUNCIONES DE PERSONA

    function togglePersona()
    {
        $("#div_persona_cedula").toggle("slow");
        $("#div_persona_button_validate").toggle("slow");
        $("#div_persona_nombre").toggle("slow");
        $("#div_persona_button_save").toggle("slow");
        $("#div_espera_verificar_cedula").toggle("slow");
        $("#persona_cedula").val(null);
        $("#persona_nombre").val(null);
        $("#div_espera_verificar_cedula").html('');
    }

    function listarPersonas(){
        $("#organismo_nombre").hide("slow");
        $("#organismo_nombre").val(null);
        $("#select_organismo option").show();
        $("#organismo_button_open").hide();
        $("#div_organismo_info").hide();

        $('#lista_personas').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando personas...');

        <?php
            echo jq_remote_function(array('update' => 'lista_personas',
            'url' => 'organismo/listarPersonas',
            'with'     => "'o_id='+$('#select_organismo').val()+'&persona_name=".$persona_name."&cargo_name=".$cargo_name."'",));
        ?>
    }

    function verificarPersona()
    {
        var error = null;
        if(!$("#select_organismo").val()){
            error = 'Seleccione el organismo.';
        } else if(!$("#persona_nombre").val()) {
            error = 'Escriba el nombre de la persona.';
        }

        if(error==null) {
            datos_organismo_id = $("#select_organismo").val();
            datos_persona = $("#persona_nombre").val();

            $('#lista_personas').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Guardando persona...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/verificarPersonas',
                type:'POST',
                dataType:'html',
                data:'datos_organismo_id='+datos_organismo_id+
                    '&datos_persona='+datos_persona+
                    '&persona_name=<?php echo $persona_name; ?>'+
                    '&cargo_name=<?php echo $cargo_name; ?>',
                success:function(data, textStatus){
                    jQuery('#lista_personas').html(data);
                }})

            $("#div_persona_nombre").hide();
            $("#div_persona_button_save").hide();
        } else {
            alert(error);
        }
    }
    
    function verificarCedula()
    {
        var error = null;
        if(!$("#select_organismo").val()){
            error = 'Seleccione el organismo.';
        } else if(!$("#persona_cedula").val()) {
            error = 'Escriba una cedula para realizar la verificacion ante el SAIME.';
        }
        
        $('#persona_nombre').val('');

        if(error==null) {
            $('#div_espera_verificar_cedula').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Verificando cedula...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/verificarCedula',
                type:'POST',
                dataType:'json',
                data:'datos_organismo_id='+$("#select_organismo").val()+
                    '&cedula_verificar='+$("#persona_cedula").val(),
                success:function(json){
                    $('#div_espera_verificar_cedula').html('');
                    if(json['persona_id']!=''){
                        $('#persona_nombre').val(json['primer_nombre']+' '+json['primer_apellido']);
                    } else {
                        $("#persona_cedula").val('');
                        $('#persona_nombre').val('Cedula no encontrada');
                    }
                }})
        } else {
            alert(error);
        }
    }

    function savePersona(persona_id)
    {
        if(persona_id === 0){
            var error = null;
            if(!$("#select_organismo").val()){
                error = 'Seleccione el organismo.';
            } else if(!$("#persona_nombre").val()) {
                error = 'Escriba el nombre de la persona.';
            }

            if(error=== null) {
                datos_organismo_id = $("#select_organismo").val();
                datos_persona = $("#persona_nombre").val();

                $('#lista_personas').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Guardando persona...');

                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/savePersona',
                    type:'POST',
                    dataType:'html',
                    data:'datos_organismo_id='+datos_organismo_id+
                        '&datos_persona='+datos_persona+
                        '&persona_name=<?php echo $persona_name; ?>'+
                        '&cargo_name=<?php echo $cargo_name; ?>',
                    success:function(data, textStatus){
                        jQuery('#lista_personas').html(data);
                    }})

                $("#div_persona_nombre").hide();
                $("#div_persona_button_save").hide();
            } else {
                alert(error);
            }
        } else {
            $('#lista_personas').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Seleccionando persona...');

            <?php
            echo jq_remote_function(array('update' => 'lista_personas',
            'url' => 'organismo/listarPersonas',
            'with'     => "'o_id='+$('#select_organismo').val()+'&persona_name=".$persona_name."&cargo_name=".$cargo_name."&id_old='+persona_id",));
            ?>
        }
    }

//    FUNCIONES DE PERSONA CARGO
//    FUNCIONES DE PERSONA CARGO
//    FUNCIONES DE PERSONA CARGO
//    FUNCIONES DE PERSONA CARGO

    function toggleCargo()
    {
        $("#div_cargo_nombre").toggle("slow");
        $("#div_cargo_button_save").toggle("slow");
        $("#cargo_nombre").val(null);
    }

    function listarCargos(){
        $('#lista_cargos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando cargos...');

        <?php
            echo jq_remote_function(array('update' => 'lista_cargos',
            'url' => 'organismo/listarCargos',
            'with'     => "'p_id='+$('#select_persona').val()+'&cargo_name=".$cargo_name."'",));
        ?>
    }

    function saveCargo()
    {
        var error = null;
        if(!$("#select_persona").val()){
            error = 'Seleccione la persona.';
        } else if(!$("#cargo_nombre").val()) {
            error = 'Escriba el nombre del cargo.';
        }

        if(error==null) {
            persona_id = $("#select_persona").val();
            cargo = $("#cargo_nombre").val();

            $('#lista_cargos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Guardando cargo...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>organismo/saveCargo',
                type:'POST',
                dataType:'html',
                data:'datos[persona_id]='+persona_id+
                    '&datos[cargo]='+cargo+
                    '&cargo_name=<?php echo $cargo_name; ?>',
                success:function(data, textStatus){
                    jQuery('#lista_cargos').html(data);
                }})

            $("#div_cargo_nombre").hide();
            $("#div_cargo_button_save").hide();
        } else {
            alert(error);
        }
    }


    function fn_agregar_grupo_externo(organismo_nombre,organismo_id,funcionario_id,funcionario_nombre,cargo_id,cargo_nombre){
            cadena = "<tr>";
            cadena += "<td><font class='f16b'>" + organismo_nombre + "</font><br/>";
            cadena += "<font class='f16n'>"+cargo_nombre+" / "+ funcionario_nombre + "</font>";
            cadena += "<input class='val_externo_repeat' name='correspondencia[receptor_externo][copias][]' type='hidden' value='" + organismo_id + "#" + funcionario_id + "#"+cargo_id+"'/>" + "</td>";
            cadena += "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
            comp = organismo_id + "#" + funcionario_id + "#"+cargo_id;
            if(cantidadExterna > 0){
                agregar = 0;
                $("#grilla_receptor_externo tbody tr td input[name='correspondencia[receptor_externo][copias][]']").each(function (){
                    if($(this).val() == comp){ agregar = 1; }
                });
                if(agregar == 0)
                {
                    $("#grilla_receptor_externo tbody").append(cadena);
                    fn_dar_eliminar_externo();
                    fn_cantidad_externo();
                    agregar = 0;
                }
            }
            else
            {
                $("#grilla_receptor_externo tbody").append(cadena);
                fn_dar_eliminar_externo();
                fn_cantidad_externo();
            }

    }


    function fn_guardar_grupo_externo(click)
    {
        if(click == 1)
        {
            $("#guardarGrupoExterno").slideToggle();
            $("#guardarGrupoExterno").remove();
            agregar = '<td id="guardarGrupoExterno"><input type="text" name="nombreGrupoExterno" /><input type="button" onclick="fn_guardar_grupo_externo(2);" value="Guardar" /></td>';
            $("#grilla_receptor_externo > tbody > tr:first").append(agregar);
        }
        if(click == 2)
        {
            var d = 0;
            var grupoId = '';
            var nombreGrupo;

            $("#grilla_receptor_externo tbody tr td input[name='correspondencia[receptor_externo][copias][]']").each(function (){

                if(d==0){grupoId = $(this).val(); d = 1;}
                else{grupoId = grupoId+","+$(this).val();}

            });
            nombreGrupo = $("input[name='nombreGrupoExterno']").val();
            $.ajax(
            {
                url: '<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/guardarGrupo',
                type:'POST',
                dataType:'html',
                data:'grupoId='+grupoId+'&nombreGrupo='+nombreGrupo+'&tipo=E',
                success:function(data, textStatus) {
                    $("#guardarGrupoExterno").slideToggle();
                    $("#guardarGrupoExterno").remove();
                }
            });
        }
        else
        {
            if((($("#grupoReceptorExterno").val() != "") && (modificado == 1)) || ($("#grupoReceptorExterno").val() == ""))
            {
                if($("#guardarGrupoExterno").length <= 0){
                    agregar = '<td id="guardarGrupoExterno"><input type="button" onclick="fn_guardar_grupo_externo(1);" value="Guardar grupo" /></td>';
                    $("#grilla_receptor_externo > tbody > tr:first").append(agregar);
                }
            }
        }
    }

    function agregarOtro(field) {
        if(field != '0' && !$('#correspondencia_correspondencia_n_correspondencia_externa').length)
            $('#div_grilla_receptor_externo').show();
    }
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_emisor_externo_resumen">
    <?php if(count($grupoReceptoresExternos)>0) { ?>
    <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_grupo_receptor">
        <div>
            <label for="correspondencia[receptor_externo][grupo]">Grupos</label>
            <div class="content">
                <select id="grupoReceptorExterno">
                    <option value=""><- Seleccione -></option>
                    <?php foreach($grupoReceptoresExternos as $grupoReceptor) { ?>
                    <option value="<?php echo $grupoReceptor->getGrupoId(); ?>"><?php echo $grupoReceptor->getNombre(); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <?php } ?>
    <div>
        <br/><br/>
        <label for="organismo_emisor">Organismo</label>

        <div class="content" style="position: relative;">
                <?php echo image_tag('icon/find.png', array('onClick' => 'javascript: toggleFinder()', 'style'=> 'float: left; cursor: pointer')); ?>
                <input style="left: 25px; display: none;" type="text" id="organismo_nombre" size="50" onKeyUp="keyupFunc()"/>
                <a href="javascript: organismo_button_open_func()" id="organismo_button_open" style="display: none;"><?php echo image_tag('icon/new.png', array('style'=> 'vertical-align: middle')).' Agregar nuevo organismo'; ?></a>
                <div class="div_organismo_info" id="div_organismo_info">
                    <div>
                        <input type="text" value="<- SIGLAS ->" id="organismo_siglas" onFocus="javascript: clear_siglas()" size="10"/>
                        <div class="helpy">Indique las siglas del organismo. Ej.: MCTI</div>
                    </div>
                    <div>
                        <select id="organismo_tipo">
                            <option value=""><- Tipo de Oganismo -></option>
                            <?php foreach ($organismos_tipo as $organismo_tipo) { ?>
                                <option value="<?php echo $organismo_tipo->getId(); ?>"><?php echo $organismo_tipo->getNombre(); ?></option>
                            <?php } ?>
                        </select>
                        <div class="helpy">Indique tipo de organismo que ser&aacute; agregado</div>
                    </div>
                    <div style="padding-left: 320px; cursor: pointer;" id="div_organismo_button_save" onClick="verificarOrganismo()"><?php echo image_tag('icon/filesave.png', array('style'=> 'vertical-align: middle')).' Guardar'; ?></div>
                </div>
        </div>

        <br/>
        <div class="content" style="position: relative;">
            <div id="organismos" style="position: relative;">
                <select id="select_organismo" name="<?php echo $organismo_name; ?>" onChange="listarPersonas();">
                    <option value="0"><- Seleccione -></option>
                    <?php foreach ($organismos as $organismo_id => $organismo_nombre) { ?>
                        <option <?php echo (($cor_externa_organismo == $organismo_id)? 'selected' : '') ?> value="<?php echo $organismo_id; ?>"><?php echo $organismo_nombre; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>


<div class="sf_admin_form_row sf_admin_text">
    <br/>
    <div>
        <label for="Persona">Persona</label>
        <div class="content" id="lista_personas" style="position: relative;">
            <select name="<?php echo $persona_name; ?>"><option value="0"></option></select>
        </div>
    </div>
</div>


<div class="sf_admin_form_row sf_admin_text">
    <br/>
    <div>
        <label for="Cargo">Cargo</label>
        <div class="content" id="lista_cargos" style="position: relative;">
            <select name="<?php echo $cargo_name; ?>"><option value="0"></option></select>
        </div>
        <div id="div_grilla_receptor_externo" class="content" style="display: none">
            <div class='partial_new_view partial'>
                <a href="javascript: fn_agregar_externo()" >Guardar y agregar otro</a>
            </div>
        </div>
        <div class="content">
            <br/>
            <table id="grilla_receptor_externo" class="lista">
                <tbody>
                    <?php
                    if(isset($copias)) {
                        if(count($copias) > 0) {
                            foreach($copias as $key => $value) {
                                $parts_ext= explode('#', $value);
                                $organismo = Doctrine::getTable('Organismos_Organismo')->find($parts_ext[0]);
                                $persona = Doctrine::getTable('Organismos_Persona')->find($parts_ext[1]);
                                $persona_cargo = Doctrine::getTable('Organismos_PersonaCargo')->find($parts_ext[2]);

                                $cadena = "<tr>";
                                $cadena .= "<td><font class='f16b'>" . $organismo->getNombre()." - ".$organismo->getSiglas() . "</font><br/>";
                                $cadena .= "<font class='f16n'>" . $persona_cargo->getNombre() . " / " . $persona->getNombreSimple() . "</font>";
                                $cadena .= "<input class='val_externo_repeat' name='correspondencia[receptor_externo][copias][]' type='hidden' value='" . $parts_ext[0] . "#" . $parts_ext[1].  '#' . $parts_ext[2] . "'/></td>";
                                $cadena .= "<td><a class='elimina' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td></tr>";
                                echo $cadena;
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="val_receptor_externo_repetido" />
        </div>
    </div>
</div>