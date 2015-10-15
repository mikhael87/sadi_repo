<?php use_helper('jQuery'); ?>

<script>
$(function() {

    $('#fecha_inicial').datepicker({
      autoSize: true, 
      constrainInput: true, 
      dateFormat: 'yy-mm-dd', 
      maxDate: 'M D', 
      onSelect: function(dateText, inst) { 
        $('#fecha_final').datepicker("option", "minDate",dateText); 
      } 
    });
    $('#fecha_final').datepicker({
      autoSize: true, 
      constrainInput: true, 
      dateFormat: 'yy-mm-dd', 
      maxDate: 'M D', 
      onSelect: function(dateText, inst) { 
        $('#fecha_inicial').datepicker("option", "maxDate",dateText); 
      } 
    });
});
    function abrir_estadistica() {
        $('#form_config').slideUp();
        $('#button_otras').show();
        $('#estadistica_unidad_id').removeAttr('style');
            $('#div_estadisticas_vehiculos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Generando grafico ...');
                <?php
                echo jq_remote_function(array('update' => 'div_estadisticas_vehiculos',
                'url' => 'vehiculo/estadisticaSeleccionada',
                'with'     => "'tipo='+$('#estadistica_tipo').val()+'&fi='+$('#fecha_inicial').val()+'&ff='+$('#fecha_final').val()"))
                ?>
    }
    function abrir_config(){
        $('#button_otras').hide();
        $('#form_config').slideDown();
    }
</script>


<div id="sf_admin_container">
    <h1>Estad&iacute;sticas de Veh&iacute;culos</h1>
    <div id="sf_admin_header">

            <li class="sf_admin_action_regresar_modulo">
                <a href="<?php echo sfConfig::get('sf_app_vehiculos_url').'vehiculo'; ?>">Regresar</a>
            </li>
            
            <li class="sf_admin_action_estadisticas" id="button_otras" style="display: none;">
                <a href="#" onclick="abrir_config();">Otras estadisticas</a>
            </li>

    </div>

    <div id="sf_admin_content">
        <div id="form_config" class="sf_admin_form trans">

            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_estadisticas_fechas">
                <div>
                    <label for="correspondencia_estadisticas_fechas">Fecha</label>
                    <div class="content">
                        Desde: <input name="fecha_inicial" type="text" id="fecha_inicial" /> 
                        Hasta: <input name="fecha_final" type="text" id="fecha_final" />
                    </div>
                </div>
            </div>

            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_estadisticas_fechas">
                <div>
                    <label for="correspondencia_estadisticas_fechas"></label>
                    <div class="content">
                        <select name="estadistica_tipo" id="estadistica_tipo">
                            <option value="servicios">Total General</option>
                            <option value="usoGps">Uso Gps</option>
                        </select>
                        <input type="button" value="Procesar" onclick="abrir_estadistica();"/>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="div_estadisticas_vehiculos" class="trans"></div>
        
    </div>
</div>
