<?php

class formatoOficio
{
/*    public function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    }*/

        public function executePdf($pdf,$correspondenciaformato,$correspondencia,$emisor,$receptores,$organismo)
        {
            $oficinas_clave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");

//Muestra datos del formato
    
        $n_envio = $correspondencia->get('n_correspondencia_emisor');
        $result1= $correspondenciaformato->get('correspondencia_id');
        $result2= $correspondenciaformato->get('campo_uno');
            
//Muestra nombre, cargo y unidad de los receptores internos
    $receptores_internos= '';
    if(count($receptores) > 0) {
        $receptores_internos="<table border='1'>";
        foreach ($receptores as $list_receptores) {
//            $receptores_internos.="<tr><td>" . ucwords(strtolower($list_receptores['ctnombre'])) . ',  ' . ucwords(strtolower($list_receptores['pn'])) . ' ' . ucwords(strtolower($list_receptores['pa'])) . "</td></tr>";
            $receptores_internos.="<tr><td>" . ucwords(strtolower($list_receptores['pn'])) . ' ' . ucwords(strtolower($list_receptores['pa'])) . '<br/>' . ucwords(strtolower($list_receptores['ctnombre'])) . "</td></tr>";
            $receptores_internos.="<tr><td><b>" . ucwords(strtolower($list_receptores['unombre'])) . "</b></td></tr>";
        }
        $receptores_internos.="</table>";
    }
    
//Muestra nombre, cargo y organismo de receptores externos
    $receptores_externos= '';
    if(count($organismo) > 0) {
        $receptores_externos="<table border='1'>";
        foreach ($organismo as $list_organismos) {
//            $receptores_externos.="<tr><td>" . ucwords(strtolower($list_organismos['receptor_persona_cargo'])) . ',  ' . ucwords(strtolower($list_organismos['receptor_persona'])) . "</td></tr>";
            $receptores_externos.="<tr><td><b>" . ucwords(strtolower($list_organismos['receptor_persona'])) . '<br/>' . ucwords(strtolower($list_organismos['receptor_persona_cargo'])) . "</b></td></tr>";
            $receptores_externos.="<tr><td><b>" . ucwords(strtolower($list_organismos['receptor_organismo'])) . "</b></td></tr>";
        }
        $receptores_externos.="</table>";    
    }
    
//Muestra datos del emisor
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
            }
        }

//Mostrar fecha
          $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
          
          if($correspondencia->get('f_envio')== '') {
              $day= date('d');
              $month= (int)date('m');
              $year= date('Y');
              $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
          }else {
              $day= date('d', strtotime($correspondencia->get('f_envio')));
              $month= (int)date('m', strtotime($correspondencia->get('f_envio')));
              $year= date('Y', strtotime($correspondencia->get('f_envio')));
              $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
          }
          
        //Seleccion de membrete
        $audi= '';
        $unidades_hijas = Doctrine::getTable('Organigrama_Unidad')->combounidad(FALSE,array($unidad_emisor_id));
    
        if($oficinas_clave['auditoria_interna']['seleccion'] === $unidad_emisor_id || in_array($unidad_emisor_id, $unidades_hijas))
            $audi= '_contraloria';
        
        $head_var= 'gob_pdf'.$audi.'.png';
        //HEADER INDEPENDIENTE
        $tipo_formato= Doctrine::getTable('Correspondencia_TipoFormato')->find($correspondenciaformato->get('tipo_formato_id'));
        if($tipo_formato) {
            if(file_exists('images/organismo/pdf/'.$tipo_formato->getClasse().'_gob_pdf'.$audi.'.png')) {
                $head_var= $tipo_formato->getClasse().'_'.$head_var;
            }
        }
        
        $pdf = new ConPie(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(65, 115, 65);
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData($head_var, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf-> SetAutoPageBreak(True, 90);

        $pdf->SetBarcode($correspondencia->get('n_correspondencia_emisor'));

        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();
             
//Espacio
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
//        $pdf->Cell(80, 10, "", 0, 1, 'L');
        // $pdf->Cell(0, 10, "ffdsa<aaa", 0, 0, 'C');
//html
    $signature= '';
    if ($correspondencia->getFEnvio() !== null) {
        if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $list_firman->getCi() .".jpg")) {
                $signature= "/images/firma_digital/". $list_firman->getCi().'.jpg';
        }
    }

