<?php use_helper('jQuery'); ?>
<?php include_partial('formatos/assets') ?>

<script>
    function datosFormato()
    {
        if($('#formato_tipo_formato_id').val()!=''){
             
            $('#div_emisores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando emisores...');
            $('#div_receptores').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando receptores...');  
            $('#div_receptores_externos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando receptores externos...');  
            $('#div_formatos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando contenido...');  
            $('#div_adjuntos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando adjuntos...');  
            $('#div_fisicos').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando fisicos...');  
        
            var tipo_formato_id = $('#formato_tipo_formato_id').val();
            
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/datosIdentificacion',
                type:'POST',
                dataType:'json',
                data:'tipo_formato_id='+tipo_formato_id,
                success:function(json, textStatus){
                    if(json.privacidad.show === 'false') {
                        $('#priv_emisor').attr('disabled', true);
                        $('#priv_receptor').attr('disabled', true);
                        if(json.privacidad.valores === 'privado') {
                            $("#priv_emisor").prop("checked", "checked");
                            $("#priv_receptor").prop("checked", "checked");
                        }else {
                            $("#priv_emisor").prop("checked", "");
                            $("#priv_receptor").prop("checked", "");
                        }
                    }else {
                        $('#priv_emisor').attr('disabled', false);
                        $('#priv_receptor').attr('disabled', false);
                        $("#priv_emisor").prop("checked", "");
                        $("#priv_receptor").prop("checked", "");
                    }
            }});
            
            <?php
            echo jq_remote_function(array('update' => 'div_emisores',
            'url' => 'formatos/emisores',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_receptores',
            'url' => 'formatos/receptores',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_receptores_externos',
            'url' => 'formatos/receptoresExternos',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_formatos',
            'url' => 'formatos/formato',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_adjuntos',
            'url' => 'formatos/adjuntos',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            <?php
            echo jq_remote_function(array('update' => 'div_fisicos',
            'url' => 'formatos/fisicos',
            'with'=> "'tipo_formato_id='+tipo_formato_id",))
            ?>

            $("#div_button").show("slow");
        } else {
            $('#div_emisores').html('');
            $('#div_receptores').html('');
            $('#div_receptores_externos').html('');
            $('#div_formatos').html('');
            $('#div_adjuntos').html('');
            $('#div_fisicos').html('');
            $("#priv_emisor").prop("checked", "");
            $("#priv_receptor").prop("checked", "");
            $('#priv_emisor').attr('disabled', false);
            $('#priv_receptor').attr('disabled', false);
            
            $("#div_button").hide("slow");
        }
    }

    
    function toggle_div_opciones_documento(){
        <?php if(!isset($correspondencia['identificacion'])) { ?>
            $("#div_opciones_documento_correlativo").toggle('slow');
        <?php } ?>
        $("#div_opciones_documento_privacidad").toggle('slow');
        $("#div_opciones_documento_prioridad").toggle('slow');
        
        if($("#img_config_opciones").attr("src")=='/images/icon/config_black.png')
            $("#img_config_opciones").attr("src", "/images/icon/config_gray.png");
        else
            $("#img_config_opciones").attr("src", "/images/icon/config_black.png");
    }
</script>

