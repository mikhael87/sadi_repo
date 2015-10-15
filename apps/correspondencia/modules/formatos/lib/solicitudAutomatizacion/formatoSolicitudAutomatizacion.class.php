<?php

class formatoSolicitudAutomatizacion {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["solicitudAutomatizacion_servicio"]) {
            $messages = array_merge($messages, array("solicitudAutomatizacion_servicio" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["solicitudAutomatizacion_servicio"]);
        }
        
        if (!$formulario["solicitudAutomatizacion_comentario"]) {
            $messages = array_merge($messages, array("solicitudAutomatizacion_comentario" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["solicitudAutomatizacion_comentario"]);
        }
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        
        $formulario["solicitudAutomatizacion_servicio"] = $datos["campo_uno"];
        $formulario["solicitudAutomatizacion_comentario"] = $datos["campo_dos"];

        return $formulario;
    }
    
    

    public function executePdf($pdf, $formato, $correspondencia, $emisor, $receptores) {
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(80, 60, 80);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->SetFooterMargin(40);
        $pdf->SetBarcode($correspondencia->getNCorrespondenciaEmisor());
        
        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();

        //Muestra datos del formato
        $valores = $this->executeTraer($formato);        

        foreach ($emisor as $list_firman) {
                $solicitante = $list_firman->getpa().' '.$list_firman->getsa().', '.$list_firman->getpn().' '.$list_firman->getsn();
                $solicitante_ci = $list_firman->getCi();
                $solicitante_unidad = $list_firman->getunombre();
                $solicitante_cargo_tipo = $list_firman->getctnombre();
        }
        
        $contenido = '<table width="450" "center">

                <tr>
                    <td align="right">
                        <h3>'.$correspondencia->getNCorrespondenciaEmisor().'</h3>
                        <font size="10">FECHA SOLICITUD:<br/> '.date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio())).'</font>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <h2><u>SOLICITUD AUTOAMTIZACIÓN</u></h2>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="225">
                                    <font size="12">Servicio</font>'.$valores['solicitudAutomatizacion_servicio'].'
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="225">
                                    <font size="12">Comentario</font>'.$valores['solicitudAutomatizacion_comentario'].'
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>';
        
        
        

//Espacio
        $pdf->Cell(80, 10, "", 0, 1, 'L');
        $pdf->Cell(80, 10, "", 0, 1, 'L');
        $pdf->Cell(80, 10, "", 0, 1, 'L');
        $pdf->Cell(80, 10, "", 0, 1, 'L');

//html
        $tbl = <<<EOD
<table width="450" "center">
    <tr>
        <td width="450">
            $contenido
        </td>
    </tr>
</table>

EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output($correspondencia->getNCorrespondenciaEmisor().'__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }
}