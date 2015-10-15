<?php

class formatoServiciosGenerales {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["servicios_generales_servicio"]) {
            $messages = array_merge($messages, array("servicios_generales_servicio" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["servicios_generales_servicio"]);
        }

        // campo_dos = Contenido
        if (!$formulario["servicios_generales_descripcion"]) {
            $messages = array_merge($messages, array("servicios_generales_descripcion" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["servicios_generales_descripcion"]);
        }
    }

    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["servicios_generales_servicio"] = $datos["campo_uno"];
        $formulario["servicios_generales_descripcion"] = $datos["campo_dos"];

        $servicio = Doctrine::getTable('Extenciones_ServiciosGenerales')->find($datos["campo_uno"]);
        $formulario["servicios_generales_servicio_show"] = $servicio->getNombre();

        return $formulario;
    }

    public function executePdf($pdf, $correspondenciaformato, $correspondencia, $emisor, $receptores) {
        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(40, 115, 40);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf-> SetAutoPageBreak(True, 90);
        
        $pdf->SetBarcode($correspondencia->get('n_correspondencia_emisor'));

        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        //Muestra datos del formato
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "SIN ENVIO";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }

        $n_envio = $correspondencia->get('n_correspondencia_emisor');
        $observaciones = $correspondenciaformato->get('campo_dos');
        $id= $correspondencia->get('id');

        $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);

        $servicio_datos = Doctrine::getTable('Extenciones_ServiciosGenerales')->find($formatos[0]['campo_uno']);
        $servicio = $servicio_datos->getNombre();

        $dir_gerenal='';
        $solicitante='';
    foreach ($emisor as $list_firman) {
            $dir_gerenal= $list_firman->getunombre().'. ';
            $solicitante= $list_firman->getctnombre().' / '.
                    ucwords(strtolower($list_firman->getpn())).' '.
                    ucwords(strtolower($list_firman->getpa()));
        }
$check= K_PATH_URL.'images/checkbox.png';
$nocheck= K_PATH_URL.'/images/checkbox_no.png';

//Espacio
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');

//html
        $tbl = <<<EOD
<table width="525">
    <tr>
        <td width="50%" align="rigth" style="font-size: 13px"><br/><strong>SOLICITUD DE SERVICIOS</strong></td>
        <td width="50%" align="rigth" style="font-size: 12px"><br/><strong>N°&nbsp;$n_envio</strong></td>
    </tr>
