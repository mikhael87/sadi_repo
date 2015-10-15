<?php

class formatoPuntoCuenta {
    public function executeValidar($formulario, &$messages, &$formato) {

        // campo_uno = Asunto
        if (!$formulario["punto_cuenta_asunto"]) {
            $messages = array_merge($messages, array("punto_cuenta_asunto" => "Campo requerido"));
        } else {
            $formato->setCampoUno($formulario["punto_cuenta_asunto"]);
        }
        
        // campo_dos = Sintesis
        if (!$formulario["punto_cuenta_sintesis"]) {
            $messages = array_merge($messages, array("punto_cuenta_sintesis" => "Campo requerido"));
        } else {
            $formato->setCampoDos($formulario["punto_cuenta_sintesis"]);
        }
        
        // campo_tres = Recomendaciones
        if (!$formulario["punto_cuenta_recomendaciones"]) {
            $messages = array_merge($messages, array("punto_cuenta_recomendaciones" => "Campo requerido"));
        } else {
            $formato->setCampoTres($formulario["punto_cuenta_recomendaciones"]);
        }
        
        if ($formulario["punto_cuenta_form_partida"]=='S') {
            // campo_cuatro = Partida Presupuestaria
            if (!$formulario["punto_cuenta_partida"]) {
                $messages = array_merge($messages, array("punto_cuenta_partida" => "Campo requerido"));
            } else {
                $formato->setCampoCuatro($formulario["punto_cuenta_partida"]);
            }

            // campo_cinco = Monto a Ejecutar
            if (!$formulario["punto_cuenta_partida"]) {
                $messages = array_merge($messages, array("punto_cuenta_monto" => "Campo requerido"));
            } else {
                $formato->setCampoCinco($formulario["punto_cuenta_monto"]);
            }
        }
    }
    
    public function executeTraer($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["punto_cuenta_asunto"] = $datos["campo_uno"];
        $formulario["punto_cuenta_sintesis"] = $datos["campo_dos"];
        $formulario["punto_cuenta_recomendaciones"] = $datos["campo_tres"];
        
        if($datos["campo_cuatro"]){
            $formulario["punto_cuenta_form_partida"] = 'S';
            
            $formulario["punto_cuenta_partida"] = $datos["campo_cuatro"];
            $formulario["punto_cuenta_monto"] = $datos["campo_cinco"];
        } else {
            $formulario["punto_cuenta_form_partida"] = 'N';
            
            $formulario["punto_cuenta_partida"] = '';
            $formulario["punto_cuenta_monto"] = '';
        }

        return $formulario;
    }
    
    public function executeTraerHijo($datos) {
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["revision_punto_cuenta_decision"] = $datos["campo_uno"];
        $formulario["revision_punto_cuenta_observaciones"] = $datos["campo_dos"];
        $formulario["revision_punto_cuenta_medios"] = $datos["campo_tres"];
        $formulario["revision_punto_cuenta_fecha"] = $datos["campo_cuatro"];

        return $formulario;
    }

