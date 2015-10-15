<script>
    function buscar_excel_dias_disponibles(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>configuracion/migrarDiasDisponiblesExcel',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> preparando proceso ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar_dias_disponibles').html(data);
            }});
    }
    
    function remover_tr(tr_id){
        $('#tr_'+tr_id).hide("slow", function() {
            $(this).remove();
        });
    }
</script>

<div id="div_prosesar_dias_disponibles"><script>buscar_excel_dias_disponibles();</script></div>