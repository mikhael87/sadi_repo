<?php $session_funcionario = $sf_user->getAttribute('session_funcionario');
$conversacion_con= $public_mensajes->getFuncionarioRecibe();
if($sf_user->getAttribute('funcionario_id')== $public_mensajes->getFuncionarioRecibeId())
    $conversacion_con= $public_mensajes->getFuncionarioEnvia();
?>

<div id="sms_div_<?php echo $public_mensajes->getId() ?>" style="min-width: 650px; max-width: 800px; position: relative;cursor: pointer"
     onclick="mensaje('<?php echo $public_mensajes->getFuncionarioRecibeId(); ?>','<?php echo $public_mensajes->getFuncionarioEnviaId(); ?>','Conversaci&oacute;n con <?php echo str_replace(',', '', $conversacion_con); ?>'); leidoCambio('<?php echo $public_mensajes->getId() ?>', '<?php echo $sf_user->getAttribute('funcionario_id') ?>', '<?php echo $public_mensajes->getFuncionarioRecibeId() ?>')">
    <div id="<?php echo $public_mensajes->getFuncionarioRecibeCi(); ?>" class="f19b" style="color: #084B8A;" >
        <?php
        if ($public_mensajes->getFuncionarioEnviaCi()!=$session_funcionario['cedula'])
            echo $public_mensajes->getFuncionarioEnvia();
        else 
            echo $public_mensajes->getFuncionarioRecibe();
        ?>
    </div>
    <div class="f16n" style="padding-left: 18px; padding-top: 3px; position: relative;">
        <?php 
        if ($public_mensajes->getFuncionarioEnviaCi()!=$session_funcionario['cedula'])
            echo '<div style="position: absolute; top: 0px; left: 0px;"><img src="/images/icon/back_mini.png"/></div>';
        else
            echo '<div style="position: absolute; top: 0px; left: 0px;"><img src="/images/icon/front_mini.png"/></div>';
        echo html_entity_decode($public_mensajes->getContenido()); 
        ?>        
    </div>
    <div id="leido_div_<?php echo $public_mensajes->getId() ?>" class="f12n" style="top: 0px; right: 0px; position: absolute;">
        <?php
            $fecha = new DateTime($public_mensajes->getUpdatedAt());
            $hoy = new DateTime(date('Y-m-d h:i:s A'));
            $intervalo = $fecha->diff( $hoy );

            $anios = $intervalo->format('%y');
            $meses = $intervalo->format('%m');
            $dias = $intervalo->format('%d');
            $horas = $intervalo->format('%h');
            $minutos = $intervalo->format('%i');
            $segundos = $intervalo->format('%s');

            if ( $anios > 0 ) {
                $tiempo = $anios;
                $tiempo .= ($anios > 1) ? ' años' : ' año';
            } elseif ( $meses > 0 ) {
                $tiempo = $meses;
                $tiempo .= ($meses > 1) ? ' meses' : ' mes';
            } elseif ( $dias > 0 ) {
                $tiempo = $dias;
                $tiempo .= ($dias > 1) ? ' días' : ' día';
            } elseif ( $horas > 0 ) {
                $tiempo = $horas;
                $tiempo .= ($horas > 1) ? ' horas' : ' hora';
            } elseif ( $minutos > 0 ) {
                $tiempo = $minutos;
                $tiempo .= ($minutos > 1) ? ' minutos' : ' minuto';
            } elseif ( $segundos > 0 ) {
                $tiempo = $segundos;
                $tiempo .= ($segundos > 1) ? ' segundos' : ' segundo';
            } else {
                $tiempo = 'un instante';
            }
        ?>

        <?php if($public_mensajes->getStatus() == 'A') { ?>
            Sin Leer hace <?php echo $tiempo; ?>
            <?php if($sf_user->getAttribute('funcionario_id')== $public_mensajes->getFuncionarioRecibeId()) : ?>
                <script>
                    $('#sms_div_<?php echo $public_mensajes->getId() ?>').parent().parent().css('background-color', '#ff6a6a');
                </script>
            <?php endif; ?>
        <?php } else { ?>
            Leido hace <?php echo $tiempo; ?>
        <?php } ?>
    </div>
</div>