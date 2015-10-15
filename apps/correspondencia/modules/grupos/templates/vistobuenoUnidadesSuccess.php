<br />Funcionarios:<br />    
<select name="vistobueno_funcionario" id="vistobueno_funcionario">
      <?php foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getId().'#'.$funcionario->getCid(); ?>" <?php if($funcionario->getId() == $funcionario_selected) echo "selected"; ?>>
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php } ?>
</select>

<a class='partial_new_view partial' href="#" onclick="javascript: fn_agregar_vistobueno(); return false;">Agregar</a><br/><br/>