</table>
<br/>
<table width="525" align="center" border="1" cellpadding="3" style="font-size: 7px">
    <tr>
        <td width="81%" colspan="6" align="center" height="16"><strong>PARA LA UNIDAD DE SERVICIOS GENERALES</strong></td>
        <td width="19%" align="left"><strong>FECHA:&nbsp;$f_envio</strong></td>
    </tr>
    <tr align="left">
        <td width="73%" height="20" colspan="5"><font size="10">$dir_gerenal</font></td>
        <td colspan="2" width="27%"></td>
    </tr>
    <tr align="center">
        <td width="73%" colspan="5">DIRECCION GENERAL</td>
        <td width="27%" colspan="2" >UNIDAD SOLICITANTE</td>
    </tr>
    <tr align="left">
        <td width="53%" height="20" colspan="3"><font size="10">$solicitante</font></td>
        <td width="28%" colspan="3"></td>
        <td width="19%"></td>
    </tr>
    <tr align="center">
        <td width="53%" colspan="3">PERSONA SOLICITANTE</td>
        <td width="28%" colspan="3">FIRMA</td>
        <td width="19%">TLF. / EXT.</td>
    </tr>
    <tr>
        <td width="53%" height="20" colspan="3" align="left"></td>
        <td width="28%" colspan="3" align="left"></td>
        <td width="19%" rowspan="5" align="center" valign="bottom"><font style="font-size: 5px"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>SELLO DE LA DIRECCI&Oacute;N<br/>SOLICITANTE</font></td>
    </tr>
    <tr align="center">
        <td width="53%" colspan="3">NOMBRE DEL DIRECTOR GENERAL</td>
        <td width="28%" colspan="3">FIRMA DEL DIRECTOR<br/>GENERAL</td>
    </tr>
    <tr>
        <td width="81%" colspan="6"></td>
    </tr>
    <tr align="center">
        <td width="81%" colspan="6" height="16"><strong>AGRADEZCO A UD. IMPARTIR SUS INSTRUCCIONES A FIN DE QUE SEA ELABORADO EL<br/>SIGUIENTE TRABAJO.</strong></td>
    </tr>
    <tr valign="top" align="left">
        <td width="81%" colspan="6" height="40"><font size="9"><img src="$check" /> $servicio</font></td>
    </tr>
    <tr align="left">
        <td width="100%" colspan="7" valign="top" height="35">DESCRIPCI&Oacute;N DEL TRABAJO:&nbsp;&nbsp;<font size="9">$observaciones</font></td>
    </tr>
    <tr>
        <td width="100%" colspan="7" align="center" height="16"><strong>PARA USO DE LA COORDINACI&Oacute;N DE MATENIMIENTO Y SERVICIOS GENERALES</strong></td>
    </tr>
    <tr>
        <td width="31%" align="left" height="20"></td>
        <td width="35%" colspan="3" align="left"></td>
        <td width="15%" colspan="2" align="left"></td>
        <td width="19%" rowspan="5" align="center"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>SELLO</td>
    </tr>
    <tr align="center">
        <td width="31%">DIVISI&Oacute;N-RECIBIDO</td>
        <td width="35%" colspan="3">FIRMA</td>
        <td width="15%" colspan="2">FECHA</td>
    </tr>
    <tr>
        <td width="66%" colspan="4" valign="top" height="40" align="left">
        <table width="100%" cellspacing="3">
            <tr>
                <td width="50%"><img src="$nocheck" />&nbsp;MANTENIMIENTO</td>
                <td width="50%"><img src="$nocheck" />&nbsp;MAQUINARIAS Y VEHICULOS</td>
            </tr>
            <tr>
                <td width="50%"><img src="$nocheck" />&nbsp;SERVICIOS</td>
                <td width="50%"><img src="$nocheck" />&nbsp;COMUNICACI&Oacute;N Y MANT. DE EQUIPOS</td>
            </tr>
            <tr>
                <td width="50%"><img src="$nocheck" />&nbsp;APLICACIONES Y MEJORAS</td>
                <td width="50%"></td>
            </tr>
        </table>
        </td>
        <td width="15%" colspan="2" align="center" valign="bottom"><br/><br/><br/>CORRELATIVO</td>
    </tr>
    <tr align="left">
        <td width="42%" colspan="2" height="20"></td>
        <td width="24%" colspan="2"></td>
        <td width="15%" colspan="2"></td>
    </tr>
    <tr align="center">
        <td width="42%" colspan="2">JEFE DEL DEPARTAMENTO</td>
        <td width="24%" colspan="2">FIRMA</td>
        <td width="15%" colspan="2">FECHA</td>
    </tr>
</table>
<br/>
<table width="525" align="center" border="1" cellpadding="2" style="font-size: 7px">
    <tr>
        <td width="81%" colspan="6" valign="top" height="40" align="left">OBSERVACIONES:</td>
        <td width="19%" rowspan="3" align="center" valign="bottom"><font style="font-size: 5px"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>SELLO OFICINA SOLICITANTE</font></td>
    </tr>
    <tr align="left">
        <td width="42%" colspan="2" height="20"></td>
        <td width="24%" colspan="2"></td>
        <td width="15%" colspan="2"></td>
    </tr>
    <tr align="center">
        <td width="42%" colspan="2" height="18">RECIBO CONFORME</td>
        <td width="24%" colspan="2">FIRMA</td>
        <td width="15%" colspan="2">FECHA</td>
    </tr>
</table>
<table cellpadding="2">
<tr align="center">
<td><font style="font-size: 5px">NOTA: LA EROGACI&Oacute;N QUE OCACIONE LA REALIZACI&Oacute;N DEL TRABAJO, CORRESPONDER&Aacute; A LA DIRECCI&Oacute;N QUE REALIZA LA SOLICITUD<br/>FORTCOM: D.B.S. 045-B(07-84)</font></td>
</tr>
</table>
EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output($n_envio.'__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
}