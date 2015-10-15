<?php use_helper('jQuery'); ?>

<script>
    function fn_listar_tramos(){
        serie = $('#select_serie').val();
        estante = $('#select_estante').val();
        
        <?php
            echo jq_remote_function(array('update' => 'div_tramo',
                'url' => 'expediente/listarTramos',
                'with' => "'e_id='+estante+'&s_id='+serie"));
        ?>
    };
</script>

<select id="select_estante" name="archivo_expediente[estante_id]" onchange="javascript: fn_listar_tramos();">
    <option value=""></option>
    <?php 
        foreach ($estantes as $estante) { 
    ?>
    <option value="<?php echo $estante->getId(); ?>"><?php echo $estante->getIdentificador(); ?></option>
    <?php } ?>
</select>