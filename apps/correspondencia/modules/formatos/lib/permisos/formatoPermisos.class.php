<?php

class formatoPermisos {
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
        
        $fecha_inicio= strtotime($formulario["permisos_f_inicio"]);
        
        $turno_inicio = $formulario['turno_inicio'];
        $turno_retorno = $formulario['turno_retorno'];

        //tipos de permisos:
        //0: Tiene diferencia de dias (min y max) y tiene seleccion de turno
        //1: Tinee diferencia de dias y no tiene seleccion de turno
        //2: No tiene diferencia de dias (min==max) y no tiene seleccion de turno
        //3: No tiene diferencia de dias pero min y max == 1 y tiene seleccion de turno
        $tipo_permiso= $formulario["tipo_permiso"];
        
        $dias= $formulario["permisos_dias_solicitados"];
        
        if ($tipo_permiso== 0) {
            $fecha_i= strtotime($formulario["permisos_f_inicio"]);
            $fecha_r= strtotime($formulario["permisos_f_retorno"]);

            $dias = $fecha_r - $fecha_i;
            $dias = $dias / (60 * 60 * 24);
            
            $dias = abs($dias);
            $dias = floor($dias);
        }
        
        if($dias == 1 && $formulario["permisos_f_inicio"] == $formulario["permisos_f_retorno"])
            $dias = 0;
        
        // campo_dos = tipo de permiso
        if (!$formulario["permisos_tipo_permiso"]) {
            $messages = array_merge($messages, array("permisos_tipo_permiso" => "Seleccione el tipo de permiso."));
        } else {
            $formato->setCampoDos($formulario["permisos_tipo_permiso"]);
        }

        // campo_tres = dias solicitados
        if (!$formulario["permisos_dias_solicitados"]) {
            $messages = array_merge($messages, array("permisos_dias_solicitados" => "Ingrese la cantidad días deseados."));
        } else {
            $formato->setCampoTres($dias);
        }

        // campo_cuatro = fecha de inicio
        if (!$formulario["permisos_f_inicio"]) {
            $messages = array_merge($messages, array("permisos_f_inicio" => "Por favor seleccione la fecha de inicio del permiso."));
        } else {
            $formato->setCampoCuatro($formulario["permisos_f_inicio"]);
        }
        
        $fecha_retorno = strtotime($formulario["permisos_f_inicio"]);
        $fecha_final = strtotime($formulario["permisos_f_inicio"]);
        $laborables = 0;
        $no_laborables = 0;
        $fines_semana = 0;
        
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];
        
//        if($turno_inicio== 'T' && $turno_retorno== 'M'){
//            $dias++;
//        }
        
        for ($i = 0; $i < $dias; $i++) {
            $fecha_retorno = strtotime('+1 day ' . date('Y-m-d', $fecha_retorno));

            if (date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 6 && date('N', strtotime(date('Y-m-d', $fecha_retorno))) != 7) {
                if(!isset($dias_no_laborables[date('Y-m-d',$fecha_retorno)])){
                    $laborables++;
                    
                    if($tipo_permiso != 0) {
                        if ($i == $dias - 2) {
                            $fecha_final = $fecha_retorno;
                        }    
                    }
                    
                } else {
                    $no_laborables++;
                    if($tipo_permiso != 0) {
                        $i--; 
                    }
                }
            } else {
                $fines_semana++;
                if($tipo_permiso != 0) {
                    $i--;
                }
            }
        }

        
        $fecha_final = date('Y-m-d', $fecha_final);
        
        if ($dias == 1)
            $fecha_final = date('Y-m-d', $fecha_inicio);

        $fecha_retorno = date('Y-m-d', $fecha_retorno);
        $dias_totales = $laborables + $fines_semana + $no_laborables;

        $dias_solicitados_neto= $dias;
        if($tipo_permiso== 0 || $tipo_permiso== 3) {
            if($turno_inicio=='T'){
                $dias_totales -=.5;
                $laborables -=.5;

                $dias_solicitados_neto -= .5;
            }
            
            if($turno_retorno=='T'){
                $dias_totales +=.5;
                $laborables +=.5;

                $dias_solicitados_neto += .5;
            }
        }
        
        //EN PERMISOS LOS DIAS LABORALES SIEMPRE SERAN LOS SOLICITADOS
