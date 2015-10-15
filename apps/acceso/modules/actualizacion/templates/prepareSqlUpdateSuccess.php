<script>
    var sql_totales = <?php echo count($filenames); ?>;
    var sql_procesados = 0;
    
    function executeSqlUpdate(id){
        if(confirm('¿Esta seguro de ejecutar el SQL?')){
            $('#sql_ejecutado_'+id).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25', 'title'=>'Ejecutando')); ?>');
            $('#sql_actions_execute_'+id).hide();
            $('#sql_actions_cancel_'+id).hide();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/playSqlUpdate',
                type:'POST',
                dataType:'html',
                data: '&sql_id='+id,
                success:function(data, textStatus){
                    $('#sql_result_'+id).prepend(data);
                    $('#sql_ejecutado_'+id).html('');

                    if(sql_procesados == sql_totales){
                        openSvnUpdate();
                    }
                }});
        } else {
            return false;
        }
    }
    
    function cancelSqlUpdate(id){
        if(confirm('¿Esta seguro de NO ejecutar el SQL?')){
            $('#sql_ejecutado_'+id).html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25', 'title'=>'Cancelando')); ?>');
            $('#sql_actions_execute_'+id).hide();
            $('#sql_actions_cancel_'+id).hide();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/cancelSqlUpdate',
                type:'POST',
                dataType:'html',
                data: '&sql_id='+id,
                success:function(data, textStatus){
                    $('#sql_result_'+id).prepend(data);
                    $('#sql_ejecutado_'+id).html('');

                    if(sql_procesados == sql_totales){
                        openSvnUpdate();
                    }
                }});
        } else {
            return false;
        }
    }
    
    function openSvnUpdate(){
        var cadena = '<b style="color: #1c94c4;">La base de datos se encuentra ya en su ultima version.</b><br/><br/>'+
                     '<a href="#" style="text-decoration: none;" onclick="prepareSvnUpdate(); return false;" >'+
                         '<?php echo image_tag('icon/2execute.png'); ?> Continuar con el <b>Paso 2</b>.'+
                     '</a>';
    
        $('#div_paso_uno').html(cadena);
    }
    
    function prepareSvnUpdate(){
        $('#div_actualizacion_en_curso').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Obteniendo listado de cambios disponibles...');

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/prepareSvnUpdate',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#div_actualizacion_en_curso').html(data);
            }});
    }
</script>

<div>
    <h2>Paso 1: Actualizacion de Base de Datos</h2>
    <div id="div_paso_uno">
        <?php if(count($filenames)>0) { ?>
            <table>
                <tr>
                    <th style="width: 900px;">Archivo SQL</th>
                    <th>Acciones</th>
                </tr>
                <?php $display = 'block';
                    foreach($filenames as $key => $filename) { ?>
                    <tr id="tr_sql_<?php echo $key; ?>">
                        <td>
                            <b style="color: #1c94c4;"><?php echo $filename; ?></b><hr/>
                            <div id="sql_result_<?php echo $key; ?>" style="max-height: 200px; max-width: 900px; overflow: auto; background-color: #CACACA;">
                                <pre><?php echo file_get_contents(sfConfig::get("sf_root_dir") . '/data/cambiosBD/' . $filename); ?></pre>
                            </div>
                        </td>
                        <td>
                            <div id="action_orden_<?php echo $key; ?>" style="display: <?php echo $display; ?>;">
                                <a id="sql_actions_execute_<?php echo $key; ?>" href="#" style="text-decoration: none;" title="Ejecutar" onclick="executeSqlUpdate(<?php echo $key; ?>); return false;" >
                                    <?php echo image_tag('icon/execute.png'); ?>
                                </a>
                                <a id="sql_actions_cancel_<?php echo $key; ?>" href="#" style="text-decoration: none;" title="No ejecutar este query" onclick="cancelSqlUpdate(<?php echo $key; ?>); return false;" >
                                    <?php echo image_tag('icon/stop.png'); ?>
                                </a>
                            </div>
                            <div id="sql_ejecutado_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                <?php $display= 'none'; } ?>
            </table>
        <?php } else { ?>
            <b style="color: #1c94c4;">La base de datos se encuentra ya en su ultima version.</b><br/><br/>
            <a href="#" style="text-decoration: none;" onclick="prepareSvnUpdate(); return false;" >
                <?php echo image_tag('icon/2execute.png'); ?> Continuar con el <b>Paso 2</b>.
            </a>
        <?php } ?>
    </div>
</div>