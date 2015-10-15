<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/apps/correspondencia/modules/formatos/lib/permisos/assets.php'); ?>

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
    $day_hour= date('H');
//    $permisos_dias_solicitados= (isset($formulario['permisos_dias_solicitados'])) ? $formulario['permisos_dias_solicitados'] : NULL;
//    $permisos_activos = Doctrine_Query::create()
//                            ->select('p.*')
//                            ->from('Rrhh_Permisos p')
//                            ->where('p.funcionario_id = ?', $session_funcionario_id)
//                            ->andWhereIN('p.status',array('I','A'))
//                            ->execute();

//    if($permisos_activos[0]->getId() && !$permisos_dias_solicitados){
//        if($permisos_activos[0]->getStatus()=='I')
//            $mensaje = 'Actualmente tienes una solicitud de permiso en espera por la firma de tu supervisor y maxima autoridad de tu unidad.
//                        Por lo tanto no podras solicitar uno nuevo, las alternativas serian anular la solicitud o editarla.';
//        else
//            $mensaje = 'Actualmente tienes una solicitud de permiso aprobada en espera de ejecutarse.
//                        Por lo tanto no podras solicitar uno nuevo.';
?>

<!--<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_observacion" style="background-color: red; color: white;">
    <div>
        <label for=""></label>
        <div class="content">
            <?php //echo $mensaje; ?>
        </div>
    </div>
</div>-->


