<?php

class formatoMemorandum {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["memorandum_asunto"]) {
            $messages = array_merge($messages, array("memorandum_asunto" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["memorandum_asunto"]);
        }

        // campo_dos = Contenido
        if (!$formulario["memorandum_contenido"]) {
            $messages = array_merge($messages, array("memorandum_contenido" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["memorandum_contenido"]);
        }
    }

    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["memorandum_asunto"] = $datos["campo_uno"];
        $formulario["memorandum_contenido"] = $datos["campo_dos"];

        return $formulario;
    }
    
    public function executeGuardarPlantilla($tipo_formato_id, $datos) {
        try
        {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $plantilla = new Correspondencia_Plantilla();
            $plantilla->setTipoFormatoId($tipo_formato_id);
            $plantilla->setNombre($datos['nombre_plantilla']);
            $plantilla->setCampoUno($datos['asunto']);
            $plantilla->setCampoDos($datos['contenido']);
            $plantilla->save();

            $plantilla_funcionario = new Correspondencia_PlantillaFuncionario();
            $plantilla_funcionario->setPlantillaId($plantilla->getId());
            $plantilla_funcionario->setFuncionarioId(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
            $plantilla_funcionario->save();
            
            $conn->commit();
        }

        catch(Exception $e){
            $conn->rollBack();
        }
        
        exit();
    }
    
    public function executeAdditionalCrear($correspondencia_id){}
    
    public function executeAdditionalEnviar($correspondencia_id){}
    
    public function executeAdditionalAnular($correspondencia_id){}
    
    public function executeAdditionalDevolver($correspondencia_id){}
    
    public function executeAdditionalEmisor($correspondencia_id) {}

    public function executePdf($pdf, $correspondenciaformato, $correspondencia, $emisor, $receptores) {

        $oficinas_clave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");

        //Muestra datos del formato
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }

        $n_envio = $correspondencia->get('n_correspondencia_emisor');
        $result1 = $correspondenciaformato->get('correspondencia_id');
        $asunto = $correspondenciaformato->get('campo_uno');
        $result3 = $correspondenciaformato->get('campo_dos');


//Muestra datos del receptor

        $cadena = "<table border='1'>";

    foreach ($receptores as $list_receptores) {
        $receptor = '';
            $receptor.= $list_receptores->getunombre().' / '.
                    $list_receptores->getctnombre().' / '.
                    ucwords(strtolower($list_receptores->getpn())).' '.
                    ucwords(strtolower($list_receptores->getpa()));

            $cadena.="<tr><th>" . $receptor . "</th></tr>";
        }
        $cadena.="</table>";


        $cadena1 = "<table border='1'>";
    $emisor_X = '';
    $unidad_emisor_id= 0;
    foreach ($emisor as $list_firman) {
            $unidad_emisor_id= $list_firman->getFuncionalId();
        
            $emisor_X.= $list_firman->getunombre().' / '.
                    $list_firman->getctnombre().' / '.
                    ucwords(strtolower($list_firman->getpn())).' '.
                    ucwords(strtolower($list_firman->getpa()));

            $attm_nom = ucwords(strtolower($list_firman->getpn())).' '.
                        ucwords(strtolower($list_firman->getpa()));
            $attm_uni = $list_firman->getunombre();

            $firma_cargo = trim($list_firman->getfirmaCargo());
            if($firma_cargo != '')
                $emisor_X = $firma_cargo;

            $coletilla_db = Doctrine::getTable('Funcionarios_FuncionarioCargo')->coletillaPorUnidad($list_firman->getFuncionarioId(), $unidad_emisor_id);
            foreach($coletilla_db as $value) {
                $firma= $value->getObservaciones();
            }
            if($firma == '' || $firma == ' ' || $firma == NULL){
                $firma = "<b>".$attm_nom."</b><br/>".$attm_uni;
//                $firma = '<img src="/images/firmas/'.$list_firman->getCi().'.jpg"/><br/><font size="7">wdfksjdhfsdn827d8@52^%3@$$h23d!!sdf42r2dwefdwsfcsdfsdfsdf</font><br/>'."<b>".$attm_nom."</b><br/>".$attm_uni;
            }
            
            $cadena1.="<tr><th>" . $emisor_X . "</th></tr>";
        }
        $cadena1.="</table>";

        //Seleccion de membrete
        $audi= '';
        $unidades_hijas = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,array($unidad_emisor_id));
    
        if($oficinas_clave['auditoria_interna']['seleccion'] === $unidad_emisor_id || in_array($unidad_emisor_id, $unidades_hijas))
            $audi= '_contraloria';
        
        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(80, 115, 80);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        
        $head_var= 'gob_pdf'.$audi.'.png';
        //HEADER INDEPENDIENTE
        $tipo_formato= Doctrine::getTable('Correspondencia_TipoFormato')->find($correspondenciaformato->get('tipo_formato_id'));
        if($tipo_formato) {
            if(file_exists('images/organismo/pdf/'.$tipo_formato->getClasse().'_gob_pdf'.$audi.'.png')) {
                $head_var= $tipo_formato->getClasse().'_'.$head_var;
            }
        }
        
        $pdf->SetHeaderData($head_var, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf->SetAutoPageBreak(True, 90);

//        $pdf->SetBarcode($correspondencia->get('n_correspondencia_emisor'));

        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();

//Espacio
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');

//html
        $signature= '';
        if ($correspondencia->getFEnvio() !== null) {
            if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $list_firman->getCi() .".jpg")) {
                    $signature= "/images/firma_digital/". $list_firman->getCi().'.jpg';
            }
        }
        
        $tbl = <<<EOD
