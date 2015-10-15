<script type="text/javascript" src="/picture/htdocs/webcam.js"></script>
<script type="text/javascript" src="/js/camera.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/css/camera.css" />   

<script language="JavaScript">
    $('#webcam').html(webcam.get_html(250, 300));
    
    function do_upload() {
        // save image 
        webcam.upload();
    }
    
    function my_completion_handler(msg) {
        // extract URL out of PHP output
        if (msg.match(/(http\:\/\/\S+)/)) {
            var image_url = RegExp.$1; //obteniendo url de la imagen
            var image = image_url.split('/') //obteniendo solo el nombre de la imagen
            $("#funcionario_foto_tomada").val(image[9]);
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>foto/guardarFotoTomada',
                data: $('#form_tomar_foto').serialize(),
                success:function(data, textStatus){
                    location.href='<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario';
                }
            })
        }
        else{
            alert("PHP Error: " + msg);
            $("#button_guardar").show();
        }

    }
</script>

<div class="content">

    <div id="camera_content">
        <div id="camera">
            <br />
            <p style="text-align: center;" id="webcam">
                <script></script>
            </p>
            <div id="camera_imagen"></div>
        </div>

        <div id="btn_camara">
            <div id="cargando" style="display:none"></div>
            <button id="btn_capturar" type="button" class="btn primary-btn" onClick="do_freeze()" title="Capturar"/>
            <button id="btn_reset" style="display:none"  type="button" class="btn primary-btn" onClick="do_reset()" title="Reiniciar">
        </div>

        <div id="upload_results" style="background-color:#eee;"></div>
    </div>  
    <form id="form_tomar_foto" method="POST">
        <input id="funcionario_foto_tomada" type="hidden" name="foto">
        <ul class="sf_admin_actions">
            <li class="sf_admin_action_save">
                <input id="button_guardar"type="button" style="display: none;" onclick="do_upload(); return false;" value="Guardar"/>
            </li>
        </ul>
    </form>
</div>