<script>
    $(document).ready(function(){        
        $("form").submit(function() {
            
            count_aplica = 0;
            $(".check_cargo_condicion_aplicada").each(function () {
                if ($(this).is(':checked')) {
                    count_aplica++;
                }
            });
            
            if(count_aplica>0){
                return true;
            } else {
                alert('Seleccione al menos una condicion de cargos a quienes se aplicará el diseño.');
                return false;
            }
        });
    });
    
    function preparar_parametros() {
        if(!$.isEmptyObject($.find('#div_carnet_fondo'))){
            if($('#seguridad_carnet_diseno_carnet_tipo_id').val()!=''){
                $('#div_parametros_diseno').html('');
                $('#div_parametros_diseno').html('<div style="position: absolute; width: 400px; left: 215px;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando parametros...</div>');

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>carnet_diseno/cargarParametros',
                    data: {carnet_tipo: $('#seguridad_carnet_diseno_carnet_tipo_id').val()},
                    success:function(data, textStatus){
                        $('#div_parametros_diseno').html(data);
                        $('#button_guardar').removeAttr('disabled');
                    }
                })
            } else {
                $('#div_parametros_diseno').html('<div style="position: absolute; width: 400px; left: 215px;">Seleccione el tipo de carnet que esta diseñando</div>');
                $('#button_guardar').attr('disabled','disabled');
            }
        }
    }
</script>