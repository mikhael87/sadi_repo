<?php $micro_foro = Doctrine::getTable('Correspondencia_MicroForo')->tweetCorrespondencia($this->context->getUser()->getAttribute('correspondencia_grupo')); ?>

<div style="position: absolute; left: 0px; width: 295px; padding: 3px" id="tweet">
    <?php $ban=0; foreach ($micro_foro as $tweet) { ?>
        <?php if($ban>0){ ?>
            <hr>
        <?php } ?>
                <font class="f11n gris_oscuro" ><?php echo date('d-m-Y h:m a', strtotime($tweet->getCreatedAt())); ?></font>
                <br/>
                <font class="f14n gris_oscuro"><?php echo $tweet->getUnidad().' / '.$tweet->getFuncionario(); ?></font>
                <br/>
                <?php echo $tweet->getContenido(); ?>
    <?php $ban++; } ?>

    <br/><hr/><br/>
    <textarea id="contenido_miniforo" name="contenido" cols="32"></textarea><br/>
    <input id="button_form_miniforo" onClick="enviar_form_miniforo()" name="publicar" value="Publicar" type="button"/>
    <div style="position:absolute">
        <div id="div_icon_notify" style="position: absolute; left: 65px; bottom: 3px">
<!--            <a href="javascript: enviar_notificaciones(); retun false;" title="Enviar notificaciones">-->
                <?php echo image_tag('icon/notify_a.png', array('onClick'=> 'javascript: enviar_notificaciones()', 'style'=> 'cursor: pointer', 'title'=> 'Enviar notificaciones')); ?>
<!--            </a>-->
        </div>
    </div>
    <div id="list_funcio_noti"></div>
</div>