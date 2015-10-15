
<div class="f10n">Diseño aplicado a los cargos con condición:</div> 
<?php
$indices = sfYaml::load($indices);

foreach ($indices as $indice => $status) {
    $cargo_condicion_id = str_replace('cargo_condicion_', '', $indice);
    $cargo_condicion = Doctrine::getTable('Organigrama_CargoCondicion')->find($cargo_condicion_id);
    
    if($status == 'I'){
        echo '&#186;&nbsp;&nbsp;<font class="gris_claro" class="tooltip" title="Inactivo">'.$cargo_condicion->getNombre().'</font><br/>';
    } else {
        echo '&#186;&nbsp;&nbsp;'.$cargo_condicion->getNombre().'<br/>';
    }
}
?>
