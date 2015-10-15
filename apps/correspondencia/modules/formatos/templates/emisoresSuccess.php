<?php use_helper('jQuery');?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE); ?>
<script type="text/javascript" src="/js/jqueryTooltip.js"></script>
<script>
    $(document).ready(function() {
        <?php if($avance_vb == 0) : ?>
        findVistoBueno('<?php echo $unidad_firmante ?>', 'false');
        <?php endif; ?>
    });
</script>
<fieldset id="sf_fieldset_emisores">
    <h2>Emisores</h2>

    <?php
        if(count($emisores[0])>0) {
            $i=0;
            foreach ($emisores as $emisor) {
        ?>

            <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_emisores">
                <div>
                    <label for="correspondencia[emisor][funcionario_id]"><?php echo $nombre_firma[$i]; ?></label>
                    <div class="content">
                        <div id="div_emisores_funcionarios" style="height: 25px">
                            <select style="float: left" name="correspondencia[emisor][<?php echo $i; ?>][funcionario_id]" id="correspondencia_emisor_funcionario_id" onChange="javascript: findVistoBueno('<?php echo $unidad_firmante ?>', '<?php echo ((isset($correspondencia['emisor']) ? (isset($correspondencia['emisor'][$i]) ? (isset($correspondencia['emisor'][$i]['funcionario_id']) ? $correspondencia['emisor'][$i]['funcionario_id'] : 'false') : 'false') : 'false')) ?>');" >
                                <?php if(count($emisor)>1) { ?>
                                    <option value=""><- Seleccione -></option>
                                <?php } ?>
                                <?php foreach ($emisor as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php
                                                        if (isset($correspondencia['emisor'])) {
                                                            if(isset($correspondencia['emisor'][$i])) {
                                                                if(isset($correspondencia['emisor'][$i]['funcionario_id'])) {
                                                                    if ($correspondencia['emisor'][$i]['funcionario_id']==$key)
                                                                        echo "selected";
                                                                }
                                                            }
                                                        }
                                    ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>

                            <div id="div_icon_vb" style="position: relative; float: left; clear: right;padding-left: 10px; padding-top: 5px"></div>
                        </div>
                        <!--LISTA DE VISTOS BUENO-->
                        <div id="div_visto_bueno" style="display: none; display: inline-block; padding-top: 5px"></div>
                        
                        <!--LISTA DE VISTOS BUENO ABSOLUTOS-->
                        <div id="div_visto_bueno_ab" style="display: none; position: absolute; padding: 6px; border-radius: 4px 4px 4px 4px; background-color: #e9e9e9; z-index: 998; border:2px solid; border-color: black; box-shadow: #666 0.4em 0.5em 2em;"><div id="div_visto_bueno_ab_div"></div></div>

                        <!--TRIGGER DE VISTO BUENO-->
                        <input type="hidden" id="trigger_vb" name="vistobuenos[act]" value="<?php echo (($sf_user->getAttribute('correspondencia')) ? 'A' : 'N') ?>" />

                        <!--AGREGACION DE VISTO BUENO-->
                        <div id="div_unidad_funcionario_vb" style="display: none; padding: 5px; background-color: #e9e9e9; border-radius: 9px; border: 2px #cfcfcf solid;">
                            <div id="div_vistobueno_unidad">
                                <select name="vistobueno_unidad" id="vistobueno_unidad" onChange="
                                    <?php
                                    echo jq_remote_function(array('update' => 'div_vistobueno_funcionario',
                                                            'url' => sfConfig::get('sf_app_correspondencia_url').'formatos/vistobuenoUnidades',
                                                            'with' => "'unidad_id=' +this.value",))
                                    ?>"><option value="0"><-Seleccione unidad-></option><?php
                                    foreach ($unidades as $clave => $valor) {
                                        $list_id = explode("&&", $clave); ?>
                                        <option value="<?php echo $list_id[0]; ?>">
                                            <?php echo $valor; ?>
                                        </option>
                                        <?php } ?>
                                </select>
                            </div>

                            <div id="div_vistobueno_funcionario">
                                <select name="vistobueno_funcionario" id="vistobueno_funcionario">
                                    <option value="">sin funcionarios</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            $i++;
            }
        } else {
    ?>
            <div class="sf_admin_form_row sf_admin_date sf_admin_form_field_emisores">
                <div>
                    <label for="correspondencia[emisor][funcionario_id]"><?php echo $nombre_firma[0]; ?></label>
                    <div class="content">
                        <div id="div_emisores_funcionarios">
                            <input type="hidden" id="correspondencia_emisor_funcionario_id" name="correspondencia[emisor][0][funcionario_id]"/>
                            <div class="error">
                                No se han definido firmantes validos en esta unidad para este tipo de documento.<br/><br/>
                                Unicamente pueden firmar:<br/>
                                <?php echo html_entity_decode($tipos_emisores); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php } ?>

</fieldset>
