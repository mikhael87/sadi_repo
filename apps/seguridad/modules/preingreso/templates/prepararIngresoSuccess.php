<script type="text/javascript" src="/picture/htdocs/webcam.js"></script>
<script type="text/javascript" src="/js/camera.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/css/camera.css" />

<script language="JavaScript">
    $('#webcam').html(webcam.get_html(250, 300));

    function do_upload() {
        // save image
        var error =  false;
        if($('#seguridad_ingreso_llave_ingreso_id').val()==''){
            $("#error_pase").show();
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
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/registrarIngreso',
                data: $('#form_visita_preingresada').serialize(),
                success:function(data, textStatus){

                    <?php if($visitantes_restantes>1){ ?>

                        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando proximo preingresado...');

                        $.ajax({
                            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/prepararIngreso',
                            type:'POST',
                            dataType:'html',
                            data: {preingreso_id: <?php echo $sf_user->getAttribute('preparacion_preingreso_id'); ?>},
                            success:function(data, textStatus){
                                $('#content_window_right').html(data)
                            }});
                    <?php } else { ?>
                        close_window_right_update_father();
                    <?php } ?>

                }
            })
        }
        else{
            alert("PHP Error: " + msg);
            $("#button_guardar").show();
        }

    }

    function saltar_visitante(persona_id) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/saltarVisitante',
                data: {persona_id: <?php echo $visitante['persona_id']; ?>},
                success:function(data, textStatus){

                    <?php if($visitantes_restantes>1){ ?>

                        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando proximo preingresado...');

                        $.ajax({
                            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/prepararIngreso',
                            type:'POST',
                            dataType:'html',
                            data: {preingreso_id: <?php echo $sf_user->getAttribute('preparacion_preingreso_id'); ?>},
                            success:function(data, textStatus){
                                $('#content_window_right').html(data)
                            }});
                    <?php } else { ?>
                        close_window_right_update_father();
                    <?php } ?>

                }
            })
    }

    function mostrarEquipos() {
        $("#form_equipo").toggle(200);
        $("#form_equipo1").toggle(200);
        $("#div_equipos_anteriores").toggle(200);
    }

    function equipos_persona(persona_id) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/equiposDePersona',
                data: {persona_id: persona_id},
                success:function(data, textStatus){
                    $('#div_equipos_anteriores').html(data);
                }
            })
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
<form id="form_visita_preingresada" method="POST">
<fieldset id="sf_fieldset_visitante">
<h2>Visitante</h2>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Visitante</label>
        <div class="content">


<?php if(isset($alerta_visitante)){ ?>
    <?php if($alerta_visitante){ ?>
    <div style="position: relative; background-color: tomato; color: white;">
        <div style="padding: 15px;"><img src="/images/icon/error48.png"/></div>
        <div style="position: absolute; left: 80px; top: 0px; padding: 5px;">
            <font class="f25b">Visitante en Alerta</font><br/>
            Motivo: <?php echo $alerta_visitante->getDescripcion(); ?>
        </div>
    </div>
    <br/>
<?php }} ?>

<?php if(isset($visitante['f_nacimiento'])){ ?>
    <?php
        if(date("m-d", strtotime($visitante['f_nacimiento'])) == date('m-d')){
            $mensaje_feliz = 'Hoy cumple <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +1 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Mañana cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +2 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Pasado mañana cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +3 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'En 3 dias cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +4 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'En 4 dias cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -1 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Ayer cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -2 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Antes de ayer cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -3 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Hace 3 dias cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -4 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Hace 4 dias cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else {
            $mensaje_feliz = '';
        }

        if($mensaje_feliz != ''){
    ?>
    <div style="position: relative; background-color: #A7D5EC;">
        <div style="padding: 15px;"><img src="/images/other/feliz_cumpleanos_torta.png" width="48" height="48"/></div>
        <div style="position: absolute; right: 0px; top: 0px; padding: 5px;">
            <img src="/images/other/feliz_cumpleanos_bombas.png" height="60"/>
        </div>
        <div style="position: absolute; left: 80px; top: 0px; padding: 5px;">
            <font class="f25b gris_oscuro">Desea un feliz cumpleaños a nuestro visitante</font><br/><br/>
            <?php echo $mensaje_feliz; ?>
        </div>
    </div>
    <br/>
<?php }} ?>


<div style="position: relative;">
    <?php
        if($visitante['persona_id']){ ?>
            <input type="hidden" value="<?php echo $visitante['ingreso_id']; ?>" name="seguridad_ingreso[ingreso_id]"/>
            <font class="f16n gris_medio">C.I.:</font> <?php echo $visitante['nacionalidad'].'-'.$visitante['cedula']; ?> /
            <font class="f16n gris_medio">Nombre</font> <?php echo $visitante['primer_nombre'].' '.$visitante['primer_apellido']; ?><br/>
            <font class="f16n gris_medio">F. Nac</font> <?php echo date('d-m-Y', strtotime($visitante['f_nacimiento'])); ?> /
            <font class="f16n gris_medio">Edad</font> <?php echo $visitante['edad']; ?> años<br/><br/>
            <div style="position: relative;">
                <font class="f16n gris_medio">Telefono</font>
                <div style="position: absolute; top: 0px; left: 110px;">
                    <?php echo '<input type="text" name="seguridad_persona[telefono]" value="'.$visitante['telefono'].'"/>'; ?><br/>
                </div>
                <div class="f09n">&nbsp;</div>
                <font class="f16n gris_medio">Correo electronico</font>
                <div style="position: absolute; top: 20px; left: 110px;">
                    <?php echo '<input type="text" name="seguridad_persona[correo_electronico]" value="'.$visitante['correo_electronico'].'"/>'; ?>
                </div>
            </div>
            <?php if(count($ingresos)>0){ ?>
            <div style="position: absolute; width: 397px; padding-left: 5px; top: -7px; left: 297px; background-color: #CCCCFF"><b>Ingresos anteriores</b></div>
            <div style="position: absolute; top: 10px; left: 297px; height: 85px; width: 400px; border: solid 1px; overflow-y: auto; overflow-x: none;">
                <table style="width: 385px;">
                <?php
                foreach ($ingresos as $ingreso) {
                    $color = '';
                    if($ingreso->getFEgreso()=='') $color = 'background-color: orangered;'
                ?>
                    <tr style="<?php echo $color; ?>">
                        <td>
                            <img src="/uploads/seguridad/<?php echo $ingreso->getImagen(); ?>" width="50"/>
                        </td>
                        <td class="f11n">
                            <?php
                            echo '<b>F. Ingreso: </b>'.date('d-m-Y h:i A', strtotime($ingreso->getFIngreso())).' / ';
                            echo '<b>F. Egreso: </b>';
                            if($ingreso->getFEgreso()=='')
                                echo '<b style="color: red; background-color: white;">&nbsp;&nbsp;SIN REGISTRO&nbsp;&nbsp;</b><br/>';
                            else
                                echo date('d-m-Y h:i A', strtotime($ingreso->getFEgreso())).'<br/>';

                            echo '<b>Motivo: </b>'.$ingreso->getMotivoClasificado().'<br/><br/>';
                            //echo $ingreso->getMotivoVisita();
                            echo '<b>Unidad: </b>'.$ingreso->getUnidad().'<br/>';
                            echo '<b>Funcionario: </b>'.$ingreso->getFuncionarioPrimerNombre().' '.$ingreso->getFuncionarioPrimerApellido();
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table>
            </div>
            <?php } ?>
            <script>equipos_persona(<?php echo $visitante['persona_id']; ?>);</script>
    <?php } ?>



</div>





        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text" id="fotografia">
    <div>
        <label>Fotografia</label>
        <div class="content">
            <input id="seguridad_ingreso_imagen" type="hidden" value="" name="seguridad_ingreso[imagen]"/>
            <div id="camera_content">
                <div id="camera">
                    <br />
                    <p style="text-align: center;" id="webcam">
                        <script></script>
                    </p>
                    <div id="camera_imagen"></div>
                </div>

                <div id="btn_camara">
                    <div id="cargando" style="display:none"></div>
                    <button id="btn_capturar" type="button" class="btn primary-btn" onClick="do_freeze()" title="Capturar"/>
                    <button id="btn_reset" style="display:none"  type="button" class="btn primary-btn" onClick="do_reset()" title="Reiniciar">
                </div>

                <div id="upload_results" style="background-color:#eee;"></div>
            </div>

        </div>
    </div>
</div>
</fieldset>

<fieldset id="sf_fieldset_estancia">
<h2>Estancia</h2>
<?php $llaves_ingreso = Doctrine::getTable('Seguridad_LlaveIngreso')->todasOrdenadas(); ?>

<div class="sf_admin_form_row sf_admin_text">
    <div id="error_pase" class="error" style="display: none;">Seleccione el pase de entrada.</div>
    <div>
        <label for="">N° de pase</label>
        <div class="content" id="llave_ingreso_id">
        

            <select id="seguridad_ingreso_llave_ingreso_id" name="seguridad_ingreso[llave_ingreso_id]">
                <option value="" ></option>
                <?php foreach ($llaves_ingreso as $llave_ingreso) { ?>
                <option value="<?php echo $llave_ingreso->getId(); ?>" <?php echo (($llave_ingreso->getStatus()=='O')? 'disabled="disabled"' : ''); ?>><?php echo $llave_ingreso->getNPase().(($llave_ingreso->getStatus()=='O')? ' - Ocupado' : ''); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
</fieldset>


<fieldset id="sf_fieldset_equipos_personales">
<h2>Equipos Personales | <a onclick="mostrarEquipos()" style="cursor: pointer;">Agregar Equipos</a></h2>

<?php $form_equipo = new Seguridad_EquipoForm(); ?>
<div id="div_equipos_anteriores" style="display:none"></div>
<div id="form_equipo" class="sf_admin_form_row sf_admin_text" style="display:none">
    <div>
        <label>Tipo y Marca del Equipo</label>
        <div class="content">
            <?php echo $form_equipo['tipo_id']->renderError() ?>
            <?php echo $form_equipo['tipo_id'] ?>
            <a class="agregar" onclick='$("#add_Tipo").toggle(200); $("img#busy_Tipo").hide();'></a>
            &nbsp;&nbsp;
            <?php echo $form_equipo['marca_id']->renderError() ?>
            <?php echo $form_equipo['marca_id'] ?>
            <a class="agregar" onclick='$("#add_Marca").toggle(200); $("img#busy_Marca").hide();'></a>

            <div id="add_Tipo" style="display: none; width: 260px;">
                <br />
                <input type="text" name="new_tipo" id ="new_tipo">
                <input type="button" name="send_tipo" id="send_tipo" value="Enviar" onclick="EnviarInfo('new_tipo', 'seguridad_equipo_tipo_id', 'Tipo')">
            </div>

            <div id="add_Marca" style="display: none; width: 260px;">
                <br />
                <input type="text" name="new_marca" id ="new_marca">
                <input type="button" name="send_Marca" id="send_marca" value="Enviar" onclick="EnviarInfo('new_marca', 'seguridad_equipo_marca_id', 'Marca')">
            </div>
        </div>
    </div>
</div>
<div id="form_equipo1" class="sf_admin_form_row sf_admin_text" style="display:none">
    <div>
        <?php echo $form_equipo['serial']->renderLabel() ?>
        <div class="content" id="serial">
        <?php echo $form_equipo['serial'] ?>
        <br/>
        <a href="#" onclick="agregar_equipo(); return false;" id="button_otro_equipo">Agregar otro</a>
        <br/><br/>
        <div>
            <table id="div_otros_equipos" style="display: none;">
                <tr><th style="min-width: 100px;">Tipo</th><th style="min-width: 100px;">Marca</th><th style="min-width: 150px;">Serial</th><th></th></tr>
            </table>
        </div>
        </div>
    </div>
</div>

</fieldset>

<ul class="sf_admin_actions">
    <li class="sf_admin_action_save">
        <?php if($visitantes_restantes>1){ ?>
            <input id="button_guardar"type="button" onclick="do_upload(); return false;" value="Guardar y buscar proximo"/>
            <input id="button_guardar"type="button" onclick="saltar_visitante(); return false;" value="Saltar y buscar proximo"/>
        <?php } else { ?>
            <input id="button_guardar"type="button" onclick="do_upload(); return false;" value="Guardar y finalizar"/>
            <input id="button_guardar"type="button" onclick="saltar_visitante(); return false;" value="Saltar y finalizar"/>
        <?php } ?>
    </li>
</ul>

</form>
