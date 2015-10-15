<script>
function change(id,action)
{
    if(action == "out"){ 
        $('#online_'+id).show(); 
        $('#delete_'+id).hide();
    }
    else
        {
            $('#online_'+id).hide(); 
            $('#delete_'+id).show();
        }
}
</script>
<table id="amigos_lista">
    <?php if(count($amigos_activos)>0){ ?>
    
        <?php foreach($amigos_activos as $amigo_activo){ ?>
    <tr style="cursor: pointer;" onmouseover="change(<?php echo $amigo_activo->getFuncionarioId(); ?>,'over')"
                onmouseout="change(<?php echo $amigo_activo->getFuncionarioId(); ?>,'out')">
        <td style="width: 180px;"> 
            <div id="activo_<?php echo $amigo_activo->getFuncionarioId(); ?>" style="overflow-x: hidden;" onclick="open_chat(<?php echo $amigo_activo->getFuncionarioId()?>);">
            &nbsp;
                <?php echo $amigo_activo->getPrimerNombre().' '.$amigo_activo->getPrimerApellido();  ?>
            </div>
            </td>
            <td>
            <?php
            if($amigo_activo->getUltimoStatus()){
                
                $ultimo = new DateTime($amigo_activo->getUltimoStatus());
                $actual = new DateTime(date('Y-m-d H:i:s'));
                $intervalo = $ultimo->diff( $actual );
                $minutos = $intervalo->format('%i');
                
                if($minutos < 2){ 
                    echo image_tag('icon/online.png',"id='online_".$amigo_activo->getFuncionarioId()."' style='float: right;'"); }
                }
                echo image_tag('icon/delete_user.png',"id='delete_".$amigo_activo->getFuncionarioId()."' style='float: right; display: none;' onclick='eliminarAmigo(".$amigo_activo->getFuncionarioId().",'chat')'");
            ?>
            </td>
            
          </td>
    </tr>
    <?php } } else { ?>
    <a href="<?php echo sfConfig::get('sf_app_herramientas_url')."directorio_interno"; ?>">Aun no tiene amigos haga click aqui para agregar</a>
    <?php } ?>
</table>

<script>
if(mini==1)
    {
        $("#minAmigos").attr("src","../../images/icon/max.png");
    }
</script>