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
    
<?php if(isset($preingresos)){ ?>
    <?php foreach ($preingresos as $preingreso) {?>
    <div style="position: relative; background-color: #B8E834; color: white;">
        <?php $sf_user->setAttribute('seguridad_preingreso',$preingreso->getId()); ?>
        <div style="padding: 15px;"><img src="/images/icon/hand_ok48.png"/></div>
        <div style="position: absolute; left: 80px; top: 0px; padding: 5px;">
            <font class="f25b">¡Este visitante fue preingresado!</font>&nbsp;&nbsp;
            Por favor capture la foto y llene los campos faltantes<br/><br/>
            Le dio el preingreso <?php echo $preingreso->getUserCreate(); ?> 
            en fecha <?php echo date('d-m-Y h:i A', strtotime($preingreso->getCreatedAt())); ?><br/>
            identifico que llegaria 
            <?php
                if($preingreso->getFIngresoPosibleInicio() == $preingreso->getFIngresoPosibleFinal()){
                    echo ' el dia '.date('d-m-Y', strtotime($preingreso->getFIngresoPosibleInicio()));
                } else {
                    echo ' entre los dias '.date('d-m-Y', strtotime($preingreso->getFIngresoPosibleInicio())).' y '.date('d-m-Y', strtotime($preingreso->getFIngresoPosibleFinal())); 
                }
            ?>
        </div>
    </div>
    <br/>
    <script>        
        $("#unidad_recibe option[value='<?php echo $preingreso->getUnidadId(); ?>']").attr('selected', 'selected');
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/funcionarioRecibe',
            type:'POST',
            dataType:'html',
            data: {u_id: <?php echo $preingreso->getUnidadId(); ?>},
            success:function(data, textStatus){
                $('#div_funcionario_recibe').html(data)
            },
            complete: function(json) {
                $("#funcionario_recibe option[value='<?php echo $preingreso->getFuncionarioId(); ?>']").attr('selected', 'selected');
                
                $('#div_ext_unidad').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Buscando extenciones telefonicas de la unidad...');

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '<?php echo sfConfig::get('sf_app_seguridad_url'); ?>ingresa/extencionesTelefonicasFuncionario',
                    data: {cargo_id: funcionarios_cargo[<?php echo $preingreso->getFuncionarioId(); ?>]},
                    success:function(data, textStatus){
                        $('#div_ext_funcionarios').html(data);
                    }
                });
            }});
            
        $("#seguridad_ingreso_motivo_id option[value='<?php echo $preingreso->getMotivoId(); ?>']").attr('selected', 'selected');
        $("#seguridad_ingreso_motivo_visita").val('<?php echo $preingreso->getMotivoVisita(); ?>');
    </script>
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
        <div style="padding: 15px;"><img src="/images/other/feliz_cumpleanos_torta.png" width="48" height="48"/></div>
        <div style="position: absolute; right: 0px; top: 0px; padding: 5px;">
            <img src="/images/other/feliz_cumpleanos_bombas.png" height="60"/>
        </div>
        <div style="position: absolute; left: 80px; top: 0px; padding: 5px;">
            <font class="f25b gris_oscuro">Desea un feliz cumpleaños a nuestro visitante</font><br/><br/>
            <?php echo $mensaje_feliz; ?>
        </div>
    </div>
    <br/>
<?php }} ?>

