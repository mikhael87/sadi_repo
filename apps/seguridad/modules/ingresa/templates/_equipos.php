<?php 
$equipos = Doctrine::getTable('Seguridad_IngresoEquipo')->equiposDeIngreso($seguridad_ingreso->getId()); 

foreach ($equipos as $equipo):
    if(!$equipo->getFEgreso()){
        echo $equipo->getTipo().' / '.$equipo->getMarca().'<br/>Serial: '.$equipo->getSerial();
        echo '<ul class="sf_admin_td_actions"><li class="sf_admin_action_salida"><a id="egreso_equipo_'.$equipo->getId().'" href="#" onClick="registrar_egreso_equipo('.$equipo->getId().'); return false;">'.                
             'Egreso de Equipo</a></li></ul>';
    }else{
         echo $equipo->getTipo().' / '.$equipo->getMarca().'<br/>Serial: '.$equipo->getSerial();
    }
    
?>
<div class="f10n">Fecha Salida de Equipo</div> 
<div id="f_salida_equipo_<?php echo $equipo->getId()?>">
    <?php  if($equipo->getFEgreso()){
                echo date('d-m-Y h:i A', strtotime($equipo->getFEgreso())); 
            } else {
                echo '<font style="color: red;"><i>No se ha registrado la salida</i></font>';
            }
    ?>
</div>
<hr/>
<?php
     endforeach;
?>