<table width="450" "center">
    <tr>
        <td width="450">
            <table width="450" "center">
                <tr><td></td></tr>
                <tr>
                    <td align="right"><h3>$n_envio</h3></td>
                </tr>
                <tr>
                    <td align="center"><br/>
                        <h2><u>MEMORANDO</u></h2>
                    </td>
                </tr>
                <tr>
                    <td><br/>
                        <table width="450" border="1" cellpadding="5">
                            <tr align="left">
                                <td width="60"><font size="12">PARA:</font></td>
                                <td width="390">$cadena</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font size="12">DE:</font></td>
                                <td width="390">$cadena1</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font size="12">ASUNTO:</font></td>
                                <td width="390">$asunto</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font size="12">FECHA:</font></td>
                                <td width="390">$f_envio</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="justify">
                        <font size="12">$result3</font>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                            Atentamente,<br/><br/>
EOD;
        if($signature!= '') {
            $signature= '<img src="'.$signature.'" alt="signature" width="150" height="70" border="0" />';
        }
        
        $tbl .= <<<EOD
                            $signature
                            <br/><br/>
                            <font size="10">$firma</font>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

EOD;
        
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output($n_envio.'__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
    }

    public function executeOdt($formato, $correspondencia, $emisor, $receptores) {

    $unidad_emisor_id= 0;
    foreach ($emisor as $list_firman)
            $unidad_emisor_id= $list_firman->getFuncionalId();
        
    $oficinas_clave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
    
    $audi= '';
    $unidades_hijas = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,array($unidad_emisor_id));
    
    if($oficinas_clave['auditoria_interna']['seleccion'] === $unidad_emisor_id || in_array($unidad_emisor_id, $unidades_hijas))
        $audi= '_consult';
    
    // Lee la plantilla
    $plantilla = file_get_contents(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/memorandum/plantillas/memorandum".$audi."_odt.rtf");

    $plantilla = addslashes($plantilla);
    $plantilla = str_replace('\r','\\r',$plantilla);
    $plantilla = str_replace('\t','\\t',$plantilla);

    $valores = $this->executeTraer($formato);
    $texto_puro = new herramientas();

//    echo html_entity_decode($valores['memorandum_contenido']);
//    echo "<br/><br/><br/>";
    $conten = $texto_puro->htmlRtf($valores['memorandum_contenido']);
//    echo $texto_puro->htmlRtf($valores['memorandum_contenido']);
    //$conten = addslashes($conten);
//    echo "<br/><br/><br/>";
//    echo $conten;
//    exit();

    if($correspondencia->getFEnvio()==null)
        $fecha = 'NO SE HA ENVIADO';
    else
        $fecha = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));

    $de = ''; $coletilla = ''; $solicitante_coletilla= '';
    foreach ($emisor as $list_firman) {
            $solicitante = $list_firman->getPa().' '.$list_firman->getSa().', '.$list_firman->getPn().' '.$list_firman->getSn();
            $solicitante_ci = $list_firman->getCi();
            $solicitante_unidad = $list_firman->getUnombre();
            $solicitante_cargo_tipo = $list_firman->getCtnombre();
            $solicitante_coletilla = $texto_puro->htmlRtf($list_firman->getFirmaPersonal());

            $de .= $texto_puro->htmlRtf($solicitante_unidad.' / '.$solicitante_cargo_tipo).'<p>';
    }
    
    $de .= '$%&';
    $de = str_replace('<p>$%&', '', $de);
    $de = $texto_puro->htmlRtf($de);
    $de = str_replace('\par \par ', '\par ', $de);
    if (trim($solicitante_coletilla)=='')
        $cargo= $texto_puro->htmlRtf($solicitante_cargo_tipo);
    else{
        $solicitante_coletilla = str_replace('\par\par', '\par ', $solicitante_coletilla);
        $solicitante_coletilla = preg_replace('#\\\par#', '', $solicitante_coletilla, 1);
        $cargo= '';
    }

    $para = '';
    foreach ($receptores as $list_receptores) {
        $para.= $list_receptores->getUnombre().' / '.
                $list_receptores->getCtnombre().' / '.
                ucwords(strtolower($list_receptores->getPn())).' '.
                ucwords(strtolower($list_receptores->getPa())).'<p>';
    }
    $para .= '$%&';
    $para = str_replace('<p>$%&', '', $para);
    $para = $texto_puro->htmlRtf($para);
    $para = str_replace('\par \par ', '\par ', $para);

    $plantilla = str_replace('%%CORRELATIVO%%',$correspondencia->getNCorrespondenciaEmisor(),$plantilla);
    $plantilla = str_replace('%%ASUNTO%%',$texto_puro->htmlRtf($valores['memorandum_asunto']),$plantilla);
    $plantilla = str_replace('%%CONTENIDO%%',$conten,$plantilla);
    $plantilla = str_replace('%%FECHA%%',$fecha,$plantilla);
    $plantilla = str_replace('%%DE%%',$de,$plantilla);
    $plantilla = str_replace('%%PARA%%',$texto_puro->htmlRtf($para),$plantilla);
    if (trim($solicitante_coletilla)!= '') {
        $plantilla = str_replace('%%EMISOR_NOMBRE%%','',$plantilla);
        $plantilla = str_replace('%%EMISOR_CARGO%%','',$plantilla);
        $plantilla = str_replace('%%EMISOR_COLETILLA%%',trim($solicitante_coletilla),$plantilla);
    }else {
        $plantilla = str_replace('%%EMISOR_NOMBRE%%',$texto_puro->htmlRtf($solicitante),$plantilla);
        $plantilla = str_replace('%%EMISOR_CARGO%%',$cargo,$plantilla);
        $plantilla = str_replace('%%EMISOR_COLETILLA%%','',$plantilla);
    }


