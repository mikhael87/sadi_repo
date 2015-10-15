<script type="text/javascript">
    var procesados = 0;
    var procesados_echo = 1;
    var funcionarios = new Array(); 
    <?php 
        $funcionarios = Doctrine::getTable('Funcionarios_Funcionario')->findAll();

        $i=0;
        foreach ($funcionarios as $funcionario) {
            echo 'funcionarios['.$i.']='.$funcionario->getCi().'; ';
            $i++;
        }

        echo "funcionarios[".$i."]='finalizado'; ";
        echo "var total_funcionarios = ".$i.";";

    ?>

    function generar_vacaciones_globales(funcionario) {
        if(funcionario==0){
            $('#vacaciones_generadas').append('Analizado funcionario 1 de '+total_funcionarios);
        }
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/generarVacacionesGlobales',
            type:'POST',
            dataType:'html',
            data: 'ci='+funcionarios[funcionario],
            success:function(data, textStatus){
                $('#vacaciones_generadas').append(data);
                $('#vacaciones_generadas').attr('scrollTop', $('#vacaciones_generadas').attr('scrollHeight'));
                procesados++;
                procesados_echo++;

                if(funcionarios[procesados] != 'finalizado' && funcionarios.length > procesados){
                    $('#vacaciones_generadas').append('Analizado funcionario '+procesados_echo+' de '+total_funcionarios);
                    $('#vacaciones_generadas_progreso').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> '+procesados+' de '+total_funcionarios);
                    $('#vacaciones_generadas_progreso').attr('bottom', $('#vacaciones_generadas').attr('scrollTop'));
                    generar_vacaciones_globales(procesados);
                } else {
                    $('#vacaciones_generadas').append('<hr/>Finalizado.');
                    $('#vacaciones_generadas_progreso').html('Finalizado');

                    $('#vacaciones_generadas_progreso').attr('bottom', $('#vacaciones_generadas').attr('scrollTop'));
                }
            }});
    }

</script>

<a href="#" onclick="generar_vacaciones_globales(0); return false;">Generar Vacaciones de todos</a>

<div id="vacaciones_generadas" style="position: relative; overflow-y: scroll; max-height: 500px; padding: 10px; background-color: #CCCCFF; border: 1px solid;"></div>
<div style="position: relative;">
    <div id="vacaciones_generadas_progreso" style="position: absolute; top: -55px; right: 20px; padding: 10px; background-color: #CCCCFF;"></div>
</div>