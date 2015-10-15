<?php use_helper('jQuery'); ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<tr class="sf_admin_form_row sf_admin_text sf_admin_filter_field_receptor_unidad">
    <td><label for="correspondencia_correspondencia_filters_receptor_unidad">Unidad</label></td>
    <td>

        <select name="correspondencia_correspondencia_filters[receptorUnidad]" id="correspondencia_correspondencia_filters_receptor_unidad" onchange="
        <?php
        echo jq_remote_function(array('update' => 'frunidad',
            'url' => 'enviada/funcionariosUnidad',
            'with' => "'u_id=' +this.value",)) ?>">

            <option value=""></option>

            <?php foreach ($unidades as $clave => $valor) {
            ?>
                <option value="<?php echo $clave; ?>">
                <?php echo $valor; ?>
            </option>
            <?php } ?>
        </select>
    </td>
</tr>

<tr class="sf_admin_form_row sf_admin_text sf_admin_filter_field_receptor_funcionario">
    <td>

        <label for="correspondencia_correspondencia_filters_receptor_funcionario">Funcionario</label>    </td>
    <td>
        <div id="frunidad">
            <select name="correspondencia_correspondencia_filters[receptorFuncionario]" id="correspondencia_correspondencia_filters_receptor_funcionario">
                <option value=""></option>
            </select>
        </div>
    </td>
</tr>



