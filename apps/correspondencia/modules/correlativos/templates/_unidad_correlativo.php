<?php use_helper('jQuery'); ?>

<script>
    $(document).ready(function(){
        $("form").submit(function() {
            
            if ($("#correspondencia_unidad_correlativo_unidad_id").val() == "") {
                $("#error_unidad").show();
                return false;
            } else  {
                return true;
            }
        });
    });
    
    function listar_formatos(){
        <?php
        echo jq_remote_function(array('update' => 'div_correlativo_formatos',
        'url' => 'correlativos/formatos',
        'with'=> "'unidad_id='+$('#correspondencia_unidad_correlativo_unidad_id').val()",))
        ?>
    }
</script>

<?php
if(!$sf_user->getAttribute('pae_funcionario_unidad_id')) {
    $boss= false;
    if($sf_user->getAttribute('funcionario_gr') == 99) {
        $boss= true;
        $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($sf_user->getAttribute('funcionario_id'));
    }

    $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($sf_user->getAttribute('funcionario_id'));

    $cargo_array= array();
    if($boss) {
        foreach($funcionario_unidades_cargo as $unidades_cargo) {
            $cargo_array[]= $unidades_cargo->getUnidadId();
        }
    }

    $admin_array= array();
    for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
        $admin_array[]= $funcionario_unidades_admin[$i][0];
    }

    $nonrepeat= array_merge($cargo_array, $admin_array);

    $funcionario_unidades= array();
    foreach ($nonrepeat as $valor){
        if (!in_array($valor, $funcionario_unidades)){
            $funcionario_unidades[]= $valor;
        }
    }
}else {
    $funcionario_unidades= array();
    $funcionario_unidades[]= $sf_user->getAttribute('pae_funcionario_unidad_id');
} ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_correspondencia_unidad_correlativo_unidad_id">
    <div class="error" id="error_unidad" style="display: none;">Seleccione la unidad a la que le asignara el correlativo.</div>
    <div>
        <label for="correspondencia_unidad_correlativo_unidad_id">Unidad</label>
        <div class="content">
            <?php if($sf_user->getAttribute('formatos_correlativo')) { ?>
                <?php 
                    foreach( $funcionario_unidades as $unidades ) { 
                        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidades);
                        if($unidades == $form['unidad_id']->getValue()) {
                            echo '<b>'.$unidad->getNombre().'</b>';
                            echo '<input type="hidden" name="correspondencia_unidad_correlativo[unidad_id]" value="'.$unidades.'"/>';
                            echo '<script>';
                            echo jq_remote_function(array('update' => 'div_correlativo_formatos',
                                'url' => 'correlativos/formatos',
                                'with'=> "'unidad_id=".$unidades."'",));
                            echo '</script>';
                        }
                    } ?>
            <?php } else { ?>
                <select name="correspondencia_unidad_correlativo[unidad_id]" id="correspondencia_unidad_correlativo_unidad_id" onchange="listar_formatos();">

                    <?php if(count($funcionario_unidades)>1) { ?>
                        <option value=""></option>
                    <?php } ?>
                    <?php foreach( $funcionario_unidades as $unidades ) { 
                        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidades);
                        ?>
                        <option value="<?php echo $unidades; ?>" <?php if($unidades == $form['unidad_id']->getValue()) echo "selected"; ?>>
                            <?php echo $unidad->getNombre(); ?>
                        </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>
    </div>
</div>