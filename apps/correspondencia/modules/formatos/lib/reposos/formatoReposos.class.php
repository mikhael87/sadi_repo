<?php

class formatoReposos {
    public function executeVerificarTercerizacion(&$session_funcionario_unidad_id,&$session_funcionario_cargo_id,&$session_funcionario_id) {
        
          if(!sfContext::getInstance()->getUser()->getAttribute('tercerizado')){
            $session_funcionario_unidad_id = sfContext::getInstance()->getUser()->getAttribute('funcionario_unidad_id');
            $session_funcionario_cargo_id = sfContext::getInstance()->getUser()->getAttribute('funcionario_cargo_id');
            $session_funcionario_id = sfContext::getInstance()->getUser()->getAttribute('funcionario_id');
          } else {
            $tercerizado = sfContext::getInstance()->getUser()->getAttribute('tercerizado');
            $session_funcionario_unidad_id = $tercerizado['unidad_id'];
            $session_funcionario_cargo_id = $tercerizado['cargo_id'];
            $session_funcionario_id = $tercerizado['funcionario_id'];
          }
    }
    
    public function executeValidar($formulario, &$messages, &$formato) {
        // campo_uno = solicitante
        $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);

        $formato->setCampoUno($session_funcionario_id);
        $formato->setCampoDoce($session_funcionario_unidad_id);
        $formato->setCampoTrece($session_funcionario_cargo_id);
        $formato->setCampoCatorce($formulario["clasificacion"]);


        // campo_dos = tipo de reposo
        if (!$formulario["reposos_tipo_reposo"]) {
            $messages = array_merge($messages, array("reposos_tipo_reposo" => "Seleccione el tipo de reposo."));
        } else {
            $formato->setCampoDos($formulario["reposos_tipo_reposo"]);
        }

        // campo_tres = dias solicitados
        if (!$formulario["reposos_dias_solicitados"]) {
            $messages = array_merge($messages, array("reposos_dias_solicitados" => "Ingrese la cantidad días deseados."));
        } else {
            $formato->setCampoTres($formulario["reposos_dias_solicitados"]);
        }

        // campo_cuatro = fecha de inicio
        if (!$formulario["reposos_f_inicio"]) {
            $messages = array_merge($messages, array("reposos_f_inicio" => "Por favor seleccione la fecha de inicio del reposo."));
        } else {
            $formato->setCampoCuatro($formulario["reposos_f_inicio"]);
        }

        $dias = $formulario["reposos_dias_solicitados"];
        $fecha_retorno = strtotime($formulario["reposos_f_inicio"]);
        $laborables = 0;
        $no_laborables = 0;
        $fines_semana = 0;
        
        $fecha_final_continuo=strtotime('+'.($dias-1).' day ' . date('Y-m-d', strtotime($formulario["reposos_f_inicio"])));
        
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

        for($i=0;$i<$dias;$i++)
        {
            if($i != 1)
                $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));

            if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
                if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                    $laborables++;

                    if ($i == $dias - 2)
                        $fecha_final= date('Y-m-d', $fecha_retorno);
                }else
                    $no_laborables++;
            }else
            $fines_semana++;
        }

        $fecha_retorno_continuo= $fecha_final_continuo;
        while(date('N', strtotime(date('Y-m-d',$fecha_retorno_continuo)))== 6 || date('N', strtotime(date('Y-m-d',$fecha_retorno_continuo)))== 7 || isset($dias_no_laborables[date('Y-m-d',$fecha_retorno_continuo)]) || $fecha_retorno_continuo == $fecha_final_continuo) {
            $fecha_retorno_continuo=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno_continuo));
        }

        if ($dias == 1)
            $fecha_final = date('Y-m-d', strtotime($fecha_inicio));
        
        $fecha_final=date('Y-m-d', $fecha_final_continuo);
        
        $fecha_retorno = date('Y-m-d', $fecha_retorno_continuo);

        $dias_totales = $laborables + $fines_semana + $no_laborables;

        $formato->setCampoCinco($fecha_final);
        $formato->setCampoSeis($fecha_retorno);
        $formato->setCampoSiete($dias_totales);
        $formato->setCampoOcho($laborables);
        $formato->setCampoNueve($fines_semana);
        $formato->setCampoDiez($no_laborables);

        // campo_once = observaciones
        if ($formulario["reposos_observacion"]) {
            $formato->setCampoOnce($formulario["reposos_observacion"]);
        }
    }

    public function executeTraer($datos) {
        $funcionario=Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario((int)$datos["campo_uno"]);

        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["reposos_solicitante_id"] = $datos["campo_uno"];
        $formulario["reposos_solicitante_unidad_id"] = $datos["campo_doce"];
        $formulario["reposos_solicitante_cargo_id"] = $datos["campo_trece"];
        $formulario["reposos_solicitante"] = $funcionario[0]['ctnombre'].' / '.$funcionario[0]['primer_nombre'].' '.$funcionario[0]['primer_apellido'].' - CI: '.$funcionario[0]['ci'];

        $formulario["reposos_tipo_reposo"] = $datos["campo_dos"];
        $formulario["reposos_dias_solicitados"] = $datos["campo_tres"];

        $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                   '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

        $formulario["reposos_f_inicio"] = $datos["campo_cuatro"];
        $formulario["reposos_f_inicio_show"] = date('d',strtotime($datos["campo_cuatro"])).' '.$mes[date('m',strtotime($datos["campo_cuatro"]))].' '.date('Y',strtotime($datos["campo_cuatro"]));
        $formulario["reposos_f_final"] = $datos["campo_cinco"];
        $formulario["reposos_f_final_show"] = date('d',strtotime($datos["campo_cinco"])).' '.$mes[date('m',strtotime($datos["campo_cinco"]))].' '.date('Y',strtotime($datos["campo_cinco"]));
        $formulario["reposos_f_retorno"] = $datos["campo_seis"];
        $formulario["reposos_f_retorno_show"] = date('d',strtotime($datos["campo_seis"])).' '.$mes[date('m',strtotime($datos["campo_seis"]))].' '.date('Y',strtotime($datos["campo_seis"]));
        $formulario["reposos_dias_totales"] = $datos["campo_siete"];
        $formulario["reposos_laborables"] = $datos["campo_ocho"];
        $formulario["reposos_fines_semana"] = $datos["campo_nueve"];
        $formulario["reposos_no_laborables"] = $datos["campo_diez"];
        $formulario["reposos_observacion"] = $datos["campo_once"];
        $formulario["reposos_clasificacion"] = $datos["campo_catorce"];

        return $formulario;
    }

    public function executeAdditionalCrear($correspondencia_id)
    {
        $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);
          
        $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);
        $valores = $this->executeTraer($formatos[0]);


        $condicion_trabajador = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);

        $configuraciones_reposos = Doctrine_Query::create()
                                    ->select('ca.*')
                                    ->from('Rrhh_Configuraciones ca')
                                    ->where('ca.modulo = ?', 'reposos')
                                    ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion_trabajador->getCargoCondicionId().']%')
                                    ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                    ->execute();

        $correspondencia_edit= Doctrine::getTable('Rrhh_Reposos')->findOneByCorrespondenciaSolicitudId($correspondencia_id);

        if(count($correspondencia_edit)> 1) {
            $reposos= $correspondencia_edit;
            $correspondencia_edit= TRUE;
        }else {
            $reposos = new Rrhh_Reposos();
            $correspondencia_edit= FALSE;
        }
        
        $reposos->setConfiguracionesRepososId($configuraciones_reposos[0]->getId());
        $reposos->setFuncionarioId($session_funcionario_id);
        $reposos->setCorrespondenciaSolicitudId($correspondencia_id);
        $reposos->setTipoReposo($valores["reposos_tipo_reposo"]);
        $reposos->setFInicioReposo($valores["reposos_f_inicio"]);
        $reposos->setFFinReposo($valores["reposos_f_final"]);
        $reposos->setFRetornoReposo($valores["reposos_f_retorno"]);

        $reposos->setDiasSolicitados($valores["reposos_dias_solicitados"]);
        $reposos->setDiasReposoHabiles($valores["reposos_laborables"]);
        $reposos->setDiasReposoFinSemana($valores["reposos_fines_semana"]);
        $reposos->setDiasReposoNoLaborales($valores["reposos_no_laborables"]);
        $reposos->setDiasReposoContinuo($valores["reposos_dias_totales"]);
        $reposos->setClasificacion($valores["reposos_clasificacion"]);

        $reposos->setObservacionesDescritas($valores["reposos_observacion"]);
        $reposos->setObservacionesAutomaticas("...");
        $reposos->setDiasReposoEjecutados(0);

        if(!$correspondencia_edit) {
            $reposos->setStatus('I');
        }
        //ESTATUS I CORRESPONDENCIA CREADA QUE NO SE HA ENVIADO
        //ESTATUS A CORRESPONDENCIA ENVIADA YA LA APROBO EL JEFE

        $reposos->save();
    }


    public function executeAdditionalEnviar($correspondencia_id)
    {
        $reposos = Doctrine::getTable('Rrhh_Reposos')->findByCorrespondenciaSolicitudId($correspondencia_id);

        foreach ($reposos as $reposo) {
            $reposo->setStatus('A');
            $reposo->save();
        }

        // CAMBIO_PENDIENTE: buscar si coliciona con una vacacion activa, de ser asi agregar bitacora dias adicionales.
    }

    public function executeAdditionalAnular($correspondencia_id)
    {
        $reposos = Doctrine::getTable('Rrhh_Reposos')->findByCorrespondenciaSolicitudId($correspondencia_id);

        foreach ($reposos as $reposo) {
            $reposo->setStatus('E');
            $reposo->save();
        }
    }
    
    public function executeAdditionalEmisor($correspondencia_id)
    {
        //ESTA FUNCION ADICIONAL ES PARA LISTAR MISMOS EMISORES QUE LISTA EL SOLICITANTE (CASO DE REPOSOS, VACAS Y REPOSOS)
        $emisor= Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_id);
        //PARA REPOSOS EL CAMPO UNO ES QUIEN SOLICITA
        $funcionario_emisor_id= $emisor->getCampoUno();
        if(sfContext::getInstance()->getUser()->getAttribute('funcionario_id') != $funcionario_emisor_id) {
            $tercero['cargo_id']= $emisor->getCampoTrece();
            $tercero['unidad_id']= $emisor->getCampoDoce();
            $tercero['funcionario_id']= $emisor->getCampoUno();
            sfContext::getInstance()->getUser()->setAttribute('tercerizado', $tercero);
        }
    }

    public function executeAdditionalDevolver($correspondencia_id){}

    public function executeLapsoDisponible($valores)
    {
        $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);

        $fecha_inicio = $valores['f_i'];
        
        //CALCULA FECHA FINAL
        $dias = $valores['d_s'];
        $fecha_retorno = strtotime($fecha_inicio);

        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

        $fecha_final_continuo=strtotime('+'.($dias-1).' day ' . date('Y-m-d', strtotime($fecha_inicio)));
      
        $laborables=0;$fines_semana=0;$no_laborables=0;

            $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
            $dias_no_laborables = $sf_varios['dias_no_laborables'];

        for($i=0;$i<$dias;$i++)
        {
            if($i != 1)
                $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));

            if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
                if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                    $laborables++;

                    if ($i == $dias - 2)
                        $fecha_final=date('d',$fecha_retorno).' '.date('m',$fecha_retorno).' '.date('Y',$fecha_retorno);
                }else
                    $no_laborables++;
            }else
            $fines_semana++;
        }

        if($dias==1) {
            $fecha_fin = strtotime($fecha_inicio);
        }
        
        $fecha_fin= $fecha_final_continuo;
        
        //EL ID DE USUARIO DEBE SER CAMBIADO CUANDO EL SUPERVISOR CARGA REPOSOS DE SUBORDINADO
        $otros_reposos = Doctrine::getTable('Rrhh_Reposos')->activos($session_funcionario_id);
        
        $fecha_inicio = strtotime(date('Y-m-d', strtotime($fecha_inicio)));
        $fecha_fin = strtotime(date('Y-m-d', $fecha_fin));
        
        $available= TRUE;
        foreach($otros_reposos as $reposos) {
            $old_inicio= strtotime(date('Y-m-d', strtotime($reposos->getFInicioReposo())));
            $old_fin= strtotime(date('Y-m-d', strtotime($reposos->getFFinReposo())));
            if($old_inicio != $fecha_inicio) {
                if($old_inicio < $fecha_inicio) {
                    if($old_fin >= $fecha_inicio) {
                        $available= FALSE;
                    }
                }else {
                    if($fecha_fin >= $old_inicio) {
                        if($fecha_fin <= $old_fin) {
                            $available= FALSE;
                        }
                    }
                }
            }else {
                $available= FALSE;
            }
        }
        echo $available;
        exit();
    }

    public function executeCalculoRegreso($valores)
    {
      $fecha_inicio = $valores['f_i'];
      $dias = $valores['d'];

      $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                 '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

      $fecha_retorno = strtotime($fecha_inicio);
      
      $fecha_final_continuo=strtotime('+'.($dias-1).' day ' . date('Y-m-d', strtotime($fecha_inicio)));
      
      $laborables=0;$fines_semana=0;$no_laborables=0;

        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

      for($i=0;$i<$dias;$i++)
      {
        if($i != 1)
            $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));
        
        if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
            if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                $laborables++;

                if ($i == $dias - 2)
                    $fecha_final=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
            }else
                $no_laborables++;
        }else
           $fines_semana++;
      }
      
      $fecha_retorno_continuo= $fecha_final_continuo;
      while(date('N', strtotime(date('Y-m-d',$fecha_retorno_continuo)))== 6 || date('N', strtotime(date('Y-m-d',$fecha_retorno_continuo)))== 7 || isset($dias_no_laborables[date('Y-m-d',$fecha_retorno_continuo)]) || $fecha_retorno_continuo == $fecha_final_continuo) {
          $fecha_retorno_continuo=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno_continuo));
      }

       if($dias==1) {
           $fecha_igual = strtotime($fecha_inicio);
           $fecha_final=date('d',$fecha_igual).' '.$mes[date('m',$fecha_igual)].' '.date('Y',$fecha_igual);}
      $fecha_final=date('d',$fecha_final_continuo).' '.$mes[date('m',$fecha_final_continuo)].' '.date('Y',$fecha_final_continuo);
           
      $fecha_retorno=date('d',$fecha_retorno_continuo).' '.$mes[date('m',$fecha_retorno_continuo)].' '.date('Y',$fecha_retorno_continuo);
      $dias_totales = $laborables + $fines_semana + $no_laborables;

      $cadena = "<table>
                    <tr><td>Fecha final</td><td>".$fecha_final."</td></tr>
                    <tr><td>Fecha retorno</td><td>".$fecha_retorno."</td></tr>
                    <tr><td>dias de reposo continuos</td><td>".$dias_totales."</td></tr>
                    <tr><td>dias de reposo habiles</td><td>".$laborables."</td></tr>
                    <tr><td>dias de reposo fines de semana</td><td>".$fines_semana."</td></tr>
                    <tr><td>dias de reposo no laborables</td><td>".$no_laborables."</td></tr>
                </table>";

      return $cadena;
  }
  
  //CODIGO ANTERIOR (EL NUEVO CODIGO CALCULA EN BASE A DIAS CONTINUOS PARA LA FECHA FINAL)
//  public function executeCalculoRegreso($valores)
//    {
//      $fecha_inicio = $valores['f_i'];
//      $dias = $valores['d'];
//
//      $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
//                 '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
//
//      $fecha_retorno = strtotime($fecha_inicio);
//      $laborables=0;$fines_semana=0;$no_laborables=0;
//
//        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
//        $dias_no_laborables = $sf_varios['dias_no_laborables'];
//
//      for($i=0;$i<$dias;$i++)
//      {
//        $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));
//
//        if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
//            if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
//                $laborables++;
//
//                if ($i == $dias - 2) {
//                    $fecha_final=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
//                }
//            } else {
//                $no_laborables++;
//                $i--;
//            }
//        }
//        else
//        {
//           $fines_semana++;
//           $i--;
//        }
//      }
//
//       if($dias==1) {
//           $fecha_igual = strtotime($fecha_inicio);
//           $fecha_final=date('d',$fecha_igual).' '.$mes[date('m',$fecha_igual)].' '.date('Y',$fecha_igual); }
//
//      $fecha_retorno_hidden = date('Y-m-d',$fecha_retorno);
//      $fecha_retorno=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
//      $dias_totales = $laborables + $fines_semana + $no_laborables;
//
//      $cadena = "<table>
//                    <tr><td>Fecha final</td><td>".$fecha_final."</td></tr>
//                    <tr><td>Fecha retorno</td><td>".$fecha_retorno."</td></tr>
//                    <tr><td>dias de disfrute continuos</td><td>".$dias_totales."</td></tr>
//                    <tr><td>dias de disfrute habiles</td><td>".$laborables."</td></tr>
//                    <tr><td>dias de disfrute fines de semana</td><td>".$fines_semana."</td></tr>
//                    <tr><td>dias de disfrute no laborables</td><td>".$no_laborables."</td></tr>
//                </table>";
//
//      return $cadena;
//  }

}