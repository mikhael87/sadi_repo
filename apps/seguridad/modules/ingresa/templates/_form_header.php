<script>
    $("#sf_fieldset_equipos_personales > h2").append(' | <a style="cursor: pointer;" onClick="mostrarEquipos()">Agregar Equipos</a>');
    $("#sf_fieldset_destino_y_motivo > h2").append(' | <a style="cursor: pointer;" onClick="mostrarOficina()">Oficina</a> | <a style="cursor: pointer;" onClick="mostrarFuncionario()">Funcionario</a>');    
    
    function mostrarEquipos() {
        $("#form_equipo").toggle(200);
        $("#form_equipo1").toggle(200);
        $("#div_equipos_anteriores").toggle(200);
    }
    
    function mostrarOficina() {
        $("#find_funcionario").hide(200);
        $("#piso").show(200);
        $("#unidad").show(200);
        $("#funcionario").show(200);
        $("#seguridad_ingreso_list_funcionario").val('');
        $("#autocomplete_seguridad_ingreso_list_funcionario").val('');
    }
    
    function mostrarFuncionario() {
        $("#find_funcionario").show(200);
        $("#piso").hide(200);
        $("#unidad").hide(200);
        $("#funcionario").hide(200);
    }
    
    function solo_num(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 13) {
            EnviarInfo('persona_cedula');
        }
        if ((tecla >= 35 && tecla <= 37) || tecla == 8 || tecla == 9 || tecla == 46 || tecla == 39) {
            return true;
        }
        if ((tecla >= 95 && tecla <= 105) || (tecla >= 48 && tecla <= 57)) {
            return true;
        } else {
            return false;
        }
    }
    
    function buscar_cedula() {
        if ($('#persona_cedula').val() != '') {
            $('#div_equipos_anteriores').html('');
            $('#div_datos_persona').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Buscando...');
        
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/visitanteForm',
                data: {cedula: $('#persona_cedula').val()},
                success:function(data, textStatus){
                    $('#div_datos_persona').html(data);
                    $('#persona_cedula').val('');
                }
            })
        }
    }
    
    function filtrar_pisos(){        
         $("#unidad_recibe option").hide();  
         $("#unidad_recibe option[class=piso_" + $('#unidad_pisos_id').val() + "]").show();  
         
         $("#unidad_recibe option[value='']").attr("selected", "selected");
         $("#funcionario_recibe option[value='']").attr("selected", "selected");
         $("#funcionario_recibe option[class=funcionario_id_class]").hide();  
    };
    
    function registrar_ingreso() {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/registrarIngreso',
                data: $('#form_nueva_visita').serialize(),
                success:function(data, textStatus){
                    //close_window_right();
                }
            })
    }
    
    function equipos_persona(persona_id) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/equiposDePersona',
                data: {persona_id: persona_id},
                success:function(data, textStatus){
                    $('#div_equipos_anteriores').html(data);
                }
            })
    }
</script>


<script type="text/javascript" src="/picture/htdocs/webcam.js"></script>
<script type="text/javascript" src="/js/camera.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/css/camera.css" />   

<script language="JavaScript">
    $('#webcam').html(webcam.get_html(250, 300));
    
    function do_upload() {
        // save image 
        var error =  false;
        $("#error_pase").hide();
        $("#error_pase_usado").hide();
        $("#unidad_recibe").hide();
        
        if($('#seguridad_ingreso_llave_ingreso_id').val()==''){
            $("#error_pase").show();
            error = true;
        } else {
            var paseSeleccionado = $('#seguridad_ingreso_llave_ingreso_id').val();
        
            if(paseSeleccionado.indexOf('-O') >= 0){
                $("#error_pase_usado").show();
                error = true;
            }
        }

        if ($('#unidad_recibe').val()==''){
            $("#error_unidad").show();
            error = true;
        } 

        if(error == false) {
            $("#button_guardar").hide();
            webcam.upload();
        }
    }
    
    function my_completion_handler(msg) {
        // extract URL out of PHP output
        if (msg.match(/(http\:\/\/\S+)/)) {
            var image_url = RegExp.$1; //obteniendo url de la imagen
            var image = image_url.split('/') //obteniendo solo el nombre de la imagen
            $("#seguridad_ingreso_imagen").val(image[9]);
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/registrarIngreso',
                data: $('#form_nueva_visita').serialize(),
                success:function(data, textStatus){
                    close_window_right_update_father();
                }
            })
        }
        else{
            alert("PHP Error: " + msg);
            $("#button_guardar").show();
        }

    }
</script>

<script type="text/javascript">
    function LimpiarCombo(combo) {
        while (combo.length > 0) {
            combo.remove(combo.length - 1);
        }
    }
    function LlenarCombo(json, combo, texto, module) {
        combo.options[0] = new Option('<- Seleccione ' + module + '->', '');
        for (var i = 0; i < json.length; i++) {
            //verificamos el contenido del combo y del text 
            //para q quede seleccionado el combo
            if (json[i].a_descripcion.toUpperCase() === texto.value.toUpperCase())
                combo.options[combo.length] = new Option(json[i].a_descripcion, json[i].a_id, 'selected="selected"')
            else
                combo.options[combo.length] = new Option(json[i].a_descripcion, json[i].a_id)
        }
        texto.value = '';
    }
    function EnviarInfo(texto, combo, module) {
        combo = document.getElementById(combo);
        texto = document.getElementById(texto);
        LimpiarCombo(combo);
        if (texto.value != '') {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url') ?>ingresa/ajaxAddItem'+module,
                data: {valor: texto.value},
                beforeSend: function() {
                    combo.options[0] = new Option('Cargando...', '');
                    $("#add_" + module).hide();
                    $("img#busy_" + module).show();
                },
                complete: function() {
                    $("img#busy_" + module).hide();
                },
                success: function(json) {
                    LlenarCombo(json, combo, texto, module);
                },
                error: function(Obj, err) {
                    alert('No se pudo incluir el registro!');
                }
            })
        }
    }
</script>

<style type="text/css">
    a.agregar {
        background: transparent url(/images/icon/new.png) no-repeat scroll left center;
        padding: 2px 4px 1px 20px;
        font-family: "Trebuchet MS", helvetica, sans-serif;
        font-style: normal;
        font-size: 14px;
        cursor: pointer;
    }
</style>