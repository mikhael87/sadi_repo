<script>
    function establecer_fondo(){
        if($('#imagen_fondo').val()!=''){
    
        var data = new FormData();
        data.append('archivo',$('#imagen_fondo').get(0).files[0]);

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>carnet_diseno/establecerFondo',
            type:'POST',
            contentType:false,
            data:data,
            processData:false,
            cache:false,
            success:function(data, textStatus){
                $('#div_fondo_cargado').html(data);
            }
        });
        } else {
            alert('Seleccione una imagen de fondo');
        }
    }
    
    function reiniciar_fondo(){
        $(".imgareaselect-selection").remove();
        $(".imgareaselect-outer").remove();
        $(".imgareaselect-border1").remove();
        $(".imgareaselect-border2").remove();
        
        var cadena = 'Seleccione un fondo para el carnet<br/>';
        cadena = cadena + '<input id="imagen_fondo" type="file" name="imagen_fondo" size="30" />';
        cadena = cadena + '<div>';
            cadena = cadena + '<a href="#" onclick="establecer_fondo(); return false;">';
                cadena = cadena + '<img src="/images/icon/upload.png"/>&nbsp;Subir fondo';
            cadena = cadena + '</a>';
        cadena = cadena + '</div>';
        $('#div_fondo_cargado').html(cadena);
    }
</script>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_imagen_fondo">
    <div>
        <label for="seguridad_carnet_diseno_imagen_fondo">Diseño</label>
        <div class="content" id="div_fondo_cargado">
            Seleccione un fondo para el carnet<br/>
            
            <input id="imagen_fondo" type="file" name="imagen_fondo" size="30" />
            
            <div>
                <a href="#" onclick="establecer_fondo(); return false;">
                    <img src="/images/icon/upload.png"/>&nbsp;Subir fondo
                </a>
            </div>
        </div>

    </div>
</div>