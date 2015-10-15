<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<script>   
    function nuevo_preingreso(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/new',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
    
    function preparar_ingreso(preingreso_id){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');
    
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>preingreso/prepararIngreso',
            type:'POST',
            dataType:'html',
            data: {preingreso_id: preingreso_id},
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
</script>