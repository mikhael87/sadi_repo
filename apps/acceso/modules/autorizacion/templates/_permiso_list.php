<?php
if ($sf_user->getAttribute('autorizacion') == 'punto_cuenta') {
    if ($acceso_autorizacion_modulo->getPermiso() == 'A')
        echo 'Agregar y Consultar';
    elseif ($acceso_autorizacion_modulo->getPermiso() == 'C')
        echo 'Consultar';
} else {
    $this->getUser()->setFlash('error', 'Error en los permisos.'); ?>
    <script language='JavaScript'>location.href='acceso.php/index';</script>
<?php } ?>