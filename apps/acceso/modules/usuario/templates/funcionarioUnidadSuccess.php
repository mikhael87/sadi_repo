    <select name="datos_iniciales_laborales[cargo_supervisor_id]">
      <?php foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getCid()?>">
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php } ?>
   </select>