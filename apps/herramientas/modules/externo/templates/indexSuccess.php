<?php use_helper('I18N', 'Date') ?>
<?php include_partial('externo/assets') ?>
<?php use_helper('jQuery'); ?>



<script>
    $(document).ready(function(){
        $("#progressbar").progressbar({value:0});
        if (<?php echo $sf_user->getAttribute('sms_pendientes') ?> > 0 || <?php echo $sf_user->getAttribute('sms_pendientes') ?> == -1) {
            $('#progressbar').show();
            $('#progressbar_text').show();
            var timer = setInterval(function() {
                    $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>externo/procesadosSmsTotal',
                        type:'POST',
                        dataType:'html',
                        success:function(data, textStatus){
                            $("#progressbar .ui-progressbar-value").animate({width: data+"%"}, 500);
                            if(data>=100){
                                $("#progressbar .ui-progressbar-value").animate({width: "100%"}, 500);
                                clearInterval(timer);
                                if(data== 100) {
                                    $('#progressbar').fadeOut(8000);
                                    $('#progressbar_text').fadeOut(5000);
                                }
                            }
                    }});

                    $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>externo/procesadosSmsTotalNumero',
                        type:'POST',
                        dataType:'html',
                        success:function(data, textStatus){
                            jQuery('#enviados_total').html('');
                            jQuery('#enviados_total').append(data);
                        }});
            },60000);
        }

        proceso_calculo_progreso();


        //CAMBIO DE CODIGO MIKHAEL
        $("li.sf_admin_action_cancelar").mouseover(function(){
            $(this).removeClass().addClass("sf_admin_action_cancelar_act");
        }).mouseout(function(){
            $(this).removeClass().addClass("sf_admin_action_cancelar");
        });

        $("li.sf_admin_action_pausar").mouseover(function(){
            $(this).removeClass().addClass("sf_admin_action_pausar_act");
        }).mouseout(function(){
            $(this).removeClass().addClass("sf_admin_action_pausar");
        });

        $("li.sf_admin_action_continuar").mouseover(function(){
            $(this).removeClass().addClass("sf_admin_action_continuar_act");
        }).mouseout(function(){
            $(this).removeClass().addClass("sf_admin_action_continuar");
        });
        //CAMBIO DE CODIGO MIKHAEL
    });

    //inicio abrir detalles
    function fn_ver_destinatarios(id){
        if ($('#contenido_detalles_'+id).is(":visible")){
            $('#contenido_detalles_'+id).slideUp('slow');
            $('#frase_detalles_'+id).text('Mostrar');
            $('#estatus_detalles_'+id).removeClass("partial_close").addClass("partial_open");
        } else {
            if($('#actual_detalles_'+id).val()==0){
                $.ajax({
                    url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>externo/mostrarDestinatarios',
                    type:'POST',
                    dataType:'html',
                    data:'id='+id,
                    success:function(data, textStatus){
                        $('#contenido_detalles_'+id).html(data);
                    }})

                $('#actual_detalles_'+id).val(1);
            }

            $('#contenido_detalles_'+id).slideDown('slow');
            $('#frase_detalles_'+id).text('Ocultar');
            $('#estatus_detalles_'+id).removeClass("partial_open").addClass("partial_close");
        }
    }
    //fin abrir detalles

    //inicio calculo progreso individual
    var ACTIVO_PROGRESO = false;

    function proceso_calculo_progreso(){
        ids='';
        $(".calcular_progreso").each(function(){
            ids = ids + $(this).val() + '-';
        });
        ids = ids + 'fin';
        ids = ids.replace("-fin","");

        if(ids != 'fin'){
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>externo/cargarProgreso',
                type:'POST',
                dataType:'html',
                data:'ids='+ids,
                success:function(data, textStatus){
                    $('#script_progreso').html(data);
                    ACTIVO_PROGRESO = false;
                }})
        }
    }

    function send_calculo_progreso(){
        if (ACTIVO_PROGRESO == false){
            ACTIVO_PROGRESO = true;

            proceso_calculo_progreso();
        }
    }
    setInterval('send_calculo_progreso()', 60000);


    //fin calculo progreso individual

</script>

<div id="script_progreso"></div>




<div id="sf_admin_container">
  <h1><?php echo __('Mensajes Externos', array(), 'messages') ?></h1>

  <div id="progressbar" style="display: none; width: 100%; position: relative; height: 20px;">
      <div id="progressbar_text" style="position: absolute; margin-top: 3px; margin-left: 6px; font-size: 12px; color: #666; text-shadow: 1px 1px #FFFFFF;">
          Progreso total:&nbsp;<font id="enviados_total"><?php
          if($sf_user->getAttribute('sms_pendientes') > 0)
              echo '0 de '.$sf_user->getAttribute('sms_pendientes');
          elseif($sf_user->getAttribute('sms_pendientes') == '-1')
              echo 'Sin conexión gammu';
          ?></font>
      </div>
      <div style="position: absolute; font-size: 8px; right: 3px; top: 0px; text-align: right;">
          Información calculada desde las <?php echo date('h:i:s A'); ?><br/>
          actualización del progreso cada 60 segundos
      </div>
      <div id="action_bar" style="position: absolute; margin-top: 3px; margin-left: 50%; font-size: 12px; color: #666">
          <a href="<?php echo sfConfig::get('sf_app_herramientas_url').'externo/limpiarOutbox' ?>" onClick="return confirm('Se cancelarán todos los envios pendientes, ¿Esta seguro?')" >Cancelar todos</a>
      </div>
  </div>

  <?php //include_partial('externo/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('externo/list_header', array('pager' => $pager)) ?>
  </div>


  <div id="sf_admin_content">
    <?php include_partial('externo/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('externo/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('externo/list_actions', array('helper' => $helper)) ?>
    </ul>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('externo/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
