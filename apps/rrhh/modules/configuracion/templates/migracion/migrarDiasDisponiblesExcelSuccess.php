<script>
    function subir_datos_dias_disponibles(){
        if($('#archivo_excel_dias_disponibles').val()!=''){
            var file = $('#archivo_excel_dias_disponibles').val();
            var exts = ['xls','xlsx'];

            var get_ext = file.split('.');
            get_ext = get_ext.reverse();

            if ( $.inArray ( get_ext[0].toLowerCase(), exts ) > -1 ){

                var data = new FormData();
                data.append('archivo_excel',$('#archivo_excel_dias_disponibles').get(0).files[0]);


                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarDiasDisponiblesRevision',
                    type:'POST',
                    contentType:false,
                    data:data,
                    processData:false,
                    cache:false,
                    beforeSend: function(Obj){
                        $('#div_button_upload_dias_disponibles').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando archivo ...');
                    },
                    success:function(data, textStatus){
                        $('#div_prosesar_dias_disponibles').html(data);
                    }
                });

            } else {
              alert( '¡Archivo Invalido!' );
            }
        } else {
            alert('Seleccione una hoja de calculo para migrar');
        }
    }
</script>
    
<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
<div class="sf_admin_form_row">
    <div>
        <label>Archivo Excel</label>
        <div class="content">
            <ul class="sf_admin_actions">
                <li class="sf_admin_action_excel">
                    <a href="configuracion/precargarDiasDisponiblesExcel">Descargar formato de excel precargado</a>
                </li>
            </ul>
            <input id="archivo_excel_dias_disponibles" name="archivo_excel_dias_disponibles" type="file" accept=""/>&nbsp;<?php echo image_tag('icon/info', array('class' => 'tooltip', 'title' => '[!]Formato de Hoja Excel[/!]Asegurese que el archivo tenga extensión .xls o .xlsx ::Debe contener tres (3) columnas. :: sin encabezado u otros datos. :: <font style="color: #f8a226">*El tiempo de procesamiento est&aacute; sujeto a la cantidad:: de funcionarios a los que le desee migrar o actualizar los dias disponibles simultaneamente.</font>')); ?>
        </div>
        <div class="help">
            El archivo Excel solo debe contener tres (3) columnas en el siguiente orden:<br/>
            1.- <b>CEDULA:</b> cedula de los funcionarios a los que se le actualizara la fecha de ingreso.<br/>
            2.- <b>PERIODO VACACIONAL:</b> años del período vacacional separado por guion (-) con el año completo. Ejemplo: 2013-2014 (AAAA-AAAA)<br/>
            3.- <b>DIAS DISPONIBLES:</b> cantidad de dias disponibles del período vacacional nombrado en la columna anterior. solo se permiten numers enteros.<br/>
        </div>
        <br/>
        <div class="content">
            <div id="div_button_upload_dias_disponibles"><input type="button" onclick="subir_datos_dias_disponibles(); return false;" value="Siguiente"/></div>
        </div>

    </div>
</div>