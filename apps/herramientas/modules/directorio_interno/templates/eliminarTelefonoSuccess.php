<ul class="sf_admin_td_actions">                            
    <?php foreach ($telefonos as $telefono) { ?>
        <div id="tele_edit_<?php echo $telefono->getId(); ?>">      
                <li class="sf_admin_action_telefono_edit">
                    <a href="#" onclick="javascript: fn_editar(<?php echo $telefono->getId(); ?>,<?php echo $cargo_id; ?>); return false;" title="Editar extensión"></a>
                </li>
                <li class="sf_admin_action_telefono_delete">
                    <a href="#" onclick="javascript: fn_eliminar(<?php echo $telefono->getId(); ?>,<?php echo $cargo_id; ?>); return false;" title="Eliminar extensión"></a>
                </li>
            <x id="tele_num_<?php echo $telefono->getId(); ?>"><?php echo $telefono->getTelefono(); ?></x>
        </div>
    <?php } ?>
</ul>