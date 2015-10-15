<?php use_helper('jQuery'); ?>
<script>
    var asignacion_cargo = false;
    function activar_asignacion(activacion) {
        if(activacion == 'true'){
            asignacion_cargo = true;
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/activarAsignacionCargo',
                type:'POST',
                dataType:'html',
                beforeSend: function(Obj){
                    $('#div_asignacion_cargo').html('<img src="/images/icon/cargando.gif"/>&nbsp;<font style="color: #666; font-size: 12px">Preparando asignación de cargo...</font>');
                },
                success:function(data, textStatus){
                    $('#div_asignacion_cargo').html(data);
                }});
        } else {
            asignacion_cargo = false;
            $('#div_asignacion_cargo').html('');
        }
    }
</script>

<div class="sf_admin_form_row">
    <div>
        <label>Asignación</label>
        <div class="content">
            <input type="radio" name="activar_cargo" value="true" onchange="activar_asignacion('true');"/> Activar&nbsp;&nbsp;&nbsp;
            <input type="radio" name="activar_cargo" value="false" onchange="activar_asignacion('false');" checked="checked" id="activar_cargo_false"/> Inactivar
        </div>
    </div>
</div>

<div id="div_asignacion_cargo"></div>