$tbl = <<<EOD
     <br></br>

    <table style="width: 100%">
        <tr>
            <td width="50%" align="left">$n_envio</td><td></td>
        </tr>
        <tr>
            <td width="50%"></td><td align="rigth">$f_envio</td>
        </tr>
        <br></br>
        <tr>
            <td width="50%" align="left"><b>Ciudadano(a):</b></td><td></td>
        </tr>
        <tr>
            <td width="50%" align="left">$receptores_externos</td><td></td>
        </tr>
        <tr>
            <td width="50%" align="left">$receptores_internos</td><td></td>
        </tr>
        <tr>
            <td width="50%" align="left">Su Despacho.-</td><td></td>
        </tr>
        <tr>
            <td colspan="2" align="justify"><br/>$result2</td>
        </tr>
        <br></br>
        <br></br>
        <br></br>
        <tr>
            <td colspan="2" align="center">Atentamente,<br/><br/>
EOD;
        if($signature!= '') {
            $signature= '<img src="'.$signature.'" alt="signature" width="150" height="70" border="0" />';
        }
        
        $tbl .= <<<EOD
                    $signature
                    <br/><br/><font size="10">$firma</font></td>
        </tr>
    </table>
   
EOD;
         $pdf->writeHTML($tbl, true, false, false, false, '');
         $pdf->Output($n_envio.'__'.date('d-m-Y').'.pdf');
          return sfView::NONE;
        }
        
    public function executeOdt($formato, $correspondencia, $emisor, $receptores, $organismos) {

    $unidad_emisor_id= 0;
    foreach ($emisor as $list_firman)
            $unidad_emisor_id= $list_firman->getFuncionalId();
    
    $oficinas_clave = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/oficinasClave.yml");
    
    $audi= '';
    if($oficinas_clave['consultoria_juridica']['seleccion'] === $unidad_emisor_id)
        $audi= '_consult';
    
    // Lee la plantilla
    $plantilla = file_get_contents(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/oficio/plantillas/oficio".$audi."_odt.rtf");

    $plantilla = addslashes($plantilla);
    $plantilla = str_replace('\r','\\r',$plantilla);
    $plantilla = str_replace('\t','\\t',$plantilla);

    $valores = $this->executeTraer($formato);
    $texto_puro = new herramientas();

    $conten = $texto_puro->htmlRtf($valores['oficio_contenido']);

    $n_envio = $correspondencia->get('n_correspondencia_emisor');
    $result1= $formato->get('correspondencia_id');
    
    //FECHA
    $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
          
    if($correspondencia->get('f_envio')== '') {
        $day= date('d');
        $month= (int)date('m');
        $year= date('Y');
        $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
    }else {
        $day= date('d', strtotime($correspondencia->get('f_envio')));
        $month= (int)date('m', strtotime($correspondencia->get('f_envio')));
        $year= date('Y', strtotime($correspondencia->get('f_envio')));
        $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
    }
    
    $solicitante_coletilla= '';
    foreach ($emisor as $list_firman) {
            $solicitante = $list_firman->getPa().' '.$list_firman->getSa().', '.$list_firman->getPn().' '.$list_firman->getSn();
            $solicitante_ci = $list_firman->getCi();
            $solicitante_unidad = $list_firman->getUnombre();
            $solicitante_cargo_tipo = $list_firman->getCtnombre();
            $solicitante_coletilla = $texto_puro->htmlRtf($list_firman->getFirmaPersonal());
    }

    if (trim($solicitante_coletilla)=='')
        $cargo= $texto_puro->htmlRtf($solicitante_cargo_tipo);
    else{
        $solicitante_coletilla = str_replace('\par\par', '\par ', $solicitante_coletilla);
        $solicitante_coletilla = preg_replace('#\\\par#', '', $solicitante_coletilla, 1);
        $cargo= '';
    }
    
    //RECEPTORES INTERNOS
    $receptores_internos= '';
    if(count($receptores) > 0) {
        foreach ($receptores as $list_receptores) {
            $receptores_internos.= ucwords(strtolower($list_receptores['pn'])) . ' ' . ucwords(strtolower($list_receptores['pa'])) . "<br/>" . ucwords(strtolower($list_receptores['ctnombre']));
            $receptores_internos.= " <br/>" . ucwords(strtolower($list_receptores['unombre'])) . "<br/> ";
        }
    }
    
    //RECEPTORES EXTERNOS
    $receptores_externos= '';
    if(count($organismos) > 0) {
        foreach ($organismos as $list_organismos) {
            $receptores_externos.=  ucwords(strtolower($list_organismos['receptor_persona'])) . "<br/>" . ucwords(strtolower($list_organismos['receptor_persona_cargo']));
            $receptores_externos.= " <br/>" . ucwords(strtolower($list_organismos['receptor_organismo'])) . " <br/> ";
        }
    }

    $receptores= $receptores_internos.$receptores_externos;
    
    $plantilla = str_replace('%%FECHA%%',$f_envio,$plantilla);
    $plantilla = str_replace('%%NUMERO%%',$n_envio,$plantilla);
    $plantilla = str_replace('%%CONTENIDO%%',$conten,$plantilla);
//    $plantilla = str_replace('%%DE%%',$de,$plantilla);
    $plantilla = str_replace('%%PARA%%',$texto_puro->htmlRtf($receptores),$plantilla);
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
    $nombre_def = "oficio_".$correspondencia->getNCorrespondenciaEmisor().".odt";

    file_put_contents($nombre_def,$rtf);
    if (!file_exists ($nombre_def)){
        echo "Ocurrio un error al generar el documento de word correspondiente";
    }else{
        $aleatorio = rand(1, 10000000);
        $hash_is = md5($aleatorio);
        $carpeta = $hash_is;
        mkdir(sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/oficio/plantillas/tmp/$carpeta",0777);
        $nuevoarchivo = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/oficio/plantillas/tmp/$carpeta/$nombre_def";
        
        if (!copy($nombre_def, $nuevoarchivo)) {
            echo "Ocurrio un error al generar el documento de word Temporal correspondiente";
        }
        $path_a_tu_doc = sfConfig::get("sf_root_dir")."/apps/correspondencia/modules/formatos/lib/oficio/plantillas/tmp/$carpeta";

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

    public function executeEmail($correspondenciaformato, $correspondencia, $emisor, $receptores, $organismos, $copia_emails, $copia_comentario, $emisor_copia) {

        $n_envio = $correspondencia->get('n_correspondencia_emisor');
        $result1 = $correspondenciaformato->get('correspondencia_id');
        $result3 = $correspondenciaformato->get('campo_uno');

        //FECHA
        $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        if($correspondencia->get('f_envio')== '') {
            $day= date('d');
            $month= date('m');
            $year= date('Y');
            $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
        }else {
            $day= date('d', strtotime($correspondencia->get('f_envio')));
            $month= date('m', strtotime($correspondencia->get('f_envio')));
            $year= date('Y', strtotime($correspondencia->get('f_envio')));
            $f_envio= 'Caracas, '.$day.' de '.$months[$month].' de '.$year;
        }

        //RECEPTORES INTERNOS
        $receptores_internos= '';
        if(count($receptores) > 0) {
            foreach ($receptores as $list_receptores) {
                $receptores_internos.= ucwords(strtolower($list_receptores['ctnombre'])) . ',  ' . ucwords(strtolower($list_receptores['pn'])) . ' ' . ucwords(strtolower($list_receptores['pa'])) . " ";
                $receptores_internos.= " <br/><b>" . ucwords(strtolower($list_receptores['unombre'])) . "</b><br/> ";
            }
        }

        //RECEPTORES EXTERNOS
        $receptores_externos= '';
        if(count($organismos) > 0) {
            foreach ($organismos as $list_organismos) {
                $receptores_externos.= ucwords(strtolower($list_organismos['receptor_persona_cargo'])) . ',  ' . ucwords(strtolower($list_organismos['receptor_persona'])) . " ";
                $receptores_externos.= " <br/><b>" . ucwords(strtolower($list_organismos['receptor_organismo'])) . "</b><br/> ";
            }
        }

        $receptores= $receptores_internos.$receptores_externos;

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
                    <td align="left"><h3>Nro. '.$n_envio.'</h3></td>
                </tr>
                <tr>
                    <td align="right"><h3>'.$f_envio.'</h3></td>
                </tr>
                <tr>
                    <td align="left"><br/>
                        Ciudadanos(as):<br/>
                        '. $receptores .'<br/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Presente.-<br/>
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
        
        
            $mensaje['emisor'] = 'Correspondencia '.sfConfig::get('sf_siglas').' - Oficio Nº: '.$n_envio;
            $mensaje['receptor'] = 'Livio jose lopez noguera';
            $mensaje['mensaje'] = $tbl;

            $emails = explode(",", $copia_emails);
            for($i=0;$i<count($emails);$i++){
                if(trim($emails[$i])!='')
                    Email::notificacion('correspondencia', trim($emails[$i]), $mensaje, 'inmediata');
            }
    }
    
    public function executeValidar($formulario, &$messages, &$formato)
    {
        // campo_uno = Contenido
        if (!$formulario["oficio_contenido"]) {
            $messages = array_merge($messages, array("oficio_contenido" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["oficio_contenido"]);
        }
    }

    public function executeTraer($datos)
    {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["oficio_contenido"] = $datos["campo_uno"];

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
            $plantilla->setCampoUno($datos['contenido']);
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
    
}