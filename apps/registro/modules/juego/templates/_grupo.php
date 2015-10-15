<div style="text-align: center">
    <?php
    if($operaciones_juego->getGrupoId() !== '' && $operaciones_juego->getGrupoId() !== NULL && $operaciones_juego->getGrupoId() !== ' ') {
        $grupo= Doctrine::getTable('Operaciones_Grupo')->find($operaciones_juego->getGrupoId());

        echo '<font style="font-size: 25px; color: black; font-weight: bold">'.$grupo->getNombre().'</font>';
    }else {
        echo '<font style="font-size: 17px; color: #8a8a8a">MIXTO</font>';
    }
    ?>
</div>