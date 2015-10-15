<select name="cuerpo_documental_id">
    <option value=""></option>
    <?php foreach ($cuerpos as $cuerpo) { ?>                
            <option value="<?php echo $cuerpo->getId(); ?>">
                <?php echo $cuerpo->getNombre(); ?>
            </option>
    <?php } ?>
</select>