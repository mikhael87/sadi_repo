<div style="position: relative; min-height: 100px; width: 150px;">
    <b>Nº de pase</b>: <?php echo $seguridad_ingreso->getSeguridad_LlaveIngreso()->getNPase(); ?><br/><br/>
    <div class="f10n">Fecha Ingreso</div>
    <?php echo date('d-m-Y h:i A', strtotime($seguridad_ingreso->getFIngreso())); ?><br/>
    <div class="f10n">Fecha Salida</div> 
    <div id="f_salida_<?php echo $seguridad_ingreso->getId()?>">
        <?php 
            if($seguridad_ingreso->getFEgreso()){
                echo date('d-m-Y h:i A', strtotime($seguridad_ingreso->getFEgreso())); 
            } else {
                echo '<font style="color: red;"><i>No se ha registrado la salida</i></font>';
            }
        ?><br/>
    </div>
</div>