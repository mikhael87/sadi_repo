<script>
    function subir_revision_f_ingreso(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarFIngresoPreview',
            type:'POST',
            dataType:'html',
            data: $('#form_revision_f_ingreso').serialize(),
            beforeSend: function(Obj){
                $('#div_button_upload_f_ingreso').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando datos ...');
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
                        <th>Cedula</th>
                        <th>Fecha de ingreso</th>
                        <th>Funcionario</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=1; foreach ($datos as $dato) { ?>
                    <?php 
                        $class ='';
                        if($dato['cedula_error']!='' || 
                           $dato['f_ingreso_error']!=''){
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
                            <?php if($dato['f_ingreso_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][f_ingreso]" type="text" size="8" value="<?php echo $dato['f_ingreso']; ?>"/>
                                <div class="error"><?php echo $dato['f_ingreso_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['f_ingreso']; ?>
                                <input name="datos[<?php echo $i; ?>][f_ingreso]" type="hidden" value="<?php echo $dato['f_ingreso']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if(isset($dato['nombre_apellido'])){ ?>
                                <?php echo $dato['nombre_apellido']; ?>
                                <input name="datos[<?php echo $i; ?>][nombre_apellido]" type="hidden" value="<?php echo $dato['nombre_apellido']; ?>"/>
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
            <div id="div_button_upload_f_ingreso"><input type="button" onclick="subir_revision_f_ingreso(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>