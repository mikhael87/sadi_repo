<?php 
$receptor = $sf_user->getAttribute('receptor_interno'); 
$sf_user->getAttributeHolder()->remove('receptor_interno');
?>

    <select name="receptor_interno[funcionario_id]" id="receptor_interno_funcionario_id">
      <?php foreach ($funcionarios as $funcionario) { ?>
        <option value="<?php echo $funcionario->getId()?>" <?php if($receptor['funcionario_id']==$funcionario->getId()) echo "selected"; ?>>
            <?php echo $funcionario->getCtnombre(); ?> /
            <?php echo $funcionario->getPrimer_nombre(); ?>
            <?php echo $funcionario->getSegundo_nombre(); ?>,
            <?php echo $funcionario->getPrimer_apellido(); ?>
            <?php echo $funcionario->getSegundo_apellido(); ?>
      </option>
      <?php } ?>
   </select> <div class='partial_new_view partial'><a href="#" onclick="javascript: fn_agregar();">Guardar y agregar otro</a></div>