<div id="sf_admin_container">
    <h1>Nueva Correspondencia</h1>

    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="notice"><?php echo $sf_user->getFlash('notice'); ?></div>
    <?php endif; ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="error"><?php echo $sf_user->getFlash('error'); ?></div>
    <?php endif; ?>


    <div id="sf_admin_header"></div>

    <div id="sf_admin_content">

        <div class="sf_admin_form">

            <?php if(isset($formatos)) { ?>
            <form id="form_enviada" name="form_enviada" method="post" action="<?php echo sfConfig::get('sf_app_correspondencia_url').'formatos/create'; ?>" enctype="multipart/form-data">

                <fieldset id="sf_fieldset_identificaci__n">

                    <h2>Identificación</h2>

                    <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_tipo_formato">
                        <div>
                            <label for="tipo_formato_id">Documento</label>
                            <div class="content">
                                <?php if (isset($correspondencia['identificacion'])) { ?>
                                    <?php
                                        foreach ($formatos as $formato_id => $formato_nombre) {
                                            if ($correspondencia['formato']['tipo_formato_id'] == $formato_id)
                                                echo "<font class='f25b'>".$formato_nombre."</font>";
                                        }
                                    ?>
                                    <input type="hidden" value="<?php echo $correspondencia['formato']['tipo_formato_id']; ?>" name="correspondencia[formato][tipo_formato_id]" id="formato_tipo_formato_id"/>
                                    <a href="#" onclick="toggle_div_opciones_documento(); return false;"><img id="img_config_opciones" src="/images/icon/config_black.png"></a>
                                    <script language='JavaScript'>javascript:datosFormato();</script>
                                <?php } else { ?>
                                    <select name="correspondencia[formato][tipo_formato_id]" id="formato_tipo_formato_id" onchange="javascript:datosFormato()">

                                            <option value=""></option>

                                        <?php foreach ($formatos as $formato_id => $formato_nombre) { ?>
                                            <option value="<?php echo $formato_id; ?>"
                                                <?php
                                                    if (isset($correspondencia['formato'])) {
                                                        if(isset($correspondencia['formato']['tipo_formato_id'])) {
                                                            if ($correspondencia['formato']['tipo_formato_id']==$formato_id)
                                                                echo " selected ";
                                                        }
                                                    }
                                                ?>>
                                                <?php echo $formato_nombre; ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                    
                                    <a href="#" onclick="toggle_div_opciones_documento(); return false;"><img id="img_config_opciones" src="/images/icon/config_black.png"></a>
                                    
                                    <?php // if (isset($correspondencia['formato']['tipo_formato_id'])) { ?>
                                        <!--<script language='JavaScript'>javascript:datosFormato();</script>-->
                                    <?php // } ?>

                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <?php $display_correlativo = 'none;'; if(isset($correspondencia['identificacion'])) { $display_correlativo = 'block;';} ?>
                    <div id ="div_opciones_documento_correlativo" style="display: <?php echo $display_correlativo; ?>" class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label for="correspondencia_n_correspondencia_emisor">Correlativo</label>
                            <div class="content">
                                <?php if (isset($correspondencia['identificacion']) && isset($correspondencia['identificacion']['n_correspondencia_emisor'])) {
                                        if ($correspondencia['identificacion']['n_correspondencia_emisor']==$correspondencia['identificacion']['id']){
                                            echo 'En espera para generarlo.';
                                            $help_n= "Puede generar este correlativo en cualquier momento desde las acciones.";
                                        }else { 
                                            echo "<font class='f25b'>".$correspondencia['identificacion']['n_correspondencia_emisor']."</font>";
                                            $help_n= "Número de correlativo automático generado con anterioridad";
                                        }
                                    } else {
                                        $help_n = "Seleccione en que momento desea que se genere el correlativo."; 
                                        if(isset($correspondencia['identificacion']) && isset($correspondencia['identificacion']['metodo_correlativo']))
                                            $metho = $correspondencia['identificacion']['metodo_correlativo'];
                                        else
                                            $metho = 'I';
                                        ?>
                                <input type="radio" name="correspondencia[identificacion][metodo_correlativo]" value="I" <?php echo $metho== 'I' ? 'checked': '';?> /> Al momento de guardar&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="correspondencia[identificacion][metodo_correlativo]" value="F" <?php echo $metho== 'F' ? 'checked': '';?> /> En otro momento
                                <?php } ?>
                            </div>

                            <div class="help"></div>
                        </div>
                        <div class="help"><?php echo $help_n; ?></div>
                    </div>

                    <div id ="div_opciones_documento_privacidad" style="display: none;" class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label for="correspondencia_n_correspondencia_emisor">Confidencial</label>
                            <div class="content">
                                <?php
                                    if (isset($correspondencia['identificacion']) && isset($correspondencia['identificacion']['privacidad'])) {
                                        if(isset($correspondencia['identificacion']['privacidad']['emisor'])){
                                            if($correspondencia['identificacion']['privacidad']['emisor']== 'N') {
                                                $emisor_check= '';
                                            }else {
                                                $emisor_check= 'X';
                                            }
                                        }else {
                                            $emisor_check= '';
                                        }
                                        if(isset($correspondencia['identificacion']['privacidad']['receptor'])){
                                            if($correspondencia['identificacion']['privacidad']['receptor']== 'N') {
                                                $receptor_check= '';
                                            }else {
                                                $receptor_check= 'X';
                                            }
                                        }else {
                                            $receptor_check= '';
                                        }
                                    }else {
                                        $emisor_check= '';
                                        $receptor_check= '';
                                    }
                                ?>
                                <input id="priv_emisor" type="checkbox" name="correspondencia[identificacion][privacidad][emisor]" <?php echo ($emisor_check != '') ? 'checked' : ''; ?> value="E" /> Del emisor &nbsp;&nbsp;<font color="#AAAAAA">Ver solo por quien redacta y quien firma</font><br/>
                                <input id="priv_receptor" type="checkbox" name="correspondencia[identificacion][privacidad][receptor]" <?php echo ($receptor_check != '') ? 'checked' : ''; ?> value="R" /> Del receptor &nbsp;&nbsp;<font color="#AAAAAA">Ver solo por quien recibe</font>
                            </div>

                            <div class="help"></div>
                        </div>
                    </div>

                    <div id ="div_opciones_documento_prioridad" style="display: none;" class="sf_admin_form_row sf_admin_text">
                        <div>
                            <label for="correspondencia_n_correspondencia_emisor">Prioridad</label>
                            <div class="content">
                                <?php
                                if(isset($correspondencia['identificacion']) && isset($correspondencia['identificacion']['prioridad']))
                                    $priority= $correspondencia['identificacion']['prioridad'];
                                else
                                    $priority= 'S';
                                ?>
                                <input type="radio" name="correspondencia[identificacion][prioridad]" value="S" <?php echo $priority!= 'U' ? 'checked': '';?>/> Simple&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="correspondencia[identificacion][prioridad]" value="U" <?php echo $priority== 'U' ? 'checked': '';?>/> Urgente
                            </div>

                            <div class="help"></div>
                        </div>
                    </div>
                    
                </fieldset>

                <div id="div_emisores"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando emisores..."; } ?></div></div>
                <div id="div_receptores"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando receptores..."; } ?></div></div>
                <div id="div_receptores_externos"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando receptores externos..."; } ?></div></div>
                <div id="div_formatos"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando contenido..."; } ?></div></div>
                <div id="div_adjuntos"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando adjuntos..."; } ?></div></div>
                <div id="div_fisicos"><div class="trans"><?php if(isset($correspondencia['identificacion'])) { echo image_tag('icon/cargando.gif', array('size'=>'25x25'))." Cargando fisicos..."; } ?></div></div>

                <div style="position: relative;">
                    <ul class="sf_admin_actions">
                        <li class="sf_admin_action_cancelar"><a href="<?php echo sfConfig::get('sf_app_correspondencia_url') ?>enviada">Cancelar</a></li>
                        <?php if($sf_user->getAttribute('call_module_master')) : ?>
                        <li class="sf_admin_action_volver"><a href="<?php echo $sf_user->getAttribute('call_module_master') ?>">Volver</a></li>
                        <?php endif; ?>
                        <?php if (!isset($correspondencia['identificacion'])) { ?>
                        <div id="div_button" style="position: absolute; top: 5px; left: 80px; display: none;">
                        <?php } ?>
                            <li class="sf_admin_action_save"><input value="Guardar" type="submit" id="guardar_documento" /></li>
                            <li class="sf_admin_action_save_and_add"><input value="Guardar y agregar otro" type="submit" name="save_and_add" id="guardar_documento_y_agregar"></li>
                        <?php if (!isset($correspondencia['identificacion'])) { ?>
                        </div>
                        <?php } ?>
                        
                        <?php if (!isset($correspondencia['identificacion'])) {
                            if(isset($correspondencia['formato'])) {
                                if(isset($correspondencia['formato']['tipo_formato_id'])) {?>
                            <script language='JavaScript'>javascript:datosFormato();</script>
                        <?php } } } ?>
                    </ul>
                </div>


            </form>
            <?php } ?>

        </div>
    </div>
</div>
