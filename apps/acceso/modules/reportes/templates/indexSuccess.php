<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function() {
        Highcharts.setOptions({
	lang: {
		loading: 'Cargando...',
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                shortMonths: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Nov','Dic'],
		weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                exportButtonTitle: "Exportar",
                printButtonTitle: "Imprimir",
                rangeSelectorFrom: "De",
                rangeSelectorTo: "Hasta",
                rangeSelectorZoom: "Periodo",
                downloadPNG: 'Descargar imagen PNG',
                downloadJPEG: 'Descargar imagen JPEG',
                downloadPDF: 'Descargar documento PDF',
                downloadSVG: 'Descargar imagen SVG',
                resetZoom: 'Restablecer',
                resetZoomTitle: 'Restablecer',
                printButtonTitle: 'Imprimir'
	}
        });
    });
    
    <?php if($opcion!=null) { 
        echo "var opcion = ".$opcion;
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'reportes/opciones',
        'with'=> "'opcion='+opcion",));
    }
    ?>
    
    function cambiar_opcion()
    {
        var opcion = $('#opciones').val();
        
        $('#contenido_opciones').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando reporte...');
        <?php
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'reportes/opciones',
        'with'=> "'opcion='+opcion",));
        ?>
    }
</script>
<br/><br/>
<div id="sf_admin_container">
    <h1>Reportes SIGLAS</h1>

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
                    <label for="correspondencia_n_correspondencia_emisor">Opción</label>
                    <div class="content">
                        <select id="opciones" onchange="javascript:cambiar_opcion()">
                            <option value=""></option>
                            <option value="sistemas" <?php if(isset($opcion)) if($opcion=='sistemas') echo 'selected'; ?>>Sistemas Operativos</option>
                            <option value="browsers" <?php if(isset($opcion)) if($opcion=='browsers') echo 'selected'; ?>>Navegadores</option>
                            <option value="organigrama" <?php if(isset($opcion)) if($opcion=='organigrama') echo 'selected'; ?>>Organigrama</option>
                            <option value="usuarios" <?php if(isset($opcion)) if($opcion=='usuarios') echo 'selected'; ?>>Usuarios</option>
                            <option value="correspondencia" <?php if(isset($opcion)) if($opcion=='correspondencia') echo 'selected'; ?>>Correspondencia</option>
                        </select>
                    </div>

                    <div class="help">Seleccione el reporte que desea ver.</div>
                </div>
            </div>
            <br/>
            <div id="contenido_opciones"></div>

        </div>
    </div>
</div>

<script>
    $("#opciones option[value='<?php echo $opcion ?>']").attr("selected", "selected");
    <?php 
        echo "var opcion = '".$opcion."';";
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'reportes/opciones',
        'with'=> "'opcion='+opcion",));
    ?>
</script>