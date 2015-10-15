<?php use_helper('jQuery'); 
$unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>
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
        if($("#estadistica_unidad_id").val() != ''){
        $('#form_config').slideUp();
        $('#button_otras').show();
        $('#estadistica_unidad_id').removeAttr('style');
            $('#div_estadisticas_inventario').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Generando grafico ...');
                <?php
                echo jq_remote_function(array('update' => 'div_estadisticas_inventario',
                'url' => 'articulo/estadisticaSeleccionada',
                'with'     => "'articulo='+$('#select_articulos').val()+'&tipo='+$('#estadistica_tipo').val()"))
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
<h1>Estadisticas de Correspondencia Enviada</h1>
    <div id="sf_admin_header">

            <li class="sf_admin_action_back">
                <a href="<?php echo sfConfig::get('sf_app_inventario_url').'articulo'; ?>">Regresar</a>
            </li>
            
            <li class="sf_admin_action_back" id="button_otras" style="display: none;">
                <a href="#" onclick="abrir_config();">Otras estadisticas</a>
            </li>

    </div>

    <div id="sf_admin_content">
        <div id="form_config" class="sf_admin_form trans">
                <?php  $articulos = Doctrine::getTable('Inventario_Articulo')->articulosActivosOrden('nombre'); ?>
            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_articulos_estadisticas_articulo_id">
                <div>
                    <label for="articulos_estadisticas_articulo_id">Articulo</label>
                    <div class="content">
                        <select id="select_articulos">
                            <option value="0"><- Seleccione -></option>
                            <?php foreach ($articulos as $articulo) { ?>
                                <option value="<?php echo $articulo->getId(); ?>"><?php echo $articulo->getCodigo().' - '.$articulo->getNombre().' - '.$articulo->getUnidadMedida(); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

<!--            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_estadisticas_fechas">
                <div>
                    <label for="correspondencia_estadisticas_fechas">Fecha</label>
                    <div class="content">
                        Desde: <input name="fecha_inicial" type="text" id="fecha_inicial" /> 
                        Hasta: <input name="fecha_final" type="text" id="fecha_final" />
                    </div>
                </div>
            </div>-->

            <div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_estadisticas_fechas">
                <div>
                    <label for="correspondencia_estadisticas_fechas"></label>
                    <div class="content">
                        <select name="estadistica_tipo" id="estadistica_tipo">
                            <option value="linearIngresosEgresos">Linear de ingresos y egresos</option>
                            <!--<option value="oficina">Por oficina</option>-->
                        </select>
                        <input type="button" value="Procesar" onclick="abrir_estadistica();"/>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="div_estadisticas_inventario" class="trans"></div>
        
    </div>
</div>
