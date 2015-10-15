<div style="position: relative; height: 100px;">
<?php
    $actuales = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($funcionarios_funcionario->getId());

    $top_unidad = 25;
    $top_cargo = 35;

    if(count($actuales)>0) {
        $height_tr = (count($actuales)-1) * 50;
        ?>
        <script>
            var height_x = parseFloat(($("#td_f_<?php echo $funcionarios_funcionario->getId(); ?>").css("height")).replace('px','')) + <?php echo $height_tr; ?>;
            $("#td_f_<?php echo $funcionarios_funcionario->getId(); ?>").css("height",height_x);
        </script>
        <?php foreach ($actuales as $actual) { ?>
            <div style="position: absolute; left: 10px; top: <?php echo $top_unidad; ?>px; width: 350px;">
                <img src="/images/other/barra_trans.png" width="350" height="1"/>
            </div>
            <div style="position: absolute; left: 10px; top: <?php echo $top_cargo; ?>px; width: 400px; color: #9d9d9d;">
                <b>Ubicación actual:</b> <?php echo $actual->getUnidad(); ?><br/>
                <b>Cargo:</b> <?php echo $actual->getCargoTipo(); ?>
            </div>
        <?php

            $top_unidad+=50;
            $top_cargo+=50;
        }
    } else { ?>
            <div style="position: absolute; left: 10px; top: <?php echo $top_unidad; ?>px; width: 350px;">
                <img src="/images/other/barra_trans.png" width="350" height="1"/>
            </div>
            <div style="position: absolute; left: 10px; top: <?php echo $top_cargo; ?>px; width: 400px; color: #9d9d9d;">
                <b>Ubicación actual:</b> <font style="color: red;">Sin Asignación</font><br/>
                <b>Cargo:</b> <font style="color: red;">Sin Cargo</font>
            </div>
    <?php }
    if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $funcionarios_funcionario->getCi() .".jpg")) {
    ?>
        <div style="position: absolute; bottom: -10px; right: -420px">
            <img src="/images/firma_digital/<?php echo $funcionarios_funcionario->getCi() ?>.jpg?<?php echo time(); ?>"style="width: 130px; height: 57px"/>
        </div>
    <?php } ?>
</div>