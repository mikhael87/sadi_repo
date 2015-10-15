<?php use_helper('jQuery'); ?>
<?php $semilla = time(); ?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>


<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'revision_punto_cuenta_decision')) ?>

    <div>
        <label for="revision_punto_cuenta_decision">Decision</label>
        <div class="content" style="width: 650px;">
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_decision]" checked="checked" value="Aprobado"/>&nbsp;Aprobado&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_decision]" value="Negado"/>&nbsp;Negado&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_decision]" value="Visto"/>&nbsp;Visto&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_decision]" value="Diferido"/>&nbsp;Diferido&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_decision]" value="Otro"/>&nbsp;Otro
        </div>
    </div>

    <div class="help"></div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">

    <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'revision_punto_cuenta_observaciones')) ?>

    <div>
        <label for="revision_punto_cuenta_observaciones">Observaciones Maxima Autoridad</label>
        <div class="content" style="width: 650px;">
            <textarea rows="4" cols="30" name="correspondencia[formato][revision_punto_cuenta_observaciones]" id="revision_punto_cuenta_observaciones_<?php echo $semilla; ?>"><?php if(isset($formulario['revision_punto_cuenta_observaciones'])) echo $formulario['revision_punto_cuenta_observaciones']; ?></textarea>
        </div>
    </div>

    <div class="help">Observaciones emitidas por la maxima autoridad de la institucion al revisar el Punto de Cuenta.</div>

</div>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_punto_cuenta">
    <script>
        function toogle_publicacion(){
            $('#punto_cuenta_publicacion_form').toggle();
            $('#punto_cuenta_publicacion_help').toggle();
        }
        
        function fecha_public_toggle() {
            if($('#public_a_partir_de').is(':checked'))
                $('#div_public_fecha').show();
            else
                $('#div_public_fecha').hide();
        }
    </script>
    
    <div>
        <label for="revision_punto_cuenta_publicacion">Tratamiento de información</label>
        <div class="content" style="width: 650px;">
            ¿Publicar la información de este Punto de Cuenta?<br/>
            
            <?php
            $medios_ar= Array();
            if(isset($formulario['revision_punto_cuenta_medios'])) {
                if($formulario['revision_punto_cuenta_medios'] != '') {
                    $medios= explode('#', $formulario['revision_punto_cuenta_medios']);
                    foreach($medios as $value) {
                        if($value != '')
                            $medios_ar[]= $value;
                    }
                }
            }
            
                $checked_si = '';
                $checked_no = 'checked';        
                $display_partida = 'none';
                
                if(count($medios_ar) > 0){
                    $checked_si = 'checked';
                    $checked_no = '';

                    $display_partida = 'block';
                }
            ?>
            
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_publicacion]" value="S" onclick="toogle_publicacion();" <?php echo $checked_si; ?>/> Si&nbsp;&nbsp;&nbsp;
            <input type="radio" name="correspondencia[formato][revision_punto_cuenta_publicacion]" value="N" onclick="toogle_publicacion();" <?php echo $checked_no; ?>/> No
            <br/><br/>
            <div id="punto_cuenta_publicacion_form" style="position: relative; display: <?php echo $display_partida; ?>;">
                Medios de publicaci&oacute;n:&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="correspondencia[formato][revision_punto_cuenta_web]" value="web" <?php echo ((in_array('web', $medios_ar))? 'checked' : ''); ?>> Portal Web&nbsp;&nbsp;
                <input type="checkbox" name="correspondencia[formato][revision_punto_cuenta_intranet]" value="intranet" <?php echo ((in_array('intranet', $medios_ar))? 'checked' : ''); ?>> Intranet&nbsp;&nbsp;
                <input type="checkbox" name="correspondencia[formato][revision_punto_cuenta_radio]" value="radio" <?php echo ((in_array('radio', $medios_ar))? 'checked' : ''); ?>> Radio&nbsp;&nbsp;
                <input type="checkbox" name="correspondencia[formato][revision_punto_cuenta_tv]" value="tv" <?php echo ((in_array('tv', $medios_ar))? 'checked' : ''); ?>> T.V.&nbsp;&nbsp;
                <input type="checkbox" name="correspondencia[formato][revision_punto_cuenta_twitter]" value="twitter" <?php echo ((in_array('twitter', $medios_ar))? 'checked' : ''); ?>> Twitter
                <br/><br/>
                Fecha de publicaci&oacute;n:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" id="public_inmediato" name="correspondencia[formato][revision_punto_cuenta_tiempo]" value="I" onChange="javascript: fecha_public_toggle()" <?php echo ((isset($formulario['revision_punto_cuenta_fecha']))? (($formulario['revision_punto_cuenta_fecha']== 'inmediato')? 'checked' : '') : '') ?>/>&nbsp;Inmediato&nbsp;&nbsp;
                <input type="radio" id="public_a_partir_de" name="correspondencia[formato][revision_punto_cuenta_tiempo]" value="F" onChange="javascript: fecha_public_toggle()" <?php echo ((isset($formulario['revision_punto_cuenta_fecha']))? (($formulario['revision_punto_cuenta_fecha']!= 'inmediato')? 'checked' : '') : '') ?>/>&nbsp;A partir de:&nbsp;&nbsp;
                
                
                <div id="div_public_fecha" style="display: <?php echo (($formulario['revision_punto_cuenta_fecha']!= 'inmediato')? 'block' : 'none') ?>; position: absolute; top: 30px; left: 330px">
                    <?php
                    $anio_inicio = date('Y');
                    $anio_final = date('Y')+15;

                    $years = range($anio_inicio, $anio_final);
                    
                    $fecha= (isset($formulario['revision_punto_cuenta_fecha'])? $formulario['revision_punto_cuenta_fecha'] : '');
                    
                    $w = new sfWidgetFormJQueryDate(array(
                    'image' => '/images/icon/calendar.png',
                    'culture' => 'es',
                    'config' => '{changeYear: true, yearRange: \'c-100:c+100\'}',
                    'date_widget' => new sfWidgetFormI18nDate(array(
                                    'format' => '%day%-%month%-%year%',
                                    'culture'=>'es',
                                    'empty_values' => array('day'=>'<- Día ->',
                                        'month'=>'<- Mes ->',
                                        'year'=>'<- Año ->'),
                                    'years' => array_combine($years, $years)))
                    ),array('name'=>'correspondencia[formato][revision_punto_cuenta_fecha]',));

                    echo $w->render('correspondencia[formato][revision_punto_cuenta_fecha]', $fecha);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
        CKEDITOR.config.scayt_autoStartup = true;
        CKEDITOR.config.scayt_sLang ="es_ES";
        CKEDITOR.replace('revision_punto_cuenta_observaciones<?php echo $semilla; ?>');
</script>

</fieldset>

