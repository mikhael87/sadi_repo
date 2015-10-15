<script>
    function subir_datos_f_ingreso(){
        if($('#archivo_excel_f_ingreso').val()!=''){
            var file = $('#archivo_excel_f_ingreso').val();
            var exts = ['xls','xlsx'];

            var get_ext = file.split('.');
            get_ext = get_ext.reverse();

            if ( $.inArray ( get_ext[0].toLowerCase(), exts ) > -1 ){

                var data = new FormData();
                data.append('archivo_excel',$('#archivo_excel_f_ingreso').get(0).files[0]);


                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarFIngresoRevision',
                    type:'POST',
                    contentType:false,
                    data:data,
                    processData:false,
                    cache:false,
                    beforeSend: function(Obj){
                        $('#div_button_upload_f_ingreso').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando archivo ...');
                    },
                    success:function(data, textStatus){
                        $('#div_prosesar_f_ingreso').html(data);
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
            <input id="archivo_excel_f_ingreso" name="archivo_excel_f_ingreso" type="file" accept=""/>&nbsp;<?php echo image_tag('icon/info', array('class' => 'tooltip', 'title' => '[!]Formato de Hoja Excel[/!]Asegurese que el archivo tenga extensión .xls o .xlsx ::Debe contener dos (2) columnas. :: sin encabezado u otros datos. :: <font style="color: #f8a226">*El tiempo de procesamiento est&aacute; sujeto a la cantidad:: de funcionarios a los que le desee migrar o actualizar la fecha de ingreso simultaneamente.</font>')); ?>
        </div>
        <div class="help">
            El archivo Excel solo debe contener dos (2) columnas en el siguiente orden:<br/>
            1.- <b>CEDULA:</b> cedula de los funcionarios a los que se le actualizara la fecha de ingreso.<br/>
            2.- <b>FECHA DE INGRESO:</b> fecha en la cual el funcionario ingresó a la institución en formato día-mes-año (DD-MM-AAA) ¡¡¡ASEGURESE DE QUE SOLO TENGA GUIONES!!!<br/>
        </div>
        <br/>
        <div class="content">
            <div id="div_button_upload_f_ingreso"><input type="button" onclick="subir_datos_f_ingreso(); return false;" value="Siguiente"/></div>
        </div>

    </div>
</div>