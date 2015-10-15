<?php use_helper('jQuery'); ?>

<script>
    function fn_listar_estantes(){
        serie = $('#select_serie').val();
        unidad = $('#select_unidad_ubicacion').val();
        
        <?php
            echo jq_remote_function(array('update' => 'div_estante',
                'url' => 'expediente/listarEstantes',
                'with' => "'u_id='+unidad+'&s_id='+serie"));
        ?>
    };
</script>

<select id="select_unidad_ubicacion" name="unidad" onchange="javascript: fn_listar_estantes();">
    <option value=""></option>
    <?php foreach ($unidades as $clave => $valor) { ?>
        <option value="<?php echo $clave; ?>">
            <?php echo html_entity_decode($valor); ?>
        </option>
    <?php } ?>
</select>

<?php if(count($unidades)==0){ ?>
<script>
    $('#flash_error_id').show();
    $('#div_ubicacion_expediente').hide();
    $('.sf_admin_action_save').hide();
    $('#cargando_ubicacion_id').html('');
</script>
<?php } else { ?>
<script>
    $('#flash_error_id').hide();
    $('#div_ubicacion_expediente').show();
    $('.sf_admin_action_save').show();
    $('#cargando_ubicacion_id').html('');
</script>
<?php } ?>
