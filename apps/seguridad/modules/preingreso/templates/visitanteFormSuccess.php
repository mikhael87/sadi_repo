<?php if(isset($alerta_visitante)){ ?>
    <?php if($alerta_visitante){ ?>
    <div style="position: relative; background-color: tomato; color: white;">
        <div style="padding: 15px;"><img src="/images/icon/error48.png"/></div>
        <div style="position: absolute; left: 80px; top: 0px; padding: 5px;">
            <font class="f25b">Visitante en Alerta</font><br/>
            Motivo: <?php echo $alerta_visitante->getDescripcion(); ?>
        </div>
    </div>
    <br/>
<?php }} ?>
    
<?php if(isset($visitante['f_nacimiento'])){ ?>
    <?php 
        if(date("m-d", strtotime($visitante['f_nacimiento'])) == date('m-d')){ 
            $mensaje_feliz = 'Hoy cumple <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +1 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Mañana cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +2 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Pasado mañana cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +3 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'En 3 dias cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." +4 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'En 4 dias cumple <font class="f25b azul">'.$edad_proxima.'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -1 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Ayer cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -2 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Antes de ayer cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -3 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Hace 3 dias cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else if(date("m-d", strtotime($visitante['f_nacimiento'])) == date("m-d", strtotime(date('Y-m-d')." -4 day"))){
            $edad_proxima = $visitante['edad']+1;
            $mensaje_feliz = 'Hace 4 dias cumplio <font class="f25b azul">'.$visitante['edad'].'</font> años';
        } else {
            $mensaje_feliz = '';
        }
        
        if($mensaje_feliz != ''){
    ?>
    <div style="position: relative; background-color: #A7D5EC;">
        <div style="padding: 5px;"><img src="/images/other/feliz_cumpleanos_torta.png" width="24" height="24"/></div>
        <div style="position: absolute; right: 0px; top: 0px; padding: 5px;">
            <img src="/images/other/feliz_cumpleanos_bombas.png" height="30"/>
        </div>
        <div style="position: absolute; left: 30px; top: 0px; padding: 5px;">
            <font class="f14n gris_oscuro">
                Desea un feliz cumpleaños a nuestro visitante al llegar<br/>
                <?php echo $mensaje_feliz; ?>
            </font>
        </div>
    </div>
    <br/>
<?php }} ?>

<div style="position: relative;">
    <?php
        if($visitante['persona_id']){ ?>
            <input type="hidden" value="<?php echo $visitante['persona_id']; ?>" name="preingreso_persona[]"/>
            <div>
                <?php echo $visitante['primer_nombre'].' '.$visitante['primer_apellido']; ?> / 
                F. Nac <?php echo date('d-m-Y', strtotime($visitante['f_nacimiento'])); ?> / 
                Edad <?php echo $visitante['edad']; ?> años /
                Telf. <?php echo $visitante['telefono']; ?>
            </div>
    <?php } else { ?>
            <div class="error">Persona no registrada en el SAIME ni en SIGLAS. Debera registrarse al llegar.</div>
    <?php } ?>
</div>
    <script>
            dataString['externo'][<?php echo $visitante['persona_id']; ?>] = 1;
    </script>