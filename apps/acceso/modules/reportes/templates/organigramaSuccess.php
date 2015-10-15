<div>   
    <fieldset><h2>DISTRIBUCION DEL ORGANIGRAMA</h2></fieldset>
        <br/>

        <table style="width: 100%;">

            <tr class="sf_admin_row">
                <th>Unidades</th>
                <th>Funcionarios</th>
            </tr>
            <?php  foreach ( $organigrama as $unidad_id=>$unidad_nombre ) { 
                if($unidad_id!=0){
                    
                    $rows = count($funcionarios[$unidad_id])+1;
                ?>
                <tr class="sf_admin_row">
                    <td rowspan="<?php echo $rows;?>">
                        <?php echo html_entity_decode($unidad_nombre); ?>
                    </td>
                </tr>

                <?php  foreach ( $funcionarios[$unidad_id] as $funcionario ) { ?>
                <tr class="sf_admin_row">
                    <td>
                        <?php echo $funcionario->getCtnombre(); ?> /
                        <?php echo $funcionario->getPrimer_nombre(); ?>
                        <?php echo $funcionario->getSegundo_nombre(); ?>,
                        <?php echo $funcionario->getPrimer_apellido(); ?>
                        <?php echo $funcionario->getSegundo_apellido(); ?>
                    </td>
                </tr>
                <?php } ?>
                
            <?php } } ?>
        </table>
    <div id="sf_admin_footer"> </div>
</div>