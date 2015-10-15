<?php use_helper('jQuery'); ?>
<script>
    function invitarEvento(id,cargo_id,unidad_id)
    {
        if(unidad_id === 0)
            unidad__cargo_id = cargo_id +"_"+$("#directorio_unidad_id").val();
        else
            unidad__cargo_id = cargo_id+"_"+unidad_id;
        
        dataString['invitados'][id] = unidad__cargo_id;
        $("#li_class_"+id).removeClass("sf_admin_action_calendario_add");
        $("#li_class_"+id).addClass("sf_admin_action_calendario_del");
        $("#"+id).removeAttr('onClick');
        $("#"+id).removeAttr('title');
        $("#"+id).attr('onClick','javascript: eliminarDeEvento('+id+','+cargo_id+');return false;');
        $("#"+id).attr('title','Eliminar invitación a evento');
    };
    function eliminarDeEvento(id,cargo_id)
    {
        delete dataString['invitados'][id];
        $("#li_class_"+id).removeClass("sf_admin_action_calendario_del");
        $("#li_class_"+id).addClass("sf_admin_action_calendario_add");
        $("#"+id).removeAttr('onClick');
        $("#"+id).removeAttr('title');
        $("#"+id).attr('onClick','javascript: invitarEvento('+id+','+cargo_id+',0);return false;');
        $("#"+id).attr('title','Invitar a evento');
    };
</script>
<?php $session_funcionario = $sf_user->getAttribute('session_funcionario'); ?>
<div class="sf_admin_row">
    <table style="width: 100%;">
        <tr><th></th><th>Funcionarios</th><th></th></tr>
        <?php foreach ($funcionarios as $funcionario) { ?>
        <tr style="border-bottom: solid 1px #000;">
                <td width="60"><img src="/images/fotos_personal/<?php echo $funcionario->getCi(); ?>.jpg" width="60"/></td>
                <td>
                    <font class='f16n'>
                    <?php echo $funcionario->getPrimer_nombre(); ?>
                    <?php echo $funcionario->getSegundo_nombre(); ?>,
                    <?php echo $funcionario->getPrimer_apellido(); ?>
                    <?php echo $funcionario->getSegundo_apellido(); ?>
                    </font>
                    <br/>
                    <?php echo "<font class='f16b'>" . $funcionario->getCtnombre() . "</font>"; ?>
                </td>
                <td id="sf_admin_container" width="16" align="left">
                    <ul class="sf_admin_td_actions">
                        <li class="sf_admin_action_calendario_add" id="li_class_<?php echo $funcionario->getId(); ?>">
                            <a id="<?php echo $funcionario->getId(); ?>" href="#" style="cursor: pointer;" onclick="javascript: invitarEvento(<?php echo $funcionario->getId(); ?>,<?php echo $funcionario->getCid(); ?>,0);return false;" title="Invitar a evento"></a>
                        </li>
                    </ul>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function(){
       for (var key in dataString['invitados']) {
            cargo = dataString['invitados'][key].split("_");
            invitarEvento(key,cargo[0],cargo[1]);
        } 
    });
</script>