<?php use_helper('jQuery'); ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="">Unidad</label>
        <div class="content">
            <select name="unidad_id" id="" onchange="
                <?php
                    echo jq_remote_function(array('update' => 'fdelegado',
                    'url' => 'delegar/funcionariosDelegados',
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

<div class="sf_admin_form_row sf_admin_foreignkey">
    <div>
        <label for="">Delegado</label>
        <div class="content" id="fdelegado"><select name="funcionario_id"></select></div>
    </div>
</div>