<?php
$pagos= Doctrine::getTable('Operaciones_RegistroPago')->pagosPorRegistro($empresas_empresa->getRegistro());

$cadena_inner= '';
foreach($pagos as $value) {
    $cadena_inner.= '<tr><td>'.$value->getTipoNombre().'</td><td style="text-align: right">'.$value->getMonto().'</td></tr>';
}
if($cadena_inner != '') {
    $cadena_inner= '<table style="color: #666; font-size: 11px">'.$cadena_inner.'</table>';
}else {
    $cadena_inner= '<br/>'.image_tag('icon/info.png', array('style'=>'vertical-align: middle')).'&nbsp;<font style="color: red; font-size: 11px">No se han registrado pagos</font><br7>';
}
echo $cadena_inner;
?>
