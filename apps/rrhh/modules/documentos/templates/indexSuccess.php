<script>
    $(document).ready(function(){
        <?php 
        foreach ($documentos_activos as $documento => $valores) {
            if($valores['activar']==TRUE){ 
        ?>
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>documentos/cargarFormulario',
                type:'POST',
                dataType:'html',
                data:'tipo=<?php echo $documento; ?>',
                success:function(data, textStatus){
                    $('#td_<?php echo $documento; ?>').html(data);
                }});
        <?php
            }
        }
        ?>
    });
    
    function pdf_documento(tipo)
    {
        $('#div_inactive_actions_'+tipo).show();
        $('#div_active_actions_'+tipo).hide();
        
        window.open('<?php echo sfConfig::get('sf_app_rrhh_url'); ?>documentos/pdf?'+$('#form_'+tipo).serialize(), '_blank');
        
        $('#div_inactive_actions_'+tipo).hide();
        $('#div_active_actions_'+tipo).show();
    }
</script>    

<br/><br/>
<div id="sf_admin_container">
    <h1>Autogestión de documentos <?php echo $nombre_rrhh; ?></h1>

    <div id="sf_admin_content">
        <div class="sf_admin_list">
            <table>
                <tr>
                    <th style="width: 150px;">Documento</th>
                    <th style="width: 500px;">Parametros</th>
                    <th>Acciones</th>
                </tr>
                
                <?php 
                foreach ($documentos_activos as $documento => $valores) {
                    if($valores['activar']==TRUE){  
                ?>         
                <tr>
                    <td><div style="padding-top: 5px;"><label><?php echo $valores['nombre']; ?></label></div></td>
                    <td id="td_<?php echo $documento; ?>">
                        <?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Cargando parametros...
                    </td>
                    <td>
                        <div id="div_inactive_actions_<?php echo $documento; ?>" style="display: none;"><?php echo image_tag('icon/cargando.gif', array('size'=>'20x20')); ?> Procesando...</div>
                        <div id="div_active_actions_<?php echo $documento; ?>">
                            <ul class="sf_admin_td_actions">
                                <li class="sf_admin_action_descargar">
                                    <a href="#" onclick="pdf_documento('<?php echo $documento; ?>'); return false;">Descargar</a>   
                                </li>

                                <li class="sf_admin_action_copia_email">
                                    <a href="#" onclick="email_documento('<?php echo $documento; ?>'); return false;">Enviar a mi correo electronico</a>        
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <?php
                    }
                }
                ?>

            </table>
        </div>
    </div>
</div>


