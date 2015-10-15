<?php
$tipo= $operaciones_juego->getTipoJuego().$operaciones_juego->getTipoMarcador();

if($tipo == 'VP'){ ?>
    <font style="font-size: 20px; color: #8a8a8a">VERSUS</font><br/><font style="font-size: 11px; color: #aeaeae; letter-spacing:5px;">PUNTAJE</font>
<?php }elseif($tipo == 'VS') { ?>
    <font style="font-size: 20px; color: #8a8a8a">VERSUS</font><br/><font style="font-size: 11px; color: #aeaeae; letter-spacing:8px;">SIMPLE</font>
<?php }elseif($tipo == 'MO') { ?>
    <font style="font-size: 20px; color: #8a8a8a">MULTIPLE</font><br/><font style="font-size: 11px; color: #aeaeae; letter-spacing:14px;">ORDEN</font>
<?php } ?>

