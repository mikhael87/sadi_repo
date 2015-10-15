<?php $carnet_tipos = Doctrine::getTable('Seguridad_CarnetTipo')->findByStatus('A'); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_carnet_tipo_id">
    <div>
        <label for="seguridad_carnet_diseno_carnet_tipo_id">Carnet tipo</label>
        <div class="content">
            <select id="seguridad_carnet_diseno_carnet_tipo_id" name="seguridad_carnet_diseno[carnet_tipo_id]" onchange="preparar_parametros();">
                <option value=""><- Selecione -></option>
                <?php
                foreach ($carnet_tipos as $carnet_tipo) {
                    echo '<option value="'.$carnet_tipo->getId().'">'.$carnet_tipo->getNombre().'</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>