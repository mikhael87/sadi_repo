<?php use_helper('jQuery'); ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,NULL,TRUE); ?>
<div class="sf_admin_form_row sf_admin_text" id="find_funcionario" style="display:none;">
    <div><label>Nombre del funcionario :</label>
        <div class="content">
            <?php echo $form['list_funcionario'] ?>

        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_foreignkey" id="piso">
    <div>
        <label for="">Piso</label>
        <div class="content">
            <?php 
            foreach( $unidades as $clave=>$valor ) { 
                @list($unidad_id, $piso) = explode("&&", $clave);

                if($piso == ''){
                    $array_pisos['ninguno'] = 'Sin definir';
                } else {
                    $array_pisos[str_replace(' ', '_', $piso)] = $piso;
                }
            } 
            ksort($array_pisos);
            ?>
                    
            <select name="" id="unidad_pisos_id" onchange="filtrar_pisos(); return false;">
                <option value=""></option>

                <?php foreach( $array_pisos as $clave=>$valor ) { ?>
                    <option value="<?php echo $clave; ?>">
                        <?php echo $valor; ?>
                    </option>
                <?php } ?>
            </select>

        </div>
        <div class="help">Seleccione un piso si desea filtrar las unidades</div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey" id="unidad">
    <div id="error_unidad" class="error" style="display: none;">Seleccione la unidad a la que se dirige.</div>
    <div>
        <label for="">Unidad</label>
        <div class="content">
            <select name="seguridad_ingreso[unidad_id]" id="unidad_recibe" onchange="
                <?php
                    echo jq_remote_function(array('update' => 'div_funcionario_recibe',
                    'url' => sfConfig::get('sf_app_seguridad_url').'ingresa/funcionarioRecibe',
                    'with'     => "'u_id=' +this.value",)) ?>">

                    <option value=""></option>

                <?php foreach( $unidades as $clave=>$valor ) { 
                    list($unidad_id, $piso) = explode("&&", $clave);
                    
                    if($piso == '')
                        $piso = 'ninguno';
                    else
                        $piso = str_replace(' ', '_', $piso);
                    ?>
                    <option value="<?php echo $unidad_id; ?>" class="piso_<?php echo $piso; ?>">
                        <?php echo $valor; ?>
                    </option>
                <?php } ?>
            </select>
            <div id="div_ext_unidad"></div>
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_foreignkey" id="funcionario">
    <div>
        <label for="">Funcionario</label>
        <div class="content" id="div_funcionario_recibe"><select name="seguridad_ingreso[funcionario_id]"></select></div>
    </div>
    <div class="help">Si el visitante no busca a alguien en especifico por favor no seleccione ningun funcionario.</div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <?php echo $form['motivo_id']->renderLabel() ?>
        <div class="content" id="motivo_id">
            <?php echo $form['motivo_id']->renderError() ?>
            <?php echo $form['motivo_id'] ?>
            <a class="agregar" onclick='$("#add_Motivo").toggle(200); $("img#busy_Motivo").hide();'></a>
            
            <div id="add_Motivo" style="display: none; width: 260px;">
                <br />
                <input type="text" name="new_tipo" id ="new_motivo">
                <input type="button" name="send_tipo" id="send_motivo" value="Enviar" onclick="EnviarInfo('new_motivo', 'seguridad_ingreso_motivo_id', 'Motivo')">
            </div>
        </div>
    </div>
</div>
<div class="sf_admin_form_row sf_admin_text">
    <div>
        <?php echo $form['motivo_visita']->renderLabel() ?>
        <div class="content" id="motivo_visita">
        <?php echo $form['motivo_visita']->renderError() ?>
        <?php echo $form['motivo_visita'] ?>
        </div>
    </div>
</div>