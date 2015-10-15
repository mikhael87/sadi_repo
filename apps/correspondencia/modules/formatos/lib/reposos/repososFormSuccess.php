<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/reposos/assets.php'); ?>
   
<?php
    if(!$sf_user->getAttribute('tercerizado')) {
        $session_funcionario_cargo_id = $sf_user->getAttribute('funcionario_cargo_id');
        $session_funcionario_id = $sf_user->getAttribute('funcionario_id');
    } else {
        $tercerizado = $sf_user->getAttribute('tercerizado');
        $session_funcionario_cargo_id = $tercerizado['cargo_id'];
        $session_funcionario_id = $tercerizado['funcionario_id'];
    }
?>

<fieldset id="sf_fieldset_contenido">
<h2>Contenido</h2>

<?php 

//    $reposos_activos = Doctrine_Query::create()
//                            ->select('p.*')
//                            ->from('Rrhh_Reposos p')
//                            ->where('p.funcionario_id = ?', $session_funcionario_id)
//                            ->andWhereIN('p.status',array('I','A'))
//                            ->execute(); 
//
//    if($reposos_activos[0]->getId()){
//        if($reposos_activos[0]->getStatus()=='I')
//            $mensaje = 'Actualmente tienes una solicitud de reposo en espera por la firma de tu supervisor y maxima autoridad de tu unidad.
//                        Por lo tanto no podras solicitar uno nuevo, las alternativas serian anular la solicitud o editarla.';
//        else 
//            $mensaje = 'Actualmente tienes una solicitud de reposo aprobada en espera de ejecutarse.
//                        Por lo tanto no podras solicitar uno nuevo.';
//?>
<script>    
    
//    $(document).ready(function(){
//        $("form").submit(function() {
//            alert('Actualmente tienes una solicitud de reposo.');
//            return false;
//        });
//        
//    });
    
</script>

<!--<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_observacion" style="background-color: red; color: white;">
    <div>
        <label for=""></label>
        <div class="content">
            //<?php echo $mensaje; ?>
        </div>
    </div>
</div>-->


<?php // } else { ?>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_tipo_reposo">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'reposos_tipo_reposo')) ?>

        <div>
            <label for="reposos_tipo_reposo">Tipo de reposo</label>
            <div class="content">
                <?php             
                    $condicion_trabajador = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);

                    $configuraciones_reposos = Doctrine_Query::create()
                                                ->select('ca.*')
                                                ->from('Rrhh_Configuraciones ca')
                                                ->where('ca.modulo = ?', 'reposos')
                                                ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion_trabajador->getCargoCondicionId().']%')
                                                ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                                ->execute();

                    $parametros_reposos = sfYaml::load($configuraciones_reposos[0]->getParametros());
                    $tipos_reposo = $parametros_reposos['tipos_reposo'];

                    $cadena = '<select name="reposos_tipo_reposo" id="reposos_tipo_reposo" onChange="cambiar_dias_maximo();">';
                    
                    $cadena .= '<option value=""></option>';
                    foreach ($tipos_reposo as $tipo_reposo) {
                        $cadena .= '<option ' . ((isset($formulario['reposos_tipo_reposo'])) ? (($formulario['reposos_tipo_reposo'] == $tipo_reposo['nombre']) ? 'selected' : '') : '') . ' value="'.$tipo_reposo['dias_min'].'-'.$tipo_reposo['dias_max'].'-'.$tipo_reposo['clasificacion'].'">'.$tipo_reposo['nombre'].'</option>';
                    }
                    
                    $cadena .= '</select>';

                    echo $cadena;
                ?>        
                <input type="hidden" id="input_tipo_reposo" name="correspondencia[formato][reposos_tipo_reposo]" value=""/>
            </div>
        </div>

        <div class="help">
            Seleccione el tipo de reposo.
        </div>

    </div>

    <script>
        $(document).ready(function(){
                cambiar_dias_maximo();
        });
        
        function cambiarDiaSolicitado() {
            $('#dias_solicitados').val($('#reposos_dias_solicitados').val());
        }
        
        function cambiar_dias_maximo(){
            var rango_dias = $("#reposos_tipo_reposo").val();
            var rango = rango_dias.split('-');
            var dias_min= rango[0];
            var dias_max= rango[1];
            var clasif= rango[2];
            var clas;
            if(clasif == 'D')
                clas= ' día(s)';
            else
                clas= ' semana(s)';

            if($("#reposos_tipo_reposo").val()) {
                if(parseInt(dias_min) < parseInt(dias_max)) {
                    cadena = "<select id='reposos_dias_solicitados' onChange='cambiarDiaSolicitado()'>";
                    for(i=parseInt(dias_min);i<=parseInt(dias_max);i++){
                        cadena += "<option value='"+i+"'>"+i+"</option>";
                    }
                    cadena += "</select>";
                    $('#dias_solicitados').val(dias_min);
                    $('#clasificacion').val(clasif);
                    $('#help_dias').html('Este reposo tiene un l&iacute;mite de '+ dias_max + clas +'. Seleccione la cantidad que solicita.');
                }else {
                    if(parseInt(dias_min) == parseInt(dias_max)) {
                        cadena= "<font style='font-size: 20px; font-weight: bolder;'>"+ ((clasif== "D") ? dias_max : parseInt(dias_max)/ 5) +"</font>"+clas;

                        $('#dias_solicitados').val(dias_min);
                        $('#clasificacion').val(clasif);
                        $('#help_dias').html('Este reposo esta establecido para '+ ((clasif== "D") ? dias_max : parseInt(dias_max)/ 5) +clas);
                    }else {
                        //error de configuracion de reposo
                    }
                }


                $("#div_dias_solicitados").html(cadena);
                $("#input_tipo_reposo").val($("#reposos_tipo_reposo option:selected").text());    
            }else {
                $('#div_dias_solicitados').html('');
                $('#help_dias').html('Seleccione un tipo de reposo para conocer cantidad de d&iacute;as o semanas disponibles.');
                $('#dias_solicitados').val('');
            }
            
        };
    </script>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_dias_solicitados">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'reposos_dias_solicitados')) ?>

        <div>
            <label for="reposos_dias_solicitados">Tiempo</label>
            <div class="content" id="div_dias_solicitados">       
