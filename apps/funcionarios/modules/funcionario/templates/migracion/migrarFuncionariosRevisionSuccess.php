<script>
    function subir_revision(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosCotejarUnidades',
            type:'POST',
            dataType:'html',
            data: $('#form_revision').serialize(),
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando datos ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(3);
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
            <form id="form_revision">
                <table>
                    <tr>
                        <th>Ubicación</th>
                        <th>Condición del Cargo</th>
                        <th>Tipo de Cargo</th>
                        <th>Grado del Cargo</th>
                        <th>Código del cargo</th>
                        <th>Tipo de Contrato</th>
                        <th>Cédula</th>
                        <th>Sexo</th>
                        <th>Estado civil</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=1; foreach ($datos as $dato) { ?>
                    <?php 
                        $class ='';
                        if($dato['ubicacion_error']!='' || 
                           $dato['condicion_cargo_error']!='' || 
                           $dato['tipo_cargo_error']!='' || 
                           $dato['grado_cargo_error']!='' || 
                           $dato['codigo_empleado_error']!='' || 
                           $dato['tipo_contrato_error']!='' || 
                           $dato['cedula_error']!='' || 
                           $dato['sexo_error']!='' || 
                           $dato['estado_civil_error']!=''){
                                $class='background-color: #FFBBBB;';
                        }
                    ?>
                    <tr id="tr_<?php echo $i; ?>" style="<?php echo $class; ?>">
                        <td>
                            <?php if($dato['ubicacion_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][ubicacion]" type="text" value="<?php echo $dato['ubicacion']; ?>"/>
                                <div class="error"><?php echo $dato['ubicacion_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['ubicacion']; ?>
                                <input name="datos[<?php echo $i; ?>][ubicacion]" type="hidden" value="<?php echo $dato['ubicacion']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['condicion_cargo_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][condicion_cargo]" type="text" value="<?php echo $dato['condicion_cargo']; ?>"/>
                                <div class="error"><?php echo $dato['condicion_cargo_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['condicion_cargo']; ?>
                                <input name="datos[<?php echo $i; ?>][condicion_cargo]" type="hidden" value="<?php echo $dato['condicion_cargo']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['tipo_cargo_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][tipo_cargo]" type="text" value="<?php echo $dato['tipo_cargo']; ?>"/>
                                <div class="error"><?php echo $dato['tipo_cargo_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['tipo_cargo']; ?>
                                <input name="datos[<?php echo $i; ?>][tipo_cargo]" type="hidden" value="<?php echo $dato['tipo_cargo']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['grado_cargo_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][grado_cargo]" type="text" size="5" value="<?php echo $dato['grado_cargo']; ?>"/>
                                <div class="error"><?php echo $dato['grado_cargo_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['grado_cargo']; ?>
                                <input name="datos[<?php echo $i; ?>][grado_cargo]" type="hidden" value="<?php echo $dato['grado_cargo']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['codigo_empleado_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][codigo_empleado]" type="text" size="5" value="<?php echo $dato['codigo_empleado']; ?>"/>
                                <div class="error"><?php echo $dato['codigo_empleado_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['codigo_empleado']; ?>
                                <input name="datos[<?php echo $i; ?>][codigo_empleado]" type="hidden" value="<?php echo $dato['codigo_empleado']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['tipo_contrato_error']!=''){ ?>
                                <input name="datos[<?php echo $i; ?>][tipo_contrato]" type="text" size="5" value="<?php echo $dato['tipo_contrato']; ?>"/>
                                <div class="error"><?php echo $dato['tipo_contrato_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['tipo_contrato']; ?>
                                <input name="datos[<?php echo $i; ?>][tipo_contrato]" type="hidden" value="<?php echo $dato['tipo_contrato']; ?>"/>
                            <?php } ?>
                        </td>
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
                            <?php if($dato['sexo_error']!=''){ ?>
                                <select name="datos[<?php echo $i; ?>][sexo]">
                                    <option value=""></option>
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                </select>
                                <div class="error"><?php echo $dato['sexo_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['sexo']; ?>
                                <input name="datos[<?php echo $i; ?>][sexo]" type="hidden" value="<?php echo $dato['sexo']; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($dato['estado_civil_error']!=''){ ?>
                                <select name="datos[<?php echo $i; ?>][estado_civil]">
                                    <option value=""></option>
                                    <option value="S">Soltero(a)</option>
                                    <option value="C">Casado(a)</option>
                                    <option value="D">Divorciado(a)</option>
                                    <option value="V">Viudo(a)</option>
                                </select>
                                <div class="error"><?php echo $dato['estado_civil_error']; ?></div>
                            <?php } else { ?>
                                <?php echo $dato['estado_civil']; ?>
                                <input name="datos[<?php echo $i; ?>][estado_civil]" type="hidden" value="<?php echo $dato['estado_civil']; ?>"/>
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
            <div id="div_button_upload"><input type="button" onclick="subir_revision(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>