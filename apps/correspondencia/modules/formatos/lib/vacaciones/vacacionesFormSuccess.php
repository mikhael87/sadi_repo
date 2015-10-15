<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/vacaciones/assets.php'); ?>
        
<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<?php 

$vacaciones_dias_solicitados= (isset($formulario['vacaciones_dias_solicitados'])) ? $formulario['vacaciones_dias_solicitados'] : NULL;

//    $vacaciones_activas = Doctrine::getTable('Rrhh_VacacionesDisfrutadas')->findByDql("status IN ('I','E','P')");
    
    $vacaciones_activas = Doctrine_Query::create()
                            ->select('vd.*')
                            ->from('Rrhh_VacacionesDisfrutadas vd')
                            ->innerJoin('vd.Rrhh_Vacaciones v')
                            ->where('v.funcionario_id = ?', $sf_user->getAttribute('funcionario_id'))
                            ->andWhereIN('vd.status',array('I','E','P'))
                            ->execute(); 

    if($vacaciones_activas[0]->getId() && !$vacaciones_dias_solicitados){
        if($vacaciones_activas[0]->getStatus()=='I')
            $mensaje = 'Actualmente tienes una solicitud de vacaciones en espera por la firma de tu supervisor y maxima autoridad de tu unidad.
                        Por lo tanto no podras solicitar una nueva, las alternativas serian anular la solicitud o editarla.';
        else if ($vacaciones_activas[0]->getStatus()=='E')
            $mensaje = 'Actualmente tienes una solicitud de vacaciones enviada a Recursos Humanos en espera de aprobación.
                        Por lo tanto no podras solicitar una nueva, las alternativas serian esperar a que sean aprobadas y solicitar una nueva
                        a partir de la fecha de retorno de la anterior o solicitar que devuelvan la correspondencia para editarla.';
        else
            $mensaje = 'Actualmente tienes una vacacion pausada temporalmente, no podras solicitar nuevas vacaciones hasta que las reactives o culmines.';
?>
<script>    
    
    $(document).ready(function(){
        $("form").submit(function() {
            alert('Actualmente tienes una solicitud de vacaciones.');
            return false;
        });
    });
    
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_observacion" style="background-color: red; color: white;">
    <div>
        <label for=""></label>
        <div class="content">
            <?php echo $mensaje; ?>
        </div>
    </div>
</div>


<?php } else { 

    $dias_disponibles = Doctrine_Query::create()
                        ->select('SUM(v.dias_disfrute_pendientes) as dias_disponibles')
                        ->from('Rrhh_Vacaciones v')
                        ->where('v.funcionario_id = ?', $sf_user->getAttribute('funcionario_id'))
                        ->andWhere('v.dias_disfrute_pendientes > 0')
                        ->execute();
    
    if($dias_disponibles[0]->getDiasDisponibles()==0){ 
        $mensaje = 'Actualmente NO tienes dias vacacionales disponibles. Debes esperar hasta el cumplimiento de tu año laboral para poder disfrutar de este beneficio';    
    ?>

    <script>    

        $(document).ready(function(){
            $("form").submit(function() {
                alert('Actualmente NO tienes dias vacacionales disponibles.');
                return false;
            });
        });

    </script>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_observacion" style="background-color: red; color: white;">
        <div>
            <label for=""></label>
            <div class="content">
                <?php echo $mensaje; ?>
            </div>
        </div>
    </div>

    <?php  } else { ?>
    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_dias_solicitados">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'vacaciones_dias_solicitados')) ?>

        <div>
            <label for="vacaciones_dias_solicitados">Nº de dias</label>
            <div class="content">
                <?php             
                    $condicion_trabajador = Doctrine::getTable('Organigrama_Cargo')->find($sf_user->getAttribute('funcionario_cargo_id'));

                    $configuraciones_vacaciones = Doctrine_Query::create()
                                                ->select('ca.*')
                                                ->from('Rrhh_Configuraciones ca')
                                                ->where('ca.modulo = ?', 'vacaciones')
                                                ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion_trabajador->getCargoCondicionId().']%')
                                                ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                                ->execute();

                    $parametros_vacaciones = sfYaml::load($configuraciones_vacaciones[0]->getParametros());
                    $dias_minimos = $parametros_vacaciones['dias_disfrute_minimo'];

                    $dias_disponibles = $dias_disponibles[0]->getDiasDisponibles();

                    $cadena = '<select name="correspondencia[formato][vacaciones_dias_solicitados]" id="vacaciones_dias_solicitados" onChange="calcular_periodo(); calcular_retorno();">';

                    $dias_maximo = $parametros_vacaciones['dias_disfrute_maximo'];
                    if($dias_maximo > $dias_disponibles) $dias_maximo = $dias_disponibles;
                    if($dias_minimos > $dias_disponibles) $dias_minimos = $dias_disponibles;

                    for($i=$dias_minimos;$i<=$dias_maximo;$i++){
                        $disabled_dia = "";
                        if($i!=$dias_maximo){
                            if($dias_maximo-$i < $dias_minimos){
                                $disabled_dia = "disabled";
                            }
                        }

                        $cadena .= '<option ' . (($vacaciones_dias_solicitados == $i) ? "selected" : "") . ' value="'.$i.'" '.$disabled_dia.'>'.$i.'</option>';
                    }

                    $cadena .= '</select>';

                    echo $cadena;
                    echo "&nbsp;&nbsp;Dias totales disponibles ".$dias_maximo;
                ?>        
                <div class='partial_find_reload partial'>
                </div>
            </div>
        </div>

        <div class="help">
            Cantidad de dias que solicita.<br>
            <font class="rojo">
                Recuerde, NO podra solicitar ni dejar pendientes menos de <?php echo $parametros_vacaciones['dias_disfrute_minimo']; ?> dias.
            </font>
        </div>

    </div>



    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_dias_solicitados">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'vacaciones_periodos_vacacional')) ?>

        <div>
            <label for="vacaciones_periodos_vacacional">Periodos Vacacionales</label>

            <div class="content">
                <?php                 
                    $periodos_vacacionales = Doctrine_Query::create()
                                            ->select('v.*')
                                            ->from('Rrhh_Vacaciones v')
                                            ->where('v.funcionario_id = ?', $sf_user->getAttribute('funcionario_id'))
                                            ->andWhere('v.dias_disfrute_pendientes > 0')
                                            ->orderBy('v.periodo_vacacional')
                                            ->execute();

                    $dias_periodo = array();
                    $dias_usados = array();
                    $dias_restantes = array();
                    $count_dias = 1;
                    $hasta_dia = 0;
                    $periodo_vacacional_anterior=0;

                    echo '<table>';
                        echo '<tr>';
                            echo '<th>Periodos</th>';
                            echo '<th>Dias pendientes</th>';
                            echo '<th>Dias calculados</th>';
                            echo '<th>Dias restantes</th>';
                        echo '</tr>';

                    foreach ($periodos_vacacionales as $periodo_vacacional) {

                        echo '<tr>';
                            echo '<td>'.$periodo_vacacional->getPeriodoVacacional().'</td>';
                            echo '<td style="text-align: center;" id="pendientes_'.$periodo_vacacional->getId().'">'.$periodo_vacacional->getDiasDisfrutePendientes().'</td>';
                            echo '<td style="text-align: center;" id="usados_'.$periodo_vacacional->getId().'">0</td>';
                            echo '<td style="text-align: center;" id="restantes_'.$periodo_vacacional->getId().'">'.$periodo_vacacional->getDiasDisfrutePendientes().'</td>';
                        echo '</tr>';


                        $hasta_dia += $periodo_vacacional->getDiasDisfrutePendientes();
                        $restantes = $periodo_vacacional->getDiasDisfrutePendientes()-1;

                        $dias_contados=1;

                        while($dias_contados<=$periodo_vacacional->getDiasDisfrutePendientes()){
                            $dias_periodo[$count_dias] = $periodo_vacacional->getId();
                            $dias_usados[$count_dias] = $dias_contados;
                            $dias_restantes[$count_dias] = $restantes;
                            $predecesores[$count_dias] = $periodo_vacacional_anterior;
                            $count_dias++;
                            $dias_contados++;
                            $restantes--;
                        }       

                        $periodo_vacacional_anterior = $periodo_vacacional->getId();
                    }
                    echo '</table>';
                ?>

            </div>

            <script>
                <?php 
                    echo 'var dias_periodo = new Array();';
                    foreach ($dias_periodo as $key => $value) {
                        echo 'dias_periodo['.$key.'] = '.$value.';';
                    }

                    echo 'var dias_usados = new Array();';
                    foreach ($dias_usados as $key => $value) {
                        echo 'dias_usados['.$key.'] = '.$value.';';
                    }

                    echo 'var dias_restantes = new Array();';
                    foreach ($dias_restantes as $key => $value) {
                        echo 'dias_restantes['.$key.'] = '.$value.';';
                    }

                    echo 'var predecesores = new Array();';
                    foreach ($predecesores as $key => $value) {
                        echo 'predecesores['.$key.'] = '.$value.';';
                    }
                ?>

                    var dias_maximo = <?php echo $dias_maximo; ?>;
                    function calcular_periodo(){


                        for(i=1;i<=dias_maximo;i++){
                            $('#usados_'+dias_periodo[i]).html('0');
                            $('#restantes_'+dias_periodo[i]).html($('#pendientes_'+dias_periodo[i]).html());
                        }

                        for(i=1;i<=$('#vacaciones_dias_solicitados').val();i++){
                            $('#usados_'+dias_periodo[i]).html(dias_usados[i]);
                            $('#restantes_'+dias_periodo[i]).html(dias_restantes[i]);
                        }
                    }

                    calcular_periodo();                
            </script>

        </div>

        <div class="help">Dias que se utilizaran por periodo vacacional dependiendo de la cantidad de dias que solicite.</div>
    </div>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_f_inicio">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'vacaciones_f_inicio')) ?>

        <div>
            <label for="vacaciones_f_inicio">Fecha de Inicio</label>
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

    <?php

        $vacaciones_activas = Doctrine_Query::create()
                                ->select('vd.*')
                                ->from('Rrhh_VacacionesDisfrutadas vd')
                                ->innerJoin('vd.Rrhh_Vacaciones v')
                                ->where('v.funcionario_id = ?', $sf_user->getAttribute('funcionario_id'))
                                ->andWhereIN('vd.status',array('A'))
                                ->execute();                 

        if($vacaciones_activas[0]->getId()){
            $vacaciones_retorno = '1950-01-01';
            foreach ($vacaciones_activas as $vacacion_activa) {
                if(strtotime($vacacion_activa->getFRetornoDisfrute()) > strtotime($vacaciones_retorno)){
                    $vacaciones_retorno = $vacacion_activa->getFRetornoDisfrute();
                }
            }

            $retorno_y = date('Y', strtotime($vacaciones_retorno));
            $retorno_m = date('m', strtotime($vacaciones_retorno));
            $retorno_d = date('d', strtotime($vacaciones_retorno));

            $manana =$vacaciones_retorno;
            ?>
                dia_inicio = new Date(<?php echo $retorno_y; ?>, <?php echo $retorno_m; ?> - 1, <?php echo $retorno_d; ?>);
            <?php
        } else { 
            $manana_y = date('Y', strtotime('+1 day ' . date('Y-m-d')));
            $manana_m = date('m', strtotime('+1 day ' . date('Y-m-d')));
            $manana_d = date('d', strtotime('+1 day ' . date('Y-m-d')));

            $manana =date('Y-m-d', strtotime('+1 day ' . date('Y-m-d')));
            ?>
                dia_inicio = new Date(<?php echo $manana_y; ?>, <?php echo $manana_m; ?> - 1, <?php echo $manana_d; ?>);
            <?php
        }

    ?>
      </script>  
                <?php 
                    $years = range(date('Y'), date('Y')+1);


    //                
    //                $years = array_unique(array(date('Y'), date('Y', strtotime('+40 days'))));
    //                $months = array_unique(array(date('m'), date('m', strtotime('first day of next month')), date('m', strtotime('+40 days'))));
    //                $days = range(date('d'), 31);
    //                

                    $w = new sfWidgetFormJQueryDate(array(
                    'image' => '/images/icon/calendar.png',
                    'culture' => 'es',
                        'config' => "{changeYear: true, yearRange: 'c-100:c+100', minDate: dia_inicio, beforeShowDay: noWeekendsOrHolidays, firstDay: 1}",
    //                    'config' => "{minDate: 1, maxDate: '+1M +1D', beforeShowDay: $.datepicker.noWeekends}",sfWidgetFormInputText()
                    'date_widget' => new sfWidgetFormI18nDate(array(
                                    'format' => '%day%-%month%-%year%',
                                    'culture'=>'es',
                                    'empty_values' => array('day'=>'<- Día ->',
                                    'month'=>'<- Mes ->',
                                    'year'=>'<- Año ->'),
                                    'years' => array_combine($years, $years),
    //                                'months' => array_combine($months, $months),
    //                                'days' => array_combine($days, $days)))
                                    ))
                    ),array('name'=>'correspondencia[formato][vacaciones_f_inicio]','value'=>$manana, 'onchange'=>'calcular_periodo()'));

                    if(isset($formulario['vacaciones_f_inicio']))
                        echo $w->render('vacaciones_f_inicio',$formulario['vacaciones_f_inicio']);
                    else
                        echo $w->render('vacaciones_f_inicio',$manana);
                ?>

                <script>
                    $('#vacaciones_f_inicio_day').attr('disabled','disabled');
                    $('#vacaciones_f_inicio_month').attr('disabled','disabled');
                    $('#vacaciones_f_inicio_year').attr('disabled','disabled');
                    $('#vacaciones_f_inicio_jquery_control').removeAttr('disabled');
                    <?php 
                        if(isset($formulario['vacaciones_f_inicio'])){
                            echo "$('#vacaciones_f_inicio_jquery_control').val('".$formulario['vacaciones_f_inicio']."');";
                        }
                    ?>

                    function calcular_retorno()
                    {
                        var f_i = $('#vacaciones_f_inicio_jquery_control').val();
                        var d = $('#vacaciones_dias_solicitados').val();
                        var forma = $('#formato_tipo_formato_id').val();

                        <?php
                        echo jq_remote_function(array('update' => 'regreso',
                        'url' => 'formatos/librerias',
                        'with'=> "'forma='+forma+'&func=CalculoRegreso&var[f_i]='+f_i+'&var[d]='+d",));
                        ?>
                    }  
                </script>
            </div>
        </div>

        <div class="help">Seleccione la fecha de inicio de las vacaciones.</div>

    </div>


    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_f_final">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'vacaciones_f_final')) ?>

        <div>
            <label for="vacaciones_f_final">Fecha de Retorno</label>
            <div class="content">
                <div class='partial_find_reload partial'>
                <a href="#" onclick="javascript:calcular_retorno(); return false;">Calcular Retorno</a>
                </div>
                <div id="regreso"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?></div>
            </div>
        </div>

        <div class="help"></div><script>calcular_retorno();</script>
    </div>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_vacaciones_observacion">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'vacaciones_observacion')) ?>

        <div>
            <label for="vacaciones_observacion">Observación</label>
            <div class="content" style="width: 650px;">
                <textarea rows="4" cols="30" name="correspondencia[formato][vacaciones_observacion]" id="vacaciones_observacion"><?php if(isset($formulario['vacaciones_observacion'])) echo $formulario['vacaciones_observacion']; ?></textarea>
            </div>
        </div>

        <div class="help">Agregue observaciones de ser necesario.</div>

    </div>

<?php } } ?>

</fieldset>