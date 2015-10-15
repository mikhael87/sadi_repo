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
            $('#div_estadisticas_correspondencia').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Generando grafico ...');
                <?php
                echo jq_remote_function(array('update' => 'div_estadisticas_correspondencia',
                'url' => 'estadistica/estadisticaSeleccionada',
                'with'     => "'unidad_id='+$('#estadistica_unidad_id').val()+'&tipo='+$('#estadistica_tipo').val()+'&fi='+$('#fecha_inicial').val()+'&ff='+$('#fecha_final').val()"))
                ?>
    }
    function abrir_config(){
        $('#button_otras').hide();
        $('#form_config').slideDown();
    }
</script>


<div id="sf_admin_container">
<h1>Estad&iacute;sticas</h1>
    <div id="sf_admin_header">

            <li class="sf_admin_action_back">
                <a href="<?php echo sfConfig::get('sf_app_acceso_url').'usuario/session'; ?>">Regresar</a>
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
                            $ligas = Doctrine_Core::getTable('Operaciones_Liga')->findAll();
                            $i= 1;
                            foreach($ligas as $liga) {
                                ?>
                                <option value="<?php echo $liga->getId(); ?>" <?php echo ((count($ligas)== $i)? 'selected': ''); ?>>
                                    <?php echo $liga->getMes().'-'.$liga->getAno(); ?>
                                </option>
                                <?php
                                $i++;
                            }
                            ?>
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
                            <option value="recaudosParticipantes">Total de empresas registradas por estatus general de recaudos de participantes</option>
                            <option value="cantidadJuegosEmpresa">Cantidad de juegos ganados y perdidos por empresa</option>
                            <option value="cantidadJuegosEmpresaDisciplina">Cantidad de juegos ganados y perdidos por empresa y disciplinas</option>
<!--                            <option value="totalEnviadaPorDias">Historico de cantidades</option>
                            <option value="totalEnviadaPorCreador">Total por creador</option>-->
                        </select>
                        <input type="button" value="Procesar" onclick="abrir_estadistica();"/>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="div_estadisticas_correspondencia" class="trans"></div>
        
    </div>
</div>
