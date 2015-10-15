<div style="min-width: 300px">
    <?php
    $largo= strlen($empresas_empresa->getNombre());
    $parte= $empresas_empresa->getNombre();
    if($largo > 40) {
        $parte= utf8_encode(substr(utf8_decode($parte), 0, 40));
    } ?>
    
    <font style="font-size: 15px; font-weight: bold" <?php echo (($largo != strlen($parte))? 'class="tooltip" title="[!]'.$empresas_empresa->getNombre().'[/!]"':'') ?> ><?php echo (($largo != strlen($parte))? $parte.'...': $parte) ?></font><br/>
    <font style="font-size: 12px; color: #666"><?php echo $empresas_empresa->getPublic_Estado().'-'.$empresas_empresa->getPublic_Municipio(); ?></font><br/>
    <font style="font-size: 12px; font-weight: normal"><?php echo $empresas_empresa->getRif(); ?></font><br/>
    <font style="font-size: 14px; color: #666"><?php echo $empresas_empresa->getSiglas(); ?></font><br/>
    <font style="color: #666">Email: </font>
    &nbsp;&nbsp;<?php echo (($empresas_empresa->getEmailPrincipal() !== '')? $empresas_empresa->getEmailPrincipal() : '') ?><br/>
    <font style="color: #666">Telf: </font>
    &nbsp;&nbsp;<?php echo (($empresas_empresa->getTelfUno() !== '')? $empresas_empresa->getTelfUno() : '') ?><br/><br/>
    <br/>
    <font style="font-size: 12px; color: #666">F. Inscripci&oacute;n:&nbsp;<?php echo date('d-m-Y H:m A', strtotime($empresas_empresa->getCreatedAt())); ?></font>
</div>