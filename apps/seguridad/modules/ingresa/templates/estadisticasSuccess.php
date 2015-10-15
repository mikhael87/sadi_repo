<?php use_helper('jQuery'); ?>

<script>
    <?php if($opcion!=null) { 
        echo "var opcion = ".$opcion;
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'ingresa/opcionesEstadisticas',
        'with'=> "'opcion='+opcion",));
    }
    ?>
    
    function cambiar_opcion()
    {
        var opcion = $('#opciones').val();
        indicator();
        <?php
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'ingresa/opcionesEstadisticas',
        'with'=> "'opcion='+opcion",));
        ?>
    }
    
    function indicator()
    {
        $('#indicator').css('display', 'inline')
    }
</script>
<br/><br/>
<div id="sf_admin_container" class="trans">
    <h1>Estadisticas de Visitantes</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <div id="sf_admin_content">

        <div class="sf_admin_form">
            <div class="sf_admin_form_row sf_admin_text"  style="background-image: url('../images/other/td_fond.png');">
                <div>
                    <label for="">Opción</label>
                    <div class="content">
                        <select id="opciones" onchange="cambiar_opcion()">
                            <option value=""></option>
                            <option value="motivos" <?php if(isset($opcion)) if($opcion=='motivos') echo 'selected'; ?>>Motivos de visita</option>
                            <option value="unidades" <?php if(isset($opcion)) if($opcion=='unidades') echo 'selected'; ?>>Unidades visitadas</option>
                            <option value="visitasPorDias" <?php if(isset($opcion)) if($opcion=='visitasPorDias') echo 'selected'; ?>>Visitas por fechas</option>
                        </select>
                    </div>

                    <div class="help">Seleccione la estadistica que desea ver.</div>
                </div>
            </div>
            <br/>
            <div id="contenido_opciones"><br/><font id="indicator" style="display: none"><?php echo "Cargando..."?></font></div>

        </div>
    </div>
</div>

<script>
    $("#opciones option[value='<?php echo $opcion ?>']").attr("selected", "selected");
    <?php 
        echo "var opcion = '".$opcion."';";
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'ingresa/opcionesEstadisticas',
        'with'=> "'opcion='+opcion",));
    ?>
</script>