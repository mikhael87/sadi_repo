        <?php 
            $prestamos_activos = Doctrine::getTable('Archivo_PrestamoArchivo')->prestamosActivos($archivo_expediente->getId());
            
            $prestamo_fisico = 0;
            $prestamo_digital = 0;
            $count_prestamos_activos = count($prestamos_activos);
            
            foreach ($prestamos_activos as $prestamo_activo) {
                if($prestamo_activo->getFisico()==TRUE){
                    $prestamo_fisico++;
                    $fecha_expiracion = $prestamo_activo->getFExpiracion();
                    
                    $fecha_entrega = '';
                    if($prestamo_activo->getFEntregaFisico()!=''){
                        $fecha_entrega = $prestamo_activo->getFEntregaFisico();
                        
                        $funcionario_retiro = Doctrine::getTable('Funcionarios_Funcionario')->find($prestamo_activo->getReceptorEntregaFisicoId());
                    }
                        
                }
                
                if($prestamo_activo->getDigital()==TRUE)
                    $prestamo_digital++;
                
            } 
        ?>

<div style="position: relative; font-size: 13px; width: 130px;">   
    <div style="position: relative; font-size: 10px; left: 0px;">
        <b>Fisico</b>
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;">
        <?php             
            if($prestamo_fisico>0){
                if($fecha_expiracion > date('Y-m-d')) {
                    echo "<fond style='color: red;'>Prestado</fond>";
                    if($fecha_entrega==''){
                        echo "<fond style='font-size: 10px;'><br/><u>No lo han retirado</u></fond>";
                    } else {
                        echo "<fond style='font-size: 10px;'><br/><b>Retirado el </b>".date('d-m-Y', strtotime($fecha_entrega))."</fond>";
                        echo "<fond style='font-size: 10px;'><br/><b>Por </b>".$funcionario_retiro->getPrimerNombre().' '.$funcionario_retiro->getPrimerApellido()."</fond>";
                    }
                    echo "<br/>";
                    echo "<fond style='font-size: 10px;'><br/>Disponible a partir:</fond><br/>".date('d-m-Y', strtotime($fecha_expiracion));   
                }else {
                    echo "Disponible<br/>";
                }
            } else {
                echo "Disponible<br/>";
            }
        ?>
        <br/>
    </div>
    
    <div style="position: relative; top: 5px; width: 130px;">
        <img src="/images/other/barra_trans.png" width="130" height="1"/>
    </div>
    <div style="position: relative; font-size: 10px; left: 0px;">
        <br/><b>Digitales</b>
    </div>
    <div style="position: relative; font-size: 13px; left: 0px;" title="Prestamos digitales activos">
        <?php             
            echo "Cantidad: ".$prestamo_digital;
        ?>
        <br/>
    </div>
    
</div>