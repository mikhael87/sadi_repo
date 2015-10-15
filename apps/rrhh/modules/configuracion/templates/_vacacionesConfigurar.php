<form method="post" action="<?php echo sfConfig::get('sf_app_rrhh_url').'configuracion/saveVacaciones'; ?>"> 


<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Condición de trabajador</label>
        <div class="content">
        <?php 
            $condiciones_trabajador = Doctrine_Query::create()
                                    ->select('cc.*')
                                    ->from('Organigrama_CargoCondicion cc')
                                    ->where('cc.status = ?', 'A')
                                    ->orderBy('cc.nombre')
                                    ->execute();

            foreach ($condiciones_trabajador as $condicion_trabajador) { ?>
            <input type="checkbox" value="<?php echo $condicion_trabajador->getId(); ?>" name="datos[condicion][]" checked="checked"/><?php echo $condicion_trabajador->getNombre(); ?><br/>
            <?php } ?>
        </div>
    </div>
    <div class="help">Seleccione el tipo de condición de los trabajadores a los que se aplicara esta configuración.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Dias asignados</label>
        <div class="content">
            <select id="dias_asignados_anio" name="datos[dias_asignados_anio]">
            <?php for($i=1; $i<100; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>    
        </div>
    </div>
    <div class="help">Seleccione la cantidad de dias que le corresponden a cada trabajador por año laboral cumplido.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Dias adicionales</label>
        <div class="content">
            <select id="dias_adicionales_anio" name="datos[dias_adicionales_anio]">
            <?php for($i=0; $i<=15; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select> 

            Beneficio despues del año:
            <select id="dias_adicionales_anio_inicio" name="datos[dias_adicionales_anio_inicio]">
            <?php for($i=1; $i<=30; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>  

            Beneficio hasta el año:
            <select id="dias_adicionales_anio_inicio" name="datos[dias_adicionales_anio_final]">
            <?php for($i=1; $i<=45; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>  
        </div>
    </div>
    <div class="help">Seleccione la cantidad de dias adicionales que se asignaran a cada trabajador por año laboral cumplido y la cantidad de años para disfrutar de este beneficio.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Dias de disfrute minimo</label>
        <div class="content">
            <select id="dias_disfrute_minimo" name="datos[dias_disfrute_minimo]">
            <?php for($i=1; $i<30; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>
        </div>
    </div>
    <div class="help">Seleccione la cantidad minima de dias de vacaciones que puede solicitar un trabajador.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Dias de disfrute maximo</label>
        <div class="content">
            <select id="dias_disfrute_maximo" name="datos[dias_disfrute_maximo]">
            <?php for($i=1; $i<601; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>
        </div>
    </div>
    <div class="help">Seleccione la cantidad maxima de dias de vacaciones que puede solicitar un trabajador.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Alerta periodos pendientes</label>
        <div class="content">
            <select id="alerta_periodos_pendientes" name="datos[alerta_periodos_pendientes]">
                <option value="diaria">diaria</option>
                <option value="mensual">mensual</option>
                <option value="trimestral">trimestral</option>
                <option value="semestral">semestral</option>
                <option value="anual">anual</option>
            </select>                
        </div>
    </div>
    <div class="help">Seleccione la frecuencia que desea se notifique al trabajador, al supervisor y a la unidad de RRHH que existe disponible uno o varios periodos o dias vacacionales disponibles.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Alerta dias aplazados</label>
        <div class="content">
            <select id="alerta_dias_aplazados" name="datos[alerta_dias_aplazados]">
                <option value="1">1</option>
            <?php for($i=5; $i<=60; $i+=5) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php } ?>
            </select>
        </div>
    </div>
    <div class="help">Seleccione la frecuencia en dias que desea se notifique al trabajador, al supervisor y a la unidad de RRHH que se deben retomar unas vacaiones aplazadas temporalmente.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Forma de pago</label>
        <div class="content">
            <input type="radio" name="datos[forma_abono_concepto]" value="anio" checked="checked"/> Al cumplir año de servicio<br/>
            <input type="radio" name="datos[forma_abono_concepto]" value="solicitud"/> Al solicitar las vacaciones
        </div>
    </div>
    <div class="help">Seleccione la forma de pago o abono del concepto vacacional para emitir el reporte que servirá para cargar en el "Sistema de Nomina" utilizado.</div>
</div>


<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label for="">Fecha inicial de configuración</label>
        <div class="content">

        <script type="text/javascript">

           <?php
               $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
               $dias_no_laborables = $sf_varios['dias_no_laborables'];

               $array_no_laboral = "natDays = [";

               foreach ($dias_no_laborables as $dia_no_laboral => $descripcion_dias) {
                   list($anio,$mes,$dia) = explode ("-",$dia_no_laboral);
                   list($descripcion_dia,$tmp) = explode("#", $descripcion_dias);
                   $array_no_laboral .= "[".$mes.",".$dia.",'".$descripcion_dia."'],";
               }
               $array_no_laboral .= "[0,0,'']];";

               echo $array_no_laboral;
           ?>  

           function nationalDays(date) {
               for (i = 0; i < natDays.length; i++) {
                 if (date.getMonth() == natDays[i][0] - 1
                     && date.getDate() == natDays[i][1]) {
                   return [false, '',natDays[i][2]];
                 }
               }
             return [true, ''];
           }

           function noWeekendsOrHolidays(date) {
               var noWeekend = $.datepicker.noWeekends(date);
               if (noWeekend[0]) {
                   return nationalDays(date);
               } else {
                   return noWeekend;
               }
           }
         </script>  

        <?php 
            $years = range(date('Y')-50, date('Y')+2);
            $f_disponibilidad = date('Y-m-d', strtotime('+1 day ' . date('Y-m-d')));

            $w = new sfWidgetFormJQueryDate(array(
            'image' => '/images/icon/calendar.png',
            'culture' => 'es',
            'config' => "{changeYear: true, yearRange: 'c-100:c+100', beforeShowDay: noWeekendsOrHolidays, firstDay: 1}",
            'date_widget' => new sfWidgetFormI18nDate(array(
                            'format' => '%day%-%month%-%year%',
                            'culture'=>'es',
                            'empty_values' => array('day'=>'<- Día ->',
                            'month'=>'<- Mes ->',
                            'year'=>'<- Año ->'),
                            'years' => array_combine($years, $years),
                            ))
            ),array('name'=>'datos[f_inicial_configuracion]','value'=>$f_disponibilidad));


            echo $w->render('datos[f_inicial_configuracion]', strtotime('+1 day ' . date('Y-m-d')));
        ?>            
            <script>
                $('#datos_f_inicial_configuracion_day').attr('disabled','disabled');
                $('#datos_f_inicial_configuracion_month').attr('disabled','disabled');
                $('#datos_f_inicial_configuracion_year').attr('disabled','disabled');
                $("#datos_f_inicial_configuracion_jquery_control").removeAttr('disabled');
            </script>
        </div>
    </div>
    <div class="help">Seleccione la fecha desde la cual se comenzara a aplicar esta configuración.</div>
</div>

<div id="div_form_seteado"></div>

<ul class="sf_admin_actions">
    <li class="sf_admin_action_save"><input value="Guardar" type="submit"/></li> 
</ul>

</form>    