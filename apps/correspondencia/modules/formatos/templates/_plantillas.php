<script>
    function buscar_plantillas(){
        if($('#plantillas').html()==""){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/plantillas',
                type:'POST',
                dataType:'html',
                data:'tipo_formato_id='+$('#formato_tipo_formato_id').val(),
                success:function(data, textStatus){
                    $("#plantillas").html(data);
                    if($('#plantilla_creada').val()==1){
                        $('#plantillas_listado').slideDown();
                        $('#plantilla_creada').val(0);
                    }
            }})                
        }
        
        $('#plantillas_listado').slideToggle();
        $('#div_plantilla_nueva').remove();
    }
    
    function eliminar_plantilla(plantilla_id){
        if(confirm('¿Esta seguro que desea eliminar esta plantilla?')){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/eliminarPlantilla',
                type:'POST',
                dataType:'html',
                data:'plantilla_id='+plantilla_id,
                success:function(data, textStatus){
                    $('#plantillas').html('');
                }});
        } else {
            return false;
        }
        
        $('#plantillas_listado').slideToggle();
    }
    
    function preparar_guardado(){
        if($('#plantillas_listado').is(':hidden')) {
            $('#div_plantilla_nueva').remove();
            var cadena = '<div id="div_plantilla_nueva"><input id="input_plantilla_nueva" onFocus="clear_nueva_plantilla()" name="plantilla_nueva" type="text" value="nombre de la plantilla"/><a href="#" onclick="guardar_plantilla(); return false;"><?php echo image_tag('icon/filesave.png', array('style' => 'vertical-align: middle')); ?></a><hr/></div>';
            buscar_plantillas();
            $('#plantillas_listado').prepend(cadena);
        }else {
            $('#plantillas_listado').slideToggle();
        }
    }
    
    function clear_nueva_plantilla() {
        if($('#input_plantilla_nueva').val()=='nombre de la plantilla') {
            $('#input_plantilla_nueva').val('');
        }
    };
</script>

<div style="position: relative; width: 100%;">
    <div style="position: absolute; right: 10px; top: -29px;">
        <input id="plantilla_creada" type="hidden" value="0"/>
        <a href="#" onclick="preparar_guardado(); return false;"><?php echo image_tag('icon/new.png'); ?></a>
        <a href="#" onclick="buscar_plantillas(); return false;"><?php echo image_tag('icon/star.png'); ?></a>
    </div>

    <div id="plantillas" style="position: relative;"></div>
</div>
<script>buscar_plantillas();</script>