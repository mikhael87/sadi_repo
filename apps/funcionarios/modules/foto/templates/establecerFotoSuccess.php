<script type="text/javascript">
    function preview(img, selection) { 
            var scaleX = <?php echo $thumb_width;?> / selection.width; 
            var scaleY = <?php echo $thumb_height;?> / selection.height; 

            $('#thumbnail + div > img').css({ 
                    width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
                    height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
                    marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
                    marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
            });
            $('#x1').val(selection.x1);
            $('#y1').val(selection.y1);
            $('#x2').val(selection.x2);
            $('#y2').val(selection.y2);
            $('#w').val(selection.width);
            $('#h').val(selection.height);
    } 

    $(document).ready(function () { 
            $('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
    }); 

    function guardar_foto() {
            var x1 = $('#x1').val();
            var y1 = $('#y1').val();
            var x2 = $('#x2').val();
            var y2 = $('#y2').val();
            var w = $('#w').val();
            var h = $('#h').val();
            if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
                alert("Debes ajustar la foto");
            }else{
                $.ajax({
                        type: 'get',
                        dataType: 'html',
                        url: '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>foto/guardarFotoCargada',
                        data: $('.ajustes_foto').serialize(),
                        success:function(data, textStatus){
                            location.href='<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario';
                        }
                    })
            }
    }
</script>
<div>
    <a href="#" onclick="reiniciar_foto(); return false;">
        <img src="/images/icon/color_fill.png"/>&nbsp;Seleccionar otra foto
    </a>
</div>
<div>
    <a href="#" onclick="guardar_foto(); return false;">
        <img src="/images/icon/tick.png"/>&nbsp;Finalizar ajuste de tamaño
    </a>
</div>

<div align="center">
        <img src="/<?php echo $large_image_location;?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
        <div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
                <img src="/<?php echo $large_image_location;?>" style="position: relative;" alt="Thumbnail Preview" />
        </div>
        
        <div class="help" style="text-align: justify; padding-left: 30px; padding: 10px;">
            Es necesario recortar la foto a un tamaño estandar (50mm ancho × 60mm alto).<br/><br/>
            Para realizarlo situe el cursor
            en la imagen de la izquierda (la imagen original o la imagen grande), seguidamente haga click, deje precionado y arrastre el cursor.
            Para mejorar las guias de corte puede arrastrar desde las ezquinas y laterales hasta obtener el corte deseado, para finalizar presione el
            vinculo "Finalizar ajuste de tamaño". 
        </div>
        <br style="clear:both;"/>

        <div align="left">
            <input class="ajustes_foto" type="hidden" name="x1" value="" id="x1" />
            <input class="ajustes_foto" type="hidden" name="y1" value="" id="y1" />
            <input class="ajustes_foto" type="hidden" name="x2" value="" id="x2" />
            <input class="ajustes_foto" type="hidden" name="y2" value="" id="y2" />
            <input class="ajustes_foto" type="hidden" name="w" value="" id="w" />
            <input class="ajustes_foto" type="hidden" name="h" value="" id="h" />
        </div>
</div>