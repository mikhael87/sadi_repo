<?php use_helper('jQuery'); ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="autorizacion_unidad_id">Unidad</label>
        <div class="content">
            <select name="unidad_id" id="unidad_id" onchange="
                <?php
                    echo jq_remote_function(array('update' => 'funidad',
                    'url' => 'autorizacion/funcionario',
                    'with'     => "'u_id=' +this.value",)) ?>">

                    <option value=""></option>

                <?php foreach( $unidades as $clave=>$valor ) { ?>
                    <option value="<?php echo $clave; ?>">
                        <?php echo $valor; ?>
                    </option>
                <?php } ?>
            </select>

        </div>
    </div>
</div>