<?php use_helper('I18N', 'Date') ?>

<?php use_helper('jQuery'); ?>
<table class="user_table_paso_dos">
    <tr>
        <td rowspan="4">
            <?php
            if(file_exists(sfConfig::get("sf_root_dir").'/web/images/fotos_personal/'.$funcionario->getCi().'.jpg')){
                echo image_tag('/images/fotos_personal/'.$funcionario->getCi().'.jpg', array('style'=>'width: 80px;'));
            } else {
                echo image_tag('/images/other/siglas_photo_small_'.$funcionario->getSexo().substr($funcionario->getCi(), -1).'.png', array('style'=>'width: 80px;'));
            }

            list($Y,$m,$d) = explode("-",$funcionario->getFNacimiento());
            $edad = (date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y);
            ?>
        </td>
        <td>
            <b><?php echo $funcionario->getPrimerNombre().' '.$funcionario->getSegundoNombre(); ?></b>
        </td>
    </tr>
    <tr>
        <td>
            <b><?php echo $funcionario->getPrimerApellido().' '.$funcionario->getSegundoApellido(); ?></b>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo ($edad > 1)? $edad.' años' : '' ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $funcionario->getCi(); ?>
        </td>
    </tr>
</table>
<p class="help">Click en Siguiente si se reconoce. De no ser as&iacute; verifique su n&uacute;mero de c&eacute;dula en el Paso 1 o pongase en contacto con el administrador SIGLAS</p>
<input type="hidden" id="cedula_hid" name="cedula" value="<?php echo $funcionario->getCi(); ?>" />