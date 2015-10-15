<?php use_helper('jQuery'); ?>

<script>
    <?php if($opcion!=null) { 
        echo "var opcion = ".$opcion;
        echo jq_remote_function(array('update' => 'contenido_opciones',
        'url' => 'configuracion/opciones',
        'with'=> "'opcion='+opcion",));
    }
    ?>
    
    function cambiar_opcion()
    {
        var opcion = $('#opciones').val();

        if(opcion != ''){
            $('#contenido_opciones').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando configuraciones...');
            <?php
            echo jq_remote_function(array('update' => 'contenido_opciones',
            'url' => 'configuracion/opciones',
            'with'=> "'opcion='+opcion",));
            ?>
        } else {
            $('#contenido_opciones').html('<?php echo image_tag('icon/error.png'); ?> Seleccione el tipo de configuracion.');
        }
    }

</script>

<div id="sf_admin_container">
    <h1>Configuraciones SIGLAS RRHH</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

      <div id="sf_admin_content">

        <div class="sf_admin_form">
            <div class="sf_admin_form_row sf_admin_text" style="background-image: url('../images/other/td_fond.png');">
                <div>
                    <label for="correspondencia_n_correspondencia_emisor">Opción</label>
                    <div class="content">
                        <select id="opciones" onchange="javascript:cambiar_opcion()">
                            <option value=""></option>
                            <option value="vacaciones" <?php if(isset($opcion)) if($opcion=='vacaciones') echo 'selected'; ?>>Vacaciones</option>
                            <option value="reposos" <?php if(isset($opcion)) if($opcion=='reposos') echo 'selected'; ?>>Reposos Medicos</option>
                            <option value="permisos" <?php if(isset($opcion)) if($opcion=='permisos') echo 'selected'; ?>>Permisos</option>
                        </select>
                    </div>

                    <div class="help">Seleccione la configuración que desea ver.</div>
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
        'url' => 'configuracion/opciones',
        'with'=> "'opcion='+opcion",));
    ?>
</script>