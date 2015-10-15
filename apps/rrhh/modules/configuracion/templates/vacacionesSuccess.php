<script>    
    function toggle_div(cual) {
        if ($('#div_'+cual).is(":hidden")) {
            $('#div_'+cual).slideDown("slow");
        } else {
            $('#div_'+cual).slideUp("slow");
        }
    }
</script>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2 onclick="toggle_div('actualizar_f_ingreso');" style="cursor: pointer;">Actualización de fechas de ingreso</h2>
    <div id="div_actualizar_f_ingreso" class="sf_admin_form_row sf_admin_text" style="display: none;">
        <?php include_partial('configuracion/vacacionesFIngresoFuncionarios') ?>
    </div> 
    <div class="gris_medio">
        Para activar la solicitud de vacaciones debe actualizar las fechas de ingreso de todos los funcionarios de la institución,
        esto con el objetivo de que el SIGLAS conozca el momento exacto de cuando un funcionario ingreso y de esta manera poder 
        generarle automaticamente todos los períodos vacacionales que ha cumplido indiferentemente si ya los disfruto o estan a la espera de ello.
    </div>
</fieldset>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2 onclick="toggle_div('configurar');" style="cursor: pointer;">Configuración de Vacaciones</h2>
    <div id="div_configurar" class="sf_admin_form_row sf_admin_text" style="display: none;">
        <?php include_partial('configuracion/vacacionesConfigurar') ?>
    </div> 
    <div class="gris_medio">
        Debe configurar la forma en se asignan los períodos vacacionales a cada funcionario de la institución, con esto en conjunto de haber actualizado 
        las fechas de ingreso ya el SIGLAS estara listo para generar todos los períodos vacacionales.
    </div>
</fieldset>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2 onclick="toggle_div('generacion');" style="cursor: pointer;">Generar períodos vacacionales masivamente</h2>
    
    <div id="div_generacion" class="sf_admin_form_row sf_admin_text" style="display: none;">
        <?php include_partial('configuracion/vacacionesGeneracionMasiva') ?>
    </div>    
    <div class="gris_medio">
        Genere de forma masiva todos los períodos vacacionales que han cumplido los funcionarios de la institución, una vez realizado esto
        el SIGLAS permitira que los funcionarios soliciten el disfrute de sus vacaciones.
    </div>
</fieldset>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2 onclick="toggle_div('dias_disponibles');" style="cursor: pointer;">Depurar dias disponibles de cada funcionario</h2>
    
    <div id="div_dias_disponibles" class="sf_admin_form_row sf_admin_text" style="display: none;">
        <?php include_partial('configuracion/vacacionesDepurarDiasDisponibles') ?>
    </div>    
    <div class="gris_medio">
        Cuando generas de forma masiva los periodos vacacionales que ha cumplido cada funcionario el SIGLAS interpreta que los dias de
        cada período vacacional ya no estan disponibles, es decir que el funcionario ya los ha disfrutado, por ello se debe depurar los dias
        que le restan a cada trabajador por periodo vacacional y de esta manera reactivar los dias disponibles de cada período.
    </div>
</fieldset>

<fieldset id="sf_fieldset_config_vacaciones">
    <h2 onclick="toggle_div('configuradas');" style="cursor: pointer;">Historico de configuraciones realizadas</h2>
    
    <div id="div_configuradas" class="sf_admin_form_row sf_admin_text" style="display: none;">
        <?php include_partial('configuracion/vacacionesConfiguradas') ?>
    </div>    
</fieldset>