//        $formato->setCampoTres($dias_solicitados_neto);
        $formato->setCampoTres($laborables);
        
        $formato->setCampoCinco($fecha_final);
        $formato->setCampoSeis($fecha_retorno);
        $formato->setCampoSiete($dias_totales);
        $formato->setCampoOcho($laborables);
        $formato->setCampoNueve($fines_semana);
        $formato->setCampoDiez($no_laborables);
        
        // campo_nueve = observaciones
        if ($formulario["permisos_observacion"]) {
            $formato->setCampoOnce($formulario["permisos_observacion"]);
        }
        
        //TESTER
//        echo 'dias solicitados'.$dias_solicitados_neto.'<br/>';
//        echo 'dias habiles'.$laborables.'<br/>';
//        echo 'fecha final: '.$fecha_final.'<br/>';
//        echo 'fecha retorno: '.$fecha_retorno.'<br/>';
//        echo 'continuos: '.$dias_totales.'<br/>';
//        echo 'fines de semanas'.$fines_semana.'<br/>';
//        echo 'no laborales'.$no_laborables;
//        exit;
    }
    
    public function executeTraer($datos) {
        $funcionario=Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario((int)$datos["campo_uno"]);
        
        $formulario["id"] = $datos["id"];
        $formulario["tipo_formato_id"] = $datos["tipo_formato_id"];
        $formulario["permisos_solicitante_id"] = $datos["campo_uno"];
        $formulario["permisos_solicitante_unidad_id"] = $datos["campo_doce"];
        $formulario["permisos_solicitante_cargo_id"] = $datos["campo_trece"];
        $formulario["permisos_solicitante"] = $funcionario[0]['ctnombre'].' / '.$funcionario[0]['primer_nombre'].' '.$funcionario[0]['primer_apellido'].' - CI: '.$funcionario[0]['ci'];
        
        $formulario["permisos_tipo_permiso"] = $datos["campo_dos"];
        $formulario["permisos_dias_solicitados"] = $datos["campo_tres"];
        
        $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                   '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
        
        $formulario["permisos_f_inicio"] = $datos["campo_cuatro"];
        $formulario["permisos_f_inicio_show"] = date('d',strtotime($datos["campo_cuatro"])).' '.$mes[date('m',strtotime($datos["campo_cuatro"]))].' '.date('Y',strtotime($datos["campo_cuatro"]));
        $formulario["permisos_f_final"] = $datos["campo_cinco"];
        $formulario["permisos_f_final_show"] = date('d',strtotime($datos["campo_cinco"])).' '.$mes[date('m',strtotime($datos["campo_cinco"]))].' '.date('Y',strtotime($datos["campo_cinco"]));
        $formulario["permisos_f_retorno"] = $datos["campo_seis"];
        $formulario["permisos_f_retorno_show"] = date('d',strtotime($datos["campo_seis"])).' '.$mes[date('m',strtotime($datos["campo_seis"]))].' '.date('Y',strtotime($datos["campo_seis"]));
        $formulario["permisos_dias_totales"] = $datos["campo_siete"];
        $formulario["permisos_laborables"] = $datos["campo_ocho"];
        $formulario["permisos_fines_semana"] = $datos["campo_nueve"];
        $formulario["permisos_no_laborables"] = $datos["campo_diez"];
        $formulario["permisos_observacion"] = $datos["campo_once"];
        $formulario["permisos_clasificacion"] = $datos["campo_catorce"];

        return $formulario;
    }

    public function executeAdditionalCrear($correspondencia_id)
    {
        $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);
          
        $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);
        $valores = $this->executeTraer($formatos[0]);


        $condicion_trabajador = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);

        $configuraciones_permisos = Doctrine_Query::create()
                                    ->select('ca.*')
                                    ->from('Rrhh_Configuraciones ca')
                                    ->where('ca.modulo = ?', 'permisos')
                                    ->andWhere('ca.indexado LIKE ?', '%condicion:['.$condicion_trabajador->getCargoCondicionId().']%')
                                    ->andWhere('ca.indexado LIKE ?', '%f_final_configuracion:[2038-01-01]%')
                                    ->execute();
        
        $correspondencia_edit= Doctrine::getTable('Rrhh_Permisos')->findOneByCorrespondenciaSolicitudId($correspondencia_id);

        if(count($correspondencia_edit)> 1) {
            $permisos= $correspondencia_edit;
            $correspondencia_edit= TRUE;
        }else {
            $permisos = new Rrhh_Permisos();
            $correspondencia_edit= FALSE;
        }
        
        $permisos->setConfiguracionesPermisosId($configuraciones_permisos[0]->getId());
        $permisos->setFuncionarioId($session_funcionario_id);
        $permisos->setCorrespondenciaSolicitudId($correspondencia_id);
        $permisos->setTipoPermiso($valores["permisos_tipo_permiso"]);
        $permisos->setFInicioPermiso($valores["permisos_f_inicio"]);
        $permisos->setFFinPermiso($valores["permisos_f_final"]);
        $permisos->setFRetornoPermiso($valores["permisos_f_retorno"]);
        
        $permisos->setDiasSolicitados($valores["permisos_dias_solicitados"]);
        $permisos->setDiasPermisoHabiles($valores["permisos_laborables"]);
        $permisos->setDiasPermisoFinSemana($valores["permisos_fines_semana"]);
        $permisos->setDiasPermisoNoLaborales($valores["permisos_no_laborables"]);
        $permisos->setDiasPermisoContinuo($valores["permisos_dias_totales"]);
        $permisos->setClasificacion($valores["permisos_clasificacion"]);
        
        $permisos->setObservacionesDescritas($valores["permisos_observacion"]);
        
        $automaticas = '';
        if(strpos($valores["permisos_dias_solicitados"], '.')) {
            if($valores["permisos_dias_solicitados"] > 1)
                $automaticas = "Medio dia en la mañana";
            else
                $automaticas = "Medio dia en la tarde";
        }
            
        $permisos->setObservacionesAutomaticas($automaticas);    
        $permisos->setDiasPermisoEjecutados(0);
        
        if(!$correspondencia_edit) {
            $permisos->setStatus('I');
        }
        //ESTATUS I CORRESPONDENCIA CREADA QUE NO SE HA ENVIADO
        //ESTATUS A CORRESPONDENCIA ENVIADA YA LA APROBO EL JEFE
        
        $permisos->save();
    }
    
    
    public function executeAdditionalEnviar($correspondencia_id)
    {
        $permisos = Doctrine::getTable('Rrhh_Permisos')->findByCorrespondenciaSolicitudId($correspondencia_id);
        
        foreach ($permisos as $permiso) {
            $permiso->setStatus('A');
            $permiso->save();
        }
    }
    
    public function executeAdditionalAnular($correspondencia_id)
    {
        $permisos = Doctrine::getTable('Rrhh_Permisos')->findByCorrespondenciaSolicitudId($correspondencia_id);
        
        foreach ($permisos as $permiso) {
            $permiso->setStatus('E');
            $permiso->save();
        }
    }
    
    public function executeAdditionalEmisor($correspondencia_id)
    {
        //ESTA FUNCION ADICIONAL ES PARA LISTAR MISMOS EMISORES QUE LISTA EL SOLICITANTE (CASO DE PERMISOS, VACAS Y REPOSOS)
        $emisor= Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_id);
        //PARA PERMISOS EL CAMPO UNO ES QUIEN SOLICITA
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

        $fecha_inicio = strtotime(date('Y-m-d', strtotime($valores['f_i'])));
        $fecha_fin = strtotime(date('Y-m-d', strtotime($valores['f_r'])));
        
        //EL ID DE USUARIO DEBE SER CAMBIADO CUANDO EL SUPERVISOR CARGA PERMISO DE SUBORDINADO
        $otros_permisos = Doctrine::getTable('Rrhh_Permisos')->activos($session_funcionario_id);
        
        $available= TRUE;
        foreach($otros_permisos as $permisos) {
            $old_inicio= strtotime(date('Y-m-d', strtotime($permisos->getFInicioPermiso())));
            $old_fin= strtotime(date('Y-m-d', strtotime($permisos->getFFinPermiso())));
            if($old_inicio != $fecha_inicio) {
                if($old_inicio < $fecha_inicio) {
                    if($old_fin > $fecha_inicio) {
                        $available= FALSE;
                    }
                }else {
                    if($old_inicio > $fecha_inicio) {
                        if($fecha_fin > $old_inicio) {
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
    
    public function executeCalculoIntervalo($valores)
    {
      $fecha_inicio = $valores['f_i'];
      $dias = $valores['d'];
      $turno_inicio = $valores['md_i'];
      $turno_retorno = $valores['md_r'];

      $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                 '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
      
      $fecha_retorno = strtotime($fecha_inicio);
      $fecha_final = strtotime($fecha_inicio);
      $laborables=0;$fines_semana=0;$no_laborables=0;
      
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];

        $no_lab= Array();
        $repeticion= '';
        $i=0;
        foreach($dias_no_laborables as $key=> $value) {
            $no_lab[$i]['dia']= date('d', strtotime($key));
            $no_lab[$i]['mes']= date('m', strtotime($key));
            $no_lab[$i]['anio']= date('Y', strtotime($key));
            $repeticion= explode('#', $value);
            $no_lab[$i]['tmp']= $repeticion[1];
            
            $i++;
        }

      if($turno_inicio== 'T' && $turno_retorno== 'M'){
        $dias++;
      }

      for($i=0;$i<$dias;$i++)
      {
        if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
            $fer= false;
            if(count($no_lab) > 0) {
                for($j=0; $j< count($no_lab); $j++) {
                    if($no_lab[$j]['tmp']== 't') {
                        if(date('m',$fecha_retorno)== $no_lab[$j]['mes'] && date('d',$fecha_retorno)== $no_lab[$j]['dia']) {
                            $fer= true;
                        }
                    }else {
                        if(date('d',$fecha_retorno)== $no_lab[$j]['dia'] && date('m',$fecha_retorno)== $no_lab[$j]['mes'] && date('Y',$fecha_retorno)== $no_lab[$j]['anio']) {
                            $fer= true;
                        }
                    }
                }
            }
            if($fer== false)
                $laborables++;
            else
                $no_laborables++;
        } else
           $fines_semana++;
        
        $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));
      }

      $fecha_final=date('d',$fecha_final).' '.$mes[date('m',$fecha_final)].' '.date('Y',$fecha_final);
      
       if($dias==1) { 
           $fecha_igual = strtotime($fecha_inicio); 
           $fecha_final=date('d',$fecha_igual).' '.$mes[date('m',$fecha_igual)].' '.date('Y',$fecha_igual); 
       }
           
      $fecha_retorno_hidden = date('Y-m-d',$fecha_retorno);
      $fecha_retorno=date('d',$fecha_retorno).' '.$mes[date('m',$fecha_retorno)].' '.date('Y',$fecha_retorno);
      $dias_totales = $laborables + $fines_semana + $no_laborables;

      if($turno_inicio=='T'){
          $dias_totales -=.5;
          $laborables -=.5;
      }
      
      if($turno_retorno=='T'){
          $dias_totales +=.5;
          $laborables +=.5;
      }
      $parts_totales = explode(".",$dias_totales);
      $parts_laborales = explode(".",$laborables); 

      $cadena = "<table>
                    <tr><td>d&iacute;as de permiso continuos</td><td>". ((isset($parts_totales[1])) ? (((int)$dias_totales != 0) ? (int)$dias_totales : '').' &frac12' : $dias_totales) ."</td></tr>
                    <tr><td>d&iacute;as de permiso habiles</td><td>". ((isset($parts_laborales[1])) ? (((int)$laborables != 0) ? (int)$laborables : '').' &frac12' : $laborables) ."</td></tr>
                    <tr><td>d&iacute;as de permiso fines de semana</td><td>".$fines_semana."</td></tr>
                    <tr><td>d&iacute;as de permiso no laborables</td><td>".$no_laborables."</td></tr>
                </table>";
      
      return $cadena;
  }
  
  public function executeCalculoRegreso($valores)
    {
      $fecha_inicio = $valores['f_i'];
      $dias = $valores['d'];
//      $medio_dia = $valores['md_r'];
      
//      if($medio_dia=='T'){
//          $dias++;
//      }
      
      $mes=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                 '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
      
      $fecha_retorno = strtotime($fecha_inicio);
      $fecha_final = strtotime($fecha_inicio);
      $final_r= '';
      $laborables=0;$fines_semana=0;$no_laborables=0;
      
        $sf_varios = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/varios.yml");
        $dias_no_laborables = $sf_varios['dias_no_laborables'];
      
        $no_lab= Array();
        $repeticion= '';
        $i=0;
        foreach($dias_no_laborables as $key=> $value) {
            $no_lab[$i]['dia']= date('d', strtotime($key));
            $no_lab[$i]['mes']= date('m', strtotime($key));
            $no_lab[$i]['anio']= date('Y', strtotime($key));
            $repeticion= explode('#', $value);
            $no_lab[$i]['tmp']= $repeticion[1];
            
            $i++;
        }
        
        $last= false;
      for($i=0;$i<$dias;$i++)
      {
        if(date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=6 && date('N', strtotime(date('Y-m-d',$fecha_retorno)))!=7){
            $fer= false;
            if(count($no_lab) > 0) {
                for($j=0; $j< count($no_lab); $j++) {
                    if($no_lab[$j]['tmp']== 't') {
                        if(date('m',$fecha_retorno)== $no_lab[$j]['mes'] && date('d',$fecha_retorno)== $no_lab[$j]['dia']) {
                            $fer= true;
                        }
                    }else {
                        if(date('d',$fecha_retorno)== $no_lab[$j]['dia'] && date('m',$fecha_retorno)== $no_lab[$j]['mes'] && date('Y',$fecha_retorno)== $no_lab[$j]['anio']) {
                            $fer= true;
                        }
                    }
                }
            }
            if($fer== false) {
                $laborables++;
                
                if ($i == $dias - 1) {
                    $fecha_final=$fecha_retorno;
                    
                    $f_retorno=$fecha_retorno;
                }
            }else {
                $no_laborables++;
                $i--;
            }
        }
        else
        {
           $fines_semana++;
           $i--;
        }
        $fecha_retorno=strtotime('+1 day ' . date('Y-m-d',$fecha_retorno));
        if(!$last) {
            if($i==$dias-1) {
              $i--;
              $last= true;
              $final_r= $fecha_final;
          }
        }
      }
      
      $fecha_final=date('d',$final_r).' '.$mes[date('m',$final_r)].' '.date('Y',$final_r);
      
       if($dias==1) { 
           $fecha_igual = strtotime($fecha_inicio); 
           $fecha_final=date('d',$fecha_igual).' '.$mes[date('m',$fecha_igual)].' '.date('Y',$fecha_igual); 
       }
      
      $laborables--;
       
      $f_retorno_hidden = date('Y-m-d',$fecha_retorno);
      $f_retorno=date('d',$f_retorno).' '.$mes[date('m',$f_retorno)].' '.date('Y',$f_retorno);
      $dias_totales = $laborables + $fines_semana + $no_laborables;

//      if($medio_dia=='M'){
//          $dias_totales +=.5;
//          $laborables +=.5;
//      }
      
//      if($medio_dia=='T'){
//          $dias_totales -=.5;
//          $laborables -=.5;
//      }
       
      $cadena = "<table>
                    <tr><td>Fecha final</td><td>".$fecha_final."</td></tr>
                    <tr><td>Fecha retorno</td><td>".$f_retorno."</td></tr>
                    <tr><td>d&iacute;as de permiso continuos</td><td>".$dias_totales."</td></tr>
                    <tr><td>d&iacute;as de permiso habiles</td><td>".$laborables."</td></tr>
                    <tr><td>d&iacute;as de permiso fines de semana</td><td>".$fines_semana."</td></tr>
                    <tr><td>d&iacute;as de permiso no laborables</td><td>".$no_laborables."</td></tr>
                </table>";
      
      return $cadena;
  }

}