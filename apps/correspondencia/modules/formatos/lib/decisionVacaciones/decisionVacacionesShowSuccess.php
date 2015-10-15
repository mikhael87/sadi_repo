<div style="position: relative;" class="sf_admin_form_row sf_admin_text formato_seguimiento_ver">
    <div>
        <font class="f16b">Solicitante: </font>
        <font class="f16n"><?php 
            $correspondencia_xx = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_id);
            
            $formato_padre = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_xx->getPadreId());
            $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($formato_padre[0]->getCampoUno());
        
            echo $funcionario->getPrimerNombre().' '.$funcionario->getSegundoNombre().', '.$funcionario->getPrimerApellido().' '.$funcionario->getSegundoApellido();
            ?></font>
    </div>
    <hr>
    <div>
        <font class="f16b">Resultado: </font>
        <font class="f16n"><?php if(isset($valores['decisionVacaciones_resultado'])) echo html_entity_decode($valores['decisionVacaciones_resultado']); ?></font>
    </div>
    <div>
        <font class="f16b">Observaciones: </font>
        <font class="f16n"><?php if(isset($valores['decisionVacaciones_observaciones'])) echo html_entity_decode($valores['decisionVacaciones_observaciones']); ?></font>
    </div>
</div>