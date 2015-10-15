<?php use_helper('jQuery'); ?>
    <br/><br/>
    <select name="directorio_unidad_id" id="directorio_unidad_id" onchange="
    <?php
    echo jq_remote_function(array('update' => 'funcionarios_list',
        'url' => 'directorio_interno/funcionariosCalendario',
        'with' => "'u_id=' +this.value",))
    ?>">
        <option value=""></option>

        <?php
        foreach ($unidades as $clave => $valor) {
            if ($clave != '') {
                ?>
                <option value="<?php echo $clave; ?>" <?php echo strpos(html_entity_decode($valor), ';Gerencia') != FALSE ? 'style="font-weight:bold"' : '' ?>>
                    <?php echo html_entity_decode($valor); ?>
                </option>
            <?php
            }
        }
        ?>
    </select>
    <div class="help">Seleccione la unidad que desea ver.</div>
    <br/><br/>
    <div id="funcionarios_list"></div>