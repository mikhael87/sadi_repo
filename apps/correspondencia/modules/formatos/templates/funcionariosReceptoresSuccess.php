    <select name="correspondencia[receptor][funcionario_id]" id="correspondencia_receptor_funcionario_id">
      <?php
      $cant= 0;
      foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getId()?>" <?php if($funcionario->getId() == $funcionario_selected) echo "selected"; ?>>
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php
      $cant++;
      }
      if($cant== 0): ?>
        <option value=""></option>
      <?php endif; ?>
    </select>
    <?php if(count($funcionarios) > 0): ?>
    <input type="checkbox" name="correspondencia[receptor][copia]" id="correspondencia_receptor_copia"/>&nbsp;Copia
    <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar(); return false;">Guardar y agregar otro</a></div>
    <?php endif; ?>