<?php

class formatoSolicitudExpediente {
    public function executeValidar($formulario, &$messages, &$formato) {

//        echo '<pre>';
//        print_r($formulario);
//        exit();
        // campo_uno = Asunto
        if (!$formulario["solicitud_expediente_serie_documental"]) {
            $messages = array_merge($messages, array("solicitud_expediente_serie_documental" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["solicitud_expediente_serie_documental"]);
        }
        
        // campo_dos = Asunto
        if (!$formulario["solicitud_expediente_motivo"]) {
            $messages = array_merge($messages, array("solicitud_expediente_motivo" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["solicitud_expediente_motivo"]);
        }
        
        // campo_tres = Prestamo Fisico
        if ($formulario["solicitud_expediente_prestamo_fisico"]) {
            $formato->setCampoTres('t');
        } else {
            $formato->setCampoTres('f');
        }
        
        // campo_cuatro = Prestamo Digital
        if ($formulario["solicitud_expediente_prestamo_digital"]) {
            $formato->setCampoCuatro('t');
        } else {
            $formato->setCampoCuatro('f');
        }
        
        // campo_seis = Expediente Numero
        if ($formulario["solicitud_expediente_numero"]) {
            $formato->setCampoSeis($formulario["solicitud_expediente_numero"]);
        }
        
        $yml_clasificadores = sfYAML::dump($formulario["solicitud_expediente_clasificador"]);
        $formato->setCampoCinco($yml_clasificadores);
        
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["correspondencia_id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        
        $formulario["solicitud_expediente_serie_documental"] = $datos["campo_uno"];
        
        $serie_detalles = Doctrine::getTable('Archivo_SerieDocumental')->find($formulario["solicitud_expediente_serie_documental"]);
        $formulario["solicitud_expediente_serie_documental_nombre"] = $serie_detalles->getNombre();
        
        $formulario["solicitud_expediente_motivo"] = $datos["campo_dos"];
        $formulario["solicitud_expediente_prestamo_fisico"] = $datos["campo_tres"];
        $formulario["solicitud_expediente_prestamo_digital"] = $datos["campo_cuatro"];
        
        $formulario["solicitud_expediente_clasificador"] = sfYaml::load($datos["campo_cinco"]);

        $formulario["solicitud_expediente_motivo"] = $datos["campo_seis"];
        
        $calsificadores_array = array();
        foreach ($formulario["solicitud_expediente_clasificador"] as $clasificador_id => $valor) {
            if($valor!=''){
                $clasificador = Doctrine::getTable('Archivo_Clasificador')->find($clasificador_id);
                $calsificadores_array[$clasificador->getNombre()] = $valor;
            }
        }
        $formulario["solicitud_expediente_clasificador_detalles"] = $calsificadores_array;

        return $formulario;
    }
    
    public function executeAdditionalEmisor($correspondencia_id) {}
    
    public function executeTraerDesdeArchivo($valores) {
        //Crea correspondencia desde archivo
//        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $valores['tipo_formato_id'];
        
        $datos = Doctrine::getTable('Archivo_Expediente')->find($valores['expediente_id']);
        
        $formulario["solicitud_expediente_serie_documental"] = $datos["serie_documental_id"];
        
        $serie_detalles = Doctrine::getTable('Archivo_SerieDocumental')->find($datos["serie_documental_id"]);
        $formulario["solicitud_expediente_serie_documental_nombre"] = $serie_detalles->getNombre();
        
//        $formulario["solicitud_expediente_motivo"] = $datos["campo_dos"];
        $formulario["solicitud_expediente_prestamo_fisico"] = 't';
        $formulario["solicitud_expediente_prestamo_digital"] = 'f';
        
        $formulario['solicitud_expediente_numero']= $datos["correlativo"];
        
//        Se comenta esto para llenar solo el Numero de Correspondencia
        $clasificadores= Array();
//        $datos_clas= Doctrine::getTable('Archivo_ExpedienteClasificador')->findByExpedienteId($datos["id"]);
//        
//        foreach($datos_clas as $value) {
//            $clasificadores[$value->getClasificadorId()]= $value->getValor();
//        }
        
        $formulario["solicitud_expediente_clasificador"] = $clasificadores;

        $calsificadores_array = array();
        foreach ($formulario["solicitud_expediente_clasificador"] as $clasificador_id => $valor) {
            if($valor!=''){
                $clasificador = Doctrine::getTable('Archivo_Clasificador')->find($clasificador_id);
                $calsificadores_array[$clasificador->getNombre()] = $valor;
            }
        }
        $formulario["solicitud_expediente_clasificador_detalles"] = $calsificadores_array;
//        print_r($formulario); exit();
        $correspondencia = array();
        //Vinculo llena campos de correspondencia desde elementro en archivo
        $correspondencia['formato'] = $formulario;
        $correspondencia['formato']['tipo_formato_id'] = $valores['tipo_formato_id'];

        sfContext::getInstance()->getUser()->setAttribute('correspondencia',$correspondencia);
        exit();
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
                        <h2><u>NOMBRE DEL FORMATO</u></h2>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="225">
                                    <font size="12">NOMBRE ETIQUETA</font>
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
    
    public function executeDescriptoresSerie($valores)
    {
        $serie_documental_id = $valores['serie_id'];
        $correspondencia_id= '';
        if($valores['corres_id'])
            $correspondencia_id= $valores['corres_id'];
        
        header( 'Location: '.sfConfig::get('sf_app_archivo_url').'expediente/listarValores?s_id='.$serie_documental_id.'&arch=1&c_id='.$correspondencia_id ) ;
        exit();
    }
}