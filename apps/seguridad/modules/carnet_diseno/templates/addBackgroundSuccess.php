<script>
    function abrir_metodo_carga(metodo) {
        $('.sf_admin_form_field_ci').show();
        $('#div_metodo_carga').html('');
        $('#div_metodo_carga').html('<div style="position: absolute; width: 400px; left: 215px;"><?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando metodo de carga...</div>');

        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>foto/metodoCarga',
            data: {metodo: metodo},
            success:function(data, textStatus){
                $('.sf_admin_form_field_ci').show();
                $('#div_metodo_carga').html(data);
            }
        });
    }
    
    function establecer_fondo(){
        if($('#imagen_fondo').val()!=''){
            var carnet_id= $('#carnet_id').val();
            var tipo_fondo= $('#tipo_fondo').val();
    
            var data = new FormData();
            data.append('archivo',$('#imagen_fondo').get(0).files[0]);
            data.append("carnet_id", carnet_id);
            data.append("tipo_fondo", tipo_fondo);

            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>carnet_diseno/establecerFondo',
                type:'POST',
                contentType:false,
                data:data,
                processData:false,
                cache:false,
                success:function(data, textStatus){
                    $('#div_fondo_cargado').html(data);
    //                if(data.length < 200) {
    //                    $('#div_foto_cargado').append('<div id=temp_div_error>'+data+'</div>');
    //                    $('#temp_div_error').delay(4000).hide(600);
    //                }else {
    //                    $('#div_foto_cargado').html(data);
    //                }
                }
            });
        } else {
            alert('Seleccione una imagen de fondo');
        }
    }
</script>

<div id="sf_admin_container">
    
    <h1>Agregar fondos a carnet</h1>

    <div id="sf_admin_header"></div>

    <div class="trans" style="width: 100%;">
        <div class="sf_admin_form_row sf_admin_text">
            <div>
                <label for="">Dise&ntilde;o</label>
                <div class="content">
                    <div class="content">
                        <div id="div_tipo_fondo">
                            Seleccione el tipo de fondo
                            <br/>
                            <select name="tipo_fondo" id="tipo_fondo">
                                <option value="front">Frontal</option>
                                <option value="back">Trasero</option>
                            </select>
                        </div>
                    </div><br/>
                    <div id="div_metodo_carga">
                        <div class="content" id="div_fondo_cargado">
                            Seleccione el archivo de imagen para fondo
                            <br/>
                            <input id="imagen_fondo" type="file" name="imagen_fondo" size="30" />
                            <div>
                                <a href="#" onclick="establecer_fondo(); return false;">
                                    <img src="/images/icon/upload.png"/>&nbsp;Subir fondo
                                </a>
                            </div>
                        </div>
                        <input type="hidden" value="<?php echo $id ?>" id="carnet_id"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="sf_admin_actions trans">
      <li class="sf_admin_action_regresar_modulo">
          <a href="#" onClick="javascript: $(function() { history.back(); }); return false;">Regresar</a>
      </li>
    </ul>
</div>