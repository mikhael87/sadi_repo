<?php use_helper('jQuery') ?>
<script>
    $(document).ready(function(){
        $(".div_body").hide();
    });

    function conmutar_vb(id) {
        $("#div_vistobueno_"+ id).toggle();
    }
</script>

<style>
    .stick_vistobueno {
        text-decoration: none !important;
    }
</style>

<?php if($parametros_correspondencia['privado'] == 'S'){ ?>
    <div style="position: relative;">
        <div style="position: absolute; left: -23px; top: 18px;"><?php echo image_tag('icon/confidencial.png'); ?></div>
    </div>
<?php } ?>

<?php if ($sf_user->getAttribute('ultima_vista_enviada') == $correspondencia_correspondencia->getId()) { ?>
    <div style="position: relative; z-index: 100;">
        <div style="position: absolute; top: -1px; left: 5px;">
        <?php echo image_tag('icon/last_view.gif',array('title'=>'Ultima correspondencia en la que efectuó una acción')); ?>
        </div>
    </div>
<?php } ?>

<div style="position: relative; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
    <font class="f19b">&nbsp;
    <?php
        if($correspondencia_correspondencia->getNCorrespondenciaEmisor()==$correspondencia_correspondencia->getId())
            echo "SIN NÚMERO";
        else
            echo $correspondencia_correspondencia->getNCorrespondenciaEmisor(); ?>
    </font>
</div>

<div style="width: 200px;" class="sf_admin_form_row sf_admin_text">
<div style="position: relative">
    <div style="position: absolute; top: 0px; left: -5px;">
        <font class="f10n">De</font>
    </div>
</div>

<ul>
  <?php foreach ($emisores as $emisor){ ?>
    <li>
        <div style="position: relative; height: 25px;">
            <div style="position: absolute; left: 0px; top: 0px; z-index: 201;" title="<?php echo $emisor->getCtnombre().' - '.$emisor->getUnombre(); ?>">
                <?php echo ucwords(strtolower($emisor->getpn())); ?>
                <?php echo ucwords(strtolower($emisor->getpa())); ?>
                <?php if($emisor->getFirma()=='S') echo image_tag('icon/tick.png'); ?>
            </div>
            <?php if($emisor->getProteccion()) { 
                // ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
                // ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
                // ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
                    $manager = Doctrine_Manager::getInstance();
                    $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
                    $cacheDriver->delete('correspondencia_enviada_list_formato_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_formato_list_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_enviada_list_funcionario_emisor_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_funcionario_emisor_list_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_enviada_list_receptor_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_receptor_list_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_AnexoArchivo_correspondencia_id_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_AnexoFisico_correspondencia_id_'.$correspondencia_correspondencia->getId());
                    $cacheDriver->delete('correspondencia_enviada_list_receptor_externos_'.$correspondencia_correspondencia->getId());
                // FIN ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
                // FIN ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
                // FIN ELIMINAMOS LA CACHE DE CADA CORRESPONDECNIA QUE TENGA PROTECCION DE FIRMA ELECTRONICA
            ?>
            <div id="div_firma_ident_<?php echo $correspondencia_correspondencia->getId().'_'.$emisor->getId(); ?>" style="cursor: pointer;">
                <div id="div_firma_emisor_<?php echo $emisor->getId(); ?>" style="position: absolute; left: -60px; top: 0px; background-color: #B2CBFF; width: 245px; z-index: 200;" onclick="ver_firma(<?php echo $emisor->getId(); ?>);">
                    <img id="icon_key_process" src="/images/icon/key_good.png" width="40"/>
                </div> 
                
                <script>
                    verify_signature(<?php echo $correspondencia_correspondencia->getId(); ?>,<?php echo $emisor->getId(); ?>,'formatos/verificarFirma');
                </script>
            </div>    
            <?php } ?>
            <br/>
        </div>
    </li>
   <?php } ?>
</ul>
    
