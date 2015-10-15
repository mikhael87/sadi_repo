<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 

<?php

$registros= Doctrine::getTable('Operaciones_Registro')->reporte();
        
foreach($registros as $registro) {
    echo '<b>'.$registro->getNombre().' ('.$registro->getSiglas().')</b><br/>';

    $disciplinas= Doctrine::getTable('Operaciones_Registro')->disciplinaReporte($registro->getId());

    foreach($disciplinas as $disciplina) {
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$disciplina->getDnombre().' ('.$disciplina->getVnombre().')</b><br/>';

        $personas= Doctrine::getTable('Operaciones_Registro')->personaReporte($disciplina->getId());

        foreach($personas as $persona) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$persona->getPnombre().' '.$persona->getPapellido().'<br/>';
        }
    }
}

?>