<div style="position: relative;">
    <?php
        if($visitante['persona_id']){ ?>
            <input type="hidden" value="<?php echo $visitante['persona_id']; ?>" name="seguridad_ingreso[persona_id]"/>
            <label>Cedula</label><div><?php echo $visitante['nacionalidad'].'-'.$visitante['cedula']; ?></div><br/>
            <label>Nombre</label><div><?php echo $visitante['primer_nombre'].' '.$visitante['primer_apellido']; ?></div><br/>
            <label>F. Nac</label><div><?php echo date('d-m-Y', strtotime($visitante['f_nacimiento'])); ?></div><br/>
            <label>Edad</label><div><?php echo $visitante['edad']; ?> años</div><br/>
            <label>Telefono</label><div>
                <?php echo '<input type="text" name="seguridad_persona[telefono]" value="'.$visitante['telefono'].'"/>'; ?>
            </div><br/>
            <label>Correo electronico</label><div>
                <?php echo '<input type="text" name="seguridad_persona[correo_electronico]" value="'.$visitante['correo_electronico'].'"/>'; ?>
            </div>
            
            <?php if(count($ingresos)>0){ ?>
            <div style="position: absolute; width: 397px; padding-left: 5px; top: -7px; left: 297px; background-color: #CCCCFF"><b>Ingresos anteriores</b></div>
            <div style="position: absolute; top: 10px; left: 297px; height: 175px; width: 400px; border: solid 1px; overflow-y: auto; overflow-x: none;">
                <table>
                <?php
                foreach ($ingresos as $ingreso) { 
                    $color = '';
                    if($ingreso->getFEgreso()=='') $color = 'background-color: orangered;'
                ?>
                    <tr style="<?php echo $color; ?>">
                        <td>
                            <img src="/uploads/seguridad/<?php echo $ingreso->getImagen(); ?>" width="50"/>
                        </td>
                        <td class="f11n">
                            <?php 
                            echo '<b>F. Ingreso: </b>'.date('d-m-Y h:i A', strtotime($ingreso->getFIngreso())).' / ';
                            echo '<b>F. Egreso: </b>';
                            if($ingreso->getFEgreso()=='')
                                echo '<b style="color: red; background-color: white;">&nbsp;&nbsp;SIN REGISTRO&nbsp;&nbsp;</b><br/>';
                            else
                                echo date('d-m-Y h:i A', strtotime($ingreso->getFEgreso())).'<br/>';
                            
                            echo '<b>Motivo: </b>'.$ingreso->getMotivoClasificado().'<br/><br/>';
                            //echo $ingreso->getMotivoVisita();
                            echo '<b>Unidad: </b>'.$ingreso->getUnidad().'<br/>';
                            echo '<b>Funcionario: </b>'.$ingreso->getFuncionarioPrimerNombre().' '.$ingreso->getFuncionarioPrimerApellido();
                            ?>
                        </td>    
                    </tr>
                <?php } ?>
                </table>
            </div>
            <?php } ?>
            <script>equipos_persona(<?php echo $visitante['persona_id']; ?>);</script>
    <?php
        } else { ?>
            <input type="hidden" value="" name="seguridad_ingreso[persona_id]"/>
            <label>Cedula</label>
            <div>
                <?php if(isset($visitante['nacionalidad'])) { ?>
                    <input type="hidden" name="seguridad_persona[nacionalidad]" value="<?php echo $visitante['nacionalidad']; ?>"/>
                    <?php echo $visitante['nacionalidad']; ?>-
                <?php } else { ?>
                    <select name="seguridad_persona[nacionalidad]">
                        <option value="V">V</option>
                        <option value="E">E</option>
                    </select>
                <?php } ?>
                <input type="hidden" value="<?php echo $visitante['cedula']; ?>" name="seguridad_persona[ci]"/>
                <?php echo $visitante['cedula']; ?>
            </div><br/>
            <label>Nombre</label>
            <div>
                <?php if(isset($visitante['primer_nombre'])) { ?>
                    <input type="hidden" name="seguridad_persona[primer_nombre]" value="<?php echo $visitante['primer_nombre']; ?>"/>
                    <?php echo $visitante['primer_nombre']; ?>
                <?php } else { ?>
                    <input type="text" name="seguridad_persona[primer_nombre]"/>
                <?php } ?>
            </div><br/>
            <label>Apellido</label>
            <div>
                <?php if(isset($visitante['primer_apellido'])) { ?>
                    <input type="hidden" name="seguridad_persona[primer_apellido]" value="<?php echo $visitante['primer_apellido']; ?>"/>
                    <?php echo $visitante['primer_apellido']; ?>
                <?php } else { ?>
                    <input type="text" name="seguridad_persona[primer_apellido]"/>
                <?php } ?>
            </div><br/>
            <label>F. Nac</label>
            <div>
                <?php if(isset($visitante['f_nacimiento'])) { ?>
                    <input type="hidden" name="seguridad_persona[f_nacimiento]" value="<?php echo date('Y-m-d', strtotime($visitante['f_nacimiento'])); ?>"/>
                    <?php echo date('d-m-Y', strtotime($visitante['f_nacimiento'])); ?>
                <?php } else { ?>
                    <input type="text" name="seguridad_persona[f_nacimiento]" id="persona_f_nacimiento" class="campofechahasDatepicker"/>
                    <script>
                        $('#persona_f_nacimiento').datepicker({
                            dateFormat: 'yy-mm-dd',
                            yearRange: '1900:<?php echo date('Y'); ?>',
                            changeMonth: true,
                            changeYear: true,
                        });
                    </script>
                <?php } ?>
            </div><br/>
            <label>Telefono</label>
            <div>
                <?php if(isset($visitante['telefono'])) { ?>
                    <input type="text" name="seguridad_persona[telefono]" value="<?php echo $visitante['telefono']; ?>"/>
                    <?php echo $visitante['telefono']; ?>
                <?php } else { ?>
                    <input type="text" name="seguridad_persona[telefono]"/>
                <?php } ?>
            </div><br/>
            <label>Correo Electronico</label>
            <div>
                <?php if(isset($visitante['correo_electronico'])) { ?>
                    <input type="text" name="seguridad_persona[correo_electronico]" value="<?php echo $visitante['correo_electronico']; ?>"/>
                    <?php echo $visitante['correo_electronico']; ?>
                <?php } else { ?>
                    <input type="text" name="seguridad_persona[correo_electronico]"/>
                <?php } ?>
            </div><br/>
    <?php
        }
    ?>
</div>