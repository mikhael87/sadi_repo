<script>
    function finalizar_migracion_f_ingreso(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarFIngresoMigrar',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload_f_ingreso').html('<?php echo image_tag('icon/cargando.gif'); ?> migrando datos ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar_f_ingreso').html(data);
            }});
    }
</script>
    
<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
<div class="sf_admin_form_row">
    <div>
        <label>Datos Recuperados</label>
        <div class="content">
            <form id="form_revision_f_ingreso">
                <table>
                    <tr>
                        <th>Cédula</th>
                        <th>Fecha ingreso</th>
                        <th>Funcionario</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=1; foreach ($datos as $dato) { ?>
                    <tr>
                        <td>
                            <?php echo $dato['cedula']; ?>
                        </td>
                        <td>
                            <?php echo $dato['f_ingreso']; ?>
                        </td>
                        <td>
                            <?php echo $dato['nombre_apellido']; ?>
                        </td>
                    </tr>
                <?php $i++; } $migrados = $i-1;?>
                </table>
            </form>
        </div>
        <div class="help">Fechas de ingreso a migrar: <?php echo $migrados; ?></div>
        <br/>
        <div class="content">
            <div id="div_button_upload_f_ingreso"><input type="button" onclick="finalizar_migracion_f_ingreso(); return false;" value="Migrar"/></div>
        </div>
    </div>
</div>