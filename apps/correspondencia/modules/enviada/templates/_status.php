<?php if($correspondencia_correspondencia->getStatus() == 'C') { ?>
    <div class="status_etiqueta" style="background-color: silver; color: white;"><b>&nbsp;&nbsp;Creación&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'P') { ?>
    <div class="status_etiqueta" style="background-color: #B40404; color: white;"><b>&nbsp;&nbsp;Pausado&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'E') { ?>
    <div class="status_etiqueta" style="background-color: #04B404; color: white;"><b>&nbsp;&nbsp;Enviada&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'L') { ?>
    <div class="status_etiqueta" style="background-color: #2E9AFE; color: white;"><b>&nbsp;&nbsp;Recibido&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'X') { ?>
    <div class="status_etiqueta" style="background-color: #0404B4; color: white;"><b>&nbsp;&nbsp;Asignada&nbsp;&nbsp;</b></div>
<?php } elseif($correspondencia_correspondencia->getStatus() == 'F') { ?>
    <div class="status_etiqueta" style="background-color: #0B0B61; color: white;"><b>&nbsp;&nbsp;Finalizado&nbsp;&nbsp;</b></div>
<?php } ?>
