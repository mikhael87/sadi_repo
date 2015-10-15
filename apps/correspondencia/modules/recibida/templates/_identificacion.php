<script>
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
        <div style="position: absolute; left: -16px; top: 22px;"><?php echo image_tag('icon/confidencial.png'); ?></div>
    </div>
<?php } ?>

<?php if ($sf_user->getAttribute('ultima_vista_recibida') == $correspondencia_correspondencia->getId()) { ?>
    <div style="position: relative; z-index: 101;">
        <div style="position: absolute; top: -1px; left: -10px;">
        <?php echo image_tag('icon/last_view.gif',array('title'=>'Ultima correspondencia en la que efectuó una acción')); ?>
        </div>
    </div>
<?php } ?>

<div style="position: relative;">
    <div id="list_show_i_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: absolute; left: -14px; width: 234px; z-index: 100; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
        <font class="f19b">&nbsp;<?php echo $correspondencia_correspondencia->getNCorrespondenciaEmisor(); ?></font>
    </div>
</div>
<br/>
<div style="position: relative; text-align: center;">
        <font class="f19n"><?php echo date('d-m-Y h:i:s A', strtotime($correspondencia_correspondencia->getFEnvio())); ?></font>
</div>

<?php $c_firman=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId(), TRUE); ?>

<div style="width: 200px;" class="sf_admin_form_row sf_admin_text">
<div style="position: relative">
    <div style="position: absolute; top: 0px; left: -5px;">
        <font class="f10n">De</font>
    </div>
</div>
<ul>    
    <?php if($correspondencia_correspondencia->getEmisorOrganismoId()) { ?>
    <li style="width: 200px;">
        <div>
            <div style="position: relative">
                <div style="position: absolute; top: -10px; left: -5px; background-color: #FFCC33">
                    <font class="f10n">Externo</font>
                </div>
            </div>
            <font title="<?php echo $correspondencia_correspondencia->getOrganismos_Organismo(); ?>">
                <?php echo $correspondencia_correspondencia->getOrganismos_Organismo()->getSiglas(); ?> 
            </font>
            /
            <font class="f13n" title="<?php echo $correspondencia_correspondencia->getOrganismos_PersonaCargo(); ?>">
                <?php 
                    if($correspondencia_correspondencia->getEmisorPersonaId()){
                        echo ucwords(strtolower($correspondencia_correspondencia->getOrganismos_Persona())); 
                    }
                ?>
            </font>
        </div>
    </li>
    <?php } elseif($correspondencia_correspondencia->getEmisorUnidadId()) { 
        $ci_de=Doctrine::getTable('Correspondencia_Correspondencia')->unidadEmisor($correspondencia_correspondencia->getId());
        $c_firman=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId(), TRUE);

        foreach ($ci_de as $list_ci_de):?>
    <li>
        <div>
            <font title="<?php echo ucwords(strtolower($list_ci_de->getUnombre())); ?>">
                <?php echo $list_ci_de->getSiglas(); ?>
            </font>
            <?php foreach ($c_firman as $list_firman): ?>
                /<font class="f13n" title="<?php echo ucwords(strtolower($list_firman->getCtnombre())); ?>">
                <?php
                    echo ucwords(strtolower($list_firman->getPn())).' '.ucwords(strtolower($list_firman->getPa()));
                    if($list_firman->getFirma()=='S') echo image_tag('icon/tick.png');
                ?>
                </font>
            <?php endforeach; ?>
        </div>
    </li>
    <?php endforeach; } ?>
</ul>
</div>





<?php
$ce_para=Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());

$ci_para=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
?>

<?php
    $config_vb = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->funcionarios_vistobueno($correspondencia_correspondencia->getId());
    if(count($config_vb) > 0) :
?>
    <table style="width: 100%">
        <thead>
            <tr>
                <th colspan="2">
                    <a id="stick_vistobueno" class="stick_vistobueno" href="#" onClick="javascript: conmutar_vb(<?php echo $correspondencia_correspondencia->getId(); ?>); return false;"><font style="font-size: 10px; color: #007fc1"><?php echo (($config_vb[0]['nonfirm'] == 0) ? 'Vistos bueno' : 'Visto bueno: Incompleto'); ?></font></a>
                </th>
                <th style="text-align: right">
                    <?php
                    if($config_vb[0]['nonfirm'] == 0)
                        echo image_tag('icon/tick.png', array('style' => 'cursor: pointer', 'onClick' => 'javascript: conmutar_vb(' . $correspondencia_correspondencia->getId() . ')'));
                    else
                        echo image_tag('icon/error.png', array('style' => 'cursor: pointer', 'onClick' => 'javascript: conmutar_vb(' . $correspondencia_correspondencia->getId() . ')'));
                    ?>
                </th>
            </tr>
        </thead>
        <tbody style="display: none" id="div_vistobueno_<?php echo $correspondencia_correspondencia->getId(); ?>" >
            <?php
                $tiempo_trans= new herramientas();
                foreach($config_vb as $value) {
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
                                    foreach($config_vb as $val) {
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
<?php endif;?>

<div style="width: 200px;" class="sf_admin_form_row sf_admin_text">
    <div style="position: relative">
        <div style="position: absolute; top: 0px; left: -5px;">
            <font class="f10n">Para</font>
        </div>
    </div>

    <div style="max-height: 150px; overflow-y: auto; overflow-x: hidden;">
        <ul>   
          <?php foreach ($ce_para as $list_ce_para) { ?>
            <li>
                <div>
                    <div style="position: relative">
                        <div style="position: absolute; top: -10px; left: -5px; background-color: #FFCC33">
                            <font class="f10n">Externo</font>
                        </div>
                    </div>
                    <font title="<?php echo $list_ce_para->getReceptorOrganismo(); ?>">
                        <?php echo $list_ce_para->getReceptorOrganismoSiglas(); ?>
                    </font>
                    /
                    <font class="f13n" title="<?php echo $list_ce_para->getReceptorPersonaCargo() ?>">
                        <?php echo ucwords(strtolower($list_ce_para->getReceptorPersona())); ?>
                    </font>
              </div>
            </li>
          <?php } ?>

          <?php foreach ($ci_para as $list_ci_para): ?>
            <li>
                <div>
                    <font title="<?php echo ucwords(strtolower($list_ci_para->getUnombre())); ?>">
                        <?php echo $list_ci_para->getSiglas(); ?>
                    </font>
                        /
                        <?php
                            if($list_ci_para->getCopia()=='S')
                                echo '<font class="f13b azul"> CC: </font>';
                         ?>
                    <font class="f13n" title="<?php echo ucwords(strtolower($list_ci_para->getCtnombre())); ?>">
                        <?php

                            echo ucwords(strtolower($list_ci_para->getPn())).' '.ucwords(strtolower($list_ci_para->getPa()));

                            if($list_ci_para->getFRecepcion()) {
                         ?>
                            <font title="Leido en el sistema en fecha <?php echo date('d-m-Y h:i:s A', strtotime($list_ci_para->getFRecepcion())); ?>"><?php echo image_tag('icon/open_read.png'); ?></font>
                         <?php } ?>
                    </font>
              </div>
            </li>
           <?php endforeach; ?>

        </ul>
    </div>
</div>



</div>