eval( '$rtf = <<<EOF_RTF
' . $plantilla . '
EOF_RTF;
');

    //OBTENIENDO EL NOMBRE
    $nombre_def = "memorandum_".$correspondencia->getNCorrespondenciaEmisor().".odt";

    file_put_contents($nombre_def,$rtf);
    if (!file_exists ($nombre_def)){
    echo "Ocurrio un error al generar el documento de word correspondiente";
    }else{
    $aleatorio = rand(1, 10000000);
    $hash_is = md5($aleatorio);
    $carpeta = $hash_is;
    mkdir(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/memorandum/plantillas/tmp/$carpeta",0777);
    $nuevoarchivo = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/memorandum/plantillas/tmp/$carpeta/$nombre_def";
    if (!copy($nombre_def, $nuevoarchivo)) {
    echo "Ocurrio un error al generar el documento de word Temporal correspondiente";
    }
    $path_a_tu_doc = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/memorandum/plantillas/tmp/$carpeta";

    $enlace = $path_a_tu_doc."/".$nombre_def;
    header ("Content-Disposition: attachment; filename=".$nombre_def."\n\n");
    header( "Content-Charset: utf-8");
    header ("Content-Type: application/octet-stream");
    header ("Content-Length: ".filesize($enlace));

//header( "Content-Type" content="text/html; charset=ISO-8859-1")

    readfile($enlace);
    }
    //ELIMINA ARCHIVO
    unlink($enlace);
    unlink($nombre_def);
    //ELIMINA DIRECTORIO
    rmdir($path_a_tu_doc);

    }
    
    
    
    public function executeEmail($correspondenciaformato, $correspondencia, $emisor, $receptores, $organismos, $copia_emails, $copia_comentario, $emisor_copia, $adjuntos_libres) {

        //Muestra datos del formato
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }

        $n_envio = $correspondencia->get('n_correspondencia_emisor');
        $result1 = $correspondenciaformato->get('correspondencia_id');
        $asunto = $correspondenciaformato->get('campo_uno');
        $result3 = $correspondenciaformato->get('campo_dos');

        //adjuntos
        $adjuntos_ar= Array();
        if(count($adjuntos_libres)> 0) {
            $adjuntos_ar= $adjuntos_libres;
        }

//Muestra datos del receptor

        $cadena = "<table border='0'>";

    foreach ($receptores as $list_receptores) {
        $receptor = '';
            $receptor.= $list_receptores->getunombre().' / '.
                    $list_receptores->getctnombre().' / '.
                    ucwords(strtolower($list_receptores->getpn())).' '.
                    ucwords(strtolower($list_receptores->getpa()));

            $cadena.="<tr><th>" . $receptor . "</th></tr>";
        }
        $cadena.="</table>";


        $cadena1 = "<table border='0'>";
    $emisor_X = '';
    foreach ($emisor as $list_firman) {
            $unidad_emisor_id= $list_firman->getFuncionalId();

            $emisor_X.= $list_firman->getunombre().' / '.
                    $list_firman->getctnombre().' / '.
                    ucwords(strtolower($list_firman->getpn())).' '.
                    ucwords(strtolower($list_firman->getpa()));

            $attm_nom = ucwords(strtolower($list_firman->getpn())).' '.
                        ucwords(strtolower($list_firman->getpa()));
            $attm_uni = $list_firman->getunombre();

            $firma_cargo = trim($list_firman->getfirmaCargo());
            if($firma_cargo != '')
                $emisor_X = $firma_cargo;

            $coletilla_db = Doctrine::getTable('Funcionarios_FuncionarioCargo')->coletillaPorUnidad($list_firman->getFuncionarioId(), $unidad_emisor_id);
            foreach($coletilla_db as $value) {
                $firma= $value->getObservaciones();
            }
            if($firma == '' || $firma == ' ' || $firma == NULL){
                $firma = "<b>".$attm_nom."</b><br/>".$attm_uni;
            }
            
            $cadena1.="<tr><th>" . $emisor_X . "</th></tr>";
        }
        $cadena1.="</table>";

        if($copia_comentario!='')
            $copia_comentario = ', con el siguiente comentario: '.$copia_comentario;

        $tbl = 
'<table "center" width="450">
    <tr>
        <td style="background-color: #7FADDF; color: #FFFFFF; text-align: justify;">
            Este documento ha sido generado a través del SIGLAS-'.sfConfig::get('sf_siglas').', la copia recibida ha sido enviada por '.$emisor_copia.$copia_comentario.'
        </td>
    </tr>
    <tr>
        <td>
            <table width="450">
                <tr>
                    <td align="right"><h3>'.$n_envio.'</h3></td>
                </tr>
                <tr>
                    <td align="center"><br/>
                        <h2><u>MEMORANDUM</u></h2>
                    </td>
                </tr>
                <tr>
                    <td align="center"><br/>
                        <table width="450" border="1" cellpadding="0" align="center">
                            <tr align="left">
                                <td width="60"><font>PARA:</font></td>
                                <td width="390">'.$cadena.'</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font>DE:</font></td>
                                <td width="390">'.$cadena1.'</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font>ASUNTO:</font></td>
                                <td width="390">'.$asunto.'</td>
                            </tr>
                            <tr align="left">
                                <td width="60"><font>FECHA:</font></td>
                                <td width="390">'.$f_envio.'</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="justify">
                        <font>'.$result3.'</font>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                            Atentamente,<br/><br/><font>'.$firma.'</font>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
        
        
            $mensaje['emisor'] = 'Correspondencia '.sfConfig::get('sf_siglas').' - Nº:'.$n_envio.' / '.$asunto;
            $mensaje['receptor'] = 'Livio jose lopez noguera';
            $mensaje['mensaje'] = $tbl;

            $emails = explode(",", $copia_emails);
            for($i=0;$i<count($emails);$i++){
                if(trim($emails[$i])!='')
                    Email::notificacion('correspondencia', trim($emails[$i]), $mensaje, 'inmediata', $adjuntos_ar);
            }
    }

    
}

class ConPie extends TCPDF {
     public function Footer() {
        $this->Image(sfConfig::get("sf_root_dir").'/web/images/organismo/pdf/gob_footer_pdf.png',0,700,450,70,'','','N','','','C');
    }
}