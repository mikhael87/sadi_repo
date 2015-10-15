<?php
    $created_at = date('d-m-Y h:i:s A', strtotime($acceso_autorizacion_modulo->getCreatedAt()));
    echo $acceso_autorizacion_modulo->getUserUpdate()."<br/>".$created_at;
?>

