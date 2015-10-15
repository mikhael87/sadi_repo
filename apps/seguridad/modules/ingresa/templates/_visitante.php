<div style="position: relative; min-height: 100px; width: 250px;">

    <div style="position: absolute; top: 0px; left: 0px; width: 84px; height: 98px;">
        <img src="/uploads/seguridad/<?php echo $seguridad_ingreso->getImagen(); ?>" width="80"/><br/>
    </div>
    <div style="position: absolute; left: 90px; top: 0px;">
        Cedula: <?php echo $seguridad_ingreso->getCedula(); ?><br/>
        <?php echo ucwords(strtolower($seguridad_ingreso->getPersonaPrimerNombre().' '.$seguridad_ingreso->getPersonaPrimerApellido())); ?><br/><br/>
        F. nacimiento: <?php echo date('d-m-Y', strtotime($seguridad_ingreso->getFNacimiento())); ?><br/>
    </div>
    
</div>