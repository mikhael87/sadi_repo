<?php use_helper('jQuery'); ?>

<script>
$(function() {
//$( "#fecha_inicial" ).datepicker({maxDate: "M D"});
//$( "#fecha_final" ).datepicker({maxDate: "M D"});
//$( "#fecha_inicial" ).datepicker("option", "dateFormat", 'yy-mm-dd');
//$( "#fecha_final" ).datepicker("option", "dateFormat", 'yy-mm-dd');

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
        if($("#estadistica_unidad_id").val() != ''){
        $('#form_config').slideUp();
        $('#button_otras').show();
        $('#estadistica_unidad_id').removeAttr('style');
            $('#div_estadisticas_correspondencia').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Generando grafico ...');
                <?php
                echo jq_remote_function(array('update' => 'div_estadisticas_correspondencia',
                'url' => 'recibida/estadisticaSeleccionada',
                'with'     => "'unidad_id='+$('#estadistica_unidad_id').val()+'&tipo='+$('#estadistica_tipo').val()+'&fi='+$('#fecha_inicial').val()+'&ff='+$('#fecha_final').val()"))
                ?>
        }
        else{ $('#estadistica_unidad_id').css('outline','red solid 1px'); }
    }
    function abrir_config(){
        $('#button_otras').hide();
        $('#form_config').slideDown();
    }
</script>


<div id="sf_admin_container">
<h1>Estadisticas de Correspondencia Recibida</h1>
    <div id="sf_admin_header">

            <li class="sf_admin_action_back">
                <a href="<?php echo sfConfig::get('sf_app_correspondencia_url').'recibida'; ?>">Regresar</a>
            </li>
            
            <li class="sf_admin_action_back" id="button_otras" style="display: none;">
                <a href="#" onclick="abrir_config();">Otras estadisticas</a>
            </li>

    </div>

    <div id="sf_admin_content">
        <div id="form_config" class="sf_admin_form trans">
            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_estadisticas_unidad_id">
                <div>
                    <label for="correspondencia_estadisticas_unidad_id">Unidad</label>
                    <div class="content">
                        <select name="estadistica_unidad_id" id="estadistica_unidad_id">

                            <?php 
                            if(count($funcionario_unidades)>1) { ?>
                                <option value=""></option>
                            <?php } ?>
                            <?php foreach( $funcionario_unidades as $unidades ) { 
                                $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidades);
                                ?>
                                <option value="<?php echo $unidades; ?>">
                                    <?php echo $unidad->getNombre(); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

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
                            <option value="totalStatusRecibida">Total General</option>
                            <option value="totalStatusRecibidaDeOficinas">Total de oficinas</option>
                            <option value="totalStatusRecibidaDeOrganismos">Total de externas</option>
                            <option value="totalRecibidaPorDias">Historico de cantidades</option>
                        </select>
                        <input type="button" value="Procesar" onclick="abrir_estadistica();"/>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="div_estadisticas_correspondencia" class="trans"></div>
        
    </div>
</div>
