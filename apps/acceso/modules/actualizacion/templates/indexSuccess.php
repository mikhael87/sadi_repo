<?php if($ultima_version_descargada != $ultima_version_disponible){ ?>
<script>
    function prepareUpdate(){
        if(confirm('¿Esta seguro de actualizar el SIGLAS en este momento?')){
            $('#div_actualizacion_en_curso').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando SQL a ejecutar...');
            $('#div_history_svn').remove();

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_acceso_url'); ?>actualizacion/prepareSqlUpdate',
                type:'POST',
                dataType:'html',
                success:function(data, textStatus){
                    $('#div_actualizacion_en_curso').html(data);
                }});
        } else {
            return false;
        }
    }
</script>
<?php } ?>

<div id="sf_admin_container">
    <h1>Actualizaciones del SIGLAS</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>

    <div id="sf_admin_content">

        <div class="sf_admin_list trans">
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label for="correspondencia_n_correspondencia_emisor">
                        <?php if($ultima_version_descargada != $ultima_version_disponible){ ?>
                            <img src="/images/other/siglas_update.png" width="80"/>
                        <?php } else { ?>
                            <img src="/images/other/siglas_update_ok.png" width="80"/>
                        <?php } ?>
                    </label>
                    <div id="div_actualizacion_en_curso" style="min-height: 110px;" class="content">
                        <?php if($ultima_version_descargada != $ultima_version_disponible){ ?>
                            <a href="#" title="Actualizar" onclick="prepareUpdate(); return false;" style="text-decoration: none;">
                                <br/>
                                <h1 style="color: #04B404;">Existe una nueva actualizacion. ¡Actualizar aqui!. </h1>
                                <h2>Revision SVN: <?php echo $ultima_version_disponible; ?></h2>
                                <br/>
                            </a>
                        <?php } else { ?>
                            <br/><br/>
                            <h1 style="color: #8f8f73;">El SIGLAS se encuentra actualizado a su ultima version</h1>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div id="div_history_svn" class="sf_admin_form_row sf_admin_text">
                <div>
                    <label for="correspondencia_n_correspondencia_emisor">Historial de actualizaciones</label>
                    <div class="content">
                        <?php 
                            if(count($actualizaciones_svn)==0){
                                echo 'No se han realizado actualizaciones mediante esta herramienta<br/><br/>';
                            } else {
                                foreach ($actualizaciones_svn as $actualizacion_svn) {
                                    echo 'Version: <b style="color: #1C94C4;">'.$actualizacion_svn->getVersionSiglas().'</b><br/>';
                                    echo 'Revision SVN: '.$actualizacion_svn->getRevisionSvn().'<br/>';
                                    echo 'Fecha de actualizacion: <b>'.$actualizacion_svn->getFActualizacion().'</b><br/>';
                                    
                                    echo '<hr/>';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <br/>
        </div>
    </div>
</div>

