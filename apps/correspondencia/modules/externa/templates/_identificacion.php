<?php if ($sf_user->getAttribute('ultima_vista_recibida') == $correspondencia_correspondencia->getId()) { ?>
<div style="position: relative; z-index: 100;">
    <div style="position: absolute; top: -1px; left: 5px;">
    <?php echo image_tag('icon/last_view.gif',array('title'=>'Ultima correspondencia en la que efectuó una acción')); ?>
    </div>
</div>
<?php } ?>

<div id="list_show_i_<?php echo $correspondencia_correspondencia->getId(); ?>" style="position: relative; z-index: 100; text-align: center; color: white; background-color: <?php echo $parametros_correspondencia['color']; ?>;">
    <font class="f19b">&nbsp;<?php echo $correspondencia_correspondencia->getNCorrespondenciaEmisor(); ?></font>
</div>

<div style="position: relative; text-align: center;">
        <font color ='#2E9AFE'><?php echo $correspondencia_correspondencia->getNCorrespondenciaExterna(); ?></font>
</div>


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
            <font title="<?php echo $correspondencia_correspondencia->getOrganismos_Organismo()->getNombre(); ?>">
                <?php echo $correspondencia_correspondencia->getOrganismos_Organismo()->getSiglas(); ?> 
            </font>
            /
            <font class="f13n" title="<?php 
                echo $correspondencia_correspondencia->getOrganismos_PersonaCargo()->getNombre(); ?>">
                <?php echo ucwords(strtolower($correspondencia_correspondencia->getOrganismos_Persona()->getNombreSimple())); ?>
            </font>
        </div>
    </li>
    <?php } ?>
</ul>
</div>





<?php
$ci_para=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId());
?>



<div style="width: 200px;" class="sf_admin_form_row sf_admin_text">
<div style="position: relative">
    <div style="position: absolute; top: 0px; left: -5px;">
        <font class="f10n">Para</font>
    </div>
</div>
<ul>   

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
            <font class="f13n" title="<?php echo $list_ci_para->getCtnombre(); ?>">
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

