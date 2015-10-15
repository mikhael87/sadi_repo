<td>
  <ul class="sf_admin_td_actions">
    <?php if($funcionarios_funcionario_cargo->getStatus()=='A') { ?>
    <li class="sf_admin_action_coletilla">
      <?php echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Editar Coletilla', array(), 'messages'), 'funcionario_cargo/coletilla?id='.$funcionarios_funcionario_cargo->getId(), array()) : '') ?>
    </li>
    <li class="sf_admin_action_certificar_firma">
      <?php echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Certificar Firma', array(), 'messages'), 'funcionario_cargo/certificarFirma?id='.$funcionarios_funcionario_cargo->getId(), array()) : '') ?>
    </li>
    <li class="sf_admin_action_destituir">
      <?php echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Desincorporar', array(), 'messages'), 'funcionario_cargo/edit?id='.$funcionarios_funcionario_cargo->getId(), array()) : '') ?>
    </li>
    <li class="sf_admin_action_mover">
      <?php echo ((!$sf_user->hasCredential(array('Carnetizador'), false))? link_to(__('Mover de Unidad', array(), 'messages'), 'funcionario_cargo/mover?id='.$funcionarios_funcionario_cargo->getId(), array()) : '') ?>
    </li>
    <li class="sf_admin_action_carnet" style="position: relative">
        <a href="#" onclick="javascript: conmutar(); return false;">Descargar Carnet</a>
    </li>
    <?php } ?>
  </ul>
</td>

<div id="tab_backgroud" style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: 270px; min-height:92px; left: 50%; top: 50%; margin-left: -135px; margin-top: -100px; box-shadow: #777 0.2em 0.4em 0.7em; display: none">
    <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:92px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
        <div style="top: -15px; left: -15px; position: absolute;">
                <a href="#" onclick="javascript: conmutar(); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
        </div>
        <div>
            <div style="background-color: #767676; color: white">&nbsp;DELANTEROS</div>
            <div id="front_content" style="padding-top: 5px"></div>
            <input type="hidden" name="mark_hidden_f" id="mark_hidden_f" value="0"/>
            <font style="font-size: 11px; color: #333;">Seleccione el fondo de carnet de su preferencia</font>
        </div>
        <div>
            <div style="background-color: #767676; color: white">&nbsp;TRASEROS</div>
            <div id="back_content" style="padding-top: 5px"></div>
            <input type="hidden" name="mark_hidden_b" id="mark_hidden_b" value="0"/>
            <font style="font-size: 11px; color: #333;">Seleccione imagen trasera de carnet de su preferencia</font>
        </div>
        <div style="text-align: right; width: 257px; background-color: #B7B7B7" id="renew_">
            <button onclick="javascript: print_carnet();" name="aceptar" id="tab_back_ok" type="button" disabled="disabled" >Aceptar</button>
        </div>
    </div>
</div>

<script>
    function declare() {
        $('.prev').imgPreview({
            containerID: 'imgPreviewWithStyles',
            imgCSS: {
                width: '250'
            },
            preloadImages: false,
            distanceFromCursor: {top: -100, left:50}
        });
    }
    
    function conmutar(){
        if($('#tab_backgroud').is(':hidden')){
            $('#tab_backgroud').show();
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/cargarFondos',
                data: {id: <?php echo $funcionarios_funcionario_cargo->getId() ?>},
                beforeSend: function(Obj){
                    $('#front_content').html('Espere...');
                    $('#back_content').html('Espere...');
                },
                success:function(json, textStatus){
                    if(json.error === 'empty') {
                        $('#front_content').html(json.front);
                        $('#back_content').html(json.back);
                        $('#tab_back_ok').removeAttr('disabled');
                        declare();
                    }else {
                        conmutar();
                        alert('Error con diseños de carnet, configure estos primero.');
                    }
                }
            });
        }else{
            $('#tab_backgroud').hide();
        }
    }
    
    function mark(from, id) {
        $('.mark_'+from).each(function () {
            $(this).hide();
        });
        $('#mark_'+from+'_'+id).show();
        $('#mark_hidden_'+from).val(id);
    }
    
    function print_carnet() {
        var mark_f= $('#mark_hidden_f').val();
        var mark_b= $('#mark_hidden_b').val();
        var id= '<?php echo $funcionarios_funcionario_cargo->getId() ?>';
        
        document.location.href= '<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario_cargo/descargarCarnetCargo?id='+id+'&mark_f='+mark_f+'&mark_b='+mark_b;
    }
</script>

<style>
    #imgPreviewWithStyles {
        background: #222;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        padding: 10px;
        z-index: 999;
        border: none;
    }
</style>