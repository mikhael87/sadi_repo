<?php use_helper('jQuery'); ?>
<script>
    function fn_agregar(id){
        cadena = "<table><tr><td width='50'><input type='text' size='11' maxlength='11' id='telefono_new_"+id+"'/></td>";
        cadena = cadena + "<td width='60'><input type='button' value='Guardar' onclick='javascript: fn_guardar_new("+id+");'/></td></tr>";
        cadena = cadena + "</table>";
        
        $("#tele_new_"+id).hide().html(cadena).fadeIn(1000);
    };
    
    function fn_guardar_new(id){
        telefono = $("#telefono_new_"+id).val();

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>directorio_interno/nuevoTelefono',
            type:'POST',
            dataType:'html',
            data:'cargo='+id+'&telefono='+telefono,
            success:function(data, textStatus){
                jQuery('#telefonos_'+id).html(data);
            }})
            
        $("#tele_new_"+id).html('');
    };
    
    function fn_editar(id,cargo){
        telefono = $("#tele_num_"+id).html();
    
        cadena = "<table><tr><td width='50'><input type='text' size='11' maxlength='11' id='telefono_edit_"+id+"' value='"+telefono+"'/></td>";
        cadena = cadena + "<td width='60'><input type='button' value='Guardar' onclick='javascript: fn_guardar_edit("+id+","+cargo+");'/></td></tr>";
        cadena = cadena + "</table>";
        
        $("#tele_edit_"+id).hide().html(cadena).fadeIn(1000);
    };
    
    function fn_guardar_edit(id,cargo){
        telefono = $("#telefono_edit_"+id).val();
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>directorio_interno/editarTelefono',
            type:'POST',
            dataType:'html',
            data:'id='+id+'&telefono='+telefono+'&cargo='+cargo,
            success:function(data, textStatus){
                jQuery('#telefonos_'+cargo).html(data);
            }})
    };
    
    function fn_eliminar(id,cargo){      
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_herramientas_url'); ?>directorio_interno/eliminarTelefono',
            type:'POST',
            dataType:'html',
            data:'id='+id+'&cargo='+cargo,
            success:function(data, textStatus){
                jQuery('#telefonos_'+cargo).html(data);
            }})
    };
    
    function fn_toggle_amigo_agregado(id){  
        $('#li_class_'+id).html('<a id="'+id+'" href="#" style="cursor: pointer;" onclick="javascript: eliminarAmigo('+id+'); fn_toggle_amigo_eliminado('+id+'); return false;" title="Eliminar Amigo"></a>');
        $('#li_class_'+id).removeClass("sf_admin_action_amigo_new").addClass("sf_admin_action_amigo_del");
        
        $('#amigos').load("<?php echo sfConfig::get('sf_app_herramientas_url'); ?>chat/amigosActivos", 
        null, function (){
            ACTIVO_AMIGOS = false;
        }).fadeIn("slow");
    };
    
    function fn_toggle_amigo_eliminado(id){  
        $('#li_class_'+id).html('<a id="'+id+'" href="#" style="cursor: pointer;" onclick="javascript: agregarAmigo('+id+'); fn_toggle_amigo_agregado('+id+'); return false;" title="Agregar Amigo"></a>');
        $('#li_class_'+id).removeClass("sf_admin_action_amigo_del").addClass("sf_admin_action_amigo_new");
        
        $('#amigos').load("<?php echo sfConfig::get('sf_app_herramientas_url'); ?>chat/amigosActivos", 
        null, function (){
            ACTIVO_AMIGOS = false;
        }).fadeIn("slow");
    };
