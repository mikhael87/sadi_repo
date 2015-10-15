<?php use_helper('jQuery'); ?>
<fieldset id="sf_fieldset_receptores">
    <h2>Receptores Externos</h2>
    <?php
    $copias_externos= Array();
    if(isset($correspondencia['receptor_externo']['copias']))
        $copias_externos= $correspondencia['receptor_externo']['copias'];
    include_partial('organismo/organismo_agregar', 
            array(
                'organismo_name'=>'correspondencia[receptor_externo][organismo_id]',
                'persona_name'=>'correspondencia[receptor_externo][persona_id]',
                'cargo_name'=>'correspondencia[receptor_externo][persona_cargo_id]',
                'copias'=>$copias_externos,
            ));
    ?>
</fieldset>