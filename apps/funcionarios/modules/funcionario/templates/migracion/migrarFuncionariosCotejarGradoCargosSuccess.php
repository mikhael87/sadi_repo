<script>
    function subir_grados(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosCotejarTipoContrato',
            type:'POST',
            dataType:'html',
            data: $('#form_grados').serialize(),
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
            <form id="form_grados">
                <table>
                <?php 
                    foreach ($grados_migradas as $condicion_id => $condiciones_migradas) { ?>
                        <tr>
                            <th><?php echo $condiciones[$condicion_id]; ?></th>
                            <th>Cotejo</th>
                        </tr>
                    <?php
                        foreach ($condiciones_migradas as $tipo_id => $tipos_migradas) {
                            $i=0; 
                            foreach ($tipos_migradas as $nombre_grado => $registros) { 
                    ?>
                            <tr>
                                <td>
                                    <?php echo $tipos[$tipo_id].' / '.$nombre_grado; ?>
                                    <font class="f10n gris_oscuro"><br/><?php echo $registros; ?> funcionarios detectados con este tipo de cargo</font>
                                    <input name="grados[<?php echo $condicion_id; ?>][<?php echo $tipo_id; ?>][<?php echo $i; ?>][nombre_actual]" type="hidden" value="<?php echo $nombre_grado; ?>"/>
                                </td>
                                <td>
                                    <select name="grados[<?php echo $condicion_id; ?>][<?php echo $tipo_id; ?>][<?php echo $i; ?>][grado_id]" id="grado_cotejo_<?php echo $condicion_id; ?>_<?php echo $tipo_id; ?>_<?php echo $i; ?>">
                                        <?php 
                                        $grado_migrada_cotejo = html_entity_decode($nombre_grado);
                                        $grado_migrada_cotejo = trim($grado_migrada_cotejo);
                                        $especial = array("&nbsp;"," ","-",".",",");
                                        $grado_migrada_cotejo = str_replace($especial, "", $grado_migrada_cotejo);
                                        $grado_migrada_cotejo = strtolower($grado_migrada_cotejo);
                                        $vowels = array("á", "Á", "à", "À");
                                        $grado_migrada_cotejo = str_replace($vowels, 'a', $grado_migrada_cotejo);
                                        $vowels = array("é", "É", "è", "È");
                                        $grado_migrada_cotejo = str_replace($vowels, 'e', $grado_migrada_cotejo);
                                        $vowels = array("í", "Í", "ì", "Ì");
                                        $grado_migrada_cotejo = str_replace($vowels, 'i', $grado_migrada_cotejo);
                                        $vowels = array("ó", "Ó", "ò", "Ò");
                                        $grado_migrada_cotejo = str_replace($vowels, 'o', $grado_migrada_cotejo);
                                        $vowels = array("ú", "Ú", "ù", "Ù", "ü", "Ü");
                                        $grado_migrada_cotejo = str_replace($vowels, 'u', $grado_migrada_cotejo);

                                        $menor = 10000;
                                        foreach($grados as $condicion_id_db => $tipo_db) { 
                                            if($condicion_id_db == $condicion_id){
                                            foreach($tipo_db as $tipo_id_db => $grado_db) { 
                                                if($tipo_id_db == $tipo_id){
                                                foreach($grado_db as $clave => $valor) { 
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

                                                    $x = levenshtein($grado_migrada_cotejo, $valor_cotejo);
                                                    if($x < $menor) {
                                                        $menor = $x;
                                                        $selected = $clave;
                                                    }
                                        ?>
                                                <option value="<?php echo $clave; ?>">
                                                    <?php echo html_entity_decode($valor); ?>
                                                </option>
                                    <?php }}}}} ?>
                                    </select>
                                    <script>$("#tipo_cotejo_<?php echo $condicion_id; ?>_<?php echo $tipo_id; ?>_<?php echo $i; ?> option[value='<?php echo $selected ?>']").attr("selected", "selected");</script>
                                </td>
                            </tr>
                <?php 
                            $i++; 
                            }                    
                        } 
                    }
                ?>
                </table>
            </form>
        </div>
        <div class="help"></div>
        <br/>
        <div class="content">
            <div id="div_button_upload"><input type="button" onclick="subir_grados(); return false;" value="Siguiente"/></div>
        </div>
    </div>
</div>