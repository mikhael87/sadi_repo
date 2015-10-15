<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function() {
        var opcion= $('#opciones').val();
        if(opcion !== '')
            cambiar_opcion();
    });
    
    <?php
//    if($opcion!=null) { 
//        echo "var opcion = ".$opcion;
//        echo jq_remote_function(array('update' => 'contenido_opciones',
//        'url' => 'configuracion/opciones',
//        'with'=> "'opcion='+opcion",));
//    }
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
<br/><br/>
<div id="sf_admin_container">
    <h1>Configuraciones SADI</h1>

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
                            <?php
                            $cadena= '';
//                            //AUTENTICACION
//                            if($sf_user->hasCredential('Root'))
//                                if(($prv && $opcion=='autenticacion') || (!$prv))
//                                    $cadena.= '<option value="autenticacion" '. (($opcion=='autenticacion')? 'selected="selected"' : '') .'>Autenticación</option>';
                            //VARIABLES DE SISTEMA
                            if($sf_user->hasCredential('Root'))
                                if(($prv && $opcion=='variablesSistema') || (!$prv))
                                    $cadena.= '<option value="variablesSistema" '. (($opcion=='variablesSistema')? 'selected="selected"' : '') .'>Variables de Sistema</option>';
                            //CACHE
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='cache') || (!$prv))
//                                    $cadena.= '<option value="cache" '. (($opcion=='cache')? 'selected="selected"' : '') .'>Cache de Sistema</option>';
                             //DATOS BASICOS
                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
                                if(($prv && $opcion=='datosBasicos') || (!$prv))
                                    $cadena.= '<option value="datosBasicos" '. (($opcion=='datosBasicos')? 'selected="selected"' : '') .'>Datos Basicos</option>';
                            //EMAIL
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='email') || (!$prv))
//                                    $cadena.= '<option value="email" '. (($opcion=='email')? 'selected="selected"' : '') .'>Correo Electronico</option>';
                            //FIRMA ELECTRONICA
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='firmaElectronica') || (!$prv))
//                                    $cadena.= '<option value="firmaElectronica" '. (($opcion=='firmaElectronica')? 'selected="selected"' : '') .'>Firma Electronica</option>';
                            //INTEROPERABILIDAD
//                            if($sf_user->hasCredential('Root'))
//                                if(($prv && $opcion=='interoperabilidad') || (!$prv))
//                                    $cadena.= '<option value="interoperabilidad" '. (($opcion=='interoperabilidad')? 'selected="selected"' : '') .'>Interoperabilidad</option>';
                            //GRUPO DE CORRESPONENCIA
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='grupoCorrespondencia') || (!$prv))
//                                    $cadena.= '<option value="grupoCorrespondencia" '. (($opcion=='grupoCorrespondencia')? 'selected="selected"' : '') .'>Grupos de Correspondencia</option>';
                            //CORRELATIVOS
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='correlativo') || (!$prv))
//                                    $cadena.= '<option value="correlativo" '. (($opcion=='correlativo')? 'selected="selected"' : '') .'>Correlativos de Correspondencia</option>';
                            //GRUPO DE ARCHIVO
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='grupoArchivo') || (!$prv))
//                                    $cadena.= '<option value="grupoArchivo" '. (($opcion=='grupoArchivo')? 'selected="selected"' : '') .'>Grupos de Archivo</option>';
                            //SEGURIDAD
                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
                                if(($prv && $opcion=='seguridad') || (!$prv))
                                    $cadena.= '<option value="seguridad" '. (($opcion=='seguridad')? 'selected="selected"' : '') .'>Seguridad</option>';
                            //SUBVERSION
                            if($sf_user->hasCredential('Root'))
                                if(($prv && $opcion=='subversion') || (!$prv))
                                    $cadena.= '<option value="subversion" '. (($opcion=='subversion')? 'selected="selected"' : '') .'>Subversion</option>';
                            //SMS
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='sms') || (!$prv))
//                                    $cadena.= '<option value="sms" '. (($opcion=='sms')? 'selected="selected"' : '') .'>Mensajes de Texto (SMS)</option>';
                            //GPS
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='gps') || (!$prv))
//                                    $cadena.= '<option value="gps" '. (($opcion=='gps')? 'selected="selected"' : '') .'>Tracker GPS</option>';
                            //OGRANISMOS EXTERNOS
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='organismosExternos') || (!$prv))
//                                    $cadena.= '<option value="organismosExternos" '. (($opcion=='organismosExternos')? 'selected="selected"' : '') .'>Organismos Externos</option>';
//                            //TABLAS MAESTRAS
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='tablasMaestras') || (!$prv))
//                                    $cadena.= '<option value="tablasMaestras" '. (($opcion=='tablasMaestras')? 'selected="selected"' : '') .'>Tablas Maestras</option>';
                            //CRONTAB
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='crontab') || (!$prv))
//                                    $cadena.= '<option value="crontab" '. (($opcion=='crontab')? 'selected="selected"' : '') .'>Tareas automaticas (Crontab)</option>';
                            //OFICINAS CLAVE
//                            if($sf_user->hasCredential('Root') || $sf_user->hasCredential('Administrador') || $sf_user->hasCredential('Soporte Técnico'))
//                                if(($prv && $opcion=='oficinasClave') || (!$prv))
//                                    $cadena.= '<option value="oficinasClave" '. (($opcion=='oficinasClave')? 'selected="selected"' : '') .'>Unidades Clave</option>';
                            //TIPO FORMATO
//                            if($sf_user->hasCredential('Root'))
//                                if(($prv && $opcion=='datosBasicos') || (!$prv))
//                                    $cadena.= '<option value="tipoFormato" '. (($opcion=='tipoFormato')? 'selected="selected"' : '') .'>Tipos de Correspondencia</option>';
                            //VARIOS
//                            if($sf_user->hasCredential('Root'))
//                                if(($prv && $opcion=='varios') || (!$prv))
//                                    $cadena.= '<option value="varios" '. (($opcion=='varios')? 'selected="selected"' : '') .'>Varios</option>';
                            
                            echo $cadena;
                            ?>
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
