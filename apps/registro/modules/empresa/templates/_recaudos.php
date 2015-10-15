<?php
$recaudos= Doctrine::getTable('Operaciones_RegistroEmpresaRequisito')->recaudosPorRegistro($empresas_empresa->getRegistro());
?>
<div id="div_recaudos_empresa" style="min-width: 200px">
    <table style="width: 200px; text-align: left">
    <?php
    $cadena= '';
    foreach($recaudos as $recaudo) {
        $cadena.= '<tr style="border: none">';
        $cadena.= '<td style="border: none; width: 10%; text-align: left; padding: 0px">';
        $cadena.= '<input class="input_recaudo" style="vertical-align: middle;" id="'. $recaudo->getId() .'" type="checkbox" name="registro_empresa_requisito[]" '. (($recaudo->getStatus() == 'C')? 'checked' : '') .' />&nbsp;';
        $cadena.= '</td><td style="border: none; width: 90%; padding: 0px">';
        $cadena.= $recaudo->getRnombre();
        $cadena.= '</td></tr>';
    }
    echo $cadena;
    ?>
    </table>
</div>

<?php
$pendientes= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->recaudosPendientesPorRegistro($empresas_empresa->getRegistro());

$p= 0; $c= 0; $all= 0;
foreach($pendientes as $value) {
    if($value->getStatus() == 'C') {
        $c++;
    }else {
        $p++;
    }
    $all++;
}

$cadena_alert= '<table style="width: 200px; text-align: left">';
$cadena_alert.= '<tr style="border: none">';
$cadena_alert.= '<td style="border: none; width: 10%; text-align: left; padding: 0px">';
if($all == $c && $c > 0) {
    $cadena_alert.= image_tag('icon/tick.png', array('style'=>'vertical-align: middle'));
}elseif($p > 0) {
    $cadena_alert.= image_tag('icon/info.png', array('style'=>'vertical-align: middle'));
}else {
    $cadena_alert.= image_tag('icon/error.png', array('style'=>'vertical-align: middle'));
}
$cadena_alert.= '';
$cadena_alert.= '</td><td style="border: none; width: 90%; padding: 0px">';
if($all == $c && $c > 0) {
    $cadena_alert.= '<font style="color: green; font-size: 11px">Participantes con recaudos<br/>completos</font>';
}elseif($p > 0) {
    $cadena_alert.= '<font style="color: blue; font-size: 11px">Participantes con recaudos<br/>pendientes</font>';
}else {
    $cadena_alert.= '<font style="color: red; font-size: 11px">Sin recaudos<br/>asignados</font>';
}
$cadena_alert.= '</td></tr></table>';
?>

<div>
<?php echo $cadena_alert; ?>
</div>