<select name="vistobueno_funcionario" id="vistobueno_funcionario">
      <?php
      $alguno= false;
      foreach ($funcionarios as $funcionario) { ?>
      <option value="<?php echo $funcionario->getId().'-'.$funcionario->getCid(); ?>" <?php if($funcionario->getId() == $funcionario_selected) echo "selected"; echo (($funcionario->getId() == $sf_user->getAttribute('funcionario_id') ? ' disabled ' : ''))?>>
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php $alguno= true; }
      if(!$alguno) {
          echo '<option value="">sin funcionarios</option>';
      }
      ?>
</select>
<?php if($alguno)
    echo '<a class="partial_new_view partial" href="#" onclick="javascript: fn_agregar_vistobueno(); return false;">Agregar</a>';
?>