</script>
<?php $session_funcionario = $sf_user->getAttribute('session_funcionario'); ?>
<div class="sf_admin_row">
    <table width="750">
        <tr><th></th><th>Funcionarios</th><th>Extensiones</th><th></th><th></th></tr>
        <?php foreach ($funcionarios as $funcionario) { ?>
            <tr>
                <td width="60">
                    <?php if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario->getCi().'.jpg')){ ?>
                        <img src="/images/fotos_personal/<?php echo $funcionario->getCi(); ?>.jpg" width="60"/><br/>
                    <?php } else { ?>
                            <img src="/images/other/siglas_photo_small_<?php echo $funcionario->getSexo().substr($funcionario->getCi(), -1); ?>.png" width="60"/><br/>
                    <?php } ?>
                </td>
                <td width="250">
                    <font class='f16n'>
                    <?php echo $funcionario->getPrimer_nombre(); ?>
                    <?php echo $funcionario->getSegundo_nombre(); ?>,
                    <?php echo $funcionario->getPrimer_apellido(); ?>
                    <?php echo $funcionario->getSegundo_apellido(); ?>
                    </font>
                    <br/>
                    <?php echo "<font class='f16b'>" . $funcionario->getCtnombre() . "</font>"; ?>
                </td>
                <td>
                    <div id="telefonos_<?php echo $funcionario->getCid(); ?>">
                        <ul class="sf_admin_td_actions">                            
                            <?php 
                            $telefonos = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($funcionario->getCid());
                            foreach ($telefonos as $telefono) { ?>
                                <div id="tele_edit_<?php echo $telefono->getId(); ?>">      
                                    <?php if ($sf_user->hasCredential(array('Seguridad y Recepción', 'Administrador', 'Root'), false) || ($session_funcionario['cedula'] == $funcionario->getCi())): ?>
                                        <li class="sf_admin_action_telefono_edit">
                                            <a href="#" onclick="javascript: fn_editar(<?php echo $telefono->getId(); ?>,<?php echo $funcionario->getCid(); ?>); return false;" title="Editar extensión"></a>
                                        </li>
                                        <li class="sf_admin_action_telefono_delete">
                                            <a href="#" onclick="javascript: fn_eliminar(<?php echo $telefono->getId(); ?>,<?php echo $funcionario->getCid(); ?>); return false;" title="Eliminar extensión"></a>
                                        </li>
                                    <?php endif; ?>
                                    <x id="tele_num_<?php echo $telefono->getId(); ?>"><?php echo $telefono->getTelefono(); ?></x>
                                </div>
                            <?php } ?>
                        </ul>
                    </div>
                </td>
                <td width="60"><div id="tele_new_<?php echo $funcionario->getCid(); ?>"></div></td>
                <td width="16" align="left">
                    <?php if ($sf_user->hasCredential(array('Seguridad y Recepción', 'Administrador', 'Root'), false) || ($session_funcionario['cedula']==$funcionario->getCi())){ ?>
                    <ul class="sf_admin_td_actions">
                        <li class="sf_admin_action_telefono_new">
                            <a href="" onclick="javascript: fn_agregar(<?php echo $funcionario->getCid(); ?>); return false;" title="Agregar extensión"></a>
                        </li>
                        <?php $count = 0;
                            foreach($amigos_agregados as $amigo_agregado){if($amigo_agregado == $funcionario->getId()){$count = 1; }}
                            if($count == 1) {
                                ?>
                                <li class="sf_admin_action_amigo_del" id="li_class_<?php echo $funcionario->getId(); ?>">
                                    <a id="<?php echo $funcionario->getId(); ?>" href="#" style="cursor: pointer;" onclick="javascript: eliminarAmigo(<?php echo $funcionario->getId(); ?>); fn_toggle_amigo_eliminado(<?php echo $funcionario->getId(); ?>); return false;" title="Eliminar Amigo"></a>
                                </li>
                            <?php } else { if ($funcionario->getId() != $sf_user->getAttribute('usuario_id')){?>
                                <li class="sf_admin_action_amigo_new" id="li_class_<?php echo $funcionario->getId(); ?>">
                                    <a id="<?php echo $funcionario->getId(); ?>" href="#" style="cursor: pointer;" onclick="javascript: agregarAmigo(<?php echo $funcionario->getId(); ?>); fn_toggle_amigo_agregado(<?php echo $funcionario->getId(); ?>); return false;" title="Agregar Amigo"></a>
                                </li>
                            <?php } } ?>
                    </ul>
                    <?php } else { ?>
                        <ul class="sf_admin_td_actions">
                            <?php $count = 0;
                            foreach($amigos_agregados as $amigo_agregado){if($amigo_agregado == $funcionario->getId()){$count = 1; }}
                            if($count == 1) {
                                ?>
                                <li class="sf_admin_action_amigo_del" id="li_class_<?php echo $funcionario->getId(); ?>">
                                    <a id="<?php echo $funcionario->getId(); ?>" href="#" style="cursor: pointer;" onclick="javascript: eliminarAmigo(<?php echo $funcionario->getId(); ?>); fn_toggle_amigo_eliminado(<?php echo $funcionario->getId(); ?>); return false;" title="Eliminar Amigo"></a>
                                </li>
                            <?php } else { ?>
                                <li class="sf_admin_action_amigo_new" id="li_class_<?php echo $funcionario->getId(); ?>">
                                    <a id="<?php echo $funcionario->getId(); ?>" href="#" style="cursor: pointer;" onclick="javascript: agregarAmigo(<?php echo $funcionario->getId(); ?>); fn_toggle_amigo_agregado(<?php echo $funcionario->getId(); ?>); return false;" title="Agregar Amigo"></a>
                                </li>
                            <?php } ?>
                    </ul>
                    <?php }?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>   