<?php if(count($vistos_buenos) > 0) { ?>
    <table style="width: 100%">
        <thead>
            <tr>
                <th colspan="2">
                    <a id="stick_vistobueno" class="stick_vistobueno" href="#" onClick="javascript: conmutar_vb(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;"><font style="font-size: 10px; color: #007fc1"><?php echo (($vistos_buenos[0]['nonfirm'] == 0) ? 'Visto bueno: Listo' : 'Visto bueno: En espera'); ?></font></a>
                </th>
                <th style="text-align: right">
                    <?php
                    if($vistos_buenos[0]['nonfirm'] == 0)
                        echo image_tag('icon/tick.png', array('style' => 'cursor: pointer', 'onClick' => 'javascript: conmutar_vb(' . $correspondencia_correspondencia->getId() . ')'));
                    else
                        echo image_tag('icon/error.png', array('style' => 'cursor: pointer', 'onClick' => 'javascript: conmutar_vb(' . $correspondencia_correspondencia->getId() . ')'));
                    ?>
                </th>
            </tr>
        </thead>
        <tbody class="div_body" id="div_vistobueno_<?php echo $correspondencia_correspondencia->getId(); ?>" >
            <?php
                $tiempo_trans= new herramientas();
                foreach($vistos_buenos as $value) {
                    $icon= '';
                    $cadena = '<tr>';
                    if($value->getFuncionarioId()== $sf_user->getAttribute('funcionario_id'))
                        $cadena .= '<td><font style="font-size: 10px"><b>'.$value->getPnombre().' '.$value->getPapellido().'</b></font></td>';
                    else
                        $cadena .= '<td><font style="font-size: 10px">'.$value->getPnombre().' '.$value->getPapellido().'</font></td>';
                    $cadena .= '<td style="text-align: right"><font style="font-size: 9px">';
                    if($value->getStatus() == 'V') {
                        $icon = image_tag('icon/tick.png', array('class' => 'tooltip', 'title' => 'Ya fue verificado'));
                        $cadena .= '<font style="color: #666">'.date('d-m-y g:i a', strtotime($value->getUpdatedAt())).'</font>';
                    } else {
                        if($value->getStatus() == 'E') {
                            if($value->getTurno()) {
                                $icon = image_tag('icon/clock.png', array('class' => 'tooltip', 'title' => 'A&uacute;n en espera por visto bueno'));
                                if($value->getOrden() == 1) {
                                    $cadena .= '<font style="color: red">'.$tiempo_trans->tiempo_transcurrido($value->getUpdatedAt(), TRUE). '</font>';
                                }else {
                                    $prev_fecha= '';
                                    foreach($vistos_buenos as $val) {
                                        if($val->getOrden() == $value->getOrden()- 1) {
                                            $prev_fecha= $val->getUpdatedAt();
                                        }
                                    }
                                    $cadena .= '<font style="color: red">'.$tiempo_trans->tiempo_transcurrido($prev_fecha, TRUE). '</font>';
                                }
                            }
                        } else {
                            if($value->getStatus() == 'D')
                                $icon= image_tag('icon/delete.png', array('class' => 'tooltip', 'title' => 'Cargo desincorporado'));
                        }
                    }
                    $cadena .= '</font></td>';
                    $cadena .= '<td>' . $icon . '</td>';
                    $cadena .= '</tr>';

                    echo $cadena;
                }
            ?>
        </tbody>
    </table>

    <div id="div_vistobueno_<?php echo $correspondencia_correspondencia->getId(); ?>" style="display: none; background-color: #e1e1e1; padding: 2px; width: auto">

    </div>
<?php } ?>
</div>

<div style="width: 200px;" class="sf_admin_form_row sf_admin_text">
    <div style="position: relative">
        <div style="position: absolute; top: 0px; left: -5px;">
            <font class="f10n">Para</font>
        </div>
    </div>
    
    <div style="max-height: 150px; overflow-y: auto; overflow-x: hidden;">
        <ul>
          <?php foreach ($receptores_organismo as $receptor_organismo) { ?>
            <li>
                <div>
                    <div style="position: relative">
                        <div style="position: absolute; top: -7px; left: -5px; background-color: #FFCC33">
                            <font class="f10n">Externo</font>
                        </div>
                    </div>
                    <font title="<?php echo $receptor_organismo->getReceptorOrganismo(); ?>">
                        <?php echo $receptor_organismo->getReceptorOrganismoSiglas(); ?>
                    </font>
                    /
                    <font class="f13n" title="<?php echo $receptor_organismo->getReceptorPersonaCargo() ?>">
                        <?php echo ucwords(strtolower($receptor_organismo->getReceptorPersona())); ?>
                    </font>
              </div>
            </li>
          <?php } ?>

          <?php foreach ($receptores_unidad as $receptor_unidad): ?>
            <li>
                <div>
                    <font title="<?php echo $receptor_unidad->getUnombre(); ?>">
                        <?php echo $receptor_unidad->getSiglas(); ?>
                    </font>
                        /
                        <?php
                            if($receptor_unidad->getCopia()=='S')
                                echo '<font class="f13b azul"> CC: </font>';
                         ?>
                    <font class="f13n" title="<?php echo $receptor_unidad->getCtnombre(); ?>">
                        <?php

                            echo ucwords(strtolower($receptor_unidad->getPn())).' '.ucwords(strtolower($receptor_unidad->getPa()));

                            if($receptor_unidad->getFRecepcion()) {
                         ?>
                            <font title="Leido en el sistema en fecha <?php echo date('d-m-Y h:i:s A', strtotime($receptor_unidad->getFRecepcion())); ?>"><?php echo image_tag('icon/open_read.png'); ?></font>
                         <?php } ?>
                    </font>
              </div>
            </li>
           <?php endforeach; ?>

            <?php
            if($correspondencia_correspondencia->getStatus() == 'F') {
                echo "<tr><td><b>TODOS LOS INVOLUCRADOS EN ESTA CORRESPONDENCIA</b></td></tr>";
            }
        ?>
        </ul>
    </div>
</div>

