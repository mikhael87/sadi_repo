<script>
    var tipos_reposo_count = 0;
    $(document).ready(function(){
        $("form").submit(function() {
            tipo_reposo_agregar();
        });
    });

    function tipo_reposo_agregar(){
        if($("#tipo_reposo_nombre").val())
        {
            if($('#tipo_reposo_clasif').val() == 'D') {
                if(parseInt($("#tipo_reposo_min_dias").val()) <= parseInt($("#tipo_reposo_max_dias").val())) {
                    cadena = "<tr>";
                    cadena += "<td>";
                    cadena += "<font class='f16n'>" + $("#tipo_reposo_nombre").val() + "</font> - Desde " + $("#tipo_reposo_min_dias").val() + " Hasta " + $("#tipo_reposo_max_dias").val() + " dias";
                    cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][nombre]' type='hidden' value='" + $("#tipo_reposo_nombre").val() + "'/>";
                    cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][clasificacion]' type='hidden' value='" + $("#tipo_reposo_clasif").val() + "'/>";
                    cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][dias_min]' type='hidden' value='" + $("#tipo_reposo_min_dias").val() + "'/>";
                    cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][dias_max]' type='hidden' value='" + $("#tipo_reposo_max_dias").val() + "'/>" + "</td>";
                    cadena += "<td><a class='tipo_reposo_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                    cadena += "</tr>";
                    $("#grilla_tipo_reposo tbody").append(cadena);
                    tipo_reposo_eliminar();
                    $("#tipo_reposo_nombre").val('');
                    $("#tipo_reposo_min_dias option[value=1]").attr('selected', 'selected');
                    $("#tipo_reposo_max_dias option[value=1]").attr('selected', 'selected');

                    tipos_reposo_count++;
                }else {
                    alert('El mínimo y máximo de días no es coherente');
                }
            }else {
                cadena = "<tr>";
                cadena += "<td>";
                cadena += "<font class='f16n'>" + $("#tipo_reposo_nombre").val() + "</font> - N&uacute;mero de semanas: " + parseInt($("#tipo_reposo_max_semanas").val())/5;
                cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][nombre]' type='hidden' value='" + $("#tipo_reposo_nombre").val() + "'/>";
                cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][clasificacion]' type='hidden' value='" + $("#tipo_reposo_clasif").val() + "'/>";
                cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][dias_min]' type='hidden' value='" + $("#tipo_reposo_max_semanas").val() + "'/>";
                cadena += "<input name='datos[tipos_reposo]["+ tipos_reposo_count +"][dias_max]' type='hidden' value='" + $("#tipo_reposo_max_semanas").val() + "'/>" + "</td>";
                cadena += "<td><a class='tipo_reposo_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                cadena += "</tr>";
                $("#grilla_tipo_reposo tbody").append(cadena);
                tipo_reposo_eliminar();
                $("#tipo_reposo_nombre").val('');
                $("#tipo_reposo_min_dias option[value=1]").attr('selected', 'selected');
                $("#tipo_reposo_max_dias option[value=1]").attr('selected', 'selected');
                $("#tipo_reposo_clasif option[value=D]").attr('selected', 'selected');
                $("#tipo_reposo_max_semanas option[value=5]").attr('selected', 'selected');
                $('#tipo_reposo_config_dias').show();
                $('#tipo_reposo_config_semanas').hide();

                tipos_reposo_count++;
            }
        }
    };

    function tipo_reposo_eliminar(){
        $("a.tipo_reposo_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };

    function changeConfig() {
        if($('#tipo_reposo_clasif').val() == 'D') {
            $('#tipo_reposo_config_dias').show();
            $('#tipo_reposo_config_semanas').hide();
        }else {
            $('#tipo_reposo_config_dias').hide();
            $('#tipo_reposo_config_semanas').show();
        }
    }
</script>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2>Configuraciones realizadas</h2>

    <div class="sf_admin_form_row sf_admin_text">
        <div>
                <table>
                    <tr>
                        <th>Condición del trabajador</th>
                        <th style="width: auto;">Tipos de reposo</th>
                        <th style="width: 150px;">Tiempo de validez</th>
                        <th>Acciones</th>
                    </tr>
                    <?php
                    $configuraciones_reposos = Doctrine_Query::create()
                                                  ->select('c.*')
                                                  ->addSelect('(SELECT us.nombre FROM Acceso_Usuario us WHERE us.id = c.id_update LIMIT 1) as user_update')
                                                  ->from('Rrhh_Configuraciones c')
                                                  ->where('c.modulo = ?', 'reposos')
                                                  ->orderBy('c.indexado, id')
                                                  ->execute();

                    foreach ($configuraciones_reposos as $configuracion_reposos) {
                        $parametros_creados = sfYaml::load($configuracion_reposos->getParametros());

                    ?>
                        <tr>
                            <td>
                                <?php
                                    $condicion = Doctrine::getTable('Organigrama_CargoCondicion')->find($parametros_creados['condicion']);
                                        echo $condicion->getNombre();
                                ?>
                            </td>
                            <td>
                                <div style="position: relative;">
                                    <div>
                                        <?php
                                            foreach ($parametros_creados['tipos_reposo'] as $tipos) {
                                                if($tipos['clasificacion']== 'D')
                                                    echo "<b>".$tipos['nombre']."</b> - Desde ".$tipos['dias_min']." Hasta ".$tipos['dias_max']." dias<br/>";
                                                else
                                                    echo "<b>".$tipos['nombre']."</b> - N&uacute;mero de Semanas: ".($tipos['dias_max'] / 5)."<br/>";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="position: relative;">
                                    <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Desde: </div>
                                    <div style="position: absolute; top: 0px; padding-left: 30px;"><?php echo date('d-m-Y', strtotime($parametros_creados['f_inicial_configuracion'])); ?></div>
                                </div>
                                <br/>
                                <div style="position: relative;">
                                    <div style="position: absolute; top: 3px; padding-left: 0px;" class="f10n">Hasta: </div>
                                    <div style="position: absolute; top: 0px; padding-left: 30px;">
                                        <?php
                                            if($parametros_creados['f_final_configuracion']!='2038-01-01')
                                                echo date('d-m-Y', strtotime($parametros_creados['f_final_configuracion']));
                                            else
                                                echo '<fond style="color: #04B404;">Activo</fond>';
                                        ?>
                                    </div>
                                </div>
                                <br/>
                            </td>
                            <td>
                                <br/><br/>

                                <div class="" style="position: relative;">
                                    <font class="f10n">Hecho por:</font><br/>
                                    <font class="f16n">&nbsp;&nbsp;<?php echo $configuracion_reposos->getUserUpdate(); ?><br/></font>
                                    <font class="f10n">Fecha:</font><br/>
                                    <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($configuracion_reposos->getCreatedAt())); ?><br/></font>
                                    <font class="f10n">Hora:</font><br/>
                                    <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($configuracion_reposos->getCreatedAt())); ?></font>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
        </div>
    </div>
</fieldset>




<fieldset id="sf_fieldset_config_vacaciones">
    <form method="post" action="<?php echo sfConfig::get('sf_app_rrhh_url').'configuracion/saveReposos'; ?>">
    <h2>Configuración de Reposos</h2>

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
            <label for="">Tipos de reposo</label>
            <div class="content" style="position: relative">
                <input id="tipo_reposo_nombre" type="text">
                &nbsp;&nbsp;&nbsp;Reposo por:&nbsp;
                <select id="tipo_reposo_clasif" onChange="javascript: changeConfig();">
                    <option value="D">D&iacute;as</option>
                    <option value="S">Semanas</option>
                </select>
                
                <div id="tipo_reposo_config_dias" style="position: absolute; top: 0px; left: 490px">
                    &nbsp;&nbsp;&nbsp;Minimo de dias
                    <select id="tipo_reposo_min_dias">
                    <?php for($i=1; $i<999; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;Maximo de dias
                    <select id="tipo_reposo_max_dias">
                    <?php for($i=1; $i<1000; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div id="tipo_reposo_config_semanas" style="position: absolute; top: 0px; left: 490px; display: none">
                    &nbsp;&nbsp;&nbsp;N&uacute;mero de <b>semanas</b>
                    <select id="tipo_reposo_max_semanas">
                    <?php for($i=1; $i<200; $i++) { ?>
                        <option value="<?php echo $i*5; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                </div>
                
                <div class='partial_new_view partial'><a href="#" onclick="javascript: tipo_reposo_agregar(); return false;">Agregar otro</a></div>
                <div id="div_tipo_reposo_array">
                    <table id="grilla_tipo_reposo" class="lista"><tbody></tbody></table>
                </div>
            </div>
        </div>
        <div class="help">Escriba el nombre y seleccione la cantidad de dias maximo para un nuevo tipo de reposo.</div>
    </div>


    <div class="sf_admin_form_row sf_admin_text">
        <div>
            <label for="">Alerta de falta de soportes</label>
            <div class="content">
                <select id="alerta_dias_aplazados" name="datos[alerta_soportes_faltantes]">
                    <option value="1">1</option>
                <?php for($i=5; $i<=60; $i+=5) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>
        <div class="help">Seleccione la frecuencia en dias que desea se notifique al trabajador, al supervisor y a la unidad de RRHH que no se han cargado los soportes del reposo.</div>
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
</fieldset>