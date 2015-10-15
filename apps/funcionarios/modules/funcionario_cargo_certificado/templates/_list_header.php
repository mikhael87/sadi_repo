<?php use_helper('jQuery'); ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<script>
    function cargar_certificado(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando formulario...');

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo_certificado/new',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
</script>