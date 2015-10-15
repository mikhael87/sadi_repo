<script>
    function subir_condiciones(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosCotejarTipoCargos',
            type:'POST',
            dataType:'html',
            data: $('#form_condiciones').serialize(),
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> procesando datos ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(4);
            }});
    }
</script>
    
<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>
<div class="sf_admin_form_row">
    <div>
        <label>Cotejar condiciones de los cargos</label>
        <div class="content">
            <form id="form_condiciones">
                <table>
                    <tr>
                        <th>Condiciones de cargo</th>
                        <th>Cotejo</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=0; foreach ($condiciones_migradas as $condicion_migrada => $registros) { ?>
                    <tr>
                        <td>
                            <?php echo $condicion_migrada; ?>
                            <font class="f10n gris_oscuro"><br/><?php echo $registros; ?> funcionarios detectados en esta condición de cargo</font>
                            <input name="condiciones[<?php echo $i; ?>][nombre_actual]" type="hidden" value="<?php echo $condicion_migrada; ?>"/>
                        </td>
                        <td>
                            <select name="condiciones[<?php echo $i; ?>][condicion_id]" id="condicion_cotejo_<?php echo $i; ?>">
                                <?php 
                                $condicion_migrada_cotejo = html_entity_decode($condicion_migrada);
                                $condicion_migrada_cotejo = trim($condicion_migrada_cotejo);
                                $especial = array("&nbsp;"," ","-",".",",");
                                $condicion_migrada_cotejo = str_replace($especial, "", $condicion_migrada_cotejo);
                                $condicion_migrada_cotejo = strtolower($condicion_migrada_cotejo);
                                $vowels = array("á", "Á", "à", "À");
                                $condicion_migrada_cotejo = str_replace($vowels, 'a', $condicion_migrada_cotejo);
                                $vowels = array("é", "É", "è", "È");
                                $condicion_migrada_cotejo = str_replace($vowels, 'e', $condicion_migrada_cotejo);
                                $vowels = array("í", "Í", "ì", "Ì");
                                $condicion_migrada_cotejo = str_replace($vowels, 'i', $condicion_migrada_cotejo);
                                $vowels = array("ó", "Ó", "ò", "Ò");
                                $condicion_migrada_cotejo = str_replace($vowels, 'o', $condicion_migrada_cotejo);
                                $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                $condicion_migrada_cotejo = str_replace($vowels, 'u', $condicion_migrada_cotejo);
                                
                                $menor = 10000;
                                foreach($condiciones as $condicion) { 
                                    $clave = $condicion->getId();
                                    $valor = $condicion->getNombre();
                                    
                                    $valor_cotejo = html_entity_decode($valor);
                                    $valor_cotejo = trim($valor_cotejo);
                                    $especial = array("&nbsp;"," ","-",".",",");
                                    $valor_cotejo = str_replace($especial, "", $valor_cotejo);
                                    $valor_cotejo = strtolower($valor_cotejo);
                                    $vowels = array("á", "Á", "à", "À");
                                    $valor_cotejo = str_replace($vowels, 'a', $valor_cotejo);
                                    $vowels = array("é", "É", "è", "È");
                                    $valor_cotejo = str_replace($vowels, 'e', $valor_cotejo);
                                    $vowels = array("í", "Í", "ì", "Ì");
                                    $valor_cotejo = str_replace($vowels, 'i', $valor_cotejo);
                                    $vowels = array("ó", "Ó", "ò", "Ò");
                                    $valor_cotejo = str_replace($vowels, 'o', $valor_cotejo);
                                    $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                    $valor_cotejo = str_replace($vowels, 'u', $valor_cotejo);
                                    
                                    $x = levenshtein($condicion_migrada_cotejo, $valor_cotejo);
                                    if($x < $menor) {
                                        $menor = $x;
                                        $selected = $clave;
                                    }
                                ?>
                                    <option value="<?php echo $clave; ?>">
                                        <?php echo html_entity_decode($valor); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <script>$("#condicion_cotejo_<?php echo $i; ?> option[value='<?php echo $selected ?>']").attr("selected", "selected");</script>
                        </td>
                    </tr>
                <?php $i++; } ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_condiciones(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>