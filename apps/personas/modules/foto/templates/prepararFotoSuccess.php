<?php
if($sf_user->hasAttribute('foto_cambio')) {
    $from = 'foto';
}else{
    $from = 'digifirma';
}
?>
<script>
    function establecer_foto(){
        if($('#imagen_foto').val()!=''){
    
        var data = new FormData();
        data.append('archivo',$('#imagen_foto').get(0).files[0]);
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_personas_url'); ?>foto/establecerFoto',
            type:'POST',
            contentType:false,
            data:data,
            processData:false,
            cache:false,
            success:function(data, textStatus){
                if(data.length < 200) {
                    $('#div_foto_cargado').append('<div id=temp_div_error>'+data+'</div>');
                    $('#temp_div_error').delay(4000).hide(600);
                }else {
                    $('#div_foto_cargado').html(data);
                }
            }
        });
        } else {
            alert('Seleccione una imagen de foto');
        }
    }
    
    function reiniciar_foto(){
        $(".imgareaselect-selection").remove();
        $(".imgareaselect-outer").remove();
        $(".imgareaselect-border1").remove();
        $(".imgareaselect-border2").remove();
        
        var cadena = 'Seleccione un foto para identificar el/la funcionario(a)<br/>';
        cadena = cadena + '<input id="imagen_foto" type="file" name="imagen_foto" size="30" />';
        cadena = cadena + '<div>';
            cadena = cadena + '<a href="#" onclick="establecer_foto(); return false;">';
                cadena = cadena + '<img src="/images/icon/upload.png"/>&nbsp;Subir foto';
            cadena = cadena + '</a>';
        cadena = cadena + '</div>';
        $('#div_foto_cargado').html(cadena);
    }
</script>

<div class="content" id="div_foto_cargado">
    <?php
    if($sf_user->hasAttribute('foto_cambio')) {
        echo 'Seleccione una foto para identificar el/la funcionario(a)';
    }else{
        echo 'Seleccione el archivo de firma digitalizada';
    }
    ?>
    <br/>

    <input id="imagen_foto" type="file" name="imagen_foto" size="30" />

    <div>
        <a href="#" onclick="establecer_foto(); return false;">
            <img src="/images/icon/upload.png"/>&nbsp;Subir foto
        </a>
    </div>
</div>
