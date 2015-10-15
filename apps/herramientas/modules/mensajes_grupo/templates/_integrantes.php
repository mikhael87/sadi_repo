<?php
$participantes = Doctrine::getTable('Public_MensajesParticipantes')->grupoParticipantes($public_mensajes_grupo->getId());

foreach ($participantes as $participante) {
    ?>
    <a href="mensajes_grupo/<?php echo $participante['id']; ?>/deleteParticipante"><?php echo image_tag('icon/delete_user'); ?></a>
    <?php
    echo $participante['participante'] . '<br/>';
}
?>
