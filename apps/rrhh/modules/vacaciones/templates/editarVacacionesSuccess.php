<script>
    function saveVacaciones() {
        var variables = '';

        $(".periodo_dias_pendientes").each(function(index) {
            variables += '&periodos['+$(this).attr('id')+']='+$(this).val();
        })
        
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/guardarVacaciones',
            type:'POST',
            dataType:'html',
            data:'&funcionario_id=<?php echo $funcionario->getId();?>'+variables,
            success:function(data, textStatus){
                $('#periodos_funcionario_<?php echo $funcionario->getId();?>').html(data);
                close_window_right();
            }});
    };            
    
    function periodoAdelantado() {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/generarPeriodoAdelantado',
            type:'POST',
            dataType:'html',
            data:'&funcionario_id=<?php echo $funcionario->getId();?>',
            success:function(data, textStatus){
                $('#periodos_funcionario_<?php echo $funcionario->getId();?>').html(data);
                close_window_right();
            }});
    };
    
    function eliminarPeriodo() {
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_rrhh_url'); ?>vacaciones/eliminarUltimoPeriodo',
            type:'POST',
            dataType:'html',
            data:'&funcionario_id=<?php echo $funcionario->getId();?>',
            success:function(data, textStatus){
                  if(data== 'error') {
                      alert('Existen solicitudes sobre el periodo que intenta eliminar.');
                  }else {
                      $('#periodos_funcionario_<?php echo $funcionario->getId();?>').html(data);
                      close_window_right();
                  }
            }});
    };
</script>

<div id="sf_admin_container">
    <h1>Editar las Vacaciones de <?php echo $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido(); ?></h1>

    <div id="sf_admin_content">

        <fieldset id="sf_fieldset_informacion">

            <h2>Informacion de funcionario</h2>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <b>Cedula: </b><?php echo $funcionario->getCi(); ?><br/>
                    <b>Nombre: </b><?php echo $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido(); ?><br/><br/>
                    <b>Cargos desempeñados: </b><br/>
                    <div style="max-height: 60px; overflow-y: auto; overflow-x: none; background-color: #D8D8D8; padding: 10px; border: 1px solid;">
                        <?php
                        foreach ($cargos as $cargo) {
                            echo "<b>".$cargo->getUnidad()."</b><br/>";
                            echo $cargo->getCargoCondicion()." - ";
                            echo $cargo->getCargoTipo()."<br/>";
                            echo "desde el ".date('d-m-Y', strtotime($cargo->getFIngreso()));
                            if($cargo->getFRetiro())
                                echo "<fond style='color: #DF0101;'><b> hasta el ".date('d-m-Y', strtotime($cargo->getFRetiro()))."</b></fond>.";
                            else
                                echo "<fond style='color: #088A08;'><b> hasta la actualidad</b></fond>.";
                            echo "<hr/>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </fieldset> 

        <fieldset id="sf_fieldset_periodos">
            <h2>Periodos Vacacionales</h2>
            <div style="position: relative; width: 100%;">
                <div style="position: absolute; top: -30px; right: 10px;">
                    <a href="#" onclick="eliminarPeriodo(); return false;">Eliminar &uacute;ltimo periodo</a>&nbsp;&nbsp;&nbsp;
                    <a href="#" onclick="periodoAdelantado(); return false;">Generar un (1) periodo por adelantado <?php echo image_tag('icon/reload.png'); ?></a>
                </div>
            </div>
            <div style="position: relative; font-size: 13px; width: 450px;">
                <div style="position: relative;">
                    <div style="position: absolute; font-size: 8px; top: 0px; left: 125px;">
                        Fecha de Cumplimiento
                    </div>
                    <div style="position: absolute; font-size: 8px; top: 0px; left: 225px;">
                        Establecidos
                    </div>
                    <div style="position: absolute; font-size: 8px; top: 0px; left: 290px;">
                        Adicionales
                    </div>
                    <div style="position: absolute; font-size: 8px; top: 0px; left: 365px;">
                        Totales
                    </div>
                    <div style="position: absolute; font-size: 8px; top: 0px; left: 430px;">
                        Pendientes
                    </div>
                   <div style="position: absolute; font-size: 8px; top: 0px; left: 540px;">
                        Estatus
                    </div>
                </div>
            </div>

            <?php foreach ($vacaciones as $vacacion) { ?>
            <div class="sf_admin_form_row sf_admin_text">
                <div>
                    <label><?php echo $vacacion->getPeriodoVacacional(); ?></label>
                    <div class="content"> 
                        <?php echo date('d-m-Y', strtotime($vacacion->getFCumplimiento())); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $vacacion->getDiasDisfruteEstablecidos(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $vacacion->getDiasDisfruteAdicionales(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $vacacion->getDiasDisfruteTotales(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <select id="<?php echo $vacacion->getPeriodoVacacional(); ?>" class="periodo_dias_pendientes">
                            <?php for($i=0;$i<=$vacacion->getDiasDisfruteEstablecidos();$i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($i == $vacacion->getDiasDisfrutePendientes()) echo "selected"; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
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
                </div>
            </div>
            <?php } ?>
            <br/>
        </fieldset>
        <hr/>
        <li class="sf_admin_action_save">
            <input id="guardar" type="button" value="Guardar" onclick="saveVacaciones(); return false;">
        </li>
        <br/><br/>
    </div>

</div>