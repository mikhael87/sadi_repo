<script>
    function subir_unidades(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosCotejarCondicionCargos',
            type:'POST',
            dataType:'html',
            data: $('#form_unidades').serialize(),
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
        <label>Cotejar unidades</label>
        <div class="content">
            <form id="form_unidades">
                <table>
                    <tr>
                        <th>Ubicación</th>
                        <th>Cotejo</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=0; foreach ($unidades_migradas as $unidad_migrada => $registros) { ?>
                    <tr>
                        <td>
                            <?php echo $unidad_migrada; ?>
                            <font class="f10n gris_oscuro"><br/><?php echo $registros; ?> funcionarios detectados en esta unidad</font>
                            <input name="unidades[<?php echo $i; ?>][nombre_actual]" type="hidden" value="<?php echo $unidad_migrada; ?>"/>
                        </td>
                        <td>
                            <select name="unidades[<?php echo $i; ?>][unidad_id]" id="unidad_cotejo_<?php echo $i; ?>">
                                <?php 
                                $unidad_migrada_cotejo = html_entity_decode($unidad_migrada);
                                $unidad_migrada_cotejo = trim($unidad_migrada_cotejo);
                                $especial = array("&nbsp;"," ","-",".",",");
                                $unidad_migrada_cotejo = str_replace($especial, "", $unidad_migrada_cotejo);
                                $unidad_migrada_cotejo = strtolower($unidad_migrada_cotejo);
                                $vowels = array("á", "Á", "à", "À");
                                $unidad_migrada_cotejo = str_replace($vowels, 'a', $unidad_migrada_cotejo);
                                $vowels = array("é", "É", "è", "È");
                                $unidad_migrada_cotejo = str_replace($vowels, 'e', $unidad_migrada_cotejo);
                                $vowels = array("í", "Í", "ì", "Ì");
                                $unidad_migrada_cotejo = str_replace($vowels, 'i', $unidad_migrada_cotejo);
                                $vowels = array("ó", "Ó", "ò", "Ò");
                                $unidad_migrada_cotejo = str_replace($vowels, 'o', $unidad_migrada_cotejo);
                                $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                $unidad_migrada_cotejo = str_replace($vowels, 'u', $unidad_migrada_cotejo);
                                
                                $menor = 10000;
                                foreach( $unidades as $clave=>$valor ) { 
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
                                    
                                    $x = levenshtein($unidad_migrada_cotejo, $valor_cotejo);
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
                            <script>$("#unidad_cotejo_<?php echo $i; ?> option[value='<?php echo $selected ?>']").attr("selected", "selected");</script>
                        </td>
                    </tr>
                <?php $i++; } ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_unidades(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>