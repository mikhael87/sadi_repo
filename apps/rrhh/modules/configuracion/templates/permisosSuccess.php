<script>
    var tipos_permiso_count = 0;
    $(document).ready(function(){
        blockTurno();

        $("form").submit(function() {
            tipo_permiso_agregar();
        });
    });

    function tipo_permiso_agregar(){
        if($("#tipo_permiso_nombre").val())
        {
            if($('#tipo_permiso_clasif').val() == 'D') {
                if(parseInt($("#tipo_permiso_min_dias").val()) <= parseInt($("#tipo_permiso_max_dias").val())) {
                    cadena = "<tr>";
                    cadena += "<td>";
                    cadena += "<font class='f16n'>" + $("#tipo_permiso_nombre").val() + "</font> - M&iacute;nimo " + $("#tipo_permiso_min_dias").val() + " d&iacute;as - M&aacute;ximo " + $("#tipo_permiso_max_dias").val() + " d&iacute;as - Turno permitido '" + $("#tipo_permiso_medio_dia").val() + "'";
                    cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][nombre]' type='hidden' value='" + $("#tipo_permiso_nombre").val() + "'/>";
                    cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][clasificacion]' type='hidden' value='" + $("#tipo_permiso_clasif").val() + "'/>";
                    cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][medio_dia]' type='hidden' value='" + $("#tipo_permiso_medio_dia").val() + "'/>";
                    cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][dias_max]' type='hidden' value='" + $("#tipo_permiso_max_dias").val() + "'/>" + "</td>";
                    cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][dias_min]' type='hidden' value='" + $("#tipo_permiso_min_dias").val() + "'/>" + "</td>";
                    cadena += "<td><a class='tipo_permiso_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                    cadena += "</tr>";
                    $("#grilla_tipo_permiso tbody").append(cadena);
                    tipo_permiso_eliminar();
                    $("#tipo_permiso_nombre").val('');
                    $("#tipo_permiso_min_dias option[value=1]").attr('selected', 'selected');
                    $("#tipo_permiso_max_dias option[value=1]").attr('selected', 'selected');

                    tipos_permiso_count++;
                }else {
                    alert('El mínimo y máximo de días no es coherente');
                }    
            } else {
                cadena = "<tr>";
                cadena += "<td>";
                cadena += "<font class='f16n'>" + $("#tipo_permiso_nombre").val() + "</font> - N&uacute;mero de semanas: " + parseInt($("#tipo_permiso_max_semanas").val())/5;
                cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][nombre]' type='hidden' value='" + $("#tipo_permiso_nombre").val() + "'/>";
                cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][clasificacion]' type='hidden' value='" + $("#tipo_permiso_clasif").val() + "'/>";
                cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][medio_dia]' type='hidden' value='No'/>";
                cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][dias_max]' type='hidden' value='" + $("#tipo_permiso_max_semanas").val() + "'/>" + "</td>";
                cadena += "<input name='datos[tipos_permiso]["+ tipos_permiso_count +"][dias_min]' type='hidden' value='" + $("#tipo_permiso_max_semanas").val() + "'/>" + "</td>";
                cadena += "<td><a class='tipo_permiso_eliminar' style='cursor: pointer;'><img src='/images/icon/delete.png'/></a></td>";
                cadena += "</tr>";
                $("#grilla_tipo_permiso tbody").append(cadena);
                tipo_permiso_eliminar();
                $("#tipo_permiso_nombre").val('');
                $("#tipo_permiso_clasif option[value=D]").attr('selected', 'selected');
                $("#tipo_permiso_max_semanas option[value=5]").attr('selected', 'selected');
                $('#tipo_permiso_config_dias').show();
                $('#tipo_permiso_config_semanas').hide();
                tipos_permiso_count++;
            }
            
        }
    };

    function tipo_permiso_eliminar(){
        $("a.tipo_permiso_eliminar").click(function(){
            $(this).parent().parent().fadeOut("normal", function(){
                    $(this).remove();
                })
        });
    };

    function blockTurno() {
        if(parseInt($('#tipo_permiso_min_dias').val()) > parseInt($('#tipo_permiso_max_dias').val()) || parseInt($('#tipo_permiso_min_dias').val()) == parseInt($('#tipo_permiso_max_dias').val() && $('#tipo_permiso_min_dias').val()) > 1)
            $("#tipo_permiso_medio_dia option[value=Si]").attr('disabled', 'disabled');
        else
            $("#tipo_permiso_medio_dia option[value=Si]").attr('disabled', '');
    }
    
    function changeConfig() {
        if($('#tipo_permiso_clasif').val() == 'D') {
            $('#tipo_permiso_config_dias').show();
            $('#tipo_permiso_config_semanas').hide();
        }else {
            $('#tipo_permiso_config_dias').hide();
            $('#tipo_permiso_config_semanas').show();
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
                        <th style="width: auto;">Tipos de permiso</th>
                        <th style="width: 150px;">Tiempo de validez</th>
                        <th>Acciones</th>
                    </tr>
                    <?php
                    $configuraciones_vacaciones = Doctrine_Query::create()
                                                  ->select('c.*')
                                                  ->addSelect('(SELECT us.nombre FROM Acceso_Usuario us WHERE us.id = c.id_update LIMIT 1) as user_update')
                                                  ->from('Rrhh_Configuraciones c')
                                                  ->where('c.modulo = ?', 'permisos')
                                                  ->orderBy('c.indexado, id')
                                                  ->execute();

                    foreach ($configuraciones_vacaciones as $configuracion_vacaciones) {
                        $parametros_creados = sfYaml::load($configuracion_vacaciones->getParametros());

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
                                            foreach ($parametros_creados['tipos_permiso'] as $tipos) {
                                                if($tipos['clasificacion']== 'D')
                                                    echo "<b>".$tipos['nombre']."</b> - M&iacute;nimo: ".$tipos['dias_min']." dia". (($tipos['dias_min'] != 1) ? 's' : '') ." - M&aacute;ximo: ".$tipos['dias_max']." dia". (($tipos['dias_max'] != 1) ? 's' : '') ." - Turno permitido: '".$tipos['medio_dia']."'<br/>";
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
                                    <font class="f16n">&nbsp;&nbsp;<?php echo $configuracion_vacaciones->getUserUpdate(); ?><br/></font>
                                    <font class="f10n">Fecha:</font><br/>
                                    <font class="f16n">&nbsp;&nbsp;<?php echo date('d-m-Y', strtotime($configuracion_vacaciones->getCreatedAt())); ?><br/></font>
                                    <font class="f10n">Hora:</font><br/>
                                    <font class="f16n">&nbsp;&nbsp;<?php echo date('h:i:s A', strtotime($configuracion_vacaciones->getCreatedAt())); ?></font>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
        </div>
    </div>
</fieldset>




<fieldset id="sf_fieldset_config_vacaciones">
    <form method="post" action="<?php echo sfConfig::get('sf_app_rrhh_url').'configuracion/savePermisos'; ?>">
    <h2>Configuración de Vacaciones</h2>

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
            <label for="">Tipos de permiso</label>
            <div class="content" style="position: relative">
                <input id="tipo_permiso_nombre" type="text">
                &nbsp;&nbsp;&nbsp;Permiso por:&nbsp;
                <select id="tipo_permiso_clasif" onChange="javascript: changeConfig();">
                    <option value="D">D&iacute;as</option>
                    <option value="S">Semanas</option>
                </select>
                
                <div id="tipo_permiso_config_dias" style="position: absolute; top: 0px; left: 490px">
                    &nbsp;&nbsp;&nbsp;M&iacute;nimo de d&iacute;as
                    <select id="tipo_permiso_min_dias" onChange="javascript: blockTurno();">
                    <?php for($i=1; $i<999; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;M&aacute;ximo de d&iacute;as
                    <select id="tipo_permiso_max_dias" onChange="javascript: blockTurno();">
                    <?php for($i=1; $i<1000; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;Permitir selecci&oacute;n de turno
                    <select id="tipo_permiso_medio_dia">
                        <option value="No">No</option>
                        <option value="Si">Si</option>
                    </select>
                </div>
                <div id="tipo_permiso_config_semanas" style="position: absolute; top: 0px; left: 490px; display: none">
                    &nbsp;&nbsp;&nbsp;N&uacute;mero de <b>semanas</b>
                    <select id="tipo_permiso_max_semanas">
                    <?php for($i=1; $i<200; $i++) { ?>
                        <option value="<?php echo $i*5; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class='partial_new_view partial'><a href="#" onclick="javascript: tipo_permiso_agregar(); return false;">Agregar otro</a></div>
                <div id="div_tipo_permiso_array">
                    <table id="grilla_tipo_permiso" class="lista"><tbody></tbody></table>
                </div>
            </div>
        </div>
        <div class="help">Escriba el nombre y seleccione la cantidad de dias maximo para un nuevo tipo de permiso.</div>
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