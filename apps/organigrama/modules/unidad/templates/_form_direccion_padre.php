<script>
    function copiar_direccion() {
        if($('#organigrama_unidad_padre_id').val()!=''){
            $('#div_cargando_dir').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'15x15')); ?>');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_organigrama_url'); ?>unidad/copiarDireccion',
                data: {padre_id: $('#organigrama_unidad_padre_id').val()},
                success:function(json){
                    var direccion = json;
                    $('#organigrama_unidad_estado_id').val(direccion['geografico']['estado_id']).change().ajaxSuccess(
                        $('#organigrama_unidad_municipio_id').val(direccion['geografico']['municipio_id']).change().ajaxSuccess(
                            $('#organigrama_unidad_parroquia_id').val(direccion['geografico']['parroquia_id']).change()
                        )
                    )
                    
                    var key;
                    for (key in direccion['detalle']) {
                        $('#'+key).val(direccion['detalle'][key]);
                    }

                    $('#div_cargando_dir').html('');
                }
            })
        } else {
            $('#div_cargando_dir').html('<font class="rojo">No se ha seleccionado una dpendencia para buscar la direccion de la misma.</font>');
        }
    }
</script>

<div style="position: relative;">
    <div style="position: absolute;  left: 80px; top: -20px;">
        <a href="#" onclick="copiar_direccion(); return false;">
            (usar dirección de la unidad de la cual depende) 
            <div id="div_cargando_dir" style="position: absolute; top: 2px; left: 280px;"></div>
        </a>
    </div>
</div>