<?php // } else { ?>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_tipo_permiso">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_tipo_permiso')) ?>
        
        <div>
            <label for="permisos_tipo_permiso">Tipo de permiso</label>
            <div class="content">
                <?php
                    $condicion_trabajador = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);

                    $configuraciones_permisos = Doctrine_Query::create()
                                                ->select('ca.*')
                                                ->from('Rrhh_Configuraciones ca')
                                                ->where('ca.modulo = ?', 'permisos')
                                                ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion_trabajador->getCargoCondicionId().']%')
                                                ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                                ->execute();

                    $parametros_permisos = sfYaml::load($configuraciones_permisos[0]->getParametros());
                    $tipos_permiso = $parametros_permisos['tipos_permiso'];

                    $cadena = '<select name="permisos_tipo_permiso" id="permisos_tipo_permiso" onChange="cambiar_dias_maximo();">';

                    $cadena .= '<option value=""></option>';
                    foreach ($tipos_permiso as $tipo_permiso) {
                        $cadena .= '<option ' . ((isset($formulario['permisos_tipo_permiso'])) ? (($formulario['permisos_tipo_permiso'] == $tipo_permiso['nombre']) ? 'selected' : '') : '') . ' value="'.$tipo_permiso['dias_min'].'-'.$tipo_permiso['dias_max'].'-'.$tipo_permiso['medio_dia'].'-'.$tipo_permiso['clasificacion'].'">'. $tipo_permiso['nombre'] .'</option>';
                    }

                    $cadena .= '</select>';

                    echo $cadena;
                ?>
                <input type="hidden" id="input_tipo_permiso" name="correspondencia[formato][permisos_tipo_permiso]" value=""/>
            </div>
        </div>

        <div class="help">
            Seleccione el tipo de permiso.
        </div>

    </div>

    <script>
        $(document).ready(function(){
                cambiar_dias_maximo();
        });
        
        function cambiaTurno(who) {
            if(who== 'inicio')
                $('#input_turno_inicio').val($('#select_turno_inicio').val());
            else
                $('#input_turno_retorno').val($('#select_turno_retorno').val());
        }

        function cambiarDiaSolicitado() {
            $('#dias_solicitados').val($('#permisos_dias_solicitados').val());
        }

        function cambiar_dias_maximo(){
            var parametros_dias = $("#permisos_tipo_permiso").val();
            var parametros_dias = parametros_dias.split('-');
            var dias_min = parseInt(parametros_dias[0]);
            var dias_max = parseInt(parametros_dias[1]);
            var medio_dia = parametros_dias[2];
            var clasif = parametros_dias[3];
            var clas;
            if(clasif == 'D')
                clas= ' día(s)';
            else
                clas= ' semana(s)';

            if($("#permisos_tipo_permiso").val()) {
                if(dias_min < dias_max) {
                    cadena = "<select id='permisos_dias_solicitados' onChange='cambiarDiaSolicitado()'>";
                    for(i=dias_min;i<=parseInt(dias_max);i++){
                        cadena += "<option value='"+i+"'>"+i+"</option>";
                    }
                    cadena += "</select>";

                    if(medio_dia== 'Si') {
                        $('#div_retorno').show();
                        $('#turno_inicio').show();
                        $('#turno_retorno').show();
                        $('#tipo_permiso').val(0);
                    }else {
                        $('#div_retorno').hide();
                        $('#turno_inicio').hide();
                        $('#turno_retorno').hide();
                        $('#tipo_permiso').val(1);
                    }

                    $('#dias_solicitados').val(dias_min);
                    $('#clasificacion').val(clasif);
                    $('#help_dias').html('Este permiso tiene un l&iacute;mite de '+ dias_max + clas +'. Seleccione la cantidad que solicita.');
                }else {
                    if(dias_min == dias_max) {
                        if(medio_dia== 'No') {
                            cadena= "<font style='font-size: 20px; font-weight: bolder;'>"+ ((clasif== "D") ? dias_max : parseInt(dias_max)/ 5) +"</font>"+clas;

                            $('#div_retorno').hide();
                            $('#turno_inicio').hide();
                            $('#turno_retorno').hide();
                            $('#dias_solicitados').val(dias_max);
                            $('#tipo_permiso').val(2);
                            $('#clasificacion').val(clasif);
                            $('#help_dias').html('Este permiso esta establecido para '+ ((clasif== "D") ? dias_max : parseInt(dias_max)/ 5) +clas);
                        }else {
                            if(dias_min == 1) {
                                cadena= "<font style='font-size: 20px; font-weight: bolder;'>"+ dias_max +"</font>"+clas;

                                $('#div_retorno').show();
                                $('#turno_inicio').show();
                                $('#turno_retorno').show();
                                $('#dias_solicitados').val(dias_max);
                                $('#tipo_permiso').val(3);
                                $('#clasificacion').val(clasif);
                                $('#help_dias').html('Este permiso tiene un l&iacute;mite de '+ dias_max + clas);
                            }else {
                                //error de config permiso
                            }
                        }
                    }else {
                        //error de config de permiso
                    }
                }
                
                $('#div_dias_solicitados').html(cadena);
                $("#input_tipo_permiso").val($("#permisos_tipo_permiso option:selected").text());
            }else {
                $('#div_dias_solicitados').html('');
                $('#help_dias').html('Seleccione un tipo de permiso para conocer cantidad de d&iacute;as o semanas disponibles.');
                $('#dias_solicitados').val('');
            }
        };
    </script>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_dias_solicitados">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_dias_solicitados')) ?>

        <div>
            <label for="permisos_dias_solicitados">Tiempo</label>
            <div class="content" id="div_dias_solicitados">

            </div>
            <!--<input type="hidden" id="input_medio_dia" name="correspondencia[formato][permisos_medio_dia]" value="C"/>-->

            <input type="hidden" id="input_turno_inicio" name="correspondencia[formato][turno_inicio]" value="<?php echo (($day_hour > 12) ? 'M' : 'T') ?>"/>
            <input type="hidden" id="input_turno_retorno" name="correspondencia[formato][turno_retorno]" value="<?php echo (($day_hour > 12) ? 'T' : 'M') ?>"/>
            <input type="hidden" id="dias_solicitados" name="correspondencia[formato][permisos_dias_solicitados]" value=""/>
            <input type="hidden" id="tipo_permiso" name="correspondencia[formato][tipo_permiso]" value="" />
            <input type="hidden" id="clasificacion" name="correspondencia[formato][clasificacion]" value="" />
        </div>

        <div class="help" id="help_dias">
            Seleccione un tipo de permiso para conocer cantidad de d&iacute;as o semanas disponibles.
        </div>

    </div>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_f_inicio">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_f_inicio')) ?>

        <div>
        <label for="permisos_f_inicio">Fecha de Inicio</label>
            <div class="content" style="position: relative">

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
                    $manana = ($day_hour > 12) ? date('Y-m-d', strtotime('+1 day ' . date('Y-m-d'))) : date('Y-m-d');
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
                    ),array('name'=>'correspondencia[formato][permisos_f_inicio]','value'=>$manana, 'onchange'=>'calcular_retorno()'));

                    if(isset($formulario['permisos_f_inicio']))
                        echo $w->render('permisos_f_inicio',$formulario['permisos_f_inicio']);
                    else
                        echo $w->render('permisos_f_inicio',$manana);
                ?>
               <div id="turno_inicio"style="position: absolute; left: 425px; top: 0px; display: none">
                   <em>A partir de la: </em>
                   <select id="select_turno_inicio" onChange="cambiaTurno('inicio')">
                       <option <?php echo (($day_hour > 12) ? 'selected' : '') ?> value="M">Ma&ntilde;ana</option>
                       <option <?php echo (($day_hour > 12) ? '' : 'selected') ?> value="T">Tarde</option>
                   </select>
               </div>
                <script>
                    $('#permisos_f_inicio_day').attr('disabled','disabled');
                    $('#permisos_f_inicio_month').attr('disabled','disabled');
                    $('#permisos_f_inicio_year').attr('disabled','disabled');
                    $('#permisos_f_inicio_jquery_control').removeAttr('disabled');
                    <?php
                        if(isset($formulario['permisos_f_inicio'])){
                            echo "$('#permisos_f_inicio_jquery_control').val('".$formulario['permisos_f_inicio']."');";
                        }
                    ?>

                    function calcular_retorno(no_laboral)
                    {
                        if($('#tipo_permiso').val()) {
                            if($('#dias_solicitados').val()){
                                $('#regreso').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Calculando retorno...');

                                var tipo_permiso = $('#tipo_permiso').val();
                                var f_i = $('#permisos_f_inicio_jquery_control').val();
                                var f_r = $('#permisos_f_retorno_jquery_control').val();
                                var md_i= null;
                                var md_r= null;
                                var func_term= 'Regreso';

                                if(tipo_permiso== 1 || tipo_permiso== 2) {
                                    var d = $('#dias_solicitados').val();
                                    func_term= 'Regreso';
                                }else {
                                    md_i= $('#input_turno_inicio').val();
                                    md_r= $('#input_turno_retorno').val();
                                    var d = diasDiferencia(f_i, f_r, md_i, md_r, no_laboral);
                                    func_term= 'Intervalo';
                                }

                                var parametros_dias = $("#permisos_tipo_permiso").val();
                                var parametros_dias = parametros_dias.split('-');
                                var dias_max = parseInt(parametros_dias[1]);
                                
                                if(d >= 0) {
                                    if(d <= dias_max) {
                                        var turn_error= false;
                                        if(d== 0 && md_i== md_r) {
                                            $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, seleccione un rango de fecha valido, tome en cuenta los turnos.');
                                            turn_error= true;
                                        }else {
                                            if(d== 0 && md_i== 'T' && md_r== 'M') {
                                                $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, seleccione un rango de fecha valido, tome en cuenta los turnos.');
                                                turn_error= true;
                                            }
                                        }
                                            
                                        if(!turn_error) {
                                            var forma = $('#formato_tipo_formato_id').val();
                                            if(func_term=== 'Intervalo') {
                                                d_x = diasDiferenciaContinuo(f_i, f_r, md_i, md_r);
                                                d_x= parseInt(d_x);
                                            }else {
                                                d_x= d;
                                            }
                                            
                                            <?php
                                            echo jq_remote_function(array('update' => 'regreso',
                                            'url' => 'formatos/librerias',
                                            'with'=> "'forma='+forma+'&func=Calculo'+func_term+'&var[f_i]='+f_i+'&var[d]='+d_x+'&var[md_i]='+md_i+'&var[md_r]='+md_r",));
                                            ?>    
                                        }
                                    } else {
                                        if(d % 1 != 0 && d < dias_max + 1)
                                            $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Ha solicitado '+ parseInt(d) +' &frac12; d&iacute;as, no exeda la cantidad de tiempo permitido por el tipo de permiso.');
                                        else
                                            $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, no exeda la cantidad de d&iacute;as permitidos por el tipo de permiso.');
                                    }
                                }else {
                                    $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, seleccione un rango de fecha valido.');
                                }
                            } else {
                                $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, seleccione la cantidad de d&iacute;as.');
                            }
                        }else {
                            $('#regreso').html('<?php echo image_tag('icon/error.png'); ?> Por favor, seleccione un tipo de permiso.');
                        }
                    }
                    
                    function diasDiferenciaContinuo(i, r, md_i, md_r){
                        var d1 = i.split("-");
                        var dat1 = new Date(d1);
                        var d2 = r.split("-");
                        var dat2 = new Date(d2);

                        var fin = dat2.getTime() - dat1.getTime();
                        var dias = Math.floor(fin / (1000 * 60 * 60 * 24))

                        if(md_r=='T')
                            dias += parseFloat('0.5');
                        
                        if(md_i=='T')
                            dias -= parseFloat('0.5');
                        
                        return dias;
                    }
                    
                    function diasDiferencia(i, r, md_i, md_r, no_laboral){
                        var cap= no_laboral.split(",");

                        var dias_no_lab= new Array();
                        
                        for(var y=0; y< cap.length; y++) {
                            var cap2= cap[y].split("#");
                            
                            var val_day= cap2[0].split("-");
                            var day= val_day[0];
                            var val_month= cap2[0].split("-");
                            var month= val_month[1];
                            var val_year= cap2[0].split("-");
                            var year= val_year[2];
                            var fecha_c= year+','+month+','+day;
                            
                            var tmp= cap2[1];
                            
                            dias_no_lab[y]= new Array(fecha_c, tmp);
                        }
                        
                        var d1 = i.split("-");
                        var dat1 = new Date(d1);
                        var d2 = r.split("-");
                        var dat2 = new Date(d2);

                        var dat_run = new Date(d1);
//                        dat_run.setDate(dat_run.getDate() + 1);

//                        var fin = dat2.getTime() - dat1.getTime();

                        var lap= 0;
                        for(var k= (dat1.getDate()); k < dat2.getDate(); k++) {
                            if(dat_run.getDay()!== 6 && dat_run.getDay()!== 0) {
                                var fer= false;
                                if(dias_no_lab.length > 0) {
                                    for(var j=0; j< dias_no_lab.length; j++) {
                                        var fec= new Date(dias_no_lab[j][0]);
                                        if(dias_no_lab[j][1]=== 't') {
                                            if(dat_run.getDate() === fec.getDate() && dat_run.getMonth() === fec.getMonth()) {
                                                fer= true;
                                            }
                                        }else {
                                            if(dat_run.getDate() === fec.getDate() && dat_run.getMonth() === fec.getMonth() && dat_run.getYear() === fec.getYear()) {
                                                fer= true;
                                            }
                                        }
                                    }
                                }
                                if(fer=== false)
                                    lap++;
                            }
                            dat_run.setDate(dat_run.getDate() + 1);
                        }
//                        var dias = Math.floor(lap / (1000 * 60 * 60 * 24))
                        var dias= lap;
                        if(md_r=='T')
                            dias += parseFloat('0.5');
                        
                        if(md_i=='T')
                            dias -= parseFloat('0.5');
                        
                        return dias;
                    }
                </script>
            </div>
        </div>

        <div class="help">Seleccione la fecha de inicio del permiso.</div>

    </div>



    <div id="div_retorno" class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_f_inicio" style="display: none">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_f_inicio')) ?>

        <div>
            <label for="permisos_f_inicio">Fecha de Retorno</label>
            <div class="content" style="position: relative">
                <?php
                    $manana = date('Y-m-d', strtotime('+1 day ' . date('Y-m-d')));
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
                    ),array('name'=>'correspondencia[formato][permisos_f_retorno]', 'id'=> 'permisos_f_retorno_day','value'=>$manana, 'onchange'=>'calcular_retorno()'));

                    if(isset($formulario['permisos_f_retorno']))
                        echo $w->render('permisos_f_retorno',$formulario['permisos_f_retorno']);
                    else
                        echo $w->render('permisos_f_retorno',$manana);
                ?>
                <div id="turno_retorno"style="position: absolute; left: 425px; top: 0px; display: none">
                   <em>A partir de la: </em>
                   <select id="select_turno_retorno" onChange="cambiaTurno('retorno')">
                       <option <?php echo (($day_hour > 12) ? '' : 'selected') ?> value="M">Ma&ntilde;ana</option>
                       <option <?php echo (($day_hour > 12) ? 'selected' : '') ?> value="T">Tarde</option>
                   </select>
               </div>
                <script>
                    $('#permisos_f_retorno_day').attr('disabled','disabled');
                    $('#permisos_f_retorno_day').addClass('pickerday');
                    $('#permisos_f_retorno_month').attr('disabled','disabled');
                    $('#permisos_f_retorno_year').attr('disabled','disabled');
                    $('#permisos_f_retorno_jquery_control').removeAttr('disabled');
                    <?php
                        if(isset($formulario['permisos_f_inicio'])){
                            echo "$('#permisos_f_inicio_jquery_control').val('".$formulario['permisos_f_inicio']."');";
                        }
                    ?>
                </script>
            </div>
        </div>

        <div class="help">Seleccione la fecha de retorno del permiso.</div>

    </div>



    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_f_final">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_f_final')) ?>
        <?php
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

        $array_no_laboral = "";

        foreach ($dias_no_laborables as $dia_no_laboral => $descripcion_dias) {
            list($anio,$mes,$dia) = explode ("-",$dia_no_laboral);
            list($descripcion_dia,$tmp) = explode("#", $descripcion_dias);
            $array_no_laboral .= $dia."-".$mes."-".$anio."#".$tmp.",";
        }
        $array_no_laboral.= 'end';
        $array_no_laboral= str_replace(',end', '', $array_no_laboral);
        ?>
        <div>
            <label for="permisos_f_final">Fecha de Retorno</label>
            <div class="content">
                <div class='partial_find_reload partial'>
                <a href="#" onclick="javascript:calcular_retorno('<?php echo $array_no_laboral; ?>'); return false;">Calcular Retorno</a>
                </div>
                <div id="regreso"></div>
            </div>
        </div>

        <div class="help"></div>
    </div>

    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_permisos_observacion">

        <?php include_partial('formatos/sessionFlashes', array('error_namen' => 'permisos_observacion')) ?>

        <div>
            <label for="permisos_observacion">Observación</label>
            <div class="content" style="width: 650px;">
                <textarea rows="4" cols="30" name="correspondencia[formato][permisos_observacion]" id="permisos_observacion"><?php if(isset($formulario['permisos_observacion'])) echo $formulario['permisos_observacion']; ?></textarea>
            </div>
        </div>

        <div class="help">Agregue observaciones de ser necesario.</div>

    </div>

<?php // } ?>
<input type="hidden" name="val_permiso" />
</fieldset>