<!--                <select id="reposos_dias_solicitados"></select>-->
            </div>
            
            <input type="hidden" id="dias_solicitados" name="correspondencia[formato][reposos_dias_solicitados]" value=""/>
            <input type="hidden" id="tipo_reposo" name="correspondencia[formato][tipo_reposo]" value="" />
            <input type="hidden" id="clasificacion" name="correspondencia[formato][clasificacion]" value="" />
        </div>

        <div class="help" id="help_dias">
            Seleccione un tipo de reposo para conocer cantidad de d&iacute;as o semanas disponibles.
        </div>

    </div>




    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_f_inicio">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'reposos_f_inicio')) ?>

        <div>
            <label for="reposos_f_inicio">Fecha de Inicio</label>
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

                       $vacaciones_activas = Doctrine_Query::create()
                                               ->select('vd.*')
                                               ->from('Rrhh_VacacionesDisfrutadas vd')
                                               ->innerJoin('vd.Rrhh_Vacaciones v')
                                               ->where('v.funcionario_id = ?', $session_funcionario_id)
                                               ->andWhereIN('vd.status',array('A'))
                                               ->execute();                 


                       foreach ($vacaciones_activas as $vacacion_activa) {

                           $dia_estudio = $vacacion_activa->getFInicioDisfrute();

                           while($dia_estudio < $vacacion_activa->getFRetornoDisfrute()){
                               list($anio,$mes,$dia) = explode ("-",$dia_estudio);
                               $array_no_laboral .= "[".$mes.",".$dia.",'En vacaciones'],";
                               $dia_estudio =date('Y-m-d', strtotime('+1 day ' . $dia_estudio));
                           }
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
                    $manana =date('Y-m-d', strtotime('+1 day ' . date('Y-m-d')));
                    $years = range(date('Y'), date('Y')+1);

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
                    ),array('name'=>'correspondencia[formato][reposos_f_inicio]','value'=>$manana, 'onchange'=>'calcular_retorno()'));

                    if(isset($formulario['reposos_f_inicio']))
                        echo $w->render('reposos_f_inicio',$formulario['reposos_f_inicio']);
                    else
                        echo $w->render('reposos_f_inicio',$manana);
                ?>

                <script>
                    $('#reposos_f_inicio_day').attr('disabled','disabled');
                    $('#reposos_f_inicio_month').attr('disabled','disabled');
                    $('#reposos_f_inicio_year').attr('disabled','disabled');
                    $('#reposos_f_inicio_jquery_control').removeAttr('disabled');
                    <?php 
                        if(isset($formulario['reposos_f_inicio'])){
                            echo "$('#reposos_f_inicio_jquery_control').val('".$formulario['reposos_f_inicio']."');";
                        }
                    ?>

                    function calcular_retorno()
                    {
                        if($('#dias_solicitados').val()){
                            $('#regreso').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Calculando retorno...');

                            var f_i = $('#reposos_f_inicio_jquery_control').val();
                            var d = $('#dias_solicitados').val();
                            var forma = $('#formato_tipo_formato_id').val();

                            <?php
                            echo jq_remote_function(array('update' => 'regreso',
                            'url' => 'formatos/librerias',
                            'with'=> "'forma='+forma+'&func=CalculoRegreso&var[f_i]='+f_i+'&var[d]='+d",));
                            ?>
                        } else {
                            $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Seleccione la cantidad de dias.');
                        }
                    }  
                </script>
            </div>
        </div>

        <div class="help">Seleccione la fecha de inicio del reposo.</div>

    </div>


    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_f_final">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'reposos_f_final')) ?>

        <div>
            <label for="reposos_f_final">Fecha de Retorno</label>
            <div class="content">
                <div class='partial_find_reload partial'>
                <a href="#" onclick="javascript:calcular_retorno(); return false;">Calcular Retorno</a>
                </div>
                <div id="regreso"></div>
            </div>
        </div>

        <div class="help"></div>
    </div>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_reposos_observacion">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'reposos_observacion')) ?>

        <div>
            <label for="reposos_observacion">Observación</label>
            <div class="content" style="width: 650px;">
                <textarea rows="4" cols="30" name="correspondencia[formato][reposos_observacion]" id="reposos_observacion"><?php if(isset($formulario['reposos_observacion'])) echo $formulario['reposos_observacion']; ?></textarea>
            </div>
        </div>

        <div class="help">Agregue observaciones de ser necesario.</div>

    </div>

<?php // } ?>
<input type="hidden" name="val_reposo" />
</fieldset>