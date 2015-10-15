<script>
    function subir_revision_dias_disponibles(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarDiasDisponiblesPreview',
            type:'POST',
            dataType:'html',
            data: $('#form_revision_dias_disponibles').serialize(),
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando datos ...');
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
                    <?php 
                        $class ='';
                        if($dato['cedula_error']!='' || 
                           $dato['periodo_vacacional_error']!='' ||
                           $dato['dias_disponibles_error']!=''){
                                $class='background-color: #FFBBBB;';
                        }
                    ?>
                    <tr id="tr_<?php echo $i; ?>" style="<?php echo $class; ?>">
                        <td>
                            <?php if($dato['cedula_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][cedula]" type="text" size="8" value="<?php echo $dato['cedula']; ?>"/>
                                <div class="error"><?php echo $dato['cedula_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['cedula']; ?>
                                <input name="datos[<?php echo $i; ?>][cedula]" type="hidden" value="<?php echo $dato['cedula']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['periodo_vacacional_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][periodo_vacacional]" type="text" size="8" value="<?php echo $dato['periodo_vacacional']; ?>"/>
                                <div class="error"><?php echo $dato['periodo_vacacional_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['periodo_vacacional']; ?>
                                <input name="datos[<?php echo $i; ?>][periodo_vacacional]" type="hidden" value="<?php echo $dato['periodo_vacacional']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['dias_disponibles_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][dias_disponibles]" type="text" size="8" value="<?php echo $dato['dias_disponibles']; ?>"/>
                                <div class="error"><?php echo $dato['dias_disponibles_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['dias_disponibles']; ?>
                                <input name="datos[<?php echo $i; ?>][dias_disponibles]" type="hidden" value="<?php echo $dato['dias_disponibles']; ?>"/>
                            <?php } ?>
                        </td>
                        <td><a onclick="remover_tr(<?php echo $i; ?>); return false;" href="#"><img src='/images/icon/delete.png'/></a></td>
                    </tr>
                <?php $i++; } ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_revision_dias_disponibles(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>