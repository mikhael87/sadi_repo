<script type="text/javascript" src="/js/jqueryTooltip.js"></script>
<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16n">
            <?php 
                  if(isset($valores['revision_punto_cuenta_decision'])) {
                      if($valores['revision_punto_cuenta_decision'] == 'Aprobado'){
                          $color='#00FF00';
                      } else if ($valores['revision_punto_cuenta_decision'] == 'Negado'){
                          $color='tomato';
                      } else if ($valores['revision_punto_cuenta_decision'] == 'Visto'){
                          $color='#F1F300';
                      } else if ($valores['revision_punto_cuenta_decision'] == 'Diferido'){
                          $color='sandybrown';
                      } else if ($valores['revision_punto_cuenta_decision'] == 'Otro'){
                          $color='#999999';
                      }
                      
                      echo '<h2 style="text-align: center; background-color: '.$color.'; width: 100%;">'.strtoupper($valores['revision_punto_cuenta_decision']).'</h2>';
                  }
            ?></font>
    </div>
    <div>
        <font class="f16b">Observacion: </font>
        <font class="f16n"><?php if(isset($valores['revision_punto_cuenta_observaciones'])) echo html_entity_decode($valores['revision_punto_cuenta_observaciones']); ?></font>
    </div>
    <?php
    $medios_ar= '';
        if($valores['revision_punto_cuenta_medios'] != '') {
            $medios= explode('#', $valores['revision_punto_cuenta_medios']);
            foreach($medios as $value) {
                if($value != '')
                    $medios_ar.= $value.', ';
            }
            $medios_ar.= '#end';
        }
        $medios_ar= str_replace(', #end', '', $medios_ar);
    ?>
    <div>
        <font class="f16b">Tratamiento comunicacional:&nbsp;&nbsp;&nbsp;</font>
        <font class="f16n"><?php
        if($medios_ar != '') {
            echo $medios_ar;
        }else
            echo 'Sin publicaci&oacute;n.';
        
        ?></font>
    </div>
    <?php if($medios_ar != '') {
        if($valores['revision_punto_cuenta_fecha'] == '--' || $valores['revision_punto_cuenta_fecha'] == '')
            $date= 'Inmediato';
        else
            $date = ucwords($valores['revision_punto_cuenta_fecha']);

        if($date != ''){
            if($date != 'Inmediato') {
                $months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $date = date('d', strtotime($date)) . " de " . $months[intval(date('m', strtotime($date)))] . " de " . date('Y', strtotime($date));
            }
            ?>
            <div>
                <font class="f16b">Fecha de publicaci&oacute;n:&nbsp;&nbsp;&nbsp;</font>
                <font class="f16n"><?php echo $date; ?></font>
            </div>
    <?php } } ?>
</div>
<?php
if(!isset($op)) {
    $tiempo_trans= new herramientas();
    $firman_list= Array();
    $emisor_inicial= Doctrine::getTable('Correspondencia_Correspondencia')->remitenteInicial($correspondencia_id);
    if(count($emisor_inicial)> 0)
        $firman_list=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondenciaPasivo($emisor_inicial[0]['id']);
    if(count($emisor_inicial)> 0 && count($firman_list)> 0) { ?>
    <br/>
    <div style="position: relative; width: 590px; padding: 3px; background-color: #e5e5e5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border-style: solid; border-width: 1px; border-color: #c3c3c3;">
        <div style="position: absolute; top: 3px; right: 3px; padding: 3px; text-align: right">
            <?php echo '<font class="f14b azul"><img class="tooltip" title="Correspondencia Inicial" style="width: 10px; height: 10px; vertcal-align: middle" src="/images/icon/info.png" /> '.$emisor_inicial[0]['n_correspondencia_emisor'].'</font><br/><font class="f14n">'.$tiempo_trans->tiempo_transcurrido($emisor_inicial[0]['f_envio'], FALSE).'</font><br/>' ?>
            <a class="f14n tooltip" style="text-decoration: none" href="<?php echo sfConfig::get('sf_app_correspondencia_url').'enviada/'.$correspondencia_id.'/seguimiento'; ?>" title="Seguimiento">Ver detalles...</a>
        </div>
        <?php
        echo '<font class="f14n">'.$emisor_inicial[0]['unidad'].'</font><br/>';
        foreach($firman_list as $firman) {
            echo '<font class="f14b">'.ucwords(strtolower($firman->getpn())).' '.ucwords(strtolower($firman->getpa())).'</font><font class="f14n"> ('. $firman->getCtnombre() .')</font><br/>';
        }
        //PREPARACION DE ASUNTO
        $asunto= preg_replace("/<p(.*?)>/i","",$emisor_inicial[0]['asunto']);
        $asunto= preg_replace("[</p>]","",$asunto);
        
        echo '<div style="max-width: 500px"><font class="f14n">Asunto:</font> <font class="f14b tooltip" '. ((strlen($asunto) > 80)? 'title="'. $asunto .'"' : '') .'>'.((strlen($asunto) > 80) ? substr($asunto,0,80).'...' : $asunto).'</font></div>';
        ?>
    </div>
<?php } } ?>