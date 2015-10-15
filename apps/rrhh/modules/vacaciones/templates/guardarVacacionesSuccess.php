<?php foreach ($vacaciones as $vacacion) { ?>
    <div style="position: relative; font-size: 13px; width: 600px;">
        <div style="position: relative;">
            <div style="position: absolute; top: 10px; left: 0px;">
                <?php echo $vacacion->getPeriodoVacacional(); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 80px;">
                <?php echo date('d-m-Y', strtotime($vacacion->getFCumplimiento())); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 180px;">
                <?php echo $vacacion->getDiasDisfruteEstablecidos(); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 240px;">
                <?php echo $vacacion->getDiasDisfruteAdicionales(); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 300px;">
                <?php echo $vacacion->getDiasDisfruteTotales(); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 360px;">
                <?php echo $vacacion->getDiasDisfrutePendientes(); ?>
            </div>
            <div style="position: absolute; top: 10px; left: 420px;">
                <?php 
                    if($vacacion->getStatus()=='D')
                        echo image_tag('icon/online.png')." Disponible"; 
                    elseif($vacacion->getStatus()=='A')
                        echo image_tag('icon/online.png')." Disfrutando"; 
                    elseif($vacacion->getStatus()=='E')
                        echo image_tag('icon/absent.png')." Por Disfrutar"; 
                    elseif($vacacion->getStatus()=='S')
                        echo image_tag('icon/absent.png')." Solicitada"; 
                    elseif($vacacion->getStatus()=='F')
                        echo image_tag('icon/offline.png')." Finalizada"; 
                ?>
            </div>
            <br/>
        </div>
    </div>
<?php } ?>