<?php
$tipo= Doctrine::getTable('Operaciones_TipoPago')->find($operaciones_registro_pago->getTipoPagoId());
echo $tipo->getNombre();
?>

