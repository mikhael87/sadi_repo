<script>
    function subir_tipos(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosCotejarGradoCargos',
            type:'POST',
            dataType:'html',
            data: $('#form_tipos').serialize(),
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
        <label>Cotejar tipo de cargos</label>
        <div class="content">
            <form id="form_tipos">
                <table>
                <?php 
                    foreach ($tipos_migradas as $condicion_id => $tipos_migrados) { ?>
                        <tr>
                            <th><?php echo $condiciones[$condicion_id]; ?></th>
                            <th>Cotejo</th>
                        </tr>
                    <?php
                        $i=0; 
                        foreach ($tipos_migrados as $tipo_migrada => $registros) { 
                    ?>
                        <tr>
                            <td>
                                <?php echo $tipo_migrada; ?>
                                <font class="f10n gris_oscuro"><br/><?php echo $registros; ?> funcionarios detectados con este tipo de cargo</font>
                                <input name="tipos[<?php echo $condicion_id; ?>][<?php echo $i; ?>][nombre_actual]" type="hidden" value="<?php echo $tipo_migrada; ?>"/>
                            </td>
                            <td>
                                <select name="tipos[<?php echo $condicion_id; ?>][<?php echo $i; ?>][tipo_id]" id="tipo_cotejo_<?php echo $condicion_id; ?>_<?php echo $i; ?>">
                                    <?php 
                                    $tipo_migrada_cotejo = html_entity_decode($tipo_migrada);
                                    $tipo_migrada_cotejo = trim($tipo_migrada_cotejo);
                                    $especial = array("&nbsp;"," ","-",".",",");
                                    $tipo_migrada_cotejo = str_replace($especial, "", $tipo_migrada_cotejo);
                                    $tipo_migrada_cotejo = strtolower($tipo_migrada_cotejo);
                                    $vowels = array("á", "Á", "à", "À");
                                    $tipo_migrada_cotejo = str_replace($vowels, 'a', $tipo_migrada_cotejo);
                                    $vowels = array("é", "É", "è", "È");
                                    $tipo_migrada_cotejo = str_replace($vowels, 'e', $tipo_migrada_cotejo);
                                    $vowels = array("í", "Í", "ì", "Ì");
                                    $tipo_migrada_cotejo = str_replace($vowels, 'i', $tipo_migrada_cotejo);
                                    $vowels = array("ó", "Ó", "ò", "Ò");
                                    $tipo_migrada_cotejo = str_replace($vowels, 'o', $tipo_migrada_cotejo);
                                    $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                    $tipo_migrada_cotejo = str_replace($vowels, 'u', $tipo_migrada_cotejo);

                                    $menor = 10000;
                                    foreach($tipos as $condicion_id_db => $tipo_db) { 
                                        if($condicion_id_db == $condicion_id){
                                        foreach($tipo_db as $clave => $valor) { 
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

                                            $x = levenshtein($tipo_migrada_cotejo, $valor_cotejo);
                                            if($x < $menor) {
                                                $menor = $x;
                                                $selected = $clave;
                                            }
                                    ?>
                                        <option value="<?php echo $clave; ?>">
                                            <?php echo html_entity_decode($valor); ?>
                                        </option>
                                    <?php }}} ?>
                                </select>
                                <script>$("#tipo_cotejo_<?php echo $condicion_id; ?>_<?php echo $i; ?> option[value='<?php echo $selected ?>']").attr("selected", "selected");</script>
                            </td>
                        </tr>
                <?php 
                        $i++; 
                        }                    
                    } 
                ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_tipos(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>