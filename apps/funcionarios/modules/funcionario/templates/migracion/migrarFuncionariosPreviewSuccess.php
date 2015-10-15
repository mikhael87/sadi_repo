<script>
    function finalizar_migracion(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosMigrar',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> migrando datos ...');
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
                    <tr>
                        <td>
                            <?php echo $dato['ubicacion'].'<br/><font class="azul">'.$unidades[$dato['unidad_id']].'</font>'; ?>
                        </td>
                        <td>
                            <?php echo $dato['condicion_cargo'].'<br/><font class="azul">'.$condiciones[$dato['condicion_id']].'</font>'; ?>
                        </td>
                        <td>
                            <?php echo $dato['tipo_cargo'].'<br/><font class="azul">'.$tipos[$dato['tipo_id']].'</font>'; ?>
                        </td>
                        <td>
                            <?php echo $dato['grado_cargo'].'<br/><font class="azul">'.$grados[$dato['grado_id']].'</font>'; ?>
                        </td>
                        <td>
                            <?php echo $dato['codigo_empleado']; ?>
                        </td>
                        <td>
                            <?php echo $dato['tipo_contrato'].'<br/><font class="azul">'.$tipos_contrato[$dato['tipo_contrato_id']].'</font>'; ?>
                        </td>
                        <td>
                            <?php echo $dato['cedula']; ?>
                        </td>
                        <td>
                            <?php echo $dato['sexo']; ?>
                        </td>
                        <td>
                            <?php echo $dato['estado_civil']; ?>
                        </td>
                    </tr>
                <?php $i++; } $migrados = $i-1;?>
                </table>
            </form>
        </div>
        <div class="help">Funcionarios a migrar: <?php echo $migrados; ?></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="finalizar_migracion(); return false;" value="Migrar"/></div>
        </div>
    </div>
</div>