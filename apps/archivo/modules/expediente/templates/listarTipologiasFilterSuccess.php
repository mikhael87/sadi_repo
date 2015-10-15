<?php use_helper('jQuery'); ?>

<select name="tipologia_documental[id]" onchange="<?php
        echo jq_remote_function(array('update' => 'div_etiquetas',
        'url' => 'expediente/listarEtiquetasFilter',
        'with'     => "'t_id='+this.value")); 
        ?>">
    <option value=""></option>
    <?php 
        $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($serie_documental_id,'null');

        foreach ($tipologias as $tipologia) { 
    ?>                
            <option value="<?php echo $tipologia->getId(); ?>">
                <?php echo $tipologia->getNombre(); ?>
            </option>
    <?php } ?>
    <optgroup>
    <?php 
        $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($serie_documental_id,'not null');

        $cuerpo = null;
        foreach ($tipologias as $tipologia) { 
            if($tipologia->getCuerpo()!=$cuerpo) {
                echo '</optgroup><optgroup label="'.$tipologia->getCuerpo().'">';
                $cuerpo = $tipologia->getCuerpo();
            }
    ?>

            <option value="<?php echo $tipologia->getId(); ?>">
                <?php echo $tipologia->getNombre(); ?>
            </option>
    <?php } ?>
    </optgroup>
</select>

<input type="checkbox" name="tipologia_documental[vacio]"/> Sin Archivar