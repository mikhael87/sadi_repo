<?php

$participantes= Doctrine::getTable('Personas_Persona')->personasPorRegistroDisciplina($operaciones_registro_disciplina->getId());
$cadena= '';
foreach($participantes as $participante) {
   $requisitos= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->recaudosPorPersona($participante->getId());
   
   $todos= 0;
   $pendientes= 0;
   $chequeados= 0;
   
   foreach($requisitos as $requisito) {
       if($requisito->getStatus()== 'C') {
           $chequeados++;
       }elseif($requisito->getStatus()== 'P') {
           $pendientes++;
       }
       $todos++;
   }
   
   
   $cadena.= '<div style="text-align: center">';
   if($todos > 0) {
       if($pendientes == 0) {
           $cadena.= '<font style="color: green">Completo</font><br/>';
       }else {
           $cadena.= '<font style="color: red">'. $pendientes .'</font><br/>';
       }
   }else {
       $cadena.= '<font style="color: orange">Sin recaudos</font><br/>';
   }
   $cadena.= '</div>';
}
echo $cadena;
?>
