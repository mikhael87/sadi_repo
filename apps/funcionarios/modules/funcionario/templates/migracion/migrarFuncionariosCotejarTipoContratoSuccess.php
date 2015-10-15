<script>
    function subir_tipos_contrato(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosPreview',
            type:'POST',
            dataType:'html',
            data: $('#form_tipos_contrato').serialize(),
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
        <label>Cotejar tipos de contrato de los cargos</label>
        <div class="content">
            <form id="form_tipos_contrato">
                <table>
                    <tr>
                        <th>Tipos de contrato</th>
                        <th>Cotejo</th>
                        <th>&nbsp;</th>
                    </tr>
                <?php $i=0; foreach ($tipos_contrato_migradas as $tipo_contrato_migrada => $registros) { ?>
                    <tr>
                        <td>
                            <?php echo $tipo_contrato_migrada; ?>
                            <font class="f10n gris_oscuro"><br/><?php echo $registros; ?> funcionarios detectados con este tipo de contrato</font>
                            <input name="tipos_contrato[<?php echo $i; ?>][nombre_actual]" type="hidden" value="<?php echo $tipo_contrato_migrada; ?>"/>
                        </td>
                        <td>
                            <select name="tipos_contrato[<?php echo $i; ?>][tipo_contrato_id]" id="tipo_contrato_cotejo_<?php echo $i; ?>">
                                <?php 
                                $tipo_contrato_migrada_cotejo = html_entity_decode($tipo_contrato_migrada);
                                $tipo_contrato_migrada_cotejo = trim($tipo_contrato_migrada_cotejo);
                                $especial = array("&nbsp;"," ","-",".",",");
                                $tipo_contrato_migrada_cotejo = str_replace($especial, "", $tipo_contrato_migrada_cotejo);
                                $tipo_contrato_migrada_cotejo = strtolower($tipo_contrato_migrada_cotejo);
                                $vowels = array("á", "Á", "à", "À");
                                $tipo_contrato_migrada_cotejo = str_replace($vowels, 'a', $tipo_contrato_migrada_cotejo);
                                $vowels = array("é", "É", "è", "È");
                                $tipo_contrato_migrada_cotejo = str_replace($vowels, 'e', $tipo_contrato_migrada_cotejo);
                                $vowels = array("í", "Í", "ì", "Ì");
                                $tipo_contrato_migrada_cotejo = str_replace($vowels, 'i', $tipo_contrato_migrada_cotejo);
                                $vowels = array("ó", "Ó", "ò", "Ò");
                                $tipo_contrato_migrada_cotejo = str_replace($vowels, 'o', $tipo_contrato_migrada_cotejo);
                                $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                $tipo_contrato_migrada_cotejo = str_replace($vowels, 'u', $tipo_contrato_migrada_cotejo);
                                
                                $menor = 10000;
                                foreach($tipos_contrato as $tipo_contrato) { 
                                    $clave = $tipo_contrato->getId();
                                    $valor = $tipo_contrato->getNombre();
                                    
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
                                    
                                    $x = levenshtein($tipo_contrato_migrada_cotejo, $valor_cotejo);
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
                            <script>$("#tipo_contrato_cotejo_<?php echo $i; ?> option[value='<?php echo $selected ?>']").attr("selected", "selected");</script>
                        </td>
                    </tr>
                <?php $i++; } ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_tipos_contrato(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>