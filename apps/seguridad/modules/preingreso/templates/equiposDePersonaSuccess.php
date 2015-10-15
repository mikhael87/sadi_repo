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

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Equipos anteriormente ingresados</label>
        <div class="content">
            <table>
                <tr>
                    <th style="min-width: 100px;">Tipo</th>
                    <th style="min-width: 100px;">Marca</th>
                    <th style="min-width: 150px;">Serial</th>
                    <th></th>
                </tr>
                <?php foreach ($equipos as $equipo) { ?>
                    <tr id="tr_equipo_anterior_<?php echo $equipo->getId(); ?>">
                        <td><?php echo $equipo->getMarca(); ?></td>
                        <td><?php echo $equipo->getTipo(); ?></td>
                        <td><?php echo $equipo->getSerial(); ?></td>
                        <td><a onclick="agregar_equipo_anterior(<?php echo $equipo->getId(); ?>,'<?php echo $equipo->getMarca(); ?>','<?php echo $equipo->getTipo(); ?>','<?php echo $equipo->getSerial(); ?>');" style="cursor: pointer;"><img src="/images/icon/tick.png"/></a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>