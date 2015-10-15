<div style="position: relative; min-height: 100px; width: 400px;">
    <?php echo $seguridad_ingreso->getUnidad(); ?><br/>
    <b>Funcionario</b>: 
        <?php
            if($seguridad_ingreso->getFuncionarioId()){
                echo $seguridad_ingreso->getFuncionarioPrimerNombre().' '.$seguridad_ingreso->getFuncionarioPrimerApellido(); 
            } else {
                echo 'Sin especificar.';
            }
        ?>
    <hr/>
    <b>Motivo</b>: <?php echo $seguridad_ingreso->getMotivoClasificado(); ?><br/>
    <?php echo $seguridad_ingreso->getMotivoVisita(); ?>
</div>