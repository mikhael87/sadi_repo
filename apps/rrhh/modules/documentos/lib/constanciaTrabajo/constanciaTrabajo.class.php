<?php

class documentoConstanciaTrabajo {
    public function executePdf($pdf) {
        
        
        $funcionario['cedula'] = '16199572';
        $funcionario['nombre'] = 'Livio José López Noguera';
        $funcionario['sexo'] = 'M';
        $funcionario['unidad'] = 'Presidencia';
        $funcionario['cargo'] = 'Presidente';
        $funcionario['condicion'] = 1;
        $funcionario['f_ingreso'] = '22-12-2014';
        
        
        
        
        
        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(80, 115, 80);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        
        $head_var= 'gob_pdf.png';
        //HEADER INDEPENDIENTE
        if(file_exists('images/organismo/pdf/constanciaTrabajo_gob_pdf.png')) {
            $head_var= 'constanciaTrabajo_gob_pdf.png';
        }
        $dominio_var = sfConfig::get('sf_dominio');
        
        $pdf->SetHeaderData($head_var, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf->SetAutoPageBreak(True, 90);

        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        
        // INICIO DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        // INICIO DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        // INICIO DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        
        $sf_oficinasClave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
        $sf_rrhh = sfYaml::load(sfConfig::get('sf_root_dir')."/config/siglas/rrhh.yml");
        
        $recursos_humanos = Doctrine::getTable('Organigrama_Unidad')->find($sf_oficinasClave['recursos_humanos']['seleccion']);
        
        $funcionario_rrhh = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDelCargo($sf_rrhh['cargo_maxima_autoridad_rrhh']);
        $cargo_rrhh = Doctrine::getTable('Organigrama_Cargo')->datosDeCargo($sf_rrhh['cargo_maxima_autoridad_rrhh']);
        
        // BUSCAR EL MODELO DE CONSTANCIA DE TRABAJO ACTIVO PARA EL FUNCIONARIO SOLICITANTE
        $modelo_constancia = Doctrine::getTable('Rrhh_Configuraciones')->findOneByModulo("constanciaTrabajo");
        $contenido_constancia = $modelo_constancia->getParametros();
        
        // BUSCAR LA FIRMA DIGITALIZADA DE LA MAXIMA AUTORIDAD DE RRHH
        $signature_rrhh= '';
        if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $funcionario_rrhh[0]->getCi() .".jpg")) {
                $signature_rrhh= "/images/firma_digital/". $funcionario_rrhh[0]->getCi().'.jpg';
        }
        
        // BUSCAR LA COLETILLA DE FIRMA DE LA MAXIMA AUTORIDAD DE RRHH
        $coletilla_rrhh=$funcionario_rrhh[0]->getColetilla();
        
        // FIN DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        // FIN DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        // FIN DE FORMATEO DE DATOS DE LA UNIDAD Y CARGO DE RRHH
        

        
        // INICIO DE FORMATEO DE DATOS DE FUNCIONARIO SOLICITANTE
        // INICIO DE FORMATEO DE DATOS DE FUNCIONARIO SOLICITANTE
        // INICIO DE FORMATEO DE DATOS DE FUNCIONARIO SOLICITANTE
        
        // AGREGAR PUNTOS SEPARADORES DE MILES A LA CEDULA
        $cedula = number_format($funcionario['cedula'], 0, '', '.');
        
        // PREPARACION DE VARIABLES SEGUN GENERO
        if($funcionario['sexo']=='M'){
            $genero_uno = 'el ciudadano';
            $genero_dos = 'adscrito';
        } else {
            $genero_uno = 'la ciudadana';
            $genero_dos = 'adscrita';
        }
        
        // PREPARACION DE LA FECHA DE SOLICITUD DE LA CONSTANCIA DE TRABAJO
        $mes[1]='enero'; $mes[2]='febrero'; $mes[3]='marzo'; $mes[4]='abril';
        $mes[5]='mayo'; $mes[6]='junio'; $mes[7]='julio'; $mes[8]='agosto';
        $mes[9]='septiembre'; $mes[10]='octubre'; $mes[11]='noviembre'; $mes[12]='diciembre';

        if(date('d') == 1)
            $dia_letra = "el primer día";
        elseif(date('d') == 21)
            $dia_letra = "a los veintiún días";  
        elseif(date('d') == 31)
            $dia_letra = "a los treinta y un días";  
        else
            $dia_letra = "a los ".numerosLetras::toWord(date('d'))." días";  

        $fecha_actual = strtolower($dia_letra.' del mes de '.$mes[date('n')].' del año '.numerosLetras::toWord(date('Y')));
            
        
        // PREPARACION DEL PREFIJO DEL NOMBRE DE LA UNIDAD
        $prefijo_unidad = Formateo::prefijo_unidad($funcionario['unidad']);
        
