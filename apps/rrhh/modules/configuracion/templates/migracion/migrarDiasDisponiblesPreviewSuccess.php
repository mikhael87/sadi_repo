<script>
    function finalizar_migracion_dias_disponibles(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarDiasDisponiblesMigrar',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> migrando datos ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar_dias_disponibles').html(data);
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
            <form id="form_revision_dias_disponibles">
                <table>
                    <tr>
                        <th>Cedula</th>
                        <th>Período Vacacional</th>
                        <th>Días Disponibles</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=1; foreach ($datos as $dato) { ?>
                    <tr>
                        <td>
                            <?php echo $dato['cedula']; ?>
                        </td>
                        <td>
                            <?php echo $dato['periodo_vacacional']; ?>
                        </td>
                        <td>
                            <?php echo $dato['dias_disponibles']; ?>
                        </td>
                    </tr>
                <?php $i++; } $migrados = $i-1;?>
                </table>
            </form>
        </div>
        <div class="help">Dias disponibles a migrar: <?php echo $migrados; ?></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="finalizar_migracion_dias_disponibles(); return false;" value="Migrar"/></div>
        </div>
    </div>
</div>