<?php

class formatoPuntoInformacion {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["punto_informacion_asunto"]) {
            $messages = array_merge($messages, array("punto_informacion_asunto" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["punto_informacion_asunto"]);
        }
        
        // campo_dos = Sintesis
        if (!$formulario["punto_informacion_sintesis"]) {
            $messages = array_merge($messages, array("punto_informacion_sintesis" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["punto_informacion_sintesis"]);
        }
        
        // campo_tres = Recomendaciones
        if (!$formulario["punto_informacion_recomendaciones"]) {
            $messages = array_merge($messages, array("punto_informacion_recomendaciones" => "Campo requerido"));
        } else {
            $formato->setCampoTres($formulario["punto_informacion_recomendaciones"]);
        }
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["punto_informacion_asunto"] = $datos["campo_uno"];
        $formulario["punto_informacion_sintesis"] = $datos["campo_dos"];
        $formulario["punto_informacion_recomendaciones"] = $datos["campo_tres"];

        return $formulario;
    }
    
    public function executeTraerHijo($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["revision_punto_informacion_observaciones"] = $datos["campo_dos"];

        return $formulario;
    }
    
    public function executeAdditionalEmisor($correspondencia_id) {}
    
    
    public function executePdf($pdf, $formato, $correspondencia, $emisor, $receptores) {

        $correspondencia_punto = $correspondencia;
        $formato_punto = $formato;
        $emisor_punto = $emisor;
        $receptores_punto = $receptores;

        $correspondencia_revision = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByPadreId($correspondencia->getId());
        if($correspondencia_revision){
            $formato_revision = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_revision->getId());
            $emisor_revision=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_revision->getId(), TRUE);
            $receptores_revision=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_revision->getId());
            
            foreach ($emisor_revision as $list_firmante) {
                    $firmante_final = $list_firmante->getpn().' '.$list_firmante->getpa().' '.$list_firmante->getsa();
                    $firmante_final_ci = $list_firmante->getCi();
                    $firmante_final_unidad = $list_firmante->getunombre();
                    $firmante_final_cargo_tipo = $list_firmante->getctnombre();
                    $firmante_final_firma = $list_firmante->getFirma();
                    $firmante_final_proteccion = $list_firmante->getProteccion();
            }
            
            $valores_revision = $this->executeTraerHijo($formato_revision);
            
            $letracolor_revision = '#FFFFFF';
            $bgcolor_revision = '';
            $bgcolor_revision_alterna = '#B40404';
        } else {
            $letracolor_revision = '#000000';
            $bgcolor_revision = '#bfbfbf';
            $bgcolor_revision_alterna = '#bfbfbf';
            $valores_revision["revision_punto_informacion_observaciones"]='';
        }

        //Muestra datos del formato
        $valores_punto = $this->executeTraer($formato_punto);

        foreach ($emisor_punto as $list_solicitante) {
                $solicitante = $list_solicitante->getpn().' '.$list_solicitante->getpa().' '.$list_solicitante->getsa();
                $solicitante_ci = $list_solicitante->getCi();
                $solicitante_unidad = $list_solicitante->getunombre();
                $solicitante_cargo_tipo = $list_solicitante->getctnombre();
                $solicitante_firma = $list_solicitante->getFirma();
                $solicitante_proteccion = $list_solicitante->getProteccion();
        }
        
        $firmante = '';
        $firmante_ci= '';
        $firmante_cargo_tipo = '';
        foreach ($receptores as $list_firmante) {
            $firmante_cargo_tipo.= $list_firmante->getpn().' '.$list_firmante->getpa();
            $firmante.= $list_firmante->getctnombre();
            $firmante_ci= $list_firmante->getCi();
            break;
        }

        if($correspondencia_revision){
            if ($correspondencia_revision->getFEnvio() == null)
                $f_decision = '<b style="color: red;">SIN REVISION</b>';
            else
                $f_decision = date('d/m/Y h:i A', strtotime($correspondencia_revision->getFEnvio()));
        } else {
            if ($correspondencia_punto->getFEnvio() == null)
                $f_decision = '<b style="color: red;">SIN PRESENTAR</b>';
            else
                $f_decision = '<b style="color: red;">SIN REVISION</b>';
        }
        
        if ($correspondencia_punto->getFEnvio() == null) {
            $f_presentacion = '<b style="color: red;">SIN PRESENTAR</b>';
        } else {
            $f_presentacion = date('d/m/Y h:i A', strtotime($correspondencia_punto->getFEnvio()));
        }
        
        $vbs= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->vistobuenoCorrespondenciaAsc($correspondencia_punto->getId());
        
        $i=0;
        $cont_vb=1;
        //CONFIGURADO PARA MAX 10 FILAS DE VERIFICADOS (40 FUNCIONARIOS) 4 X FILA
        $series= array(4, 8, 12, 16, 20, 24, 28, 32, 36, 40);
        $funcionarios_vb_str= '';
        if(count($vbs) > 0) {
            $funcionarios_vb_str= '<tr>';
            foreach($vbs as $vbs_funcionario) {
                if($vbs_funcionario->getStatus()=='V' || $vbs_funcionario->getStatus()=='E'){
                    $datos_func_vb= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionarioCargo($vbs_funcionario->getFuncionarioId(),$vbs_funcionario->getFuncionarioCargoId());
                    foreach($datos_func_vb as $datos_func) {
                        //EL WIDTH ES CALCULADO DEPENDIENDO DE LA CANTIDAD DE FUNCIONARIOS 
                        if($vbs_funcionario->getStatus()=='V'){
                            $funcionarios_vb_str .= '<td width="';
                            $resultado_vbs = ''; //AGREGAR FIRMA ELECTRONICA
                        } else {
                            $funcionarios_vb_str .= '<td bgcolor="#bfbfbf" width="';
                            $resultado_vbs = '<b style="color: red;">NO HA DADO EL VISTO BUENO</b>';
                        }

                        $real= 1;
                        //IDENTIFICA EN QUE FILA SE ENCUENTRA Y SI EL TOTAL EXCEDE O NO LOS 4 X FILA
                        for($k=0; $k < count($series); $k++) {
                            if($cont_vb <= $series[$k] && count($vbs) > $series[$k]) {
                                //SI EXCEDE LOS 4 X FILA DIVIDE POR 4
                                $real= 4;
                                break;
                            }elseif($cont_vb <= $series[$k] && count($vbs) <= $series[$k]) {
                                //SI NO EXCEDE, IDENTIFICA SI ES 1,2, 3 O ULTIMO DENTRO DE LA FILA
                                $real= count($vbs)- ($k * 4);
                                break;
                            }
                        }
                        $width= 100/ $real;
                        //TABLA ADICIONAL PARA ADMINISTRAR ESPACIOS
                        $funcionarios_vb_str .= $width.'%">';
                        $funcionarios_vb_str .= '<table style="font-size: 8px">';
                        $funcionarios_vb_str .= '<tr><td><font size="8">Corforme:</font></td></tr>';
                        if($resultado_vbs != '') {
                            $funcionarios_vb_str .= '<tr><td></td></tr><tr><td align="vcenter" align="center">'.$resultado_vbs.'</td></tr><tr><td></td></tr>';
                        }else {
                            $vb_sig= '';
                            if ($correspondencia->getFEnvio() !== null) {
                                if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $datos_func->getCi() .".jpg")) {
                                        $vb_sig= "/images/firma_digital/". $datos_func->getCi().'.jpg';

                                        $funcionarios_vb_str .= '<tr>
                                            <td style="text-align: center">
                                                <img src="'.$vb_sig.'" alt="signature" width="150" height="70" border="0" />
                                            </td>
                                        </tr>';
                                }else {
                                    $funcionarios_vb_str .= '<tr><td></td></tr><tr><td align="vcenter" align="center">'.$resultado_vbs.'</td></tr><tr><td></td></tr>';
                                }
                            }
                        }
                        $funcionarios_vb_str .= '<tr><td align="vcenter" align="center"><font size="8">'.$datos_func->getPrimerNombre().' '.$datos_func->getPrimerApellido().'</font></td></tr>';
                        $funcionarios_vb_str .= '<tr><td align="vcenter" align="center"><font size="7">'.$datos_func->getCtnombre().' de '.$datos_func->getUnombre().'</font></td></tr>';
                        $funcionarios_vb_str .= '</table>';
                        $i++;

                        $funcionarios_vb_str .= '</td>';
                    }
                    if(in_array($cont_vb, $series) && count($vbs) > $cont_vb) {
                        $funcionarios_vb_str .= '</tr><tr>';
                    }
                    foreach($series as $turn) {
                        if($cont_vb == $turn && count($vbs)== $turn) {
                            $funcionarios_vb_str .= '</tr>';
                            break;
                        }elseif($cont_vb != $turn && count($vbs) == $cont_vb) {
                            $funcionarios_vb_str .= '</tr>';
                            break;
                        }
                    }
                    $cont_vb++;
                }
            }
        }

        //Preparado por
        $datos_func_preparado= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($correspondencia_punto->getIdCreate());
        
        //Tiene adjuntos o no
        $anexo_a= Doctrine::getTable('Correspondencia_AnexoArchivo')->findByCorrespondenciaId($correspondencia_punto->getId());
        $anexo_f= Doctrine::getTable('Correspondencia_AnexoFisico')->findByCorrespondenciaId($correspondencia_punto->getId());
        
        // CORRELATIVO DE PUNTO
        if($correspondencia_revision){
            if ($correspondencia_revision->getFEnvio() == null) {
                $num_agenda = '<font size="9" align="center"><b style="color: red;">SIN AGENDAR</b></font>';
            } else {
                
                $unidad_correlativo_id = $correspondencia_revision->getUnidadCorrelativoId();
                $nomenclador_revision = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($unidad_correlativo_id);
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($nomenclador_revision->getUnidadId());
                $siglas_unidad = str_replace("/", "-", $unidad->getSiglas());
                $nomenclatura = str_replace("-", ",$", $nomenclador_revision->getNomenclador());
                
                $correlativo_revison = $correspondencia_revision->getNCorrespondenciaEmisor();
                $correlativo_revison = str_replace($siglas_unidad, "SIGLAS_DELETE", $correlativo_revison);
                eval('list($'.$nomenclatura.') = explode("-",$correlativo_revison);');
                
                if(isset($Año)){
                    $correlativo_final = '<td align="center"><font size="9">'.$Año.'</font><font size="5"><br/>Año</font></td>';
                }
                
                if(isset($Mes)){
                    $correlativo_final .= '<td align="center"><font size="9">'.$Mes.'</font><font size="5"><br/>Agenda</font></td>';
                } else {
                    $correlativo_final .= '<td align="center"><font size="9">'.date('M', strtotime($correspondencia_revision->getFEnvio())).'</font><font size="5"><br/>Agenda</font></td>';
                }
                
                if(isset($Secuencia)){
                    $correlativo_final .= '<td align="center"><font size="9">'.$Secuencia.'</font><font size="5"><br/>N°</font></td>';
                }
                
                $num_agenda = '<table align="center"><tr>'.$correlativo_final.'</tr></table>';
            }
        } else {
            if ($correspondencia_punto->getFEnvio() == null) {
                $num_agenda = '<font size="9" align="center"><b style="color: red;">SIN PRESENTAR</b></font>';
            } else {
                $num_agenda = '<font size="9" align="center"><b style="color: red;">SIN REVISION</b></font>';
            }
        }
        // FIN CORRELATIVO PUNTO
        
        $adj= FALSE;
        if(count($anexo_a) > 0 || count($anexo_f) > 0)
            $adj= TRUE;
        
        $mes_array= Array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        
        
        
        $contenido1 = '<table width="525" border="0" cellpadding="4">
                        <tr>
                            <td colspan="5" width="100%" style="height: 45px" align="center">
                                <br/><font size="18">PUNTO DE INFORMACI&Oacute;N</font>
                            </td>
                        </tr>
                    </table>
                    <table width="525" cellpadding="2">
                        <tr align="left">
                            <td rowspan="2" width="17%">
                                <table  border="1" >
                                    <tr><td style="height: 50px"><font size="7" align="center">PUNTO DE INFORMACI&Oacute;N</font><br/>'. $num_agenda .'</td></tr>
                                </table>
                            </td>
                            <td rowspan="2" width="58%" colspan="3">
                                <table  border="1" cellpadding="4">
                                    <tr><td style="height: 50px"><font size="9">Presentante:<br/><b>'. strtoupper($solicitante) .'</b><br/> '. mb_strtoupper($solicitante_unidad, 'UTF-8') .'</font></td></tr>
                                </table>
                            </td>
                            <td width="25%">
                                <table  border="1" cellpadding="4">
                                    <tr><td><font size="9">Fecha: '. $f_presentacion .'</font></td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="25%">
                                <table  border="1" cellpadding="4">
                                    <tr><td><font size="9">Pag.: #$number_page$#</font></td></tr>
                                </table> 
                            </td>
                        </tr>
                    </table>';
       
       sfContext::getInstance()->getUser()->setAttribute('header_content', $contenido1);
       sfContext::getInstance()->getUser()->setAttribute('formato_id', $formato->get('tipo_formato_id'));
        
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetMargins(40, 220, 20);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//        $pdf->SetHeaderData('punto_informacion_pdf.png', 525, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//        $pdf->setFooterFont(array(PDF_FONT_NAMEs_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(20);
        $pdf->SetBarcode($correspondencia_punto->getNCorrespondenciaEmisor());
        
        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        // FIRMA DEL SOLICITANTE
        $presentante_sig= '';
        $firma_solicitante = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
        if($solicitante_firma=='S'){
            if($emisor_punto[0]->getProteccion() != '') {
                $firma_solicitante = '<tr><td align="vcenter" align="center"><font size="9">'.$solicitante_proteccion.'</font></td></tr>';
            }else {
                $firma_solicitante = '';
            }
            
            if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $solicitante_ci .".jpg")) {
                    $presentante_sig= "/images/firma_digital/". $solicitante_ci.'.jpg';
            }
        }
        
        // FIRMA MAXIMA AUTORIDAD
        $firmante_sig= '';
        $firma_maxima = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
        if($correspondencia_revision){
            if ($correspondencia_revision->getFEnvio() == null) {
                $firma_maxima = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
            } else {
                $firma_maxima = $firmante_final_proteccion;
                if($firmante_final_proteccion != '') {
                    $firma_maxima = '<tr><td align="vcenter" align="center"><font size="9">'.$firmante_final_proteccion.'</font></td></tr>';
                }else {
                    $firma_maxima = '';
                }
                
                if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $firmante_ci .".jpg")) {
                        $firmante_sig= "/images/firma_digital/". $firmante_ci.'.jpg';
                }
            }
        }
        
       $contenido2= '<table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404; color: #FFFFFF;">
                    <td colspan="5">ASUNTO</td>
                </tr>
                <tr style="height: 25px">
                    <td colspan="5">'. $valores_punto["punto_informacion_asunto"] .'</td>
                </tr>
                <tr style="background-color: #B40404; color: #FFFFFF;">
                    <td colspan="5">SINTES&Iacute;S</td>
                </tr>
                <tr style="height: 30px">
                    <td colspan="5">'. $valores_punto["punto_informacion_sintesis"] .'</td>
                </tr>
                <tr style="background-color: #B40404; color: #FFFFFF;">
                    <td colspan="5">RECOMENDACIONES</td>
                </tr>
                <tr>
                    <td colspan="5" style="height: 30px">'. $valores_punto["punto_informacion_recomendaciones"] .'</td>
                </tr>
                <tr style="background-color: #B40404; color: #FFFFFF;">
                    <td colspan="5">OBSERVACIONES DE LA MAXIMA AUTORIDAD</td>
                </tr>
                <tr>
                    <td colspan="5" style="height: 70px" bgcolor="'.$bgcolor_revision.'">'. $valores_revision['revision_punto_informacion_observaciones'] .'</td>
                </tr>
                <tr>
                    <td colspan="3" width="50%" style="height: 70px">
                        <table>
                            <tr>
                                <td align="center"><font size="10">FIRMA Y SELLO</font><font size="5"><br/>PRESENTANTE</font></td>
                            </tr>';

            if(!$firma_solicitante == '') {
                $contenido2 .= $firma_solicitante;
            }

             if(!$presentante_sig == '') {
                 $contenido2 .= '<tr>
                             <td style="text-align: center">
                                 <img src="'.$presentante_sig.'" alt="signature" width="150" height="70" border="0" />
                             </td>
                         </tr>';
             }

             $contenido2 .= '<tr>
                                <td align="center"><font size="10">'. strtoupper($solicitante) .'</font></td>
                            </tr>
                            <tr>
                                <td align="center"><font size="10">'. strtoupper($solicitante_cargo_tipo) .'</font></td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="2" width="50%" style="height: 70px">
                        <table>
                            <tr>
                                <td align="center"><font size="10">FIRMA Y SELLO</font><font size="5"><br/>MAXIMA AUTORIDAD</font></td>
                            </tr>';

            if(!$firma_maxima == '') {
               $contenido2 .= $firma_maxima;
            }

            if(!$firmante_sig == '') {
                $contenido2 .= '<tr>
                            <td style="text-align: center">
                                <img src="'.$firmante_sig.'" alt="signature" width="150" height="70" border="0" />
                            </td>
                        </tr>';
            }

            $contenido2 .= '<tr>
                                <td align="center"><font size="10">'. strtoupper($firmante) .'</font></td>
                            </tr>
                            <tr>
                                <td align="center"><font size="10">'. strtoupper($firmante_cargo_tipo) .'</font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="background-color: '.$bgcolor_revision_alterna.'; color: '.$letracolor_revision.';">
                        FECHA DE REVISI&Oacute;N: '.$f_decision.'
                    </td>
                </tr>
           </table>';
       
       $contenido3 = '<table width="525" border="1" cellpadding="4">'.$funcionarios_vb_str.'
                <tr>
                    <td width="50%">
                        <font size="8">Prepara:</font><br/><font size="9">'. (!empty($datos_func_preparado) ? $datos_func_preparado[0]['primer_nombre'].' '.$datos_func_preparado[0]['primer_apellido'] : '') .'</font>
                    </td>
                    <td width="50%">
                        <table>
                            <tr>
                                <td colspan="2"><font size="8">Anexos:</font></td>
                            </tr>
                            <tr>
                                <td align="rigth"><font size="9">S&iacute;&nbsp;</font><img width="10" src="/images/icon/'. (($adj)? "check_lleno" : "check_vacio") .'.png"/>&nbsp;&nbsp;&nbsp;</td>
                                <td align="center"><font size="9">No&nbsp;</font><img width="10" src="/images/icon/'. ((!$adj)? "check_lleno" : "check_vacio") .'.png"/></td>
                            </tr>
                        </table>
                    </td>
                </tr></table>';

        $tbl = <<<EOD
