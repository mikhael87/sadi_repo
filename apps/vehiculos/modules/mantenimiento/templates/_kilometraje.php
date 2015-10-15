<?php
if($vehiculos_mantenimiento->getKilometraje() != '') {
    echo '~'.number_format($vehiculos_mantenimiento->getKilometraje()).' Km';
}
?>
