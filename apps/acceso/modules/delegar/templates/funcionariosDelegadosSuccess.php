    <select name="funcionario_id" id="">
      <?php foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getId()?>">
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php } ?>
   </select>