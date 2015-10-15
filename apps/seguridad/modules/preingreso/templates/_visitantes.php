<div style="width: 250px;">
    <table style="width: 250px;">
    <?php 
        $personas_preingreso = Doctrine::getTable('Seguridad_Persona')->personasPreingreso($seguridad_preingreso->getId()); 
        
        foreach ($personas_preingreso as $persona_preingreso) {
            $imagenes=''; $title='[!]El visitante no ha ingresado[/!]';
            
            $persona = 'CI:'.$persona_preingreso->getCi().' / ';
            $persona .= $persona_preingreso->getPrimerNombre().' '.$persona_preingreso->getPrimerApellido().'<br/>';
            if($persona_preingreso->getStatus()=='A'){
                $title = '[!]El visitante ya ingreso[/!]';
                $title .= 'Fecha '.date('d-m-Y h:i A', strtotime($persona_preingreso->getFIngreso()));
                $title .= '<br/>Registrado por '.$persona_preingreso->getRegistrador();
                
                $imagenes = '<img src="/images/icon/tick.png"/>';
                if($persona_preingreso->getFEgreso() != ''){
                    $title .= '<br/><br/>[!]El visitante ya se retiro[/!]';
                    $title .= 'Fecha '.date('d-m-Y h:i A', strtotime($persona_preingreso->getFEgreso()));
                    $title .= '<br/>Despachado por '.$persona_preingreso->getRegistrador();
                    
                    $imagenes .= '<img src="/images/icon/hand_ok.png"/>';
                }
            } ?>
        <tr class="tooltip" title="<?php echo $title; ?>">
            <td><?php echo $persona; ?></td>
            <td><?php echo $imagenes; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

