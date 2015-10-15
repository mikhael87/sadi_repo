<?php use_helper('jQuery'); ?>
<select id="select_tramo" name="archivo_expediente[tramo]" onchange="
                    <?php
                    echo jq_remote_function(array('update' => 'div_caja',
                    'url' => 'expediente/listarCajas',
                    'with'     => "'t_id='+this.value+'&e_id=".$e_id."'")) 
                    ?>">
    <option value=""></option>
    <?php 
        $tramos = explode(',', $tramos);
        for($i=0;$i<count($tramos);$i++){
    ?>
    <option value="<?php echo $tramos[$i]; ?>"><?php echo $tramos[$i]; ?></option>
    <?php } ?>
</select>