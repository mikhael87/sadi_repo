<script>
    function abrir_notificacion_derecha(formulario){
        
        $("#header_notificacion_derecha").css('right', '-875px');
        $("#content_notificacion_derecha").css('right', '-892px');
        $("#div_espera_documento").show();

        $('#content_notificacion_derecha').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando...');
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>ficha/'+formulario,
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_notificacion_derecha').html(data)
            }});
        
        $("#content_notificacion_derecha").animate({right:"+=892px"},1000);
        $("#header_notificacion_derecha").animate({right:"+=892px"},1000);
    };

    function cerrar_notificacion_derecha(){
        $("#content_notificacion_derecha").animate({right:"-=892px"},1000);
        $("#header_notificacion_derecha").animate({right:"-=892px"},1000);
        $("#div_espera_documento").hide();
            
        $('#content_notificacion_derecha').html('');
    };
</script>

<!--DIV TRANSPARENTE PARA EVITAR CLIC EN OTROS LUGARES-->
<div id="div_espera_documento" 
     style="display: none; position: fixed; 
            left: 0px; top: 0px; width: 100%; 
            height: 100%; background-color: black; 
            opacity: 0.4; filter:alpha(opacity=40); 
            z-index: 999;">&nbsp;
</div>

<!--BOTON DE CERRADO NOTIFICACION DERECHA-->
<div id="header_notificacion_derecha">
    <a title="Cerrar" href="#" onclick="javascript:cerrar_notificacion_derecha(); return false;">
        <?php echo image_tag('other/menu_close.png'); ?>
    </a>
</div>

<!--DIV PARA CARGAR LA INFORMACION O FORMULARIO-->
<div id="content_notificacion_derecha"></div>