<?php $session_funcionario = $sf_user->getAttribute('session_funcionario'); ?>
<div style="width: 80px;">
    <?php if ($public_mensajes->getFuncionarioEnviaCi()!=$session_funcionario['cedula']) { ?>
    <img src="/images/fotos_personal/<?php echo $public_mensajes->getFuncionarioEnviaCi(); ?>.jpg" width="70"/>
    <?php } else { ?>
    <img src="/images/fotos_personal/<?php echo $public_mensajes->getFuncionarioRecibeCi(); ?>.jpg" width="70"/>
    <?php } ?>
</div>
