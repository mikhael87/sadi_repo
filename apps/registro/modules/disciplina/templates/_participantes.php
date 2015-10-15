<?php
$participantes= Doctrine::getTable('Personas_Persona')->personasPorRegistroDisciplina($operaciones_registro_disciplina->getId());

foreach($participantes as $participante) {
    echo $participante->getPnombre().' '.$participante->getPapellido().'<br/>';
}

?>
