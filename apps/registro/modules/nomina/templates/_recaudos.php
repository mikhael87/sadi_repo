<?php
$recaudos= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->recaudosPorPersona($personas_persona->getRpersona());

$cadena= '';

if(count($recaudos) != 0) {
    foreach($recaudos as $recaudo) {
        $cadena.= '<input class="input_recaudo" id="'. $recaudo->getId() .'" style="vertical-align: middle" type="checkbox" '. (($recaudo->getStatus()== 'C')? 'checked':'') .'/>&nbsp;'.$recaudo->getRnombre().'<br/>';
    }
    echo $cadena;
}else {
    $cadena.= image_tag('/icon/error.png', array('style'=>'width: 14px')).'&nbsp;<font style="font-size: 10px; color red">Sin recaudos asignados</font>';
}
?>
