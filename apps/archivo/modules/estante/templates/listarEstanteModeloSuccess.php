<?php
        $estante_modelo = Doctrine::getTable('Archivo_EstanteModelo')
                            ->createQuery('a')
                            ->orderBy('nombre')->execute();
?>

<select name="archivo_estante[estante_modelo_id]">
    <option value=""></option>
    <?php foreach ($estante_modelo as $modelo) { ?>
        <option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getNombre(); ?></option>
    <?php } ?>
</select>