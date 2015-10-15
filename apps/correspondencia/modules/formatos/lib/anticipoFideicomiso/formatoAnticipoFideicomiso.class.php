<?php

class formatoAnticipoFideicomiso {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["anticipo_fideicomiso_motivo"]) {
            $messages = array_merge($messages, array("anticipo_fideicomiso_motivo" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["anticipo_fideicomiso_motivo"]);
        }
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["anticipo_fideicomiso_motivo"] = $datos["campo_uno"];

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
        
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }
        
        $contenido = '<table width="450" "center">

                <tr>
                    <td align="right">
                        <h3>'.$correspondencia->getNCorrespondenciaEmisor().'</h3>
                        <font size="10">FECHA SOLICITUD:<br/> '.$f_envio.'</font>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <h2><u>SOLICITUD DE ANTICIPO<br/>FIDEICOMISO PRESTACIONES SOCIALES<br/>PLAN 6776</u></h2>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="350">
                                    <font size="12">APELLIDOS Y NOMBRES:<br/> '.$solicitante.'</font>
                                </td>
                                <td width="100">
                                    <font size="12">CEDULA:<br/> '.$solicitante_ci.'</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450" colspan="2">
                                    <font size="12">DENOMINACION DEL CARGO:<br/> '.$solicitante_cargo_tipo.'</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450" colspan="2">
                                    <font size="12">UBICACION ADMINISTRATIVA:<br/></font>'.$solicitante_unidad.'
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="225">
                                    <font size="12">FECHA DE INGRESO:</font>
                                </td>
                                <td width="225">
                                    <font size="12">TELÉFONO O EXTENSIÓN:</font>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="450" colspan="3">
                                    <font size="12">MOTIVO DE LA SOLICITUD: '.$valores["anticipo_fideicomiso_motivo"].'</font>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="center">
                                <td width="450" bgcolor="#000000">
                                    <font size="12" color="#FFFFFF">AUTORIZACIÓN EN CASO DE SER CASADO</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450">
                                    <font size="10">
                                    Yo, _________________________________________________ C.I. Nº: ____________________
                                    AUTORIZO A MI CÓNYUGE, ____________________________ C.I. Nº: ____________________
                                    PARA SOLICITAR ANTICIPO DE PRESTACIONES SOCIALES.<br/><br/></font>
                                    <table width="450">
                                        <tr>
                                            <td width="250"></td>                                            
                                            <td width="200" align="center">
                                                <font size="10">______________________________<br/>
                                                FIRMA CÓNYUGE</font>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="center">
                                <td width="450" colspan="2">
                                    <font size="12">SOLO PARA USO DE LA OFICINA DE RECURSOS HUMANOS</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="180">
                                    <font size="12">CONFORMADO POR:</font><br/><br/><br/><br/>
                                    <font size="12">FECHA:        /        /     </font>
                                </td>
                                <td width="270">
                                    <font size="12">APROBADO POR:</font><br/><br/><br/>
                                    <table width="270" align="center">
                                        <tr>
                                            <td>
                                                _________________________________________<br/>
                                                <font size="12">DIRECTOR GENERAL DE LA OFICINA DE RECURSOS HUMANOS</font>
                                            </td>
                                        </tr>
                                    </table>
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