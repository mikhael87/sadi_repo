<?php

/**
 * formatos actions.
 *
 * @package    siglas-(institucion)
 * @subpackage formatos
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class formatosActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      $unidades_autorizadas = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'));

        $i=0; $unidad_ids=array(); $hijos=array();
        foreach ($unidades_autorizadas as $unidad_autorizada)
            {$unidad_ids[$i] = $unidad_autorizada->getAutorizadaUnidadId(); $i++;}

        if(count($unidad_ids)>0)
        {
            $hijos=null;
            if($this->getUser()->getAttribute('correspondencia_padre_id'))
            {
                $formato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($this->getUser()->getAttribute('correspondencia_padre_id'));

                $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato[0]->getTipoFormatoId());
                $parametros = sfYaml::load($tipo_formato[0]->getParametros());

                if(isset($parametros['hijos']))
                    $hijos = $parametros['hijos'];
            }

            $correlativos_activos = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->correlativosUnidades($unidad_ids,$hijos);

            $formatos_legitimos = array();
            $i=0;
            $formato_anterior = '';
            foreach ($correlativos_activos as $correlativo_activo) {
                $formatos_legitimos[$i]=$correlativo_activo->getTipoFormatoId().'|'.
                                        $correlativo_activo->getUnidadId().'|'.
                                        $correlativo_activo->getUnidadCorrelativoId().'|'.
                                        $correlativo_activo->getId().'|'.
                                        $correlativo_activo->getDescripcionCorrelativo();

                if($formato_anterior == $correlativo_activo->getTipoFormatoId()) {
                    $formatos_legitimos[$i-1].='|formatear';
                    $formatos_legitimos[$i].='|formatear';

                    $formatos_legitimos[$i-1]=str_replace('|formatear|formatear', '|formatear', $formatos_legitimos[$i-1]);
                    $formatos_legitimos[$i]=str_replace('|formatear|formatear', '|formatear', $formatos_legitimos[$i]);
                    }

                $formato_anterior = $correlativo_activo->getTipoFormatoId();

                $i++;
            }

            foreach ($formatos_legitimos as $i => $valor) {
                $detalles = explode('|',$valor);

                $formato_nombre = Doctrine::getTable('Correspondencia_TipoFormato')->find($detalles[0]);
                $formato[$i]=$formato_nombre->getNombre();

                if($detalles[4]!='')
                    $formato[$i].=' '.$detalles[4];

                if(count($detalles)==6){
                    $unidad_nombre = Doctrine::getTable('Organigrama_Unidad')->find($detalles[1]);
                    $formato[$i].=' ('.$unidad_nombre->getNombre().')';
                }
            }

            if(count($formatos_legitimos)==0)
            {
                $this->getUser()->setFlash('error', 'Actualmente la unidad en la que tienes acceso a la correspondencia no ha configurado ningún correlativo de envío,
                    por lo tanto no podrás redactar ni enviar correspondencia administrativa.
                    Comunícate con tu supervisor inmediato para que configure los correlativos mediante la opcion "Correlativos" del menu de herramientas.');
            }

            $this->getUser()->setAttribute('formatos_legitimos',$formatos_legitimos);
            } else {
                    $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de correspondencia
                        con el permiso de redactar,
                        por lo tanto no podrás redactar correspondencia administrativa, solo correspondencia personal.
                        Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
                        "Permisos de Grupos" del submenú de herramientas de Correspondencia.');
            }

        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS

        if(count($hijos)==0)
        {
            $formatos_privados = Doctrine::getTable('Correspondencia_TipoFormato')->findByPrivadoAndStatus('S','A');
            foreach ($formatos_privados as $formato_privado) {
                $formato[$formato_privado->getId().'#P']=$formato_privado->getNombre();
            }
        }

        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        // FORMATOS PRIVADOS
        if(isset($formato)) $this->formatos = $formato;

//        echo "<pre>"; print_r($formatos_legitimos); print_r($formato); exit();

      if($this->getUser()->getAttribute('correspondencia_id')) // Editando
      {
          $this->getUser()->getAttributeHolder()->remove('formatos_legitimos');

          $correspondencia = array();
          $carga_edicion = Doctrine::getTable('Correspondencia_Correspondencia')->find($this->getUser()->getAttribute('correspondencia_id'));
          $correspondencia['identificacion']['id'] = $carga_edicion->getId();
          $correspondencia['identificacion']['n_correspondencia_emisor'] = $carga_edicion->getNCorrespondenciaEmisor();
          $correspondencia['identificacion']['prioridad'] = $carga_edicion->getPrioridad();
          $correspondencia['identificacion']['privacidad']['emisor']= $carga_edicion->getPrivado();

          $carga_edicion = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
          $i=0;
          foreach ($carga_edicion as $emisor) {
              $correspondencia['emisor'][$i]['funcionario_id'] = $emisor->getFuncionarioId();
              $i++;
          }

          $carga_edicion = Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
          $i=0;
          $privacidad_receptor= 'N';
          foreach ($carga_edicion as $receptor) {
              $privacidad_receptor= $receptor->getPrivado();
              $correspondencia['receptor']['copias'][] = $receptor->getUnidadId().'#'.$receptor->getFuncionarioId().'#'.$receptor->getCopia();
              $i++;
          }

          $correspondencia['identificacion']['privacidad']['receptor']= $privacidad_receptor;

          $carga_edicion = Doctrine::getTable('Correspondencia_ReceptorOrganismo')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
          if(count($carga_edicion) > 0)
          {
              foreach($carga_edicion as $values) {
                  $correspondencia['receptor_externo']['copias'][] = $values->getOrganismoId().'#'.$values->getPersonaId().'#'.$values->getPersonaCargoId();
              }
          }


          $carga_edicion = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
          $destino = $this->executeDistribuidor($carga_edicion->getTipoFormatoId());
          eval('$form = new formato' . ucfirst($destino) . '();');

          $correspondencia['formato'] = $form->executeTraer($carga_edicion);
          $correspondencia['formato']['tipo_formato_id'] = $carga_edicion->getTipoFormatoId();

          $this->getUser()->setAttribute('correspondencia',$correspondencia);

          $carga_edicion = Doctrine::getTable('Correspondencia_TipoFormato')->find($carga_edicion->getTipoFormatoId());
          $this->formatos = array($carga_edicion->getId() => $carga_edicion->getNombre());
      }elseif($this->getUser()->getAttribute('correspondencia_padre_id')) {
          //CODIGO PARA CARGAR EMISORES COMO RECEPTORES DE FORMA AUTOMATICA
          $doc_padre = Doctrine::getTable('Correspondencia_Correspondencia')->find($this->getUser()->getAttribute('correspondencia_padre_id'));
          $funcionario_emisor= Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_padre_id'));

          if($doc_padre && count($funcionario_emisor) > 0) {
              foreach($funcionario_emisor as $val) {
                  $correspondencia['receptor']['copias'][] = $doc_padre->getEmisorUnidadId().'#'.$val->getFuncionarioId().'#N';
              }

              $this->getUser()->setAttribute('correspondencia',$correspondencia);
          }
      }

      if($this->getUser()->getAttribute('correspondencia')) // Volver a crear uno nuevo pero salio algun error
          $this->correspondencia = $this->getUser()->getAttribute('correspondencia');
  }

    public function executeVerificarTercerizacion(&$session_funcionario_unidad_id,&$session_funcionario_cargo_id,&$session_funcionario_id) {

          if(!$this->getUser()->getAttribute('tercerizado')){
            $session_funcionario_unidad_id = $this->getUser()->getAttribute('funcionario_unidad_id');
            $session_funcionario_cargo_id = $this->getUser()->getAttribute('funcionario_cargo_id');
            $session_funcionario_id = $this->getUser()->getAttribute('funcionario_id');
          } else {
            $tercerizado = $this->getUser()->getAttribute('tercerizado');
            $session_funcionario_unidad_id = $tercerizado['unidad_id'];
            $session_funcionario_cargo_id = $tercerizado['cargo_id'];
            $session_funcionario_id = $tercerizado['funcionario_id'];
          }
    }
    
  public function executeInverso($tipo_formato_id,$retorno)
  {
      $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);
      
      if(preg_match('/#P/', $tipo_formato_id)){
          $tipo_formato_id = explode('#',$tipo_formato_id);
          $tipo_formato_id = $tipo_formato_id[0];

          if($retorno == 'tipo_formato_id')
            return $tipo_formato_id;
          else if($retorno == 'unidad_id'){            
            return $session_funcionario_unidad_id;
          } else if($retorno == 'unidad_correlativo_id')
            return 'FALSE'; 
      } else{
          if($this->getUser()->getAttribute('formatos_legitimos'))
          {
              $formatos_legitimos = $this->getUser()->getAttribute('formatos_legitimos');
              $formatos_legitimos = $formatos_legitimos[$tipo_formato_id];
              $formatos_legitimos = explode('|',$formatos_legitimos);

              if($retorno == 'tipo_formato_id')
                return $formatos_legitimos[0];
              else if($retorno == 'unidad_id')
                return $formatos_legitimos[1];
              else if($retorno == 'unidad_correlativo_id')
                return $formatos_legitimos[2];
              else if($retorno == 'correlativos_formatos_id')
                return $formatos_legitimos[3];
          }
          else
              return $tipo_formato_id;
      }
  }

  public function executeReceptores(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
      $parametros = sfYaml::load($formato->getParametros());

      $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE);

      if($parametros['receptores']['unidades']['seteada']=='false') {
            if($parametros['receptores']['unidades']['tipos']=='false' && $parametros['receptores']['unidades']['especificas']=='false') {
                $unidades = array(); //NO SE CONFIGURO NINGUNA UNIDAD
            } else {
                $unidades_receptoras = array();

                foreach( $unidades as $clave=>$valor )
                    $unidades_receptoras[$clave] = false;

                if($parametros['receptores']['unidades']['tipos']!='false') {
                    foreach ($parametros['receptores']['unidades']['tipos'] as $unidad_tipo_parametro) {
                        foreach( $unidades as $clave=>$valor ) {
                            if($unidades_receptoras[$clave] == false && $clave != ''){
                                list($unidad_id, $tipo) = explode("&&", $clave);
                                if($tipo == $unidad_tipo_parametro)
                                    $unidades_receptoras[$clave] = true;
                            }
                        }
                    }
                }

                if($parametros['receptores']['unidades']['especificas']!='false') {
                    foreach ($parametros['receptores']['unidades']['especificas'] as $unidad_especificas_parametro) {
                        foreach( $unidades as $clave=>$valor ) {
                            if($unidades_receptoras[$clave] == false){
                                if($clave){
                                list($unidad_id, $tipo) = explode("&&", $clave);
                                if($unidad_id == $unidad_especificas_parametro)
                                    $unidades_receptoras[$clave] = true;
                                }
                            }
                        }
                    }
                }

                foreach( $unidades as $clave=>$valor )
                    if($unidades_receptoras[$clave] == false)
                        unset($unidades[$clave]);
            }
      } else {
          if($parametros['receptores']['unidades']['seteada']=='inicial') {

              if(!$this->getUser()->getAttribute('correspondencia_padre_id')){
                  $unidades = array();
              } else {
                  $flujo_correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($this->getUser()->getAttribute('correspondencia_padre_id'));
                  $firmantes_iniciales = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->findByCorrespondenciaId($flujo_correspondencia->getGrupoCorrespondencia());

                  $unidades_inicial = array();
                  foreach ($firmantes_iniciales as $firmante_inicial) {
                      $unidad_firmante = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($firmante_inicial->getFuncionarioId());
                      $unidades_inicial[$unidad_firmante[0]->getUnidadId()] = true;
                  }

                  foreach( $unidades as $clave=>$valor ) {
                      if($clave) {
                          list($unidad_id, $tipo) = explode("&&", $clave);

                        if(!isset($unidades_inicial[$unidad_id]))
                            unset($unidades[$clave]);
                      }
                  }
              }
          //CODIGO PARA HABILITAR SOLO RECEPTORES UN PASO ARRIBA Y UNIDADES DEL MISMO NIVEL
          }elseif($parametros['receptores']['unidades']['seteada']=='admin3') {
              $unidad_propia= $this->getUser()->getAttribute('funcionario_unidad_id');
              $unidad_propia_datos = Doctrine::getTable('Organigrama_Unidad')->find($unidad_propia);
              
              $padre= ''; $semejantes= array();
              if($unidad_propia_datos->getPadreId() !== null) {
                  $padre = Doctrine::getTable('Organigrama_Unidad')->find($unidad_propia_datos->getPadreId());
                  $semejantes = Doctrine::getTable('Organigrama_Unidad')->findByPadreId($unidad_propia_datos->getPadreId());
              }
              
              $add= array();
              foreach($semejantes as $value) {
                  if($value->getId() !== $unidad_propia) {
                      $add[]= $value->getId();
                  }
              }
              if($padre !== '') {
                  $add[]= $padre->getId();
              }
              
              foreach( $unidades as $clave=>$valor ) {
                if($clave) {
                    list($unidad_id, $tipo) = explode("&&", $clave);

                    if(!in_array($unidad_id, $add)) {
                        unset($unidades[$clave]);
                    }
                }
              }
              
              if(count($unidades) == 1) {
                  $unidades= array_filter($unidades);
              }
          }
      }

      $this->unidades = $unidades;

      if($this->getUser()->getAttribute('correspondencia'))
         $this->correspondencia = $this->getUser()->getAttribute('correspondencia');

      if($parametros['receptores']['unidades']['seteada']=='false' && $parametros['receptores']['unidades']['tipos']=='false')
          $grupoReceptores= Array();
      else
          $grupoReceptores = Doctrine::getTable('Correspondencia_GrupoReceptor')->getNombres("I");

      $this->grupoReceptores = $grupoReceptores;
      $this->editado = count($this->getUser()->getAttribute('correspondencia'));
  }

  public function executeReceptoresExternos(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
      $parametros = sfYaml::load($formato->getParametros());

      if($parametros['receptores']['externos']=='true'){
          if($this->getUser()->getAttribute('correspondencia'))
             $this->correspondencia = $this->getUser()->getAttribute('correspondencia');
      }
      else exit();
  }

    public function executeOrganismos(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');

        $req = Doctrine::getTable('Organismos_Organismo')->getNombres($string);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    }

    public function executePersonas(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');
        $organismo_id = $request->getParameter('organismo_id');

        $req = Doctrine::getTable('Organismos_Persona')->getNombres($string,$organismo_id);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombreSimple();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    }

    public function executePersonasCargos(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');
        $persona_id = $request->getParameter('persona_id');

        $req = Doctrine::getTable('Organismos_PersonaCargo')->getNombres($string,$persona_id);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    }

  public function executeFuncionariosReceptores(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
      $parametros = sfYaml::load($formato->getParametros());
      
      $this->funcionario_selected = 0;
      if($parametros['receptores']['funcionarios'][0] == 'todos') {
            $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('unidad_id')));
      }else {
          $cargo_tipos= Array();
          foreach($parametros['receptores']['funcionarios'] as $value) {
              $cargo_tipos[]= $value;
          }
          $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionariosCargoTipo(array($request->getParameter('unidad_id')), $cargo_tipos);
      }
  }
  
  public function executeEmisores(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($tipo_formato_id);
      $parametros = sfYaml::load($tipo_formato[0]->getParametros());

      $emisores = array(); $i=0;
      foreach ($parametros['emisores']['funcionarios'] as $tipo_firmates) {

          $avance_vb= 0;
          if($this->getUser()->getAttribute('correspondencia_id')){
              $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($this->getUser()->getAttribute('correspondencia_id'));
              $unidad_firmante = $correspondencia->getEmisorUnidadId();
              
              //LLAMADO A LIBRERIAS ADICIONAL PARA TERCERIZAR LISTAS DE EMISORES EN CASO REQUERIDO (EJ. PERMISOS, VACAS, REPOSOS)
              $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($this->getUser()->getAttribute('correspondencia_id'));
              $destino = $this->executeDistribuidor($formatos[0]->getTipoFormatoId());

              eval('$add = new formato' . ucfirst($destino) . '();');
              
              $adional = $add->executeAdditionalEmisor($this->getUser()->getAttribute('correspondencia_id'));

              //UTILIZADO PARA SABER SI EL DOC EDITADO YA TIENE AVANCE EN RUTA DE VB
              //OJO: VISTO BUENO PARA UN SOLO FIRMANTE
              $visto_bueno= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
              foreach($visto_bueno as $value) {
                  if($value->getStatus() == 'V')
                      $avance_vb++;
              }
          } else {
              $unidad_firmante = $this->executeInverso($request->getParameter('tipo_formato_id'),'unidad_id');
          }
          
          $this->executeVerificarTercerizacion($session_funcionario_unidad_id,$session_funcionario_cargo_id,$session_funcionario_id);

          if($tipo_firmates['tipo_cargos'][0]=='autorizados'){
                $emisores[$i] = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->firmantesUnidades($unidad_firmante,'autorizados');
                $tipos_emisores = 'Autorizados a firmar en el grupo';
          } else if($tipo_firmates['tipo_cargos'][0]=='todos') {
                $emisores[$i] = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->firmantesUnidades($unidad_firmante,'todos');
                $tipos_emisores = 'Cualquier funcionario de la unidad'; //ESTE ERROR ES IMPOSIBLE QUE SE MUESTRE
          } else if($tipo_firmates['tipo_cargos'][0]=='supervisor') {
                $cargo_padre = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);
                $supervisor = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findOneByCargoIdAndStatus($cargo_padre->getPadreId(),'A');
                $funcionario_firma = Doctrine::getTable('Funcionarios_Funcionario')->find($supervisor->getFuncionarioId());

                $nombre = $funcionario_firma->getPrimerNombre().' '.
                          $funcionario_firma->getSegundoNombre().' '.
                          $funcionario_firma->getPrimerApellido().' '.
                          $funcionario_firma->getSegundoApellido();

                $nombre = str_replace('  ', ' ', $nombre);

                if($funcionario_firma->getSexo()=='M')
                    $cargo = $supervisor->getOrganigrama_Cargo()->getOrganigrama_CargoTipo()->getMasculino();
                else
                    $cargo = $supervisor->getOrganigrama_Cargo()->getOrganigrama_CargoTipo()->getFemenino();

                $opciones = array();
                $opciones[$funcionario_firma->getId().'-'.$supervisor->getId()] = $nombre.' ('.$cargo.')';
                $emisores[$i] = $opciones;
                $tipos_emisores = 'Supervisor inmediato de quien redacta '; //ESTE ERROR ES IMPOSIBLE QUE SE MUESTRE
          } else if($tipo_firmates['tipo_cargos'][0]=='solicitante') {
                $funcionario_firma = Doctrine::getTable('Funcionarios_Funcionario')->find($session_funcionario_id);
                $funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findOneByFuncionarioIdAndCargoIdAndStatus($session_funcionario_id,$session_funcionario_cargo_id,'A');
                $cargo = Doctrine::getTable('Organigrama_Cargo')->find($session_funcionario_cargo_id);

                $nombre = $funcionario_firma->getPrimerNombre().' '.
                          $funcionario_firma->getSegundoNombre().' '.
                          $funcionario_firma->getPrimerApellido().' '.
                          $funcionario_firma->getSegundoApellido();

                $nombre = str_replace('  ', ' ', $nombre);

                if($funcionario_firma->getSexo()=='M')
                    $cargo = $cargo->getOrganigrama_CargoTipo()->getMasculino();
                else
                    $cargo = $cargo->getOrganigrama_CargoTipo()->getFemenino();

                $opciones = array();
                $opciones[$funcionario_firma->getId().'-'.$funcionario_cargo->getId()] = $nombre.' ('.$cargo.')';
                $emisores[$i] = $opciones;
                $tipos_emisores = 'Quien redacta el documento '; //ESTE ERROR ES IMPOSIBLE QUE SE MUESTRE
          } else {
              $emisores[$i] = Doctrine::getTable('Correspondencia_FuncionarioEmisor')->firmantesUnidades($unidad_firmante,'especificos',$tipo_firmates['tipo_cargos']);
              $tipos_emisores = '';
              foreach ($tipo_firmates['tipo_cargos'] as $tipo_cargos) {
                  $tipo_cargo = Doctrine::getTable('Organigrama_CargoTipo')->find($tipo_cargos);

                  //REVISAR VERSIONES ANTERIORES, SE COLOCA VALIDACION POR ERROR EN VACACIONES, PERMISOS Y REPOSOS
                  if(count($tipo_cargo) > 1) {
                      $tipos_emisores .= '&bull; '.$tipo_cargo->getNombre().'<br/>';
                  }
              }
          }

          $nombre_firma[$i] = $tipo_firmates['nombre_firma'];

          $i++;
      }

      if(count($emisores[0])==0){
          $this->tipos_emisores = $tipos_emisores;
      }

      $this->unidad_firmante = $unidad_firmante;
      $this->emisores = $emisores;
      $this->nombre_firma = $nombre_firma;
      $this->avance_vb= $avance_vb;

      if($this->getUser()->getAttribute('correspondencia'))
         $this->correspondencia = $this->getUser()->getAttribute('correspondencia');
  }

  public function executeAdjuntos(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($tipo_formato_id);
      $parametros = sfYaml::load($tipo_formato[0]->getParametros());

      if($parametros['options_create']['adjunto_archivo']=='true'){
          if($this->getUser()->getAttribute('correspondencia_id'))
              $this->adjuntos = Doctrine_Core::getTable('Correspondencia_AnexoArchivo')
                              ->createQuery('a')
                              ->where('correspondencia_id = ?',$this->getUser()->getAttribute('correspondencia_id'))
                              ->orderBy('id asc')
                              ->execute();
      } else { exit(); }
  }
  
  public function executePlantillas(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');
      $destino = $this->executeDistribuidor($tipo_formato_id);
      
      $this->plantillas = Doctrine::getTable('Correspondencia_Plantilla')->tipoFormatoDeFuncionario($tipo_formato_id,$this->getUser()->getAttribute('funcionario_id'));
      
      $this->setTemplate('../lib/'.$destino.'/plantillas');
  }

  public function executeGuardarPlantilla(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');
      $destino = $this->executeDistribuidor($tipo_formato_id);

      eval('$form = new formato'.ucfirst($destino).'();');

      $form->executeGuardarPlantilla($tipo_formato_id,$request->getParameter('plantilla'));
      exit();
  }
  
  public function executeEliminarPlantilla(sfWebRequest $request)
  {
      $plantilla_id = $request->getParameter('plantilla_id');
      
        try
        {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            Doctrine::getTable('Correspondencia_PlantillaFuncionario')
                      ->createQuery()
                      ->delete()
                      ->where('plantilla_id = ?', $plantilla_id)
                      ->andWhere('funcionario_id = ?', $this->getUser()->getAttribute('funcionario_id'))
                      ->execute();

            $plantilla_funcionario = Doctrine::getTable('Correspondencia_PlantillaFuncionario')->findOneByPlantillaId($plantilla_id);

            if(!$plantilla_funcionario){
                  Doctrine::getTable('Correspondencia_Plantilla')
                            ->createQuery()
                            ->delete()
                            ->where('id = ?', $plantilla_id)
                            ->execute();
            }
            
            $conn->commit();
        }

        catch(Exception $e){
            $conn->rollBack();
        }

      exit();
  }
  
  public function executeFisicos(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($tipo_formato_id);
      $parametros = sfYaml::load($tipo_formato[0]->getParametros());

      if($parametros['options_create']['adjunto_fisico']=='true'){
          $this->fisicos = Doctrine::getTable('Correspondencia_TipoAnexoFisico')
              ->createQuery('a')
              ->orderBy('nombre')->execute();

          if($this->getUser()->getAttribute('correspondencia_id'))
              $this->anexos_fisicos = Doctrine_Core::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($this->getUser()->getAttribute('correspondencia_id'));

      } else { exit(); }
  }

  public function executeDistribuidor($destino)
  {
      $destino_formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($destino);
      $destino = $destino_formato->getClasse();

      return $destino;
  }

  public function executeFormato(sfWebRequest $request)
  {
      $tipo_formato_id = $this->executeInverso($request->getParameter('tipo_formato_id'),'tipo_formato_id');

      $this->getUser()->setAttribute('tipo_formato_id',$tipo_formato_id);

        $this->fecha = new Correspondencia_FormatoForm();
        $destino = $this->executeDistribuidor($tipo_formato_id);

        if($this->getUser()->getAttribute('correspondencia'))
        {
           $correspondencia = $this->getUser()->getAttribute('correspondencia');
           if(isset($correspondencia['formato']))
               $this->formulario = $correspondencia['formato'];
        }

        $this->setTemplate('../lib/'.$destino.'/'.$destino.'Form');
  }

  public function executeValidador($formulario,&$messages,&$formato)
  {
        $destino = $this->executeDistribuidor($formulario["tipo_formato_id"]);
        eval('$form = new formato'.ucfirst($destino).'();');

        $form->executeValidar($formulario,$messages,$formato);
  }

  public function executeLibrerias(sfWebRequest $request)
  {
      $forma = $request->getParameter('forma');
      if($forma == 'undefined') $forma = 0;

      $tipo_formato_id = $this->executeInverso($forma,'tipo_formato_id');

        $destino = $this->executeDistribuidor($tipo_formato_id);
        eval('$form = new formato'.ucfirst($destino).'();');
        eval('$cadena = $form->execute'.$request->getParameter('func').'($request->getParameter(\'var\'));');

        echo $cadena; exit();
  }


  public function executeAdditionalLibrerias(sfWebRequest $request)
  {
      $libreria = $request->getParameter('libreria');
      $correspondencia_id = $request->getParameter('correspondencia_id');

      $formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($correspondencia_id);



        $destino = $this->executeDistribuidor($formato->getTipoFormatoId());
        eval('$form = new formato'.ucfirst($destino).'();');
        eval('$cadena = $form->executeAdditional'.ucfirst($libreria).'($request->getParameter(\'correspondencia_id\'));');

        if($libreria == 'enviar' || $libreria == 'anular'){
            $this->redirect('enviada/index');
        } elseif ($libreria == 'devolver') {
            $this->redirect('recibida/index');
        }

  }


  public function executeVer(sfWebRequest $request)
  {
      $id = $request->getParameter('a_id');
      $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->find($id);
      $this->getUser()->setAttribute('formato_id', $id);

      $destino = $this->executeDistribuidor($request->getParameter('forma'));
      eval('$form = new formato'.ucfirst($destino).'();');

      $this->valores = $form->executeTraer($correspondencia_formato);
      $this->setTemplate('ver'.$destino);
  }

  public function executeShow(sfWebRequest $request) {
        $correspondencia_id = $request->getParameter('idc');
        $op = $request->getParameter('op');
        $formatos = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($correspondencia_id);

        if (count($formatos) > 0) {
            $this->correspondencia_id = $correspondencia_id;
            $formato = $formatos[0];
            $this->formato = $formato;

            if($op){
                $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($formato->getTipoFormatoId());
                $parametros = sfYaml::load($tipo_formato[0]->getParametros());

                $descargas= '';
                if($parametros['options_object']['descargas']['pdf']=='true' || $parametros['options_object']['descargas']['odt']=='true' || $parametros['options_object']['descargas']['doc']=='true') {
                    $descargas = '<div style="position: absolute; width: 17px; padding: 1px; right: -23px; top: -1px; z-index: 100; background-color: #ffffff; border-width: 1px; border-radius: 0px 5px 5px 0px; border-style: solid">';
                        if($parametros['options_object']['descargas']['pdf']=='true') {
                        $descargas .= '<a style="padding-bottom: 2px" href="'.sfConfig::get('sf_app_correspondencia_url').'formatos/pdf?id='.$formato->getId().'" title="Descargar en PDF">';
                            $descargas .= '<img src="/images/icon/pdf.png">';
                        $descargas .= '</a><br/>';
                        }

                        if($parametros['options_object']['descargas']['odt']=='true') {
                        $descargas .= '<a style="padding-bottom: 2px" href="'.sfConfig::get('sf_app_correspondencia_url').'formatos/odt?id='.$formato->getId().'" title="Descargar en ODT (Linux - Canaima - Writer)">';
                            $descargas .= '<img src="/images/icon/odt.png">';
                        $descargas .= '</a><br/>';
                        }

                        if($parametros['options_object']['descargas']['doc']=='true') {
                        $descargas .= '<a href="'.sfConfig::get('sf_app_correspondencia_url').'formatos/doc?id='.$formato->getId().'" title="Descargar en DOC (Windows - Word)">';
                            $descargas .= '<img src="/images/icon/doc.png">';
                        $descargas .= '</a>';
                        }
                    $descargas .= '</div>';
                }

                echo $descargas;
                $this->op = $op;
            }

            $destino = $this->executeDistribuidor($formato->getTipoFormatoId());
            eval('$form = new formato' . ucfirst($destino) . '();');
            $this->valores = $form->executeTraer($formato);

            $this->setTemplate('../lib/'.$destino.'/'.$destino.'Show');

        } else {
            echo '<font class="f10n rojo">Error: no existe el tipo de documento.</font>';
            exit();
        }
    }

  public function executePdf(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->find($id);

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_formato->getCorrespondenciaId());
    $emisor=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_formato->getCorrespondenciaId(), TRUE);
    $receptores=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondenciaActual($correspondencia_formato->getCorrespondenciaId());
    $organismos=Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia_formato->getCorrespondenciaId());
    $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->find($id);

    $n_correspondencia_emisor = $correspondencia->getNCorrespondenciaEmisor();

    // ################ INIZIALIZAR EL OBJETO DE PDF  #################
    $config = sfTCPDFPluginConfigHandler::loadConfig('pdf_configs.yml');
    // pdf object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // settings

    // ################ FIN INIZIALIZAR EL OBJETO DE PDF  #################
    $destino = $this->executeDistribuidor($correspondencia_formato->get('tipo_formato_id'));
    eval('$form = new formato' . ucfirst($destino) . '();');

    $form->executePdf($pdf,$correspondencia_formato,$correspondencia,$emisor,$receptores,$organismos);

    exit();
  }

  public function executeOdt(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->find($id);

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($correspondencia_formato->get('correspondencia_id'));
    $emisor=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_formato->get('correspondencia_id'), TRUE);
    $receptores=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($correspondencia_formato->get('correspondencia_id'));
    $organismos=Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($correspondencia_formato->get('correspondencia_id'));
    $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->find($id);

    $n_correspondencia_emisor = $correspondencia->getNCorrespondenciaEmisor();

    $destino = $this->executeDistribuidor($correspondencia_formato->get('tipo_formato_id'));
    eval('$form = new formato' . ucfirst($destino) . '();');

    $form->executeOdt($correspondencia_formato,$correspondencia,$emisor,$receptores,$organismos);

    exit();
  }

  public function executeEnviarCopiaEmail(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $copia_emails = $request->getParameter('emails');
    $copia_comentario = $request->getParameter('comentario');

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
    $emisor=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($id);
    $receptores=Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);
    $organismos=Doctrine::getTable('Correspondencia_ReceptorOrganismo')->filtrarPorCorrespondencia($id);
    $correspondencia_formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($id);

    $emisor_copia_funcionario=Doctrine::getTable('Funcionarios_Funcionario')->find($this->getUser()->getAttribute('funcionario_id'));
    $emisor_copia_unidad_cargo=Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($this->getUser()->getAttribute('funcionario_id'));

    $emisor_copia = $emisor_copia_unidad_cargo[0]->getUnidad().' / '.
                    $emisor_copia_unidad_cargo[0]->getCargoTipo().' / '.
                    $emisor_copia_funcionario->getPrimerNombre().' '.$emisor_copia_funcionario->getSegundoNombre().' '.
                    $emisor_copia_funcionario->getPrimerApellido().' '.$emisor_copia_funcionario->getSegundoApellido();


    $n_correspondencia_emisor = $correspondencia->getNCorrespondenciaEmisor();

    $adjuntos= Doctrine::getTable('Correspondencia_AnexoArchivo')->findByCorrespondenciaId($id);

    $adjuntos_libres= array();
    if(count($adjuntos) > 0) {
        foreach ($adjuntos as $values) {
            $adjuntos_libres[]= $values->getRuta();
        }
    }

    $destino = $this->executeDistribuidor($correspondencia_formato->get('tipo_formato_id'));
    eval('$form = new formato' . ucfirst($destino) . '();');

    $form->executeEmail($correspondencia_formato,$correspondencia,$emisor,$receptores,$organismos, $copia_emails, $copia_comentario, $emisor_copia, $adjuntos_libres);

    exit();
  }

    public function executeCreate(sfWebRequest $request)
    {
      $correspondencia = $request->getParameter('correspondencia');
      $formato = $correspondencia['formato'];
      $prioridad = $correspondencia['identificacion']['prioridad'];

      $metodo_correlativo = 'I';
      if(isset($correspondencia['identificacion']['metodo_correlativo']))
        $metodo_correlativo = $correspondencia['identificacion']['metodo_correlativo'];

      //echo $formato['tipo_formato_id'].'<br/>';
      $tipo_formato_id = $this->executeInverso($formato['tipo_formato_id'],'tipo_formato_id');
      //echo $tipo_formato_id.'<br/>';
      $unidad_id = $this->executeInverso($formato['tipo_formato_id'],'unidad_id');
      $unidad_correlativo_id = $this->executeInverso($formato['tipo_formato_id'],'unidad_correlativo_id');
      //echo $unidad_correlativo_id.'<br/>';
      $formato['tipo_formato_id'] = $tipo_formato_id;


      if (isset($correspondencia['identificacion']['privacidad']) && isset($correspondencia['identificacion']['privacidad']['emisor'])) {
            if ($correspondencia['identificacion']['privacidad']['emisor'] == 'E')
                $privado_emisor = 'S';
            else
                $privado_emisor = 'N';
      }else {
            $privado_emisor = 'N';
      }


      $messages=array();
      $texto_puro = new herramientas();

      //inicio Capturar datos de los adjuntos seteados en caso de existir
        foreach ($request->getFiles() as $file) {
            $file = $file['adjunto'];

            if(isset($file['seteado'])){
                $seteados = $file['seteado'];
                foreach ($seteados as $campo => $seteado) {
                    $formato[$campo] = $seteado;
                }
            }
        }
        //fin Capturar datos de los adjuntos seteados en caso de existir

      if($this->getUser()->getAttribute('correspondencia_id'))
          $datos_formato = Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($this->getUser()->getAttribute('correspondencia_id'));
      else
          $datos_formato = new Correspondencia_Formato();

      $this->executeValidador($formato,$messages,$datos_formato);

      if(!$messages)
      {
        try
        {
            $manager = Doctrine_Manager::getInstance();
            $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
          // INICIO ACONDICIONAR EL CORRELATIVO
          // INICIO ACONDICIONAR EL CORRELATIVO
          // INICIO ACONDICIONAR EL CORRELATIVO
          // INICIO ACONDICIONAR EL CORRELATIVO

            if($this->getUser()->getAttribute('correspondencia_id'))
                $datos_identificacion = Doctrine::getTable('Correspondencia_Correspondencia')->find($this->getUser()->getAttribute('correspondencia_id'));
            else
            {
                if($metodo_correlativo == 'F') {
                    $correlativo_final = crypt(time(),time());
                }
                else
                {
                    $correlativo_activo = new correlativosGenerador();
                    $listo = 0;
                    while ($listo == 0) {
                        if($unidad_correlativo_id=='FALSE'){
                            $correlativo_final = $correlativo_activo->generarDeFuncionario();
                            $privado_emisor = 'S';
                        } else {
                            $correlativo_final = $correlativo_activo->generarDeUnidad($unidad_correlativo_id);
                        }

                        $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($correlativo_final);
                        if (!$correspondencia_find) { $listo = 1; }
                    }
                }


                $datos_identificacion = new Correspondencia_Correspondencia();

                if($unidad_correlativo_id=='FALSE'){
                    $correlativos_funcionario = Doctrine::getTable('Correspondencia_FuncionarioCorrelativo')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
                    $correlativos_funcionario->setUltimoCorrelativo($correlativo_final);
                    $correlativos_funcionario->setSecuencia($correlativos_funcionario->getSecuencia()+1);
                    $correlativos_funcionario->save();

                    $datos_identificacion->setFuncionarioCorrelativoId($correlativos_funcionario->getId());

                }else{
                    $correlativos_unidad = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($unidad_correlativo_id);
                    $correlativos_unidad->setUltimoCorrelativo($correlativo_final);
                    $correlativos_unidad->setSecuencia($correlativos_unidad->getSecuencia()+1);
                    $correlativos_unidad->save();

                    $datos_identificacion->setUnidadCorrelativoId($unidad_correlativo_id);
                }

                $datos_identificacion->setNCorrespondenciaEmisor($correlativo_final);
                $datos_identificacion->setPrivado($privado_emisor);
                $datos_identificacion->setEmisorUnidadId($unidad_id);
                $datos_identificacion->setPrioridad($prioridad);

                $datos_identificacion->save();

            }

            if($datos_identificacion->getStatus()=='D'){
                $datos_identificacion->setStatus('C');
                $datos_identificacion->save();
            }

            if($metodo_correlativo == 'F'){
                $datos_identificacion->setNCorrespondenciaEmisor($datos_identificacion->getId());
                $datos_identificacion->save();
            }

            if($datos_identificacion->getGrupoCorrespondencia()==null){
                $datos_identificacion->setGrupoCorrespondencia($datos_identificacion->getId());
                $datos_identificacion->save();
            }

            $datos_identificacion->setPrivado($privado_emisor);
            $datos_identificacion->setPrioridad($prioridad);
            $datos_identificacion->setIdUpdate($this->getUser()->getAttribute('usuario_id'));
            $datos_identificacion->save();

          // FIN ACONDICIONAR EL CORRELATIVO
          // FIN ACONDICIONAR EL CORRELATIVO
          // FIN ACONDICIONAR EL CORRELATIVO
          // FIN ACONDICIONAR EL CORRELATIVO

          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO

          $datos_formato->setTipoFormatoId($formato['tipo_formato_id']);
          $datos_formato->setCorrespondenciaId($datos_identificacion->getId());
          $datos_formato->save();
          //$formatos = $correspondencia['formato'];

          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO
          // FIN ACONDICIONAR FORMATO
          
          
        //
        //COMUNICACIONES
        //
        //NOTIFICACIONES PARA EL CREADOR DE LA CORRESPONDENCIA
        $notificacion = new formatosNotify();
        if($datos_identificacion->getIdCreate() != $this->getUser()->getAttribute('usuario_id')) {

            $notificacion->notifyDesk($datos_identificacion->getIdCreate(), $this->getUser()->getAttribute('usuario_id'), $datos_identificacion->getNCorrespondenciaEmisor(), $datos_identificacion->getId(), 'normal');

            $notificacion->notifyEmail($datos_identificacion->getIdCreate(), $this->getUser()->getAttribute('usuario_id'), $datos_identificacion->getNCorrespondenciaEmisor(), 'normal');
        }
        
        //NOTIFICACIONES EN CASO DE QUE LA CORRESPONDENCIA TENGA VISTOS BUENOS
        $all_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaId($datos_identificacion->getId());
        if(count($all_vb)> 0) {
            $notis_vb= array();
            foreach($all_vb as $value) {
                if($value->getStatus()== 'V' && $value->getFuncionarioId()!= $this->getUser()->getAttribute('usuario_id')) {
                    $notis_vb[]= $value->getFuncionarioId();
                }
            }
            
            foreach($notis_vb as $funcionario_afectado) {
                $notificacion->notifyDesk($funcionario_afectado, $this->getUser()->getAttribute('usuario_id'), $datos_identificacion->getNCorrespondenciaEmisor(), $datos_identificacion->getId(), 'vistobueno');

                $notificacion->notifyEmail($funcionario_afectado, $this->getUser()->getAttribute('usuario_id'), $datos_identificacion->getNCorrespondenciaEmisor(), 'vistobueno');
            }
        }
        //
        //FIN DE COMUNICACIONES
        //
          

          // INICIO ACONDICIONAR EMISOR
          // INICIO ACONDICIONAR EMISOR
          // INICIO ACONDICIONAR EMISOR
          // INICIO ACONDICIONAR EMISOR

          $emisores = $correspondencia['emisor'];
          if($emisores[0]['funcionario_id']!=null){
              $delete_emisores = Doctrine::getTable('Correspondencia_FuncionarioEmisor')
                ->createQuery()
                ->delete()
                ->where('correspondencia_id = ?', $datos_identificacion->getId())
                ->execute();

              $cacheDriver->delete('correspondencia_enviada_list_funcionario_emisor_'.$datos_identificacion->getId());

              $emisores_listos='';
              foreach ($emisores as $emisor) {

                  list($funcionario_id,$funcionario_cargo_id) = explode('-', $emisor['funcionario_id']);

                  if(!preg_match('/.'.$funcionario_id.'./', $emisores_listos))
                  {
                      $datos_emisor = new Correspondencia_FuncionarioEmisor();
                      $datos_emisor->setCorrespondenciaId($datos_identificacion->getId());
                      $datos_emisor->setFuncionarioId($funcionario_id);
                      $datos_emisor->setFuncionarioCargoId($funcionario_cargo_id);
                      $datos_emisor->save();

                      $emisores_listos .= '.'.$funcionario_id.'.';
                  }
              }
          }
          // FIN ACONDICIONAR EMISOR
          // FIN ACONDICIONAR EMISOR
          // FIN ACONDICIONAR EMISOR
          // FIN ACONDICIONAR EMISOR

          // INICIO ACONDICIONAR RECEPTOR
          // INICIO ACONDICIONAR RECEPTOR
          // INICIO ACONDICIONAR RECEPTOR
          // INICIO ACONDICIONAR RECEPTOR

          $delete_receptores = Doctrine::getTable('Correspondencia_Receptor')
            ->createQuery()
            ->delete()
            ->where('correspondencia_id = ?', $datos_identificacion->getId())
            ->execute();

          $cacheDriver->delete('correspondencia_enviada_list_receptor_'.$datos_identificacion->getId());

          $receptores = $correspondencia['receptor'];
          if($receptores['funcionario_id']!=null || isset($receptores['copias'])){

              $cacheDriver->delete('correspondencia_enviada_list_receptor_'.$datos_identificacion->getId());

              $receptores_cc = array(); if(isset($receptores['copias'])) $receptores_cc = $receptores['copias'];

              if($receptores['unidad_id']!='') {
                $copia_fuera = 'N'; if(isset($receptores['copia'])) { $copia_fuera = 'S'; }
                $receptor_fuera = array($receptores['unidad_id'].'#'.$receptores['funcionario_id'].'#'.$copia_fuera);
                $receptores = array_merge($receptores_cc, $receptor_fuera); }
              else
              { $receptores = $receptores_cc; }

              if(isset($correspondencia['identificacion']['privacidad']) && isset($correspondencia['identificacion']['privacidad']['receptor'])) {
                  if ($correspondencia['identificacion']['privacidad']['receptor'] == 'R')
                    $privado_receptor = 'S';
                  else
                    $privado_receptor = 'N';
              }else {
                  $privado_receptor= 'N';
              }

              $receptores = array_unique($receptores);
//              echo "<pre>"; print_r($receptores); exit();
              foreach ($receptores as $receptor) {
                  list($unidad_id, $funcionario_id, $copia) = explode('#', $receptor);

                  if($unidad_id != 0 || $funcionario_id != 0) {
                    $datos_receptor = new Correspondencia_Receptor();
                    $datos_receptor->setCorrespondenciaId($datos_identificacion->getId());
                    $datos_receptor->setUnidadId((int)$unidad_id);
                    $datos_receptor->setFuncionarioId((int)$funcionario_id);
                    $datos_receptor->setCopia($copia);
                    $datos_receptor->setPrivado($privado_receptor);
                    $datos_receptor->setEstablecido('S');

                    @$datos_receptor->save();
                  }
              }

              unset ($correspondencia['receptor']);
              $correspondencia['receptor']['copias'] = $receptores;
          }

          // RECEPTOR EXTERNO
          // RECEPTOR EXTERNO
          // RECEPTOR EXTERNO

            if(isset($correspondencia['receptor_externo'])) {
                $receptor_externo = $correspondencia['receptor_externo'];

                if($correspondencia['receptor_externo']['organismo_id']!=0 || isset($correspondencia['receptor_externo']['copias'])) {
                    $delete_receptores_externos = Doctrine::getTable('Correspondencia_ReceptorOrganismo')
                    ->createQuery()
                    ->delete()
                    ->where('correspondencia_id = ?', $datos_identificacion->getId())
                    ->execute();

                    $cacheDriver->delete('correspondencia_enviada_list_receptor_externos_'.$datos_identificacion->getId());
                }


                if($correspondencia['receptor_externo']['organismo_id']!=0) {
                    $datos_receptor_externo = new Correspondencia_ReceptorOrganismo();
                    $datos_receptor_externo->setCorrespondenciaId($datos_identificacion->getId());
                    $datos_receptor_externo->setOrganismoId($receptor_externo['organismo_id']);
                    if($receptor_externo['persona_id']!=null)
                        $datos_receptor_externo->setPersonaId($receptor_externo['persona_id']);
                    if($receptor_externo['persona_cargo_id']!=null)
                        $datos_receptor_externo->setPersonaCargoId($receptor_externo['persona_cargo_id']);

                    $datos_receptor_externo->save();
                }

                if(isset($correspondencia['receptor_externo']['copias'])){
                    $receptores_externos_cc= Array();
                    if(isset($receptor_externo['copias']))
                            $receptores_externos_cc= $receptor_externo['copias'];

                    //guarda las copias de receptores externos
                    if(count($receptores_externos_cc) > 0) {
                        foreach($receptores_externos_cc as $key => $values) {
                            $parts_copias= explode('#', $values);

                            $datos_receptor_externo = new Correspondencia_ReceptorOrganismo();
                            $datos_receptor_externo->setCorrespondenciaId($datos_identificacion->getId());
                            $datos_receptor_externo->setOrganismoId($parts_copias[0]);
                            if($parts_copias[1]!=null)
                                $datos_receptor_externo->setPersonaId($parts_copias[1]);
                            if($parts_copias[2]!=null)
                                $datos_receptor_externo->setPersonaCargoId($parts_copias[2]);

                            $datos_receptor_externo->save();
                        }
                    }

                    $correspondencia['receptor_externo'] = $receptor_externo;
                }
            }

          // FIN ACONDICIONAR RECEPTOR
          // FIN ACONDICIONAR RECEPTOR
          // FIN ACONDICIONAR RECEPTOR
          // FIN ACONDICIONAR RECEPTOR



          // INICIO ACONDICIONAR ADJUNTOS
          // INICIO ACONDICIONAR ADJUNTOS
          // INICIO ACONDICIONAR ADJUNTOS
          // INICIO ACONDICIONAR ADJUNTOS
          // INICIO ACONDICIONAR ADJUNTOS

            $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->find($formato['tipo_formato_id']);

            if(isset($correspondencia['adjunto'])){
                $adjuntos = $correspondencia['adjunto'];
                if(isset($adjuntos['libre_delet']) && $adjuntos['libre_delet']!='.'){
                    $files_delete = str_replace('.#', '', $adjuntos['libre_delet']);
                    $files_delete = explode( "#", $files_delete);

                    foreach ($files_delete as $file_delete) {
                        $anexo_archivo = Doctrine::getTable('Correspondencia_AnexoArchivo')->find($file_delete);
                        $ruta = "uploads/correspondencia/".$anexo_archivo->getRuta();
                        if (file_exists($ruta)) {
                            unlink($ruta);
                        }

                        $anexo_archivo->delete();
                    }
                }
            }

            foreach ($request->getFiles() as $file) {
                $ruta_classe = sfConfig::get('sf_upload_dir').'/correspondencia/'.$tipo_formato->getClasse();
                if(!is_dir($ruta_classe)){ mkdir($ruta_classe, 0777); chmod($ruta_classe, 0777); }

                $file = $file['adjunto'];

                if(isset($file['libre'])){
                    $libres = $file['libre'];
                    foreach ($libres as $libre) {
                        $theFileName = $datos_identificacion->getNCorrespondenciaEmisor().'__'.str_replace(' ','_',$texto_puro->limpiar_metas($libre['name']));

                        $ruta_libres = $ruta_classe.'/libres';
                        if(!is_dir($ruta_libres)){ mkdir($ruta_libres, 0777); chmod($ruta_libres, 0777); }

                        if (file_exists($ruta_libres.'/'.$theFileName)) {
                            $theFileName = date('d-m-Y_h:i:s').'_'.$theFileName;
                            $libre['name'] = $libre['name'].' ('.date('d-m-Y h:i:s').')';
                        }

                        if (move_uploaded_file($libre['tmp_name'], $ruta_libres.'/'.$theFileName)){
                            // El archivo ha sido cargado correctamente
                            $datos_anexo_archivo = new Correspondencia_AnexoArchivo();
                            $datos_anexo_archivo->setCorrespondenciaId($datos_identificacion->getId());
                            $datos_anexo_archivo->setNombreOriginal($libre['name']);
                            $datos_anexo_archivo->setRuta($tipo_formato->getClasse().'/libres/'.$theFileName);
                            $datos_anexo_archivo->setTipoAnexoArchivo($libre['type']);
                            $datos_anexo_archivo->save();
                        }else{
                            if($libre['tmp_name']!=null)
                                $this->getUser()->setFlash('error', 'Ocurrió algún error al subir el archivo. No pudo guardarse.');
                        }
                    }
                }

                if(isset($file['seteado'])){
                    $seteados = $file['seteado'];
                    foreach ($seteados as $campo => $seteado) {
                        $theFileName = $datos_identificacion->getNCorrespondenciaEmisor().'__'.$campo.'__'.str_replace(' ','_',$texto_puro->limpiar_metas($seteado['name']));

                        $ruta_seteados = $ruta_classe.'/seteados';
                        if(!is_dir($ruta_seteados)){ mkdir($ruta_seteados, 0777); chmod($ruta_seteados, 0777); }

                        $ruta_correspondencia = $ruta_seteados.'/'.$datos_identificacion->getNCorrespondenciaEmisor();
                        if(!is_dir($ruta_correspondencia)){ mkdir($ruta_correspondencia, 0777); chmod($ruta_correspondencia, 0777); }

                        if (move_uploaded_file($seteado['tmp_name'], $ruta_correspondencia.'/'.$theFileName)){
                            foreach ($datos_formato as $campo_numero => $valor) {
//                                $tmp = 'campo_'.$campo;
                                $tmp = $campo;
                                if($valor==$tmp)
                                    $campo_actualizar = str_replace(' ', '', ucwords(str_replace('_', ' ', $campo_numero)));
                            }

                            eval('$datos_formato->set'.$campo_actualizar.'("'.$tipo_formato->getClasse().'/seteados/'.$datos_identificacion->getNCorrespondenciaEmisor().'/'.$theFileName.'");');
                            $datos_formato->save();

                        }else{
                            if($libre['tmp_name']!=null)
                                $this->getUser()->setFlash('error', 'Ocurrió algún error al subir el archivo. No pudo guardarse.');
                        }
                    }
                }
            }

            // FIN ACONDICIONAR ADJUNTOS
            // FIN ACONDICIONAR ADJUNTOS
            // FIN ACONDICIONAR ADJUNTOS
            // FIN ACONDICIONAR ADJUNTOS


          // INICIO ACONDICIONAR FISICOS
          // INICIO ACONDICIONAR FISICOS
          // INICIO ACONDICIONAR FISICOS
          // INICIO ACONDICIONAR FISICOS
          // INICIO ACONDICIONAR FISICOS

            if(isset($correspondencia['fisicos'])){
                $fisicos = $correspondencia['fisicos'];

                if($fisicos['id']!=''){
                    $datos_fisicos = new Correspondencia_AnexoFisico();
                    $datos_fisicos->setCorrespondenciaId($datos_identificacion->getId());
                    $datos_fisicos->setTipoAnexoFisicoId($fisicos['id']);
                    $datos_fisicos->setObservacion($fisicos['observacion']);
                    $datos_fisicos->save();
                }

                if(isset($fisicos['otros'])){
                    if(count($fisicos['otros'])>0){
                        foreach ($fisicos['otros'] as $otros_fisicos) {
                            list($tipo_fisico_id,$obsevacion_fisico) = explode('#', $otros_fisicos);
                            $datos_fisicos = new Correspondencia_AnexoFisico();
                            $datos_fisicos->setCorrespondenciaId($datos_identificacion->getId());
                            $datos_fisicos->setTipoAnexoFisicoId($tipo_fisico_id);
                            $datos_fisicos->setObservacion($obsevacion_fisico);
                            $datos_fisicos->save();
                        }
                    }
                }

                if(isset($fisicos['delet']) && $fisicos['delet']!='.'){
                    $fisicos_delete = str_replace('.#', '', $fisicos['delet']);
                    $fisicos_delete = explode( "#", $fisicos_delete);

                    // NO SE PUEDE HACER UN DELETE MULTIPLE YA QUE SE DEBE BORRAR LA CACHE DOCTRINE
                    foreach ($fisicos_delete as $fisico_delete) {
                        $fisico_delete_ok = Doctrine::getTable('Correspondencia_AnexoFisico')->find($fisico_delete);
                        $fisico_delete_ok->delete();
                    }
                }
            }

          // FIN ACONDICIONAR FISICOS
          // FIN ACONDICIONAR FISICOS
          // FIN ACONDICIONAR FISICOS
          // FIN ACONDICIONAR FISICOS



          // INICIO LLAMADO LIBRERIAS MASTER CREAR
          // INICIO LLAMADO LIBRERIAS MASTER CREAR
          // INICIO LLAMADO LIBRERIAS MASTER CREAR
          // INICIO LLAMADO LIBRERIAS MASTER CREAR

            $parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
            $parametros = sfYaml::load($parametros->getParametros());

            if($parametros['additional_actions']['crear']=='true'){
                $destino = $this->executeDistribuidor($tipo_formato_id);
                eval('$form = new formato' . ucfirst($destino) . '();');
                $form->executeAdditionalCrear($datos_identificacion->getId());
            }

          // FIN LLAMADO LIBRERIAS MASTER CREAR
          // FIN LLAMADO LIBRERIAS MASTER CREAR
          // FIN LLAMADO LIBRERIAS MASTER CREAR
          // FIN LLAMADO LIBRERIAS MASTER CREAR



          // INICIO ACONDICIONAR VISTO BUENO
          // INICIO ACONDICIONAR VISTO BUENO
          // INICIO ACONDICIONAR VISTO BUENO
          // INICIO ACONDICIONAR VISTO BUENO

            if(($parametros['options_create']['vistobueno']=='G' || $parametros['options_create']['vistobueno']=='F') || $parametros['options_create']['vistobueno_dinamico']=='true') {
                    $vistobueno= $request->getParameter('vistobuenos');

                    if($vistobueno['act'] == 'N') {
                        $delete_vb = Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')
                                        ->createQuery()
                                        ->delete()
                                        ->where('correspondencia_id = ?', $datos_identificacion->getId())
                                        ->execute();

                        if(isset($vistobueno['funcionarios'])) {
                            $orden_invertido= count($vistobueno['funcionarios']);
                            foreach($vistobueno['funcionarios'] as $funcionario) {
                                list($id_funcionario, $id_cargo, $status) = explode('-', $funcionario);

                                $datos_vistobueno = new Correspondencia_CorrespondenciaVistobueno();
                                $datos_vistobueno->setCorrespondenciaId($datos_identificacion->getId());
                                $datos_vistobueno->setFuncionarioId($id_funcionario);
                                $datos_vistobueno->setFuncionarioCargoId($id_cargo);
                                if($status== 'A')
                                    $datos_vistobueno->setStatus('E');
                                else
                                    $datos_vistobueno->setStatus('D');
                                //SETEA EL MISMO ORDEN DE LA LISTA EN EL FORMULARIO PERO INVERTIDO
                                $datos_vistobueno->setOrden($orden_invertido);
                                //SETEA EL TURNO ACTIVO AL PRIMER FUNCIONARIO DE VB PARA QUE SOLO EL LA VEA
                                if($orden_invertido == 1)
                                    $datos_vistobueno->setTurno(TRUE);
                                else
                                    $datos_vistobueno->setTurno(FALSE);

                                $datos_vistobueno->save();

                                $orden_invertido--;
                            }
                        }
                    }else {
                        //CASO TIPICO DONDE SE EDITA EL DOC SIN TOCAR VB
                        //LIMPIA TODA LA RUTA DE VB
                        //SETEA STATUS EN ESPERA TODA LA RUTA PARA ESTE DOC
                        $all_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->vistobuenoCorrespondenciaAsc($datos_identificacion->getId());

                        if(count($all_vb)> 0) {
                            $q = Doctrine_Query::create($conn);
                              $q->update('Correspondencia_CorrespondenciaVistobueno')
                                ->set('status', '?', 'E')
                                ->set('turno', '?', FALSE)
                                ->where('correspondencia_id = ?', $datos_identificacion->getId())
                                ->andWhereNotIn('status', array('D'))
                                ->execute();
                            //BUSCA EL TURNO DE ORDEN MAS BAJO QUE NO SEA STATUS D

                            $orden= 1;
                            foreach($all_vb as $value) {
                                $new_turn= '';
                                if($value->getOrden()== $orden && $value->getStatus() != 'D') {
                                    $new_turn= $value->getId();
                                    break;
                                }
                                $orden++;
                            }
                            if($new_turn != '') {
                                $registro_vb= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->find($new_turn);
                                $registro_vb->setTurno(TRUE);
                                $registro_vb->save();
                            }
                        }
                    }
                }

          // FIN ACONDICIONAR VISTO BUENO
          // FIN ACONDICIONAR VISTO BUENO
          // FIN ACONDICIONAR VISTO BUENO
          // FIN ACONDICIONAR VISTO BUENO
            
            if($this->getUser()->getAttribute('correspondencia_padre_id'))
            {
                $unidades_autorizadas = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado(sfContext::getInstance()->getUser()->getAttribute('funcionario_id'));

                $i=0;
                foreach ($unidades_autorizadas as $unidad_autorizada) {
                    $unidad_ids[$i] = $unidad_autorizada->getAutorizadaUnidadId();
                    $i++;
                }

                $correspondencia_receptores = Doctrine::getTable('Correspondencia_Receptor')->filtrarRespuestaExacta($this->getUser()->getAttribute('correspondencia_padre_id'),$unidad_ids);

                $exacta=0;
                foreach ($correspondencia_receptores as $correspondencia_receptor) {
                    if(preg_match('/.'.$correspondencia_receptor->getFuncionarioId().'./', $emisores_listos))
                           $exacta = $correspondencia_receptor->getId();
                }

                if($exacta==0) $exacta=$correspondencia_receptor->getId();
                $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->find($exacta);

                if($correspondencia_receptor->getRespuestaCorrespondenciaIds()==null)
                    $correspondencia_receptor->setRespuestaCorrespondenciaIds(".-".$datos_identificacion->getId()."-.");
                else
                    $correspondencia_receptor->setRespuestaCorrespondenciaIds($correspondencia_receptor->getRespuestaCorrespondenciaIds().".-".$datos_identificacion->getId()."-.");

                $correspondencia_receptor->save();
            }

            $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            if($ultima_tocada){
                $ultima_tocada->setCorrespondenciaEnviadaId($datos_identificacion->getId());
                @$ultima_tocada->save();
            } else {
                $ultima_tocada = new Correspondencia_UltimaVista();
                $ultima_tocada->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
                $ultima_tocada->setCorrespondenciaEnviadaId($datos_identificacion->getId());
                $ultima_tocada->setCorrespondenciaRecibidaId($datos_identificacion->getId());
                $ultima_tocada->setCorrespondenciaExternaId($datos_identificacion->getId());
                $ultima_tocada->save();
            }

            $conn->commit();

            if($this->getUser()->getAttribute('correspondencia_id'))
                $this->getUser()->setFlash('notice', 'La correspondencia número '.$datos_identificacion->getNCorrespondenciaEmisor().' se ha actualizado con éxito');
            else
                $this->getUser()->setFlash('notice', 'La correspondencia se ha guardado éxitosamente con el número '.$datos_identificacion->getNCorrespondenciaEmisor());

            $this->getUser()->setAttribute('correspondencia_id', $datos_identificacion->getId());
        }

        catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnologia.');

            $this->getUser()->setAttribute('correspondencia', $correspondencia);
            $this->redirect(sfConfig::get('sf_app_correspondencia_url').'formatos/index');
        }

      }
      else
      {
        $this->getUser()->setFlash('error', 'El elemento no se ha guardado porque se ha producido algún error.');

        foreach ($messages as $session => $mensaje)
        {
            $this->getUser()->setAttribute($session, $mensaje);
        }

        //El receptor seleccionado es colocado como una copia para mostrarlo luego de un error
        $copia = 'N';
            if ($correspondencia['receptor']['unidad_id'] != '' && $correspondencia['receptor']['funcionario_id'] != '') {
                if (isset($correspondencia['receptor']['copia']))
                    $copia = 'S';
                $correspondencia['receptor']['copias'][] = $correspondencia['receptor']['unidad_id'] . '#' . $correspondencia['receptor']['funcionario_id'] . '#' . $copia;
                unset($correspondencia['receptor']['unidad_id']);
                unset($correspondencia['receptor']['funcionario_id']);
                if (isset($correspondencia['receptor']['copia']))
                    unset($correspondencia['receptor']['copia']);
            }

            if ($correspondencia['receptor_externo']['organismo_id'] != '' && $correspondencia['receptor_externo']['persona_id']!= '' && $correspondencia['receptor_externo']['persona_cargo_id'] != '') {
                $correspondencia['receptor_externo']['copias'][]= $correspondencia['receptor_externo']['organismo_id'].'#'.$correspondencia['receptor_externo']['persona_id'].'#'.$correspondencia['receptor_externo']['persona_cargo_id'];
                unset($correspondencia['receptor_externo']['organismo_id']);
                unset($correspondencia['receptor_externo']['persona_id']);
                unset($correspondencia['receptor_externo']['persona_cargo_id']);
            }

        $this->getUser()->setAttribute('correspondencia', $correspondencia);
        $this->redirect(sfConfig::get('sf_app_correspondencia_url').'formatos/index');
      }

      if($request->getParameter('save_and_add')){
        $this->getUser()->getAttributeHolder()->remove('correspondencia');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
        $this->getUser()->getAttributeHolder()->remove('seguimiento_externa');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_padre_id');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_formulario');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_grupo');
        $this->getUser()->getAttributeHolder()->remove('pag_seguimiento_atras');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_n_emisor');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_privado');
        $this->getUser()->getAttributeHolder()->remove('correspondencia_emisor_unidad');
        $this->getUser()->getAttributeHolder()->remove('formatos_correlativo');

        $this->redirect(sfConfig::get('sf_app_correspondencia_url').'formatos/index');
      } else {
          if($this->getUser()->getAttribute('call_module_master')){
            $this->redirect($this->getUser()->getAttribute('call_module_master'));
          } else {
            $this->redirect(sfConfig::get('sf_app_correspondencia_url').'enviada/index');
          }
      }
      $this->nasasdasd = 0;
  }

//  public function executeNotiEdicion(sfWebRequest $request)
//  {
//        $id = $request->getParameter('id');
//
//        $correspondencia = Doctrine::getTable('Correspondencia_correspondencia')->find($id);
//        $correspondencia->setEditado(null);
//        $correspondencia->save();
//        $this->redirect(sfConfig::get('sf_app_acceso_url') . 'usuario/session');
//  }

  public function executeGenerator(sfWebRequest $request)
  {
        $this->setTemplate('../lib/_generator/index');
  }

  public function executeFuncionariosGrupo(sfWebRequest $request)
  {
      $funcionariosGrupo = Doctrine::getTable('Correspondencia_GrupoReceptor')->getReceptores($request->getParameter('grupo_id'));
      $cadena = "<script>";


      foreach ($funcionariosGrupo as $funcionarioGrupo)
      {
          if($request->getParameter('tipo') == "I"){
                $funcionarioCargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDelCargo($funcionarioGrupo->getCargoReceptorId());

                $cargo = Doctrine::getTable('Organigrama_Cargo')->find($funcionarioGrupo->getCargoReceptorId());
                $nombreCargo = Doctrine::getTable('Organigrama_CargoTipo')->find($cargo->getCargoTipoId());
                $cargo_nombre = $nombreCargo->getNombre();

                $nombreUnidad = Doctrine::getTable('Organigrama_Unidad')->find($funcionarioGrupo->getUnidadReceptorId());
                $unidad_nombre = $nombreUnidad->getNombre();

                foreach($funcionarioCargo as $funcionario)
                {
                      $funcionario_id = $funcionario->getId();
                      $unidad_id = $funcionarioGrupo->getUnidadReceptorId();
                      $funcionario_nombre = $cargo_nombre." / ".$funcionario->getPersona();

                      $cadena .= "fn_agregar_grupo('$unidad_nombre',$unidad_id,$funcionario_id,'$funcionario_nombre');";
                }
          }
          elseif($request->getParameter('tipo') == "E")
          {
              $datosFuncionario = Doctrine::getTable('Organismos_PersonaCargo')->getNombrePersonaPorCargo($funcionarioGrupo->getCargoReceptorId());
              $datosCargo = Doctrine::getTable('Organismos_PersonaCargo')->find($funcionarioGrupo->getCargoReceptorId());
              $datosOrganismo = Doctrine::getTable('Organismos_Organismo')->find($funcionarioGrupo->getUnidadReceptorId());

              $cargo_nombre = $datosCargo->getNombre();
              $organismo_nombre = $datosOrganismo->getNombre()." / ".$datosOrganismo->getSiglas();
              $organismo_id = $datosOrganismo->getId();
              $cargo_id = $funcionarioGrupo->getCargoReceptorId();

              foreach($datosFuncionario as $datoFuncionario)
              {
                  $funcionario_nombre = $datoFuncionario->getNombreSimple();
                  $funcionario_id = $datoFuncionario->getId();
                  $cadena .= "fn_agregar_grupo_externo('$organismo_nombre',$organismo_id,$funcionario_id,'$funcionario_nombre',$cargo_id,'$cargo_nombre');";
              }
          }
      }
      $cadena .= "</script>";

      echo $cadena;
      exit();
  }

  public function executeGuardarGrupo(sfWebRequest $request)
  {
      $grupo = $request->getParameter('grupoId');
      $nombre = $request->getParameter('nombreGrupo');
      $tipo = $request->getParameter('tipo');
      $grupoId = rand(1,9999999);
      $unidadDuena = $this->getUser()->getAttribute('funcionario_unidad_id');

      if($tipo == "I"){
        $primero = explode("," , $grupo);
        foreach($primero as $primer){
          $segundo = explode("#" , $primer);

          $funcionario_id = $segundo[1];
          $unidadReceptora = $segundo[0];

          $cargoReceptorId = Doctrine::getTable('Funcionarios_FuncionarioCargo')->cargoDelFuncionario($funcionario_id,$unidadReceptora);
          foreach($cargoReceptorId as $cargo){ $cargoReceptor = $cargo->getCargoId(); }

          $grupoReceptor = new Correspondencia_GrupoReceptor();
          $grupoReceptor->setNombre($nombre);
          $grupoReceptor->setUnidadDuenaId($unidadDuena);
          $grupoReceptor->setUnidadReceptorId($unidadReceptora);
          $grupoReceptor->setCargoReceptorId($cargoReceptor);
          $grupoReceptor->setGrupoId($grupoId);
          $grupoReceptor->setTipo($tipo);
          $grupoReceptor->save();
        }
      }
      elseif($tipo == "E")
      {
          $primero = explode("," , $grupo);
        foreach($primero as $primer){
          $segundo = explode("#" , $primer);

          $cargoReceptor = $segundo[2];
          $unidadReceptora = $segundo[0];

          $grupoReceptor = new Correspondencia_GrupoReceptor();
          $grupoReceptor->setNombre($nombre);
          $grupoReceptor->setUnidadDuenaId($unidadDuena);
          $grupoReceptor->setUnidadReceptorId($unidadReceptora);
          $grupoReceptor->setCargoReceptorId($cargoReceptor);
          $grupoReceptor->setGrupoId($grupoId);
          $grupoReceptor->setTipo($tipo);
          $grupoReceptor->save();
        }
      }
      exit();
  }

  public function executePrepararFirma(sfWebRequest $request)
  {
      $correspondencia_id = $request->getParameter('current_id');

      echo dataToSign::concatenar($correspondencia_id);
      exit();
  }
  
  public function executePrepararFirmaMultiple(sfWebRequest $request){

      $ids = $request->getParameter('ids');

      $array_datas = array();
      foreach ($ids as $id) {
          $array_datas[$id] = dataToSign::concatenar($id);
      }
      //realizo una consulta y retorno un arreglo como json
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($array_datas));
  }

  public function executeVerificarFirma(sfWebRequest $request)
  {
      $correspondencia_id = $request->getParameter('current_id');
      $firma_id = $request->getParameter('firma_id');

      $firma_emisor=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->find($firma_id);
      $funcionario_emisor=Doctrine::getTable('Funcionarios_Funcionario')->find($firma_emisor->getFuncionarioId());
      $proteccion = sfYaml::load($firma_emisor->getProteccion());
      $certificado=Doctrine::getTable('Funcionarios_FuncionarioCargoCertificado')->find($proteccion['certificado_id']);
//echo $proteccion['certificado_id'];
      $paquete['data'] = dataToSign::concatenar($correspondencia_id);
      $paquete['certificado'] = $certificado->getCertificado();
      $paquete['firma'] = $proteccion['resultado_firma'].'';
      $paquete['algoritmo_firma'] = $proteccion['algoritmo_firma'].'';
      

//      echo '<pre>';
//      print_r($paquete);
//      exit();
//print_r($proteccion);
//echo $paquete['algoritmo_firma'];
//      echo '<br><br><br>'.$paquete['data']; exit();
      
      $signature_verify = SignSiglas::verificarIntegridad($paquete);
//      echo '<br><br><br>-->'.$signature_verify; echo '</pre>'; 
//
//exit();

      $this->correspondencia_id = $correspondencia_id;
      $this->firma_id = $firma_id;
      $this->sign = $proteccion['resultado_firma'];
      $this->signature_verify = $signature_verify;
      $this->firma_emisor = $firma_emisor;
      $this->funcionario_emisor = $funcionario_emisor;
      $this->ssl_open = openssl_x509_parse($certificado->getCertificado());
  }

  public function executeVistobuenoUnidades(sfWebRequest $request)
  {
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('unidad_id')));
  }

  public function executeBuscaVistoBueno(sfWebRequest $request)
  {
      list($id_funcionario, $id_cargo)= explode('-', $request->getParameter('id'));
      $unidad_firmante= $request->getParameter('ud');

      $new= $request->getParameter('nuevo');
      $formato_id = $request->getParameter('formato');
      $tipo_formato_id = $this->executeInverso($formato_id,'tipo_formato_id');

      $formato_parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
      $parametros = sfYaml::load($formato_parametros->getParametros());
      
      //PARAMETRO VISTOBUENO DEL YML
      //"G": VISTO BUENO DE "GRUPO", EXTRAIDO DE LA CONFIGURACION DE PERMISOS DE GRUPOS DE CORRESPONDENCIA
      //"F": VISTO BUENO "FIJO", EXTRAIDO DE LA CONFIGURACION DE TIPOS DE FORMATOS
      //"N": NO APLICA VISTO BUENO, NO SE USA NINGUN VISTO BUENO ASI ESTOS ESTEN CREADOS
      
      $cadena = '<div style="position: relative"><table id="grilla_vb"><tbody>';
      $cadena .= '<tr><td colspan="2" style="height: 0px; min-width: 180px; padding: 0px"></td></tr>';
      
      $vistosbueno= array();
      if($parametros['options_create']['vistobueno']== 'G') {
          $vistosbueno= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->vistobuenoConfig($unidad_firmante, $id_funcionario);
          
            $creador_vb= 0;
            foreach ($vistosbueno as $val) {
                  if ($this->getUser()->getAttribute('funcionario_id') == $val['funcionario_id']) {
                      $creador_vb = $val['orden'];
                  }
              }

              $count_vb = 0;
              if ($creador_vb != 0) {
                  //Establece nuevo orden en caso que el creador este en la cadena de vistos buenos
                  foreach ($vistosbueno as $value) {
                      if ($value['orden'] > $creador_vb) {
                          $count_vb++;
                      }
                  }
              }

            foreach($vistosbueno as $value) {
                  if($value['orden'] > $creador_vb) {
                      $datos_func= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($value->getFuncionarioId());

                      $cadena .= '<tr id="tr_'. $datos_func[0]['funcionario_id'] .'">';
                      $cadena .= '<td><img style="vertical-align: middle" src="/images/icon/'. (($value->getStatus()== "A") ? 'arrow_up' : 'error') .'.png" /> <font class="f17b">'.$datos_func[0]['primer_nombre'].' '.$datos_func[0]['primer_apellido'].'</font> <font class="f15b">('.$datos_func[0]['unombre'].')</font></td>';
                      $cadena .= '<input type="hidden" value="'. $datos_func[0]['funcionario_id'] .'-'. $datos_func[0]['cargo_id'] .'-'. $value->getStatus() .'" name="vistobuenos[funcionarios][]" />';
                      $cadena .= '<td>';
                      if($parametros['options_create']['vistobueno_dinamico']=='true')
                          $cadena .= '<a onClick="borrame(\''. $datos_func[0]['funcionario_id'] .'\')" style="cursor: pointer;"><img src="/images/icon/delete_old.png"/></a>';
                      $cadena .= '</td>';
                      $cadena .= '</tr>';
                  }
            }
            $cadena .= '</tbody></table>';
      }elseif($parametros['options_create']['vistobueno']== 'F') {
//          $vistosbueno_config= Doctrine::getTable('Correspondencia_VistobuenoGeneralConfig')->findByTipoFormatoIdAndStatus($tipo_formato_id, 'A');
//          
//          $cadena_ruta= '';
//          foreach($vistosbueno_config as $values) {
//              $cadena_ruta .= '<table>';
//              $cadena_ruta .= '<th>'. $values['nombre'] .'<br/><font style="font-size: 10px; font-color: #666">'. $values['descripcion'] .'</font></th>';
//              $cadena_ruta .= '<tr><td>';
//              
//              $vistosbueno= Doctrine::getTable('Correspondencia_VistobuenoGeneral')->findByVistobuenoGeneralConfigIdAndStatus($values['id'], 'A');
//              $creador_vb= 0;
//              foreach ($vistosbueno as $val) {
//                if ($this->getUser()->getAttribute('funcionario_id') == $val['funcionario_id']) {
//                    $creador_vb = $val['orden'];
//                }
//              }
//
//              $count_vb = 0;
//              if ($creador_vb != 0) {
//                //Establece nuevo orden en caso que el creador este en la cadena de vistos buenos
//                foreach ($vistosbueno as $value) {
//                    if ($value['orden'] > $creador_vb) {
//                        $count_vb++;
//                    }
//                }
//              }
//              
//              $cadena_vb= '<table id="grilla_vb_fijo">';
//              foreach($vistosbueno as $value) {
//                if($value['orden'] > $creador_vb) {
//                    $datos_func= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($value->getFuncionarioId());
//
//                    $cadena_vb .= '<tr id="tr_'. $datos_func[0]['funcionario_id'] .'">';
//                    $cadena_vb .= '<td colspan="2"><img style="vertical-align: middle" src="/images/icon/'. (($value->getStatus()== "A") ? 'arrow_up' : 'error') .'.png" /> <font class="f17b">'.$datos_func[0]['primer_nombre'].' '.$datos_func[0]['primer_apellido'].'</font> <font class="f15b">('.$datos_func[0]['unombre'].')</font></td>';
//                    $cadena_vb .= '</tr>';
//                }
//              }
//              
//              $cadena_ruta .= $cadena_vb.'</table>';
//              
//              $cadena_ruta .= '</td></tr>';
//              $cadena_ruta .= '</table>';
//          }
//          
//          $cadena .= $cadena_ruta;
      }elseif($parametros['options_create']['vistobueno']== 'N') {
          //SIN VISTOS BUENO
      }

          $cadena .= '<div style="position: absolute; right: 0px; bottom: -15px">';
          if($this->getUser()->getAttribute('correspondencia')) {
              $var_sec= $this->getUser()->getAttribute('correspondencia');
              if(isset($var_sec['identificacion']))
                  $cadena .= '<x class="tooltip" title="[!]Activado:[/!]Esta ruta de visto bueno sustituir&aacute; la anterior que pudiera tener este documento.::[!]Inactivo:[/!]<b>No</b> se recrear&aacute;n vistos bueno (esta lista no se tomar&aacute; en cuenta)."><font style="color: #464646">[</font><input id="vistobueno_act" style="vertical-align: middle" type="checkbox" ' . (($new != 'false') ? 'checked' : '') . ' value="T" onChange="change_trigger_vb()" />&nbsp;<font style="color: #464646">Rehacer]</font></x>';
          }

          if($parametros['options_create']['vistobueno_dinamico']=='true')
              $cadena .= '&nbsp;&nbsp;<a href="javascript: agregar_vb();" class="tooltip" title="Le permite agregar nuevos miembros a la ruta de visto bueno" >[Buscar nuevo]</a>';

          $cadena .= '</div>';
//          $cadena .= '</div></tbody></table>';
          $cadena .= '</div>';

          $request_list['exist'] = (count($vistosbueno) > 0)? true : false;
          $request_list['dinamico'] = ($parametros['options_create']['vistobueno_dinamico']=='true') ? true : false;
          $request_list['data'] = $cadena;

          return $this->renderText(json_encode($request_list));
  }

  public function executeIconVistoBueno(sfWebRequest $request)
  {
      //STATUS INDICA SI HAY O NO HAY VB PARA EL FIRMANTE EN CONFIGURACION DE GRUPO
      $stat= $request->getParameter('status');
      //DINAMICO INDICA SI PARA ESTE FORMATO LOS VB DINAMICOS ESTAN ACTIVOS O NO (INFO DESDE CONFIG YML TIPO FORMATO)
      $dinamico= $request->getParameter('dinamic');

      $formato_id = $request->getParameter('formato');
      $tipo_formato_id = $this->executeInverso($formato_id,'tipo_formato_id');

      $formato_parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($tipo_formato_id);
      $parametros = sfYaml::load($formato_parametros->getParametros());
      
      $cadena= '';
      if($parametros['options_create']['vistobueno']== 'G') {
            if($stat== 'true') {
                //EL FIRMANTE SELECCIONADO TIENE VB EN CONFIG DE GRUPO
                $cadena = '<img id="icon_vb" class="tooltip" title="[!]Firmante con ruta de visto bueno[/!]'. (($dinamico== 'true') ? 'Click aqui para editar, agregar o eliminar miembros.' : 'Click aqui para ver ruta.') .'" style="display: none; cursor: pointer; position: absolute" src="/images/icon/ruta_vb.png" onClick="javascript: toggle_div_vb();" />';
            }else {
                //EL FIRMANTE SELECCIONADO NO TIENE VB EN CONFIG DE GRUPO
                if($dinamico== 'true') {
                    $cadena = '<img id="icon_vb" class="tooltip" title="[!]Firmante sin ruta de visto bueno[/!]Click aqui para agregar ruta de preferencia." style="display: none; cursor: pointer; position: absolute" src="/images/icon/ruta_vb_inactive.png" onClick="javascript: toggle_div_vb();" />';
                }else {
                    $cadena = '<img id="icon_vb" class="tooltip" title="[!]Firmante sin ruta de visto bueno[/!]Ruta de visto bueno din&aacute;mica <b>inactiva</b>, consulte al administrador SIGLAS." style="display: none; position: absolute" src="/images/icon/ruta_vb_inactive.png" />';
                }
            }
      }elseif($parametros['options_create']['vistobueno']== 'F') {
          $cadena = '<img id="icon_vb_ab" class="tooltip" style="display: none; cursor: pointer; position: absolute" title="[!]Visto bueno General[/!]Click aqui, para seleccionar una ruta establecida" src="/images/icon/ruta_vb.png" onClick="javascript: toggle_div_vb_ab('. $tipo_formato_id .');" />';
      }else {
          $cadena = '<img id="icon_vb" class="tooltip" title="[!]Firmante sin ruta de visto bueno[/!]Ruta de visto bueno din&aacute;mica <b>inactiva</b>, consulte al administrador SIGLAS." style="display: none; position: absolute" src="/images/icon/ruta_vb_inactive.png" />';
      }

      echo $cadena;

      exit;
  }
  
  public function executeBuscaVistoBuenoAb(sfWebRequest $request)
  {
      $this->formato_id= $request->getParameter('formato_id');
  }
  
  public function executeUpdateVbAbsoluto(sfWebRequest $request)
  {
      $formato_id = $request->getParameter('formato_id');

      $formato_parametros = Doctrine::getTable('Correspondencia_TipoFormato')->find($formato_id);
      $parametros = sfYaml::load($formato_parametros->getParametros());
      
      $vb_general_config_id= $request->getParameter('vb_general_config_id');
      
      $vistosbueno= Doctrine::getTable('Correspondencia_VistobuenoGeneral')->findByVistobuenoGeneralConfigIdAndStatus($vb_general_config_id, 'A');

        $creador_vb= 0;
        foreach ($vistosbueno as $val) {
              if ($this->getUser()->getAttribute('funcionario_id') == $val['funcionario_id']) {
                  $creador_vb = $val['orden'];
              }
          }

          $count_vb = 0;
          if ($creador_vb != 0) {
              //Establece nuevo orden en caso que el creador este en la cadena de vistos buenos
              foreach ($vistosbueno as $value) {
                  if ($value['orden'] > $creador_vb) {
                      $count_vb++;
                  }
              }
          }
          
          $cadena = '<div style="position: relative"><table id="grilla_vb"><tbody>';
          $cadena .= '<tr><td colspan="2" style="height: 0px; min-width: 180px; padding: 0px"></td></tr>';

        foreach($vistosbueno as $value) {
              if($value['orden'] > $creador_vb) {
                  $datos_func= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($value->getFuncionarioId());

                  $cadena .= '<tr id="tr_'. $datos_func[0]['funcionario_id'] .'">';
                  $cadena .= '<td><img style="vertical-align: middle" src="/images/icon/'. (($value->getStatus()== "A") ? 'arrow_up' : 'error') .'.png" /> <font class="f17b">'.$datos_func[0]['primer_nombre'].' '.$datos_func[0]['primer_apellido']. $parametros['options_create']['vistobueno_dinamico'].'</font> <font class="f15b">('.$datos_func[0]['unombre'].')</font></td>';
                  $cadena .= '<input type="hidden" value="'. $datos_func[0]['funcionario_id'] .'-'. $datos_func[0]['cargo_id'] .'-'. $value->getStatus() .'" name="vistobuenos[funcionarios][]" />';
                  $cadena .= '<td>';
                  if($parametros['options_create']['vistobueno_dinamico']=="true")
                      $cadena .= '<a onClick="borrame(\''. $datos_func[0]['funcionario_id'] .'\')" style="cursor: pointer;"><img src="/images/icon/delete_old.png"/></a>';
                  $cadena .= '</td>';
                  $cadena .= '</tr>';
              }
        }
        $cadena .= '</tbody></table>';
        
        $cadena .= '<div style="position: absolute; right: 0px; bottom: -15px">';
          if($this->getUser()->getAttribute('correspondencia')) {
              $var_sec= $this->getUser()->getAttribute('correspondencia');
              if(isset($var_sec['identificacion']))
                  $cadena .= '<x class="tooltip" title="[!]Activado:[/!]Esta ruta de visto bueno sustituir&aacute; la anterior que pudiera tener este documento.::[!]Inactivo:[/!]<b>No</b> se recrear&aacute;n vistos bueno (esta lista no se tomar&aacute; en cuenta)."><font style="color: #464646">[</font><input id="vistobueno_act" style="vertical-align: middle" type="checkbox" ' . (($new != 'false') ? 'checked' : '') . ' value="T" onChange="change_trigger_vb()" />&nbsp;<font style="color: #464646">Rehacer]</font></x>';
          }

          if($parametros['options_create']['vistobueno_dinamico']=="true")
              $cadena .= '&nbsp;&nbsp;<a href="javascript: agregar_vb();" class="tooltip" title="Le permite agregar nuevos miembros a la ruta de visto bueno" >[Buscar nuevo]</a>';

          $cadena .= '</div>';
          $cadena .= '</div>';

          echo $cadena;
          exit;
  }
  
  public function executeDatosIdentificacion(sfWebRequest $request)
  {
      $tipo_formato_id = $request->getParameter('tipo_formato_id');
      $tipo_formato_id = $this->executeInverso($tipo_formato_id,'tipo_formato_id');
      
      $tipo_formato = Doctrine::getTable('Correspondencia_TipoFormato')->tipoFormatoCacheado($tipo_formato_id);
      $parametros = sfYaml::load($tipo_formato[0]->getParametros());
      
      $results= Array();
      if($parametros['options_create']['privacidad']['habilitado'] == 'false') {
          $results['privacidad']['show']= 'false';
          if(isset($parametros['options_create']['privacidad']['habilitado'])) {
              if($parametros['options_create']['privacidad']['publico'] == 'false') {
                  $results['privacidad']['valores']= 'privado';
              }else {
                  $results['privacidad']['valores']= 'publico';
              }
          }else {
              $results['privacidad']['valores']= 'publico';
          }
      }else {
          $results['privacidad']['show']= 'true';
          $results['privacidad']['valores']= 'publico';
      }
      
      return $this->renderText(json_encode($results));
  }
  
  public function executeCleanSession(sfWebRequest $request)
  {
      //ACCION QUE PERMITE BORRAR SESSIONES
      $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
      //Esta accion no carga template
      exit();
  }
}