        // PREPARACION DE CALCULO DEL SALARIO
        $salario = 1000+12000+3340.12;
        
        // TRANSFORMAR SALARIO NUMERICO EN LETRAS
        $salario_letra = trim(numerosLetras::toWord($salario, 'bolívares', 'céntimos'));
        
        // FORMATEOD DE SALARIO NUMERICO CON SEPARADORES DE MILES
        $salario = number_format($salario, 2, ',', '.');
        
        $genero_tres = '';
        
        // REEMPLAZAR VARIABLES DEL MODELO DE CONSTANCIA
        $contenido_constancia = str_replace('%%CEDULA%%', $cedula, $contenido_constancia);
        $contenido_constancia = str_replace('%%NOMBRE%%', $funcionario['nombre'], $contenido_constancia);
        
        $contenido_constancia = str_replace('%%FECHA_INGRESO%%', $funcionario['f_ingreso'], $contenido_constancia);
        $contenido_constancia = str_replace('%%CARGO%%', $funcionario['cargo'], $contenido_constancia);
        $contenido_constancia = str_replace('%%UNIDAD%%', $funcionario['unidad'], $contenido_constancia);
        
        $contenido_constancia = str_replace('%%SALARIO_LETRA%%', $salario_letra, $contenido_constancia);
        $contenido_constancia = str_replace('%%SALARIO_NUMERO%%', $salario, $contenido_constancia);
        
        $contenido_constancia = str_replace('%%PREFIJO_UNIDAD%%', $prefijo_unidad, $contenido_constancia);
        $contenido_constancia = str_replace('%%GENERO_UNO%%', $genero_uno, $contenido_constancia);
        $contenido_constancia = str_replace('%%GENERO_DOS%%', $genero_dos, $contenido_constancia);
        $contenido_constancia = str_replace('%%GENERO_TRES%%', $genero_tres, $contenido_constancia);
        
        $contenido_constancia .= "<br/><br/>";
        $contenido_constancia .= "Constancia generada automaticamente a solicitud de la parte interesada, ".$fecha_actual.".";
                
        $codigo_documento = strtolower(bin2hex(openssl_random_pseudo_bytes(5)));
        $var_doc = strtolower(bin2hex(openssl_random_pseudo_bytes(1)));
        
        $file_qr = 'images/qr/'.$codigo_documento.'.png';        
        QRcode::png($dominio_var.'/documentos?'.$var_doc.'='.$codigo_documento, $file_qr, 'L', 4, 2); 
        
        $documentos = new Public_Documentos();
        $documentos->setDocumentoTipoId(1);
        $documentos->setContenido($contenido_constancia);
        $documentos->setCodigo($codigo_documento);
        $documentos->save();
        
        
        
        $tbl = <<<EOD
<table width="450" "center">
    <tr>
        <td width="450">
            <table width="450" "center">
                <tr><td></td></tr>
                <tr>
                    <td align="center"><br/>
                        <h1><u>CONSTANCIA DE TRABAJO</u></h1><br/>
                    </td>
                </tr>
                <tr>
                    <td align="justify">
                        <font size="12">$contenido_constancia</font><br/><br/>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                            <font size="12">Atentamente,</font><br/><br/>
EOD;
        if($signature_rrhh!= '') {
            $signature_rrhh= '<img src="'.$signature_rrhh.'" width="150" height="70" border="0" />';
        }
        
        $tbl .= <<<EOD
                            $signature_rrhh
                            <br/><br/>
                            <font>$coletilla_rrhh</font>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

EOD;
        
$html_valid = '<table width="450" align="center">
<tr>
<td><font size="8">Valide la veracidad y vigencia de este documento descargando el original desde el siguiente enlace<br/>
<u color="blue"><a href="'.$dominio_var.'/documentos?'.$var_doc.'='.$codigo_documento.'">'.$dominio_var.'/documentos?'.$var_doc.'='.$codigo_documento.'</a></u>
</font></td>
</tr>
</table>';
        
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->writeHTMLCell(0, 0, 80, 660, $html_valid);
        $pdf->Image(sfConfig::get("sf_root_dir").'/web/images/qr/'.$codigo_documento.'.png',-450,630,0,70,'','','N','','','C');
        $pdf->Output('ConstanciaTrabajo__'.$codigo_documento.'.pdf');
        return sfView::NONE;
    }
}

class ConPie extends TCPDF {
     public function Footer() {
        
        $this->Image(sfConfig::get("sf_root_dir").'/web/images/organismo/pdf/gob_footer_pdf.png',0,700,450,70,'','','N','','','C');
        
    }
}