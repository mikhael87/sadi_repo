<?php 
    $grados = Doctrine::getTable('Organigrama_CargoGradoTipo')->findByCargoTipoId($organigrama_cargo_tipo->getId());
    
    foreach ($grados as $grado) {
        $detalles = Doctrine::getTable('Organigrama_CargoGrado')->find($grado->getCargoGradoId());
        
        echo $detalles->getNombre()."<br/>";
    }
?>