<script>
    function buscar_excel(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosExcel',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> preparando proceso ...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(1);
            }});
    }
    
    function remover_tr(tr_id){
        $('#tr_'+tr_id).hide("slow", function() {
            $(this).remove();
        });
    }
    
    function reiniciar_pasos(paso){
        $('.vinculo').hide();
        
        for (i = 1; i < paso; i++) {
            $('#div_vinculo_'+i).show();
        }
        
        $('.pasos').css('background-color','');
        $('#div_paso_'+paso).css('background-color','#CCCCFF');
        
        $('.pasos').css('font-weight','normal');
        $('#div_paso_'+paso).css('font-weight','bold');
        
        $('.pasos').css('color','#aaa');
        $('#div_paso_'+paso).css('color','#000');
    }
</script>

<div id="sf_admin_container">
  <h1>
    Migración de Funcionarios
  </h1>


    <div id="sf_admin_header"></div>

    <div id="sf_admin_content">
        <div class="sf_admin_form">
            <fieldset>
                <div style="position: relative; height: 50px;">
                    <div id="div_paso_1" class="pasos" style="position: absolute; left: 0px; background-color: #CCCCFF; font-weight: bold; color: #000; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 1</div>
                            <div style="position: absolute; font-size: 10px; width: 120px; text-align: center; top: 30px;">Subir datos</div>
                        </div>
                        <div id="div_vinculo_1" class="vinculo" onclick="reiniciar_pasos(1); return false;" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_2" class="pasos" style="position: absolute; left: 120px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 2</div>
                            <div style="position: absolute; font-size: 10px; width: 120px; text-align: center; top: 30px;">Revisar datos</div>
                        </div>
                        <div id="div_vinculo_2" class="vinculo" onclick="reiniciar_pasos(2); return false;" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_3" class="pasos" style="position: absolute; left: 240px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 3</div>
                            <div style="position: absolute; font-size: 10px; width: 120px; text-align: center; top: 30px;">Cotejar unidades</div>
                        </div>
                        <div id="div_vinculo_3" class="vinculo" onclick="reiniciar_pasos(3); return false;" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_4" class="pasos" style="position: absolute; left: 360px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 4</div>
                            <div style="position: absolute; font-size: 10px; width: 120px; text-align: center; top: 30px;">Cotejar cargos</div>
                        </div>
                        <div id="div_vinculo_4" class="vinculo" onclick="reiniciar_pasos(4); return false;" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                    <div id="div_paso_5" class="pasos" style="position: absolute; left: 480px; background-color: ''; font-weight: normal; color: #aaa; height: 50px; width: 120px;">
                        <div style="position: relative;">
                            <div style="position: absolute; font-size: 25px; width: 120px; text-align: center; top: 5px;">PASO 5</div>
                            <div style="position: absolute; font-size: 10px; width: 120px; text-align: center; top: 30px;">Migrar</div>
                        </div>
                        <div id="div_vinculo_5" class="vinculo" onclick="reiniciar_pasos(5); return false;" style="position: absolute; width: 120px; height: 50px; cursor: pointer;"></div>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <h2>Proceso</h2>
                <div id="div_prosesar"><script>buscar_excel();</script></div>
            </fieldset>
        </div>
  </div>

  <div id="sf_admin_footer">
    <ul class="sf_admin_actions trans">
        <li class="sf_admin_action_regresar_modulo">
            <a href="<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/index">Regresar a los funcionarios</a>
        </li>
        <li class="sf_admin_action_donwload_form">
            <a href="<?php echo sfConfig::get('sf_app_funcionarios_url'); ?>funcionario/migrarFuncionariosDescargarExcel">Formulario de migración</a>
        </li>
    </ul>
  </div>
</div>