    public function executePdf($pdf, $formato, $correspondencia, $emisor, $receptores) {
        
        $correspondencia_punto = $correspondencia;
        $formato_punto = $formato;
        $emisor_punto = $emisor;
        $receptor_punto = $receptores;
        $valores_punto = $this->executeTraer($formato_punto);
        
        $correspondencia_revision = Doctrine::getTable('Correspondencia_Correspondencia')->padreNotMe($correspondencia->getId());
        
        if(count($correspondencia_revision) > 0){
            $formato_revision = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_revision[0]->getId());
            $emisor_revision=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_revision[0]->getId(), TRUE);
            $receptor_revision=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_revision[0]->getId());
            $valores_revision = $this->executeTraerHijo($formato_revision);
            
            $letracolor_revision = '#FFFFFF';
            $bgcolor_revision = '';
            $bgcolor_revision_alterna = '#B40404';
            $height_observaciones = '0';
        } else {
            $letracolor_revision = '#000000';
            $bgcolor_revision = '#bfbfbf';
            $bgcolor_revision_alterna = '#bfbfbf';
            $valores_revision["revision_punto_cuenta_decision"]='';
            $valores_revision["revision_punto_cuenta_observaciones"]='';
            $height_observaciones = '70';
        }

        if(count($correspondencia_revision) > 0){
            if ($correspondencia_revision[0]->getFEnvio() == null) {
                $f_decision = '<b style="color: red;">SIN DECISION</b>';
                $cadena_publicacion = '<b style="color: red;">SIN DECISION</b>';
            } else {
                $f_decision = date('d/m/Y h:i A', strtotime($correspondencia_revision[0]->getFEnvio()));
                
                if($valores_revision["revision_punto_cuenta_medios"]=='' && $valores_revision["revision_punto_cuenta_fecha"]==''){
                    $cadena_publicacion = '<b align="center">NO PUBLICAR</b>';
                } else {
                    if($valores_revision["revision_punto_cuenta_fecha"]=='inmediato'){
                       $cadena_publicacion = 'PUBLICAR DE INMEDIATO'; 
                    } else {
                       $cadena_publicacion = 'PUBLICAR A PARTIR DE LA FECHA: '.date('d/m/Y', strtotime($valores_revision["revision_punto_cuenta_fecha"]));  
                    }
                    
                    $cadena_publicacion .= '<br/><br/><b>MEDIOS DE COMUNICACION:</b><br/>';
                    
                    $medios= explode('#', $valores_revision['revision_punto_cuenta_medios']);
                    
                    $cadena_publicacion .= '<table>
                            <tr align="center">
                                <td>PORTAL WEB<img width="10" src="/images/icon/'. ((in_array('web', $medios)) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;&nbsp;</td>
                                <td>INTRANET<img width="10" src="/images/icon/'. ((in_array('intranet', $medios)) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;&nbsp;</td>
                                <td>RADIO<img width="10" src="/images/icon/'. ((in_array('radio', $medios)) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;&nbsp;</td>
                                <td>T.V.<img width="10" src="/images/icon/'. ((in_array('tv', $medios)) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;&nbsp;</td>
                                <td>TWITTER<img width="10" src="/images/icon/'. ((in_array('twitter', $medios)) ? 'check_lleno.png' : 'check_vacio.png') .'"/>&nbsp;&nbsp;</td>
                            </tr>
                        </table>';
                }
                
                $height_observaciones = '0';
                if($valores_revision["revision_punto_cuenta_observaciones"]==''){
                    $valores_revision["revision_punto_cuenta_observaciones"]= '<b align="center">SIN OBSERVACIONES</b>';
                }
            }
        } else {
            if ($correspondencia_punto->getFEnvio() == null) {
                $f_decision = '<b style="color: red;">SIN PRESENTAR</b>';
                $cadena_publicacion = '<b style="color: red;">SIN PRESENTAR</b>';
            } else {
                $f_decision = '<b style="color: red;">SIN REVISION</b>';
                $cadena_publicacion = '<b style="color: red;">SIN REVISION</b>';
            }
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
            $funcionarios_vb_str= '<tr>'; $en= FALSE;
            foreach($vbs as $vbs_funcionario) {
                if($vbs_funcionario->getStatus()=='V' || $vbs_funcionario->getStatus()=='E'){
                    $datos_func_vb= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionarioCargoHistorico($vbs_funcionario->getFuncionarioId(),$vbs_funcionario->getFuncionarioCargoId());
                    foreach($datos_func_vb as $datos_func) {
                        //EL WIDTH ES CALCULADO DEPENDIENDO DE LA CANTIDAD DE FUNCIONARIOS 
                        if($vbs_funcionario->getStatus()=='V'){
                            $funcionarios_vb_str .= '<td width="';
                             //AGREGAR FIRMA DIGITAL
                            if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $datos_func->getCi() .".jpg")) {
                                $vb_sig= "/images/firma_digital/". $datos_func->getCi().'.jpg';

                                $resultado_vbs = '<img src="'.$vb_sig.'" alt="signature" width="150" height="70" border="0" />';
                            }
//                            $resultado_vbs = '<img src="/images/firmas/'.$datos_func->getCi().'.jpg"/><br/><font size="7">wdfksjdhfsdn827d8@52^%3@$$h23d!!sdf42r2dwefdwsfcsdfsdfsdf</font>';
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
                        $funcionarios_vb_str .= '<tr><td><font size="8">Corforme:</font></td></tr><tr><td></td></tr><tr><td align="vcenter" align="center">'.$resultado_vbs.'</td></tr><tr><td></td></tr>';
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
                            $en= TRUE;
                            $funcionarios_vb_str .= '</tr>';
                            break;
                        }elseif($cont_vb != $turn && count($vbs) == $cont_vb) {
                            $en= TRUE;
                            $funcionarios_vb_str .= '</tr>';
                            break;
                        }
                    }
                    $cont_vb++;
                }
            }
        }
        if(!$en)
            $funcionarios_vb_str .= '</tr>';

        //Preparado por
        $datos_func_preparado= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionario($correspondencia_punto->getIdCreate());
        
        //Tiene adjuntos o no
        $anexo_a= Doctrine::getTable('Correspondencia_AnexoArchivo')->findByCorrespondenciaId($correspondencia_punto->getId());
        $anexo_f= Doctrine::getTable('Correspondencia_AnexoFisico')->findByCorrespondenciaId($correspondencia_punto->getId());
        
        // CORRELATIVO DE PUNTO
        if(count($correspondencia_revision) > 0){
            if ($correspondencia_revision[0]->getFEnvio() == null) {
                $num_agenda = '<font size="9" align="center"><b style="color: red;">SIN AGENDAR</b></font>';
            } else {
                
                $unidad_correlativo_id = $correspondencia_revision[0]->getUnidadCorrelativoId();
                $nomenclador_revision = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($unidad_correlativo_id);
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($nomenclador_revision->getUnidadId());
                $siglas_unidad = str_replace("/", "-", $unidad->getSiglas());
                $nomenclatura = str_replace("-", ",$", $nomenclador_revision->getNomenclador());
                
                $correlativo_revison = $correspondencia_revision[0]->getNCorrespondenciaEmisor();
                $correlativo_revison = str_replace($siglas_unidad, "SIGLAS_DELETE", $correlativo_revison);
                eval('list($'.$nomenclatura.') = explode("-",$correlativo_revison);');
                
                if(isset($Año)){
                    $correlativo_final = '<td align="center"><font size="9">'.$Año.'</font><font size="5"><br/>Año</font></td>';
                }
                
                if(isset($Mes)){
                    $correlativo_final .= '<td align="center"><font size="9">'.$Mes.'</font><font size="5"><br/>Agenda</font></td>';
                } else {
                    $correlativo_final .= '<td align="center"><font size="9">'.date('M', strtotime($correspondencia_revision[0]->getFEnvio())).'</font><font size="5"><br/>Agenda</font></td>';
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
                                <br/><font size="18">PUNTO DE CUENTA</font>
                            </td>
                        </tr>
                    </table>
                    <table width="525" cellpadding="2">
                        <tr align="left">
                            <td rowspan="2" width="17%">
                                <table  border="1" >
                                    <tr><td style="height: 50px"><font size="7" align="center">PUNTO DE CUENTA</font><br/>'. $num_agenda .'</td></tr>
                                </table>
                            </td>
                            <td rowspan="2" width="58%" colspan="3">
                                <table  border="1" cellpadding="4">
                                    <tr><td style="height: 50px"><font size="9">Presentante:<br/><b>'. strtoupper($emisor_punto[0]->getpn().' '.$emisor_punto[0]->getpa().' '.$emisor_punto[0]->getsa()) .'</b><br/> '. mb_strtoupper($emisor_punto[0]->getUnombre(), 'UTF-8') .'</font></td></tr>
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
        $firma_presentante = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
        if($emisor_punto[0]->getFirma()=='S'){
            if($emisor_punto[0]->getProteccion() != '') {
                $firma_presentante = '<tr><td align="vcenter" align="center"><font size="9">'.$emisor_punto[0]->getProteccion().'</font></td></tr>';
            }else {
                $firma_presentante = '';
            }
            
            if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $emisor_punto[0]->getCi() .".jpg")) {
                    $presentante_sig= "/images/firma_digital/". $emisor_punto[0]->getCi().'.jpg';
            }
//      $firma_presentante = '<img src="/images/firmas/'.$emisor_punto[0]->getCi().'.jpg"/><br/><font size="7">aa283hh2@#$2923!@n943f34n3indk^:43mf-4.deefknksdjfooofsdf</font>';
        }

        // FIRMA MAXIMA AUTORIDAD
        $firmante_sig= '';
        if(count($correspondencia_revision) > 0){
            if ($correspondencia_revision[0]->getFEnvio() == null) {
                $firma_revision = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
                
                if (file_exists(sfConfig::get("sf_root_dir")."/web/images/firma_digital/". $receptor_punto[0]->getCi() .".jpg")) {
                        $firmante_sig= "/images/firma_digital/". $receptor_punto[0]->getCi().'.jpg';
                }
            } else {
                if($emisor_punto[0]->getProteccion() != '') {
                    $firma_revision = '<tr><td align="vcenter" align="center"><font size="9">'.$emisor_revision[0]->getProteccion().'</font></td></tr>';
                    
                }else {
                    $firma_revision = '';
                }
//               $firma_revision = '<img src="/images/firmas/'.$emisor_revision[0]->getCi().'.jpg"/><br/><font size="7">wdfksjdhfsdn827d8@52^%3@$$h23d!!sdf42r2dwefdwsfcsdfsdfsdf</font>';
            }
        } else {
            $firma_revision = '<tr><td align="vcenter" align="center"><font size="9"><b style="color: red;">NO HA FIRMADO</b></font></td></tr>';
        }

       $contenido2= '<table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">ASUNTO</font></td>
                </tr>
                <tr style="height: 25px">
                    <td colspan="5">'. $valores_punto["punto_cuenta_asunto"] .'</td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">SINTES&Iacute;S</font></td>
                </tr>
                <tr style="height: 30px">
                    <td colspan="5">'. $valores_punto["punto_cuenta_sintesis"] .'</td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">RECOMENDACIONES</font></td>
                </tr>
                <tr>
                    <td colspan="5" style="height: 30px">'. $valores_punto["punto_cuenta_recomendaciones"] .'</td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">DECISI&Oacute;N</font></td>
                </tr>
                <tr>
                    <td colspan="5" bgcolor="'.$bgcolor_revision.'">
                        <table>
                            <tr align="center">
                                <td>APROBADO <img width="10" src="/images/icon/'. (($valores_revision['revision_punto_cuenta_decision']== 'Aprobado') ? 'check_lleno.png' : 'check_vacio.png') .'"/></td>
                                <td>NEGADO <img width="10" src="/images/icon/'. (($valores_revision['revision_punto_cuenta_decision']== 'Negado') ? 'check_lleno.png' : 'check_vacio.png') .'"/></td>
                                <td>VISTO <img width="10" src="/images/icon/'. (($valores_revision['revision_punto_cuenta_decision']== 'Visto') ? 'check_lleno.png' : 'check_vacio.png') .'"/></td>
                                <td>DIFERIDO <img width="10" src="/images/icon/'. (($valores_revision['revision_punto_cuenta_decision']== 'Diferido') ? 'check_lleno.png' : 'check_vacio.png') .'"/></td>
                                <td>OTRO <img width="10" src="/images/icon/'. (($valores_revision['revision_punto_cuenta_decision']== 'Otro') ? 'check_lleno.png' : 'check_vacio.png') .'"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">OBSERVACIONES DE LA MAXIMA AUTORIDAD</font></td>
                </tr>
                <tr>
                    <td colspan="5" style="height: '.$height_observaciones.'px;" bgcolor="'.$bgcolor_revision.'">'. $valores_revision['revision_punto_cuenta_observaciones'] .'</td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr style="background-color: #B40404;">
                    <td colspan="5"><font style="color: #FFFFFF;">TRATAMIENTO COMUNICACIONAL</font></td>
                </tr>
                <tr>
                    <td colspan="5" bgcolor="'.$bgcolor_revision.'">'. $cadena_publicacion .'</td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr>
                    <td colspan="3" width="50%" style="height: 70px">
                        <table>
                            <tr>
                                <td align="center"><font size="10">FIRMA Y SELLO</font><font size="5"><br/>PRESENTANTE</font></td>
                            </tr>';

       if($firma_presentante != '') {
            $contenido2 .= $firma_presentante;
        }
        
        if(!$presentante_sig == '') {
            $contenido2 .= '<tr>
                        <td style="text-align: center">
                            <img src="'.$presentante_sig.'" alt="signature" width="150" height="70" border="0" />
                        </td>
                    </tr>';
        }
        
                            
        $contenido2 .= '<tr>
                                <td align="center"><font size="10">'. strtoupper($emisor_punto[0]->getpn().' '.$emisor_punto[0]->getpa().' '.$emisor_punto[0]->getsa()) .'</font></td>
                            </tr>
                            <tr>
                                <td align="center"><font size="10">'. strtoupper($emisor_punto[0]->getCtnombre()) .'</font></td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="2" width="50%" style="height: 70px">
                        <table>
                            <tr>
                                <td align="center"><font size="10">FIRMA Y SELLO</font><font size="5"><br/>MAXIMA AUTORIDAD</font></td>
                            </tr>';
        
        if(!$firma_revision == '') {
           $contenido2 .= $firma_revision;
        }
        
        if(!$firmante_sig == '') {
            $contenido2 .= '<tr>
                        <td style="text-align: center">
                            <img src="'.$firmante_sig.'" alt="signature" width="150" height="70" border="0" />
                        </td>
                    </tr>';
        }

        $contenido2 .= '<tr>
                                <td align="center"><font size="10">'. strtoupper($receptor_punto[0]->getPn().' '.$receptor_punto[0]->getPa()) .'</font></td>
                            </tr>
                            <tr>
                                <td align="center"><font size="10">'. strtoupper($receptor_punto[0]->getCtnombre()) .'</font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="525" border="1" cellpadding="4">
                <tr>
                    <td colspan="5" style="background-color: '.$bgcolor_revision_alterna.'; color: '.$letracolor_revision.';">
                        FECHA DE DECISI&Oacute;N: '.$f_decision.'
                    </td>
                </tr>
           </table>';
       
       if($valores_punto["punto_cuenta_form_partida"]=='S'){
        $partida_presupuestaria = $valores_punto["punto_cuenta_partida"];
        $monto= str_replace(',', '.', $valores_punto['punto_cuenta_monto']);
        if(is_numeric($monto))
            $monto_solicitado = number_format($monto, 2, ',', '.').' Bs.';
        else
            $monto_solicitado = '';
        $bgcolor_partida="";
       } else {
        $partida_presupuestaria = 'NO APLICA';
        $monto_solicitado = 'NO APLICA';
        $bgcolor_partida="#bfbfbf";
       }
       
       $contenido3 = '<table width="525" border="1" cellpadding="4">
                <tr>
                    <td width="25%" bgcolor="'.$bgcolor_partida.'" style="height: 35px"><font size="8">Partida presupuestaria:</font><br/><font size="9"><b>'.$partida_presupuestaria.'</b></font></td>
                    <td width="25%" bgcolor="'.$bgcolor_partida.'"><font size="8">Monto Solicitado:</font><br/><font size="9"><b>'.$monto_solicitado.'</b></font></td>
                    <td width="25%">
                        <font size="8">Prepara:</font><br/><font size="9">'. (!empty($datos_func_preparado) ? $datos_func_preparado[0]['primer_nombre'].' '.$datos_func_preparado[0]['primer_apellido'] : '') .'</font>
                    </td>
                    <td width="25%">
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
                </tr>'.$funcionarios_vb_str.'</table>';

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
    

    public function executeAdditionalCrear($correspondencia_id){}
    
    public function executeAdditionalEnviar($correspondencia_id){}
    
    public function executeAdditionalAnular($correspondencia_id){}
    
    public function executeAdditionalDevolver($correspondencia_id){}
    
    public function executeAdditionalEmisor($correspondencia_id) {}
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