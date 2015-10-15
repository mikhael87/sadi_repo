<script>
    function buscar_extenciones_funcionario() {
        if ($('#funcionario_recibe').val() != '') {
            $('#div_ext_funcionarios').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Buscando extenciones telefonicas del funcionario...');
        
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/extencionesTelefonicasFuncionario',
                data: {cargo_id: funcionarios_cargo[$('#funcionario_recibe').val()]},
                success:function(data, textStatus){
                    $('#div_ext_funcionarios').html(data);
                }
            })
        }
    }
</script>

<select name="seguridad_ingreso[funcionario_id]" id="funcionario_recibe" onchange="buscar_extenciones_funcionario();">
    <option value=""></option>
    <?php foreach ($funcionarios as $funcionario) { ?>
    <?php $funcionarios_cargos[$funcionario->getId()] = $funcionario->getCid(); ?>
    <option value="<?php echo $funcionario->getId()?>" class="funcionario_id_class">
          <?php echo html_entity_decode($funcionario->getCtnombre()); ?> /
          <?php echo $funcionario->getPrimer_nombre(); ?>
          <?php echo $funcionario->getSegundo_nombre(); ?>,
          <?php echo $funcionario->getPrimer_apellido(); ?>
          <?php echo $funcionario->getSegundo_apellido(); ?>
    </option>
    <?php } ?>
</select>

<script>
    <?php 
        echo 'var funcionarios_cargo = new Array();';
        foreach ($funcionarios_cargos as $key => $value) {
            echo 'funcionarios_cargo['.$key.'] = '.$value.';';
        }
    ?>
        
    if ($('#unidad_recibe').val() != '') {
        $('#div_ext_unidad').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Buscando extenciones telefonicas de la unidad...');

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/extencionesTelefonicasUnidad',
            data: {cargos_unidad: funcionarios_cargo},
            success:function(data, textStatus){
                $('#div_ext_unidad').html(data);
            }
        })
    }
</script>

<div id="div_ext_funcionarios"></div>