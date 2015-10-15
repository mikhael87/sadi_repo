    <select name="correspondencia_correspondencia_filters[receptorFuncionario]" id="correspondencia_correspondencia_filters_receptor_funcionario">
      <option value=""></option>
      <?php foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getId()?>" <?php if($funcionario->getId() == $funcionario_selected) echo "selected"; ?>>
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php } ?>
   </select>