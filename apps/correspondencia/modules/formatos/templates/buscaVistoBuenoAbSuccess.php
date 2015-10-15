<?php
$vb_general_config= Doctrine::getTable('Correspondencia_VistobuenoGeneralConfig')->findByTipoFormatoIdAndStatus($formato_id, 'A');

$cadena= '';
foreach($vb_general_config as $values) {
    $cadena .= '<a style="text-decoration: none" href="javascript: select_route_vb('. $values->getId() .', '. $formato_id .')">'.$values->getNombre().'</a><br/>';
}

echo $cadena;
?>
