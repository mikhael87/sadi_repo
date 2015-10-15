<?php
$disciplina_datos= Doctrine::getTable('Operaciones_DisciplinaVariante')->datosDisciplina($operaciones_juego->getDisciplinaVarianteId());

$fecha= '';
foreach($disciplina_datos as $value) {
    echo '<font style="font-size: 15px; font-weight: bold">'.$value->getDnombre().'</font><br/>';
    echo '<font style="font-size: 12px; color: #666">'.$value->getVnombre().'</font><br/><br/>';
    
    
    $fecha= '<font style="font-size: 13px; color: ';
    if(strtotime($operaciones_juego->getFecha()) <= strtotime(date('d-m-Y'))) {
        $fecha.= 'green"';
    }else {
        $fecha.= 'blue" class="tooltip" title="[!]Juego programado[/!]"';
    }
    
    $fecha.= '>'.date('d-m-Y', strtotime($operaciones_juego->getFecha())).'</font>';
    
    echo $fecha;
}
?>
