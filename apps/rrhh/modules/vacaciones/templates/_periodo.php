<div style="position: relative; width: 180px;">
    <div style="position: relative; font-size: 13px; left: 20px; font-size:14px; color: #00008B; font-weight: bold;" title="">
        <?php echo $rrhh_vacaciones->getPeriodoVacacional(); ?>
    </div>
    
    <div style="position: relative; font-size: 13px; left: 0px;"><br/>
        <?php 
            if($rrhh_vacaciones->getStatus()=='D')
                echo image_tag('icon/online.png')." Disponible"; 
            elseif($rrhh_vacaciones->getStatus()=='A')
                echo image_tag('icon/online.png')." Disfrutando"; 
            elseif($rrhh_vacaciones->getStatus()=='E')
                echo image_tag('icon/absent.png')." Por Disfrutar"; 
            elseif($rrhh_vacaciones->getStatus()=='S')
                echo image_tag('icon/absent.png')." Solicitada"; 
            elseif($rrhh_vacaciones->getStatus()=='F')
                echo image_tag('icon/offline.png')." Finalizada"; 
        ?>
        <br/>
    </div>    
</div>