<table width="450" "center">
    <tr>
        <td width="450">
            $contenido2
        </td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td width="450">
            $contenido3
        </td>
    </tr>
</table>

EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output($correspondencia_punto->getNCorrespondenciaEmisor().'__'.date('d-m-Y').'.pdf');
        return sfView::NONE;
        
    }
}

class MYPDF extends TCPDF {

            //Page header
            public function Header() {
                // Logo
//                $image_file = K_PATH_IMAGES.'gobierno.png';
//                $this->Image($image_file, 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                // Set font
                $this->SetFont('helvetica', '', 12);
                // Title
                $contenido1 = sfContext::getInstance()->getUser()->getAttribute('header_content');
                $formato_id = sfContext::getInstance()->getUser()->getAttribute('formato_id');
                
                $contenido1= str_replace('#$number_page$#', $this->getAliasNumPage().' de '.$this->getAliasNbPages(), $contenido1);
                
//                Seleccion de membrete
                $head_var= 'gob_pdf.png';
                //HEADER INDEPENDIENTE
                $tipo_formato= Doctrine::getTable('Correspondencia_TipoFormato')->find($formato_id);
                if($tipo_formato) {
                    if(file_exists('images/organismo/pdf/'.$tipo_formato->getClasse().'_gob_pdf.png')) {
                        $head_var= $tipo_formato->getClasse().'_'.$head_var;
                    }
                }
                
                $tbl = '<table width="450" "center">
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="450">
                                    <img width="500" src="/images/organismo/pdf/'.$head_var.'" />
                                </td>
                            </tr>
                            <tr>
                                <td width="450">'.$contenido1.'</td>
                            </tr>
                        </table>';
                
        $this->writeHTML($tbl, true, false, false, false, '');
    }
}
