<?php

class formatoVacaciones {
    public function executeValidar($formulario, &$messages, &$formato) {
        // campo_uno = solicitante
        $formato->setCampoUno(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));
        $formato->setCampoOnce(sfContext::getInstance()->getUser()->getAttribute('funcionario_unidad_id'));
        $cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findOneByFuncionarioIdAndStatus(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'),'A');
        $formato->setCampoDoce($cargo->getId());
        
        // campo_dos = dias solicitados
        if (!$formulario["vacaciones_dias_solicitados"]) {
            $messages = array_merge($messages, array("vacaciones_dias_solicitados" => "Ingrese la cantidad días deseados."));
        } else {
            $formato->setCampoDos($formulario["vacaciones_dias_solicitados"]);
        }

        // campo_tres = fecha de inicio
        if (!$formulario["vacaciones_f_inicio"]) {
            $messages = array_merge($messages, array("vacaciones_f_inicio" => "Por favor seleccione la fecha de inicio de las vacaciones."));
        } else {
            $formato->setCampoTres($formulario["vacaciones_f_inicio"]);
        }
        
        $dias = $formulario["vacaciones_dias_solicitados"];
        $fecha_retorno = strtotime($formulario["vacaciones_f_inicio"]);
        $laborables = 0;
        $no_laborables = 0;
        $fines_semana = 0;
        
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

        for ($i = 0; $i < $dias; $i++) {
            $fecha_retorno = strtotime('+1 day ' . date('Y-m-d', $fecha_retorno));

            if (date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 6 && date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 7) {
                if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                    $laborables++;

                    if ($i == $dias - 2) {
                        $fecha_final = date('Y-m-d', $fecha_retorno);
                    }
                } else {
                    $no_laborables++;
                    $i--;                    
                }
            } else {
                $fines_semana++;
                $i--;
            }
        }

        $fecha_retorno = date('Y-m-d', $fecha_retorno);
        
        if ($dias == 1)
            $fecha_final = date('Y-m-d', strtotime($fecha_inicio));

        $dias_totales = $laborables + $fines_semana + $no_laborables;
        
        $formato->setCampoCuatro($fecha_final);
        $formato->setCampoCinco($fecha_retorno);
        $formato->setCampoSeis($dias_totales);
        $formato->setCampoSiete($laborables);
        $formato->setCampoOcho($fines_semana);    
        $formato->setCampoNueve($no_laborables);
        
        // campo_nueve = observaciones
        if ($formulario["vacaciones_observacion"]) {
            $formato->setCampoDiez($formulario["vacaciones_observacion"]);
        }
        
        $periodos_usados=null;
        if($formulario["vacaciones_dias_solicitados"]) {
            $periodos_vacacionales = Doctrine_Query::create()
                                    ->select('v.*')
                                    ->from('Rrhh_Vacaciones v')
                                    ->where('v.funcionario_id = ?', sfContext::getInstance()->getUser()->getAttribute('funcionario_id'))
                                    ->andWhere('v.dias_disfrute_pendientes > 0')
                                    ->orderBy('v.periodo_vacacional')
                                    ->execute();

            $dias_periodo = array();
            $dias_usados = array();
            $dias_restantes = array();
            $count_dias = 1;
            $hasta_dia = 0;
            $periodo_vacacional_anterior=0;


            foreach ($periodos_vacacionales as $periodo_vacacional) {

                $hasta_dia += $periodo_vacacional->getDiasDisfrutePendientes();
                $restantes = $periodo_vacacional->getDiasDisfrutePendientes()-1;

                $dias_contados=1;

                while($dias_contados<=$periodo_vacacional->getDiasDisfrutePendientes()){
                    $dias_periodo[$count_dias] = $periodo_vacacional->getId();
                    $dias_usados[$count_dias] = $dias_contados;
                    $dias_restantes[$count_dias] = $restantes;
                    $predecesores[$count_dias] = $periodo_vacacional_anterior;
                    $count_dias++;
                    $dias_contados++;
                    $restantes--;
                }       

                $periodo_vacacional_anterior = $periodo_vacacional->getId();
            }

            for($i=1;$i<=$formulario["vacaciones_dias_solicitados"];$i++){
                $periodos_usados[$dias_periodo[$i]] = array($dias_usados[$i],$dias_restantes[$i]);
            }
        }
        
        if ($periodos_usados==null) {
            $messages = array_merge($messages, array("vacaciones_dias_solicitados" => "Ingrese la cantidad días deseados."));
        } else {
            $formato->setCampoTrece(sfYAML::dump($periodos_usados));
        }
    }
    
    public function executeAdditionalEmisor($correspondencia_id) {}
    
    public function executeTraer($datos) {
        $funcionario=Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario((int)$datos["campo_uno"]);
        
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["vacaciones_solicitante_id"] = $datos["campo_uno"];
        $formulario["vacaciones_solicitante_unidad_id"] = $datos["campo_once"];
        $formulario["vacaciones_solicitante_cargo_id"] = $datos["campo_doce"];
        $formulario["vacaciones_solicitante"] = $funcionario[0]['ctnombre'].' / '.$funcionario[0]['primer_nombre'].' '.$funcionario[0]['primer_apellido'].' - CI: '.$funcionario[0]['ci'];
        
        $formulario["vacaciones_dias_solicitados"] = $datos["campo_dos"];
        
        $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                   '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
        
        $formulario["vacaciones_f_inicio"] = $datos["campo_tres"];
        $formulario["vacaciones_f_inicio_show"] = date('d',strtotime($datos["campo_tres"])).' '.$mes[date('m',strtotime($datos["campo_tres"]))].' '.date('Y',strtotime($datos["campo_tres"]));
        $formulario["vacaciones_f_final"] = $datos["campo_cuatro"];
        $formulario["vacaciones_f_final_show"] = date('d',strtotime($datos["campo_cuatro"])).' '.$mes[date('m',strtotime($datos["campo_cuatro"]))].' '.date('Y',strtotime($datos["campo_cuatro"]));
        $formulario["vacaciones_f_retorno"] = $datos["campo_cinco"];
        $formulario["vacaciones_f_retorno_show"] = date('d',strtotime($datos["campo_cinco"])).' '.$mes[date('m',strtotime($datos["campo_cinco"]))].' '.date('Y',strtotime($datos["campo_cinco"]));
        $formulario["vacaciones_dias_totales"] = $datos["campo_seis"];
        $formulario["vacaciones_laborables"] = $datos["campo_siete"];
        $formulario["vacaciones_fines_semana"] = $datos["campo_ocho"];
        $formulario["vacaciones_no_laborables"] = $datos["campo_nueve"];
        $formulario["vacaciones_observacion"] = $datos["campo_diez"];
        $formulario["vacaciones_periodo_vacacional"] = sfYaml::load($datos["campo_trece"]);
        
        $formulario["vacaciones_periodo_vacacional_show"] = '';
        foreach ($formulario["vacaciones_periodo_vacacional"] as $periodos_id => $valores) {
            $periodo_vacacional = Doctrine::getTable('Rrhh_Vacaciones')->find($periodos_id);
            $formulario["vacaciones_periodo_vacacional_show"] .= $periodo_vacacional->getPeriodoVacacional().' ('.$valores[0].' dias), ';   
        }
        $formulario["vacaciones_periodo_vacacional_show"] .= '#';
        $formulario["vacaciones_periodo_vacacional_show"] = str_replace('), #', ')', $formulario["vacaciones_periodo_vacacional_show"]);

        return $formulario;
    }
    
    public function executeAdditionalCrear($correspondencia_id)
    {
        $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);
        $valores = $this->executeTraer($formatos[0]);
        
        
        $fecha_retorno = $valores["vacaciones_f_inicio"];
        foreach ($valores["vacaciones_periodo_vacacional"] as $periodos_id => $dias_seteados) {
            
            
            
            $dias = $dias_seteados[0];
            $fecha_inicio = $fecha_retorno;
            $fecha_retorno = strtotime($fecha_retorno);
            $laborables = 0;
            $no_laborables = 0;
            $fines_semana = 0;

            $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
            $dias_no_laborables = $sf_varios['dias_no_laborables'];

            for ($i = 0; $i < $dias; $i++) {
                $fecha_retorno = strtotime('+1 day ' . date('Y-m-d', $fecha_retorno));

                if (date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 6 && date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 7) {
                    if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                        $laborables++;

                        if ($i == $dias - 2) {
                            $fecha_final = date('Y-m-d', $fecha_retorno);
                        }
                    } else {
                        $no_laborables++;
                        $i--;                    
                    }
                } else {
                    $fines_semana++;
                    $i--;
                }
            }

            $fecha_retorno = date('Y-m-d', $fecha_retorno);

            if ($dias == 1)
                $fecha_final = date('Y-m-d', strtotime($fecha_inicio));

            $dias_totales = $laborables + $fines_semana + $no_laborables;



            $vacaciones_disfrutadas = new Rrhh_VacacionesDisfrutadas();
            $vacaciones_disfrutadas->setVacacionesId($periodos_id);
            $vacaciones_disfrutadas->setCorrespondenciaSolicitudId($correspondencia_id);
            $vacaciones_disfrutadas->setFInicioDisfrute($fecha_inicio);
            $vacaciones_disfrutadas->setFFinDisfrute($fecha_final);
            $vacaciones_disfrutadas->setFRetornoDisfrute($fecha_retorno);
            $vacaciones_disfrutadas->setDiasSolicitados($dias);                   
            $vacaciones_disfrutadas->setDiasDisfruteHabiles($laborables);
            $vacaciones_disfrutadas->setDiasDisfruteFinSemana($fines_semana);       
            $vacaciones_disfrutadas->setDiasDisfruteNoLaborales($no_laborables);
            $vacaciones_disfrutadas->setDiasDisfruteContinuo($dias_totales);   
            
            $vacaciones_disfrutadas->setObservacionesDescritas($valores["vacaciones_observacion"]);
            $vacaciones_disfrutadas->setObservacionesAutomaticas('...');
            $vacaciones_disfrutadas->setDiasDisfruteEjecutados(0);
            $vacaciones_disfrutadas->setDiasPendientes($dias);
            
            $vacaciones_disfrutadas->setStatus('I'); 
            //ESTATUS I CORRESPONDENCIA CREADA QUE NO SE HA ENVIADO
            //ESTATUS E CORRESPONDENCIA ENVIADA YA LA APROBO EL JEFE
            //ESTATUS R CORRESPONDENCIA REMOVIDA O ANULADA
            //ESTATUS A APROBADA POR RRHH
            //ESTATUS N NEGADA POR RRHH
            //ESTATUS P PAUSADA TEMPORAL POR JEFE INMEDIATO
            //ESTATUS C VACACIONES CANCELADAS DEFINITIVAMENTE
            
            $vacaciones_disfrutadas->save();
            
            
            
            $vacacion = Doctrine::getTable('Rrhh_Vacaciones')->find($periodos_id); 
            $vacacion->setStatus('S');
            $vacacion->save();
        }
    }
    
    
    public function executeAdditionalEnviar($correspondencia_id)
    {
        $vacaciones_disfrutadas = Doctrine::getTable('Rrhh_VacacionesDisfrutadas')->findByCorrespondenciaSolicitudId($correspondencia_id);
        
        foreach ($vacaciones_disfrutadas as $vacacion_disfrutada) {
            $vacacion_disfrutada->setStatus('E');
            $vacacion_disfrutada->save();
        }
    }
    
    public function executeAdditionalAnular($correspondencia_id){}
    
    public function executeAdditionalDevolver($correspondencia_id){}
    
    public function executeCalculoRegreso($valores)
    {
      $fecha_inicio = $valores['f_i'];
      $dias = $valores['d'];
      
      $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                 '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
      
      $fecha_retorno = strtotime($fecha_inicio);
      $laborables=0;$fines_semana=0;$no_laborables=0;
      
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];
      
      for($i=0;$i<$dias;$i++)
      {
        $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));  
          
        if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
            if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                $laborables++;

                if ($i == $dias - 2) {
                    $fecha_final=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
                }
            } else {
                $no_laborables++;
                $i--;                    
            }
        }
        else
        {
           $fines_semana++;
           $i--;
        }
      }

       if($dias==1) { 
           $fecha_igual = strtotime($fecha_inicio); 
           $fecha_final=date('d',$fecha_igual).' '.$mes[date('m',$fecha_igual)].' '.date('Y',$fecha_igual); }
          
      $fecha_retorno_hidden = date('Y-m-d',$fecha_retorno);
      $fecha_retorno=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
      $dias_totales = $laborables + $fines_semana + $no_laborables;

      $cadena = "<table>
                    <tr><td>Fecha final</td><td>".$fecha_final."</td></tr>
                    <tr><td>Fecha retorno</td><td>".$fecha_retorno."</td></tr>
                    <tr><td>dias de disfrute continuos</td><td>".$dias_totales."</td></tr>
                    <tr><td>dias de disfrute habiles</td><td>".$laborables."</td></tr>
                    <tr><td>dias de disfrute fines de semana</td><td>".$fines_semana."</td></tr>
                    <tr><td>dias de disfrute no laborables</td><td>".$no_laborables."</td></tr>
                </table>";
      
      return $cadena;
  }
  
  
   
    public function executePdf($pdf, $formato, $correspondencia, $emisor, $receptores) {
        
        $head_var= 'gob_pdf.png';
        //HEADER INDEPENDIENTE
        $tipo_formato= Doctrine::getTable('Correspondencia_TipoFormato')->find($formato->get('tipo_formato_id'));
        if($tipo_formato) {
            if(file_exists('images/organismo/pdf/'.$tipo_formato->getClasse().'_gob_pdf.png')) {
                $head_var= $tipo_formato->getClasse().'_'.$head_var;
            }
        }
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(80, 60, 80);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData($head_var, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->SetFooterMargin(40);
        $pdf->SetBarcode($correspondencia->getNCorrespondenciaEmisor());
        
        // init pdf doc
        $pdf->AliasNbPages();
        $pdf->AddPage();

        //Muestra datos del formato
        $valores = $this->executeTraer($formato);        
        $solicitante = Doctrine::getTable('Funcionarios_Funcionario')->find($valores["vacaciones_solicitante_id"]);
        $solicitante_unidad = Doctrine::getTable('Organigrama_Unidad')->find($valores["vacaciones_solicitante_unidad_id"]);
        $solicitante_funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($valores["vacaciones_solicitante_cargo_id"]);
        $solicitante_cargo = Doctrine::getTable('Organigrama_Cargo')->find($solicitante_funcionario_cargo->getCargoId());
        $solicitante_cargo_tipo = Doctrine::getTable('Organigrama_CargoTipo')->find($solicitante_cargo->getCargoTipoId());
        
        if ($solicitante->getSexo() == 'M') {
            $solicitante_cargo_tipo = $solicitante_cargo_tipo->getMasculino();
        } else {
            $solicitante_cargo_tipo = $solicitante_cargo_tipo->getFemenino();
        }
        
        
        
        $emisores = '';
        foreach ($emisor as $list_firman) {
            if($list_firman->getFuncionarioId()!=$valores["vacaciones_solicitante_id"]){
                $emisores.= $list_firman->getunombre().' / '.
                        $list_firman->getctnombre().' / '.
                        ucwords(strtolower($list_firman->getpn())).' '.
                        ucwords(strtolower($list_firman->getpa())).'<br/>';
            }
        }
        
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }
        
        if($emisores != '')
        {
            $emisores .= '$%&';
            $emisores = str_replace('<br/>$%&', '', $emisores);
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
                        <h2><u>SOLICITUD DE VACACIONES</u></h2>
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr>
                    <td align="center">
                        <table width="450" border="1" cellpadding="5" align="center">
                            <tr align="left">
                                <td width="350">
                                    <font size="12">APELLIDOS Y NOMBRES:<br/> '.$solicitante->getPrimerApellido().' '.$solicitante->getSegundoApellido().', '.$solicitante->getPrimerNombre().' '.$solicitante->getSegundoNombre().'</font>
                                </td>
                                <td width="100">
                                    <font size="12">CEDULA:<br/> '.$solicitante->getCi().'</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450" colspan="2">
                                    <font size="12">DENOMINACION DEL CARGO:<br/> '.$solicitante_cargo_tipo.'</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450" colspan="2">
                                    <font size="12">UBICACION ADMINISTRATIVA:<br/></font>'.$solicitante_unidad->getNombre().'
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
                                    <font size="12">PERIODO SOLICITADO:</font>'.$valores["vacaciones_periodo_vacacional_show"].'
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
                                <td width="450" colspan="4">
                                    <font size="12">VACACIONES QUE SOLICITA</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="112">
                                    <font size="12">FECHA INICIO:<br/> '.$valores["vacaciones_f_inicio_show"].'</font>
                                </td>
                                <td width="112">
                                    <font size="12">FECHA FINAL:<br/> '.$valores["vacaciones_f_final_show"].'</font>
                                </td>
                                <td width="113">
                                    <font size="12">FECHA RETORNO:<br/> '.$valores["vacaciones_f_retorno_show"].'</font>
                                </td>
                                <td width="113">
                                    <font size="12">-</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="112">
                                    <font size="12">DIAS HABILES: '.$valores["vacaciones_laborables"].'</font>
                                </td>
                                <td width="112">
                                    <font size="12">DIAS FIN SEMANA: '.$valores["vacaciones_fines_semana"].'</font>
                                </td>
                                <td width="113">
                                    <font size="12">DIAS NO LABORABLES: '.$valores["vacaciones_no_laborables"].'</font>
                                </td>
                                <td width="113">
                                    <font size="12">DIAS CONTINUOS: '.$valores["vacaciones_dias_totales"].'</font>
                                </td>
                            </tr>
                            <tr align="left">
                                <td width="450" colspan="4">
                                    <font size="12">OBSERVACIONES:<br/> '.$valores["vacaciones_observacion"].'</font>
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
                                    <font size="12">AUTORIZADO POR:</font><br/> '.$emisores.'
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
                                    <font size="12">ANALISTA DE RRHH:</font><br/><br/><br/><br/>
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
                            <tr align="left">
                                <td width="450" colspan="2">
                                    <font size="10"><b>NOTA: EL DISFRUTE DE LAS VACACIONES, SOLO PODRA INICIARSE UNA VEZ OBTENIDA LA APROBACIÓN DE RECURSOS HUMANOS</b></font>
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