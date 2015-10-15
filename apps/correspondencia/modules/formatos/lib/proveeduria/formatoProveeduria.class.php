<?php

class formatoProveeduria {
    public function executeValidar(&$formulario, &$messages, &$formato) {
        // campo_uno = articulos
        $formato->setCampoUno(sfYAML::dump($formulario["proveeduria_articulos"]));

        // campo_dos = observacion
        $formato->setCampoDos($formulario["proveeduria_observacion"]);
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        
        $formulario['proveeduria_articulos'] = sfYaml::load($datos["campo_uno"]);
        $formulario["proveeduria_observacion"] = $datos["campo_dos"];

        return $formulario;
    }

    public function executeAdditionalCrear($correspondencia_id){}
    
    public function executeAdditionalEnviar($correspondencia_id){}
    
    public function executeAdditionalAnular($correspondencia_id){}
    
    public function executeAdditionalDevolver($correspondencia_id){}
 
    public function executePdf($pdf, $formato, $correspondencia, $emisor, $receptores) {
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(80, 115, 80);
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
                $solicitante = $list_firman->getPn().' '.$list_firman->getSn().', '.$list_firman->getPa().' '.$list_firman->getSa();
                $solicitante_ci = $list_firman->getCi();
                $solicitante_unidad = $list_firman->getUnombre();
                $solicitante_cargo_tipo = $list_firman->getCtnombre();
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
                        <h2><u>SOLICITUD DE MATERIALES</u></h2>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="450">
                                    <font size="12">UNIDAD SOLICITANTE: '.$solicitante_unidad.'</font>
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
                                <td width="60">
                                    <font size="9"><b>RENGLÓN</b></font>
                                </td>
                                <td width="260">
                                    <font size="9"><b>ARTÍCULOS</b></font>
                                </td>
                                <td width="60">
                                    <font size="9"><b>CANTIDAD</b></font>
                                </td>
                                <td width="70">
                                    <font size="9"><b>U. MEDIDA</b></font>
                                </td>
                            </tr>';
        
                $articulos = $valores['proveeduria_articulos'];
                
                $i=1;
                foreach ($articulos as $articulo_id => $cantidad) {
                    $articulo = Doctrine::getTable('Inventario_Articulo')->find($articulo_id);
                    $unidad_medida = Doctrine::getTable('Inventario_UnidadMedida')->find($articulo->getUnidadMedidaId());
                    
                    $contenido .=  '<tr>
                                        <td width="60" style="font-size: 10px;">'.$i.'</td>
                                        <td width="260" align="left" style="font-size: 10px;">'.$articulo->getNombre().'</td>
                                        <td width="60" style="font-size: 10px;">'.$cantidad.'</td>
                                        <td width="70" style="font-size: 10px;">'.$unidad_medida->getNombre().'</td>
                                    </tr>';
                    $i++;
                }
        
        
                $contenido .= '</table>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="450" colspan="3">
                                    <font size="12">OBSERVACIONES: '.$valores["proveeduria_observacion"].'</font>
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
                                <td width="350">
                                    <font size="12">DIRECTOR GENERAL QUE AUTORIZA:<br/> '.$solicitante.'</font>
                                </td>
                                <td width="100">
                                    <font size="9">FIRMA Y SELLO<br/></font>
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
                                    <font size="12">SOLO PARA USO DE LA OFICINA DE ADMINISTRACIÓN Y SERVICIOS</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="180">
                                    <font size="12">ASIGNADO A:</font><br/><br/><br/><br/>
                                    <font size="12">FECHA:        /        /     </font>
                                </td>
                                <td width="270">
                                    <font size="12">APROBADO POR:</font><br/><br/><br/>
                                    <table width="270" align="center">
                                        <tr>
                                            <td>
                                                _________________________________________<br/>
                                                <font size="12">DIRECTOR GENERAL DE LA OFICINA DE ADMINISTRACIÓN Y SERVICIOS</font>
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