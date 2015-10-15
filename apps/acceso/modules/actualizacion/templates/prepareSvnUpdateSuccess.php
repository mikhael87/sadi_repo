<script>
    function executeSvnUpdate(id){
        if(confirm('¿Esta seguro de actualizar el SIGLAS?')){
            $('#div_svn_update').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Descargando actualizacion...');

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/playSvnUpdate',
                type:'POST',
                dataType:'html',
                success:function(data, textStatus){
                    $('#div_svn_update').html(data);
                    $('#svn_actions_execute').hide();
                    $('#title_paso_dos').hide();
                    $('#regresar_inicio').show();
                }});
        } else {
            return false;
        }
    }
</script>

<div>
    <h2 id="title_paso_dos">Paso 2: Actualizacion del sistema de archivos</h2>
    <a id="regresar_inicio" style="display: none;" href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/index">
        <?php echo image_tag('icon/back.png'); ?>&nbsp;Regresar al inicio
    </a>

    <?php if(count($lista_cambios)>0) { ?>
        <div id="div_svn_update">
            <table>
                <tr>
                    <th colspan="2">Cambios disponibles</th>
                    <a id="svn_actions_execute" href="#" style="text-decoration: none;" onclick="executeSvnUpdate(); return false;" >
                        <?php echo image_tag('icon/execute.png'); ?>&nbsp;<font class="f19b" style="color: #0F75D1;">¡Actualizar el SIGLAS ahora!.</font>
                    </a>
                    <br/><hr/><br/><br/>
                </tr>
                <?php foreach($lista_cambios as $cambios) { ?>
                    <tr>
                        <td>
                            <?php
                                echo '<b style="color: #1c94c4;">Revision: '.$cambios['rev'].'</b><br/>'; 
                                echo 'fecha: '.date('d-m-Y h:m a', strtotime($cambios['date'])).'<br/>'; 
                                echo '<i style="color: #939090;">'.$cambios['msg'].'</i>'; 
                                if($cambios['msg']!='')
                                    echo '<br/>';
                            ?>
                            <br/>
                            <div id="sql_result_<?php echo $key; ?>" style="max-height: 200px; width: 900px; overflow: auto; background-color: #CACACA;">
                                <?php 
                                foreach($cambios['paths'] as $cambio) { 
                                    switch ($cambio['action']) {
                                        case 'A':
                                            $color = 'green';
                                            break;
                                        case 'M':
                                            $color = 'blue';
                                            break;
                                        case 'D':
                                            $color = 'red';
                                            break;
                                    }
                                    echo '<b style="color: '.$color.';">'.$cambio['action'].'</b> -> '; 
                                    echo $cambio['path'].'<br/>'; 
                                }
                                ?>
                            </div>
                        </td>
                        <td>

                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } else { ?>
        <b style="color: #1c94c4;">El sistema de archivos se encuentra ya en su ultima version.</b><br/><br/>
        <a href="<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/index" style="text-decoration: none;">
            <?php echo image_tag('icon/regresar.png'); ?> Volver al inicio</b>.
        </a>
    <?php } ?>
</div>