<select id="asignacion_cargo" name="funcionarios_funcionario_cargo[cargo_id]">
    <?php if(count($cargos_vacios)>0) { ?>
        <option value=""></option>
        <?php foreach( $cargos_vacios as $cargo_vacio ) { ?>
            <option value="<?php echo $cargo_vacio->getId(); ?>"><?php echo $cargo_vacio->getCodigoNomina().' - '.$cargo_vacio->getCargoCondicion().' - '.$cargo_vacio->getCargoTipo().' - '.$cargo_vacio->getCargoGrado(); ?></option>
        <?php } ?>
    <?php } else { ?>
        <option value="" style="color: red;">-- Sin cargos disponibles --</option>
    <?php } ?>
</select>