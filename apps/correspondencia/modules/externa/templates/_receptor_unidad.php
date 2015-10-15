<?php 
use_helper('jQuery'); 
$receptor = $sf_user->getAttribute('receptor_interno');
?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_receptor_interno_unidad">
<?php if ($sf_user->hasFlash('error_receptor')): ?>
  <div class="error"><?php echo $sf_user->getFlash('error_receptor'); ?></div>
<?php endif; ?>

    <div>
        <label for="receptor_interno_unidad">Unidad</label>
        <div class="content">
            <select name="receptor_interno[unidad_id]" id="receptor_interno_unidad_id" onchange="
                <?php
                    echo jq_remote_function(array('update' => 'funidad',
                    'url' => 'externa/receptorFuncionario',
                    'with'     => "'u_id=' +this.value",)) ?>">

                    <option value=""></option>
                    
                <?php foreach( $unidades as $clave=>$valor ) { ?>
                    <option value="<?php echo $clave; ?>" <?php if($receptor['unidad_id'] == $clave) echo " selected"; ?>>
                        <?php echo $valor; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

