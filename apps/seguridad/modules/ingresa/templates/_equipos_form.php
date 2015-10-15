<script>
    function agregar_equipo(){
        if($("#seguridad_equipo_tipo_id").val() && $("#seguridad_equipo_marca_id").val() && $("#seguridad_equipo_serial").val()){
            $("#div_otros_equipos").append('<tr><td><input name="seguridad_equipo[otros][]" type="hidden" value="'+$("#seguridad_equipo_tipo_id").val()+'###'+$("#seguridad_equipo_marca_id").val()+'###'+$.trim($("#seguridad_equipo_serial").val())+'"/>'+$("#seguridad_equipo_tipo_id option:selected").text()+'</td><td>'+$("#seguridad_equipo_marca_id option:selected").text()+'</td><td>'+jQuery.trim($("#seguridad_equipo_serial").val())+'</td><td><a class="elimina" style="cursor: pointer;"><img src="/images/icon/delete.png"/></a></td></tr>');
            $("#div_otros_equipos").show();
            
            $("#seguridad_equipo_tipo_id option[value='']").attr("selected", "selected");
            $("#seguridad_equipo_marca_id option[value='']").attr("selected", "selected");
            $("#seguridad_equipo_serial").val('');
            
            eliminar_equipo();
        } else {
            alert('Debe seleccionar un tipo una marca y escribir el serial del equipo para agregar otro.');
        }
    }
    
    function eliminar_equipo(){
        $("a.elimina").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };
    
    function eliminar_anterior(equipo_id){
        $("#tr_equipo_anterior_"+equipo_id).show();
        $("#tr_equipo_anterior_agregado_"+equipo_id).remove();
    };
    
    function agregar_equipo_anterior(equipo_id,marca,tipo,serial){
        $("#div_otros_equipos").append('<tr id="tr_equipo_anterior_agregado_'+equipo_id+'"><td><input name="seguridad_equipo[otros_anteriores][]" type="hidden" value="'+equipo_id+'"/>'+tipo+'</td><td>'+marca+'</td><td>'+serial+'</td><td><a onclick="eliminar_anterior('+equipo_id+');" style="cursor: pointer;"><img src="/images/icon/delete.png"/></a></td></tr>');
        $("#tr_equipo_anterior_"+equipo_id).hide();
        $("#div_otros_equipos").show();
    }
</script>

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