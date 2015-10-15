desde: <?php echo date('d-m-Y', strtotime($funcionarios_funcionario_cargo_certificado->getFValidoDesde())); ?><br/>
hasta: <?php echo date('d-m-Y', strtotime($funcionarios_funcionario_cargo_certificado->getFValidoHasta())); ?>
<br/><br/>
<?php
    echo '<hr/>Estatus por fecha:<br/>';
    if(strtotime($funcionarios_funcionario_cargo_certificado->getFValidoHasta()) > strtotime(date('Y-m-d'))){
        echo "<font class='azul'>Certificado Vigente</font>";
    } else {
        echo "<font class='rojo'>Certificado Vencido</font>";
    }
    
    echo '<hr/>Estatus por accion:<br/>';
    if($funcionarios_funcionario_cargo_certificado->getStatus() == 'A'){
        echo "<font class='azul'>Activo</font>";
    } else if($funcionarios_funcionario_cargo_certificado->getStatus() == 'I'){
        echo "<font class='rojo'>Anulado</font>";
    } else if($funcionarios_funcionario_cargo_certificado->getStatus() == 'V'){
        echo "<font class='rojo'>Vencido</font>";
    }
?>