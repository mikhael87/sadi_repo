<?php 
    $grupos = Doctrine::getTable('Public_MensajesGrupo')
        ->findByFuncionarioId(sfContext::getInstance()->getUser()->getAttribute('funcionario_id')); 
?>

<div class="sf_admin_form_row sf_admin_foreignkey sf_admin_form_field_unidad_id">
    <div>
        <label for="grupo_id">Grupo</label>
        <div class="content">
            <select name="mensajes_grupo_id" id="mensajes_grupo_id">
                    <option value=""></option>
                <?php foreach( $grupos as $grupo ) { ?>
                    <option value="<?php echo $grupo['id']; ?>">
                        <?php echo $grupo['nombre']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>