<?php
$pagos= Doctrine::getTable('Operaciones_RegistroPago')->pagosPorRegistro($empresas_empresa->getRegistro());

$cadena_inner= '';
foreach($pagos as $value) {
    $cadena_inner.= '<tr><td>'.$value->getTipoNombre().'</td><td style="text-align: right">'.$value->getMonto().'</td></tr>';
}
if($cadena_inner != '') {
    $cadena_inner= '<table style="color: #666; font-size: 10px">'.$cadena_inner.'</table>';
}else {
    $cadena_inner= '<br/>'.image_tag('icon/info.png', array('style'=>'vertical-align: middle')).'&nbsp;<font style="color: red; font-size: 11px">No se han registrado pagos</font><br7>';
}
echo $cadena_inner;
?>

<?php
$encargados= Doctrine::getTable('Operaciones_RegistroEncargado')->cantidadPorRegistro($empresas_empresa->getRegistro());
$delegados= Doctrine::getTable('Operaciones_RegistroDelegado')->cantidadPorRegistro($empresas_empresa->getRegistro());
$equipos= Doctrine::getTable('Operaciones_RegistroDisciplina')->cantidadPorRegistro($empresas_empresa->getRegistro());
?>

<table>
    <tr>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px">Encargados: </font></td>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px"><?php echo $encargados[0][0]; ?></font></td>
    </tr>
    <tr>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px">Delegados: </font></td>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px"><?php echo $delegados[0][0]; ?></font></td>
    </tr>
    <tr>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px">Equipos: </font></td>
        <td style="padding: 1px"><font style="color: #666; font-size: 10px"><?php echo $equipos[0][0]; ?></font></td>
    </tr>
</table>

<?php
$tipos_pago= Doctrine::getTable('Operaciones_TipoPago')->findByStatus('A');
?>

<div  style="position: relative; width: 10px; height: 10px" >
    <div id="tab_pago_<?php echo $empresas_empresa->getRegistro() ?>" class="caja"  style="padding: 1px; border-radius: 4px 4px 4px 4px; background-color: #000; z-index: 998; position: absolute; width: 285px; min-height:92px; left: 0px; top: -100px; display: none">
        <div class="inner" style="border-radius: 4px 4px 4px 4px; background-color: #ebebeb; z-index: 999; min-height:92px; padding: 5px; box-shadow: #777 0.1em 0.2em 0.1em;">
            <div style="top: -15px; left: -15px; position: absolute;">
                <a href="#" onclick="conmutar_pago(<?php echo $empresas_empresa->getRegistro();?>,'siglas'); return false;"><?php echo image_tag('icon/icon_close.png') ?></a>
            </div>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr><td colspan="2" style="background-color: #666; text-align: center"><font style="font-weight: bold">Nuevo Pago</font></td></tr>
                            <tr>
                                <td style="width: 30%">Banco:</td>
                                <td style="width: 70%"><input type="text" id="banco_<?php echo $empresas_empresa->getRegistro() ?>"/></td>
                            </tr>
                            <tr>
                                <td style="width: 30%">Referencia:</td>
                                <td style="width: 70%"><input type="text" id="referencia_<?php echo $empresas_empresa->getRegistro() ?>"/></td>
                            </tr>
                            <tr>
                                <td style="width: 30%">Tipo de pago:</td>
                                <td style="width: 70%">
                                    <select id="tipo_pago_<?php echo $empresas_empresa->getRegistro() ?>">
                                        <?php foreach($tipos_pago as $value) :
                                            echo '<option value="'. $value->getId() .'">'. $value->getNombre() .'</option>';
                                        endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%">Monto:</td>
                                <td style="width: 70%"><input type="text" id="monto_<?php echo $empresas_empresa->getRegistro() ?>"/>
                                <div class="help" style="font-size: 11px">Use coma (,) para indicar decimales</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div style="text-align: right; width: 275px; background-color: #B7B7B7" id="save_pago_<?php echo $empresas_empresa->getRegistro(); ?>">
                <input onClick="javascript: savePago('<?php echo $empresas_empresa->getRegistro(); ?>'); return false;" name="guardar" type="button" value="Guardar" />
            </div>
        </div>
    </div>
</div>