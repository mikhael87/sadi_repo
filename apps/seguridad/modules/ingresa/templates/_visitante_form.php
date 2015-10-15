<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Nº Cedula</label>
        <div class="content">
            <input type="text" name="persona_cedula" id="persona_cedula" onkeydown="return solo_num(event)" />
            <input type="button" value="Buscar" name="Buscar" onclick="buscar_cedula(); return false;" />
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text">
    <div>
        <label>Visitante</label>
        <div class="content" id="div_datos_persona">
            Ingrese una cedula para buscar sus datos.
        </div>
    </div>
</div>

<div class="sf_admin_form_row sf_admin_text" id="fotografia">
    <div>
        <label>Fotografia</label>
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
                
                
                <?php 
                $agente = $_SERVER["HTTP_USER_AGENT"];
                
                if(preg_match("/Firefox\//", $agente)){
                ?>
                    <div style="position: relative; width: 430px;">
                        <div style="position: absolute; top: -80px; right: 0px;">
                            <a href="http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager06.html" target="_blank">
                                <?php echo image_tag('other/camera_firefox.png'); ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <div id="upload_results" style="background-color:#eee;"></div>
            </div>  

        </div>
    </div>
</div>