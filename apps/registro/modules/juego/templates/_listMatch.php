<div style="max-height: 200px; overflow-y: auto; width: 350px">
    <?php
    $tipo= $operaciones_juego->getTipoJuego().$operaciones_juego->getTipoMarcador();

    $resultados= Doctrine::getTable('Operaciones_Resultado')->findbyJuegoId($operaciones_juego->getId());
    ?>
    <table style="min-width: 230px">

    <?php
    $pot= 0;
    foreach($resultados as $resultado) {
        $empresa= Doctrine::getTable('Empresas_Empresa')->empresaPorRegistro($resultado->getRegistroId());
        ?>
        <tr>
            <td>
                <?php
                foreach($empresa as $value) {

                    $largo= strlen($value->getNombre());
                    $parte= $value->getNombre();
                    if($largo > 40) {
                        $parte= utf8_encode(substr(utf8_decode($parte), 0, 40));
                    } ?>

                    <font style="font-size: 12px; font-weight: bold; color: #553a3a" <?php /* echo (($largo != strlen($parte))? 'class="tooltip" title="[!]'.$value->getNombre().'[/!]"':'') */ echo 'class="tooltip" title="[!]'.$value->getNombre().'[/!]"' ?> ><?php /* echo (($largo != strlen($parte))? $parte.'...': $parte) */ echo $value->getSiglas() ?></font><?php
                    ?><br/><font style="font-size: 10px; color: #666"><?php echo (($largo != strlen($parte))? $parte.'...': $parte) ?></font><?php
                } ?>
            </td>
            <td>
                <?php if($tipo == 'VP'){ ?>
                    <font style="font-size: 17px; font-weight: bold; color: #666"><?php echo $resultado->getMarcador(); ?></font>
                <?php }elseif($tipo== 'VS'){
                    if($resultado->getMarcador() == 1) {
                        echo image_tag('icon/tick');
                    }else {
                        echo image_tag('icon/delete_old');
                    }
                }elseif($tipo== 'MO'){ ?>
                    <font style="font-size: 17px; font-weight: bold; color: #666"><?php echo $resultado->getMarcador(); ?>°</font>
                <?php }else {  } ?>
            </td>
        <tr>
    <?php $pot++; } ?>
    </table>
</div>