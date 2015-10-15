<?php
$directores= Doctrine::getTable('Operaciones_RegistroDirectorTecnico')->directoresPorRegistroDisciplina($operaciones_registro_disciplina->getId());

if(count($directores) > 0) {
    foreach($directores as $director) {
        echo $director->getPnombre().' '.$director->getPapellido().'<br/>';
    }
}else {
    echo '<font style="color: red">Sin director</font>';
}


?>
