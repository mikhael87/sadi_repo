<?php use_helper('I18N', 'Date') ?>
<?php include_partial('enviada/assets') ?>
<?php include(sfConfig::get("sf_root_dir").'/lib/partial/window_right.php'); ?>

<?php 
    $app_call_firma = 'correspondencia'; // VARIABLE QUE VERIFICA SI ESTA ACTIVA LA FIRMA PARA LA APLICACION. VERIFICAR PERMISOS EN config/siglas/firmaElectronica.yml
    include(sfConfig::get("sf_root_dir").'/lib/partial/certified_signature.php'); 
?>

<?php use_helper('jQuery'); ?>
<script>
    function crear_correspondencia(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando tipos de documento...');

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/index',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $('#content_window_right').html(data)
            }});
    }
    
    function open_form_copia_email(id){
        html  = '<div id="form_copia_email_'+id+'" class="caja" style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: 330px; min-height:150px; left: -211px; top: -20px;">';
        html += '    <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:150px;">';
        html += '        <div style="top: -15px; left: -15px; position: absolute;">';
        html += '            <a href="#" onclick="javascript:close_form_copia_email('+id+'); return false;"><?php echo image_tag('icon/icon_close.png'); ?></a>';
        html += '        </div>';
        html += '        <div style="top: 10px; left: 10px; position: absolute;">';
        html += '            <input id="text_copia_email_'+id+'" type="text" style="width: 305px;" value="Correos electronicos separados por coma (,)" onfocus="javascript:clean_text_copia_email('+id+');"/><br/>';
        html += '            <textarea id="area_copia_email_'+id+'" style="width: 305px; height: 80px;" onfocus="javascript:clean_text_copia_email('+id+');">Comentarios adicionales como encabezados al contenido del documento</textarea><br/>';
        html += '            <div style="text-align: right; width: 310px; background-color: #B7B7B7;"><button type="button" id="submit_mail" onclick="javascript:send_form_copia_email('+id+'); return false;">Enviar</button></div>';
        html += '        </div>';
        html += '    </div>';
        html += '</div>';

        $("#div_copia_email_"+id).html(html);
    }
    
    function close_form_copia_email(id){
        $("#div_copia_email_"+id).html('');
    }
    
    function clean_text_copia_email(id){
        if($("#text_copia_email_"+id).val()=='Correos electronicos separados por coma (,)')
            $("#text_copia_email_"+id).val(null);
        
        if($("#area_copia_email_"+id).val()=='Comentarios adicionales como encabezados al contenido del documento')
            $("#area_copia_email_"+id).val(null);
    }
    
    function send_form_copia_email(id){
        var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
        var emails = $.trim($("#text_copia_email_"+id).val());
        
        if(emails == ''){
            alert("Ingrese al menos un correo electronico");
        } else {
            if($("#text_copia_email_"+id).val()!='Correos electronicos separados por coma (,)'){
                var emails_split = emails.split(',');

                incorrectos = '';
                for(var i=0;i<emails_split.length;i++){
                    if(!filter.test(emails_split[i]))
                        incorrectos += emails_split[i]+' -';
                }

                if(incorrectos!='')
                    alert('Los siguientes correos son incorrectos: '+incorrectos);
                else {
                    $.ajax({
                        url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>formatos/enviarCopiaEmail',
                        type:'POST',
                        dataType:'html',
                        data:'id='+id+
                            '&emails='+emails+
                            '&comentario='+$.trim($("#area_copia_email_"+id).val()),
                        beforeSend: function(Obj){
                              jQuery('#submit_mail').html('Espere...');
                        },
                        success:function(data, textStatus){
                            jQuery('#x').html(data);
                            
                            alert('Se ha enviado con exito la copia mediante correo electronico.');
                            $("#div_copia_email_"+id).html('');
                        }});

                }
            } else {
                alert("Ingrese al menos un correo electronico");
            }
        }
    }
    
    $(document).ready(function (){
        $("#correspondencia_correspondencia_filters_formato option").clone().appendTo("#clon");
        $('#clon option:first').html('Todos los documentos');
        $("#correspondencia_correspondencia_filters__csrf_token2").val($("#correspondencia_correspondencia_filters__csrf_token").val());

            $("#clon").change(function(){
                if($('#clon').val() == '')
                    location.href= '<?php echo sfConfig::get('sf_app_correspondencia_url') ?>enviada/filter/action?_reset&_csrf_token?='+$("#correspondencia_correspondencia_filters__csrf_token").val();
                else
                $("#quickfilter").submit();
            });

        $("#correspondencia_correspondencia_filters_status option").clone().appendTo("#clon_status");
        $('#clon_status option:first').html('Todos los documentos');

            $("#clon_status").change(function(){
                if($('#clon_status').val() == '')
                    location.href= '<?php echo sfConfig::get('sf_app_correspondencia_url') ?>enviada/filter/action?_reset&_csrf_token?='+$("#correspondencia_correspondencia_filters__csrf_token").val();
                else
                $("#quickfilter").submit();
            });
    });
</script>

<div id="sf_admin_container">
  <h1><?php echo __('Correspondencia y Solicitudes enviadas', array(), 'messages') ?></h1>

  <div style="position: relative;">  
    <div style="position: absolute; top: -45px; right: 0px; text-align: right; padding: 0px;" class="trans">
        <?php
            $unidades_autorizadas = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($sf_user->getAttribute('funcionario_id'));

            foreach ($unidades_autorizadas as $unidad_autorizada){
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_autorizada->getAutorizadaUnidadId());
                echo $unidad->getNombre().'. ';
                echo ' Leer: ';
                if($unidad_autorizada->getLeer()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo ' Redactar: ';
                if($unidad_autorizada->getRedactar()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo ' Firmar: ';
                if($unidad_autorizada->getFirmar()) echo image_tag('icon/tick.png'); else echo image_tag('icon/delete_old.png');
                echo '<br/>';
            } 
            
            if(count($unidades_autorizadas)==0)
                echo '<fond class="rojo">NO esta autorizado en ningún grupo de correspondencia.</fond>';
        ?>
    </div>
  </div>
  
  <?php include_partial('enviada/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('enviada/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('enviada/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('correspondencia_correspondencia_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('enviada/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('enviada/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('enviada/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('enviada/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
