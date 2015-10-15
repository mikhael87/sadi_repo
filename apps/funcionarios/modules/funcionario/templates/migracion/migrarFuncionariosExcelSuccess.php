<script>
    function subir_datos(){
        if($('#archivo_excel').val()!=''){
            var file = $('#archivo_excel').val();
            var exts = ['xls','xlsx'];

            var get_ext = file.split('.');
            get_ext = get_ext.reverse();

            if ( $.inArray ( get_ext[0].toLowerCase(), exts ) > -1 ){

                var data = new FormData();
                data.append('archivo_excel',$('#archivo_excel').get(0).files[0]);


                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosRevision',
                    type:'POST',
                    contentType:false,
                    data:data,
                    processData:false,
                    cache:false,
                    beforeSend: function(Obj){
                        $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando archivo ...');
                    },
                    success:function(data, textStatus){
                        $('#div_prosesar').html(data);
                        reiniciar_pasos(2);
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
            <input id="archivo_excel" name="archivo_excel" type="file" id="archivo" accept=""/>&nbsp;<?php echo image_tag('icon/info', array('class' => 'tooltip', 'title' => '[!]Formato de Hoja Excel[/!]Asegurese que el archivo tenga extensión .xls o .xlsx ::Debe contener siete (7) columnas. :: sin encabezado u otros datos. :: <font style="color: #f8a226">*El tiempo de procesamiento est&aacute; sujeto a la cantidad:: de funcionarios que desee migrar simultaneamente.</font>')); ?>
        </div>
        <div class="help">
            El archivo Excel solo debe contener siete (7) columnas en el siguiente orden:<br/>
            1.- <b>UBICACION:</b> nombre de la unidad administrativa a la que pertenece el funcionario.<br/>
            2.- <b>CONDICION DEL CARGO:</b> condición del contrato del trabajador (fijo, contratado, obrero, asesor, directivo, etc.)<br/>
            3.- <b>TIPO DE CARGO:</b> Tipo de cargo que desempeña el trabajador. (Bachiller I, bachiller II, Técnico, Profesional, Director, etc.)<br/>
            4.- <b>GRADO DEL CARGO:</b> Jerarquia del cargo que desempeña el funcionario. (número entero)<br/>
            5.- <b>CODIGO DE EMPLEADO:</b> numero de identificación del funcionario en la nomina, si no posee este numero coloque 0 (cero).<br/>
            6.- <b>CEDULA:</b> cédula de identidad del nuevo funcionario.<br/>
            7.- <b>SEXO:</b> sexo (M=masculino, F=femenino)<br/>
            8.- <b>ESTADO CIVIL:</b> estado civil del funcionario (S=soltero, C=casado, D=divorciado, V=viudo)<br/>
        </div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_datos(); return false;" value="Siguiente"/></div>
        </div>

    </div>
</div>