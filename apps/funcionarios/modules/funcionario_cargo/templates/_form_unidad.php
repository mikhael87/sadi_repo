<?php use_helper('jQuery'); ?>
<script>
    function mostrar_cargos_vacios(unidad_id) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/cargosVacios',
            type:'POST',
            dataType:'html',
            data: 'unidad_id='+unidad_id,
            beforeSend: function(Obj){
                $('#cargos_vacios_id').html('<img src="/images/icon/cargando.gif"/>&nbsp;<font style="color: #666; font-size: 12px">Cargando cargos disponibles...</font>');
            },
            success:function(data, textStatus){
                $('#cargos_vacios_id').html(data);
            }});
    }
</script>

<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="autorizacion_unidad_id">Unidad</label>
        <div class="content">
            <select name="unidad_id" id="unidad_id" onchange="mostrar_cargos_vacios($(this).val()); return false;">
                <option value=""></option>

                <?php foreach( $unidades as $unidad_id=>$unidad_nombre ) { ?>
                    <?php if($unidad_id!='') { ?>
                        <?php 
                        $cargos_vacios = Doctrine::getTable('Organigrama_Cargo')->cargosVacios($unidad_id); 
                        if(count($cargos_vacios)>0){ $color = 'blue'; } else { $color = 'black'; }
                        
                        ?>
                        <option value="<?php echo $unidad_id; ?>" style="color: <?php echo $color; ?>;">
                            <?php 
                            echo $unidad_nombre; 
                            if(count($cargos_vacios)>0){
                                echo ' / CARGOS DISPONIBLES: '.count($cargos_vacios); 
                            }
                            ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>

        </div>
    </div>
</div>