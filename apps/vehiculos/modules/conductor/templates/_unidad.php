<div>
    <?php
    $unidad= Doctrine::getTable('Organigrama_Unidad')->datosCargoUnidad($vehiculos_conductor->getCargoId());
    foreach($unidad as $unidad) {
        echo '<font class="unidad_c">'.$unidad->getUnidadNombre().'</font>';
    }
    ?>
</div>
