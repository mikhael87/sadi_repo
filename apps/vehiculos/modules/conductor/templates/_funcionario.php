<?php use_helper('jQuery'); ?>
<?php $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(); ?>

<script>
    function carga_fun(val) {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_vehiculos_url'); ?>conductor/funcionarioUnidad',
            type:'POST',
            dataType:'html',
            data: 'u_id='+val,
            beforeSend: function(Obj){
                $('#funidad').html('<img src="/images/icon/cargando.gif" />&nbsp;<font style="color: #666; font-size: 12px">Cargando funcionarios...</font>');
            },
            success:function(data, textStatus){
                $('#funidad').html(data);
                $('#label_fun').html('&nbsp;&nbsp;Funcionario');
            }});
    }
</script>
<br/>
<label>&nbsp;&nbsp;Unidad</label>
<select name="datos_iniciales_laborales[unidad_id]" id="unidad_id" onchange="carga_fun(this.value)">

        <option value=""></option>

    <?php foreach( $unidades as $clave=>$valor ) { ?>
        <option value="<?php echo $clave; ?>">
            <?php echo $valor; ?>
        </option>
    <?php } ?>
</select>
<br/><br/>
<label id="label_fun"></label><div id="funidad"></div><br/>
