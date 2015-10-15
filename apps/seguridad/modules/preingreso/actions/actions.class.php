<?php

require_once dirname(__FILE__).'/../lib/preingresoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/preingresoGeneratorHelper.class.php';

/**
 * preingreso actions.
 *
 * @package    siglas
 * @subpackage preingreso
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class preingresoActions extends autoPreingresoActions
{
  protected function CalculaEdad( $fecha ) {
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
  }
  
  public function executeRegresar(sfWebRequest $request)
  {
    $this->redirect(sfConfig::get('sf_app_seguridad_url').'ingresa');
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('preingresos_saltados');
    // sorting
    if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
  }
  
  public function executeEquiposDePersona(sfWebRequest $request)
  {
    $this->equipos = Doctrine::getTable('Seguridad_Equipo')->equiposDePersona($request->getParameter('persona_id'));
  }
  
  public function executeFuncionarioRecibe(sfWebRequest $request)
  {
    $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($request->getParameter('u_id')));
  }
  
  public function executeVisitanteForm(sfWebRequest $request)
  {
        $visitante['persona_id'] = "";
        
        if ($request->getParameter('cedula') != null) {
            $persona = Doctrine::getTable('Seguridad_Persona')->findOneByCi($request->getParameter('cedula'));

            if (!$persona) {
                $visitante['cedula'] = $request->getParameter('cedula');
                $sf_seguridad = sfYaml::load(sfConfig::get('sf_root_dir') . "/config/siglas/seguridad.yml");

                $result=NULL;
                
                if($sf_seguridad['conexion_saime']['activo']==true){
                    try{
                        $manager = Doctrine_Manager::getInstance()
                                ->openConnection(
                                'pgsql' . '://' .
                                $sf_seguridad['conexion_saime']['username'] . ':' .
                                $sf_seguridad['conexion_saime']['password'] . '@' .
                                $sf_seguridad['conexion_saime']['host'] . ':'. $sf_seguridad['conexion_saime']['port'] .'/' .
                                $sf_seguridad['conexion_saime']['dbname'], 'dbSAIME');

                        $query = "SELECT ".$sf_seguridad['conexion_saime']['consulta']['campo_nacionalidad'].", 
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula'].",
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre'].",
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre'].",
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido'].",
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido'].",
                                         ".$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']."
                                  FROM ".$sf_seguridad['conexion_saime']['consulta']['tabla']."
                                  WHERE ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula']."=" . $request->getParameter('cedula');

                        $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                        Doctrine_Manager::getInstance()->closeConnection($manager);
                    } catch (Exception $e) {}
                }

                if ($result) {
                    $visitante['primer_nombre'] = ucwords(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]));
                    $visitante['primer_apellido'] = ucwords(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]));
                    $visitante['nacionalidad'] = $result[0][$sf_seguridad['conexion_saime']['consulta']['campo_nacionalidad']];
                    $visitante['f_nacimiento'] = $result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']];
                    $visitante['edad'] = $this->CalculaEdad(date('Y-m-d', strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']])));
                    $visitante['telefono'] = "";
                    $visitante['correo_electronico'] = "";
                    
                    $seguridad_persona = new Seguridad_Persona();
                    $seguridad_persona->setCi($visitante['cedula']);
                    $seguridad_persona->setNacionalidad($visitante['nacionalidad']);
                    $seguridad_persona->setPrimerNombre($visitante['primer_nombre']);
                    $seguridad_persona->setPrimerApellido($visitante['primer_apellido']);
                    $seguridad_persona->setFNacimiento($visitante['f_nacimiento']);
                    $seguridad_persona->setTelefono('');
                    $seguridad_persona->setCorreoElectronico('');
                    $seguridad_persona->save();
                    
                    $visitante['persona_id'] = $seguridad_persona->getId();
                }
            } else {
                $visitante['persona_id'] = $persona->getId();
                $visitante['cedula'] = $persona->getCi();
                $visitante['primer_nombre'] = $persona->getPrimerNombre();
                $visitante['primer_apellido'] = $persona->getPrimerApellido();
                $visitante['nacionalidad'] = $persona->getNacionalidad();
                $visitante['f_nacimiento'] = $persona->getFNacimiento();
                $visitante['edad'] = $this->CalculaEdad($persona->getFNacimiento());
                $visitante['telefono'] = $persona->getTelefono();
                $visitante['correo_electronico'] = $persona->getCorreoElectronico();
            }
            
            $alerta_visitante = Doctrine::getTable('Seguridad_AlertaVisitante')->findOneByCiAndStatus($request->getParameter('cedula'),'A');
            $this->alerta_visitante = $alerta_visitante;
        }
        $this->visitante = $visitante;
  }
  
  public function executePrepararIngreso(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->seguridad_ingreso = $this->form->getObject();
    
    
      $this->getUser()->setAttribute('preparacion_preingreso_id',$request->getParameter('preingreso_id'));
      
      if($this->getUser()->getAttribute('preingresos_saltados')){
          $preingresos_saltados = $this->getUser()->getAttribute('preingresos_saltados');
          print_r($preingresos_saltados);
          $personas_preingreso = Doctrine::getTable('Seguridad_Ingreso')->personasPreingreso($request->getParameter('preingreso_id'),$preingresos_saltados);
      } else {
          $personas_preingreso = Doctrine::getTable('Seguridad_Ingreso')->personasPreingreso($request->getParameter('preingreso_id'));
      }
      
      $visitante['persona_id'] = '';
      
      if(count($personas_preingreso)>0){
            $visitante['ingreso_id'] = $personas_preingreso[0]->getId();
            $visitante['persona_id'] = $personas_preingreso[0]->getPersonaId();
            $visitante['cedula'] = $personas_preingreso[0]->getCi();
            $visitante['primer_nombre'] = $personas_preingreso[0]->getPrimerNombre();
            $visitante['primer_apellido'] = $personas_preingreso[0]->getPrimerApellido();
            $visitante['nacionalidad'] = $personas_preingreso[0]->getNacionalidad();
            $visitante['f_nacimiento'] = $personas_preingreso[0]->getFNacimiento();
            $visitante['edad'] = $this->CalculaEdad($personas_preingreso[0]->getFNacimiento());
            $visitante['telefono'] = $personas_preingreso[0]->getTelefono();
            $visitante['correo_electronico'] = $personas_preingreso[0]->getCorreoElectronico();
            
            $alerta_visitante = Doctrine::getTable('Seguridad_AlertaVisitante')->findOneByCiAndStatus($personas_preingreso[0]->getCi(),'A');
            $this->alerta_visitante = $alerta_visitante;
            
            $this->ingresos = Doctrine::getTable('Seguridad_Ingreso')->ingresosAnteriores($personas_preingreso[0]->getPersonaId(),'A');
      }
      
      if(count($personas_preingreso)>1){
          $this->proximo_en_registrar = 'C.I.: '.$personas_preingreso[1]->getCi().' / '.$personas_preingreso[1]->getPrimerNombre().' '.$personas_preingreso[1]->getPrimerApellido();
      }

      $this->visitantes_restantes = count($personas_preingreso);
      $this->visitante = $visitante;
  }
  
  public function executeGetNPases(sfWebRequest $request){
      $this->getResponse()->setContentType('application/json');
      $string = $request->getParameter('q');

      $req = Doctrine::getTable('Seguridad_LlaveIngreso')->getDataNPaseSimilar($string);
      $results = array();
      if (count($req) > 0){
          foreach ( $req as $result ){
              if($result->getStatus() == 'O') {
                $results[$result->getId().'-O'] = $result->getNPase().' - Ocupado';
              } else {
                $results[$result->getId()] = $result->getNPase();
              }
          }
          return $this->renderText(json_encode($results));
      }else{
          $results[0] = 'No se encontraron resultados';
          return $this->renderText(json_encode($results));
      }
  } 
  
  public function executeSaltarVisitante(sfWebRequest $request)
  {
      if($this->getUser()->getAttribute('preingresos_saltados')){
          $preingresos_saltados = $this->getUser()->getAttribute('preingresos_saltados');
      }
      
      $preingresos_saltados[] = $request->getParameter('persona_id');
      
      $this->getUser()->setAttribute('preingresos_saltados',$preingresos_saltados);
      exit();
  }
  
  
  
  public function executeRegistrarIngreso(sfWebRequest $request)
  {
      
      $persona = $request->getParameter('seguridad_persona');
      $ingreso = $request->getParameter('seguridad_ingreso');
      $equipo = $request->getParameter('seguridad_equipo');

        try {
            $manager = Doctrine_Manager::getInstance();
            $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();

            // INICIO PROCESAR INGRESO
            $seguridad_ingreso =  Doctrine::getTable('Seguridad_Ingreso')->find($ingreso['ingreso_id']);
            $seguridad_ingreso->setImagen($ingreso['imagen']);
            $seguridad_ingreso->setLlaveIngresoId($ingreso['llave_ingreso_id']);
            $seguridad_ingreso->setFIngreso(date('Y-m-d H:i:s'));
            $seguridad_ingreso->setRegistradorId($this->getUser()->getAttribute('usuario_id'));
            $seguridad_ingreso->setStatus('A');
            $seguridad_ingreso->save();
            // FIN PROCESAR INGRESO

            // INICIO PROCESAR PERSONA
            $seguridad_persona =  Doctrine::getTable('Seguridad_Persona')->find($seguridad_ingreso->getPersonaId());
            $seguridad_persona->setTelefono($persona['telefono']);
            $seguridad_persona->setCorreoElectronico($persona['correo_electronico']);
            $seguridad_persona->save();
            // FIN PROCESAR PERSONA
             
            // INICIO OCUPAR LLAVE INGRESO
            $seguridad_llave_ingreso =  Doctrine::getTable('Seguridad_LlaveIngreso')->find($ingreso['llave_ingreso_id']);
            $seguridad_llave_ingreso->setStatus('O');
            $seguridad_llave_ingreso->save();
            // FIN OCUPAR LLAVE INGRESO
            
            // INICIO PROCESAR EQUIPOS
            if($equipo['serial']!=''){
                $seguridad_equipo = new Seguridad_Equipo();
                $seguridad_equipo->setTipoId($equipo['tipo_id']);
                $seguridad_equipo->setMarcaId($equipo['marca_id']);
                $seguridad_equipo->setSerial($equipo['serial']);
                $seguridad_equipo->save();
                
                $seguridad_ingreso_equipo = new Seguridad_IngresoEquipo();
                $seguridad_ingreso_equipo->setEquipoId($seguridad_equipo->getId());
                $seguridad_ingreso_equipo->setIngresoId($seguridad_ingreso->getId());
                $seguridad_ingreso_equipo->save();
            }
            
            if(isset($equipo['otros'])){
                foreach ($equipo['otros'] as $equipo_otro) {
                    list($tipo_id,$marca_id,$serial) = explode("###", $equipo_otro);
                    
                    $seguridad_equipo = new Seguridad_Equipo();
                    $seguridad_equipo->setTipoId($tipo_id);
                    $seguridad_equipo->setMarcaId($marca_id);
                    $seguridad_equipo->setSerial($serial);
                    $seguridad_equipo->save();

                    $seguridad_ingreso_equipo = new Seguridad_IngresoEquipo();
                    $seguridad_ingreso_equipo->setEquipoId($seguridad_equipo->getId());
                    $seguridad_ingreso_equipo->setIngresoId($seguridad_ingreso->getId());
                    $seguridad_ingreso_equipo->save();
                }
            }
            
            if(isset($equipo['otros_anteriores'])){
                foreach ($equipo['otros_anteriores'] as $equipo_anterior) {
                    $seguridad_ingreso_equipo = new Seguridad_IngresoEquipo();
                    $seguridad_ingreso_equipo->setEquipoId($equipo_anterior);
                    $seguridad_ingreso_equipo->setIngresoId($seguridad_ingreso->getId());
                    $seguridad_ingreso_equipo->save();
                }
            }
            
            // FIN PROCESAR EQUIPOS
            
            $conn->commit();
        } catch(Exception $e) {
            $conn->rollBack();
            echo $e;
        }
//      echo "<pre>";
//      print_r($persona);
//      print_r($ingreso);
      exit();
  }
  
  
  
  
  
  
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'Preingreso registrado con exito.' : 'Preingreso actualizado con exito.';

      try {
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();
            
        $seguridad_preingreso = $form->save();
        
        $preingreso_persona = $request->getParameter('preingreso_persona');
//        print_r($preingreso_persona); exit();
        foreach ($preingreso_persona as $persona_id) {
            // INICIO PROCESAR INGRESO
            $seguridad_ingreso = new Seguridad_Ingreso();
            $seguridad_ingreso->setPersonaId($persona_id);
            $seguridad_ingreso->setPreingresoId($seguridad_preingreso->getId());
            $seguridad_ingreso->setUnidadId($seguridad_preingreso->getUnidadId());
            $seguridad_ingreso->setFuncionarioId($seguridad_preingreso->getFuncionarioId());
            $seguridad_ingreso->setImagen('');
            $seguridad_ingreso->setMotivoId($seguridad_preingreso->getMotivoId());
            $seguridad_ingreso->setMotivoVisita($seguridad_preingreso->getMotivoVisita());
            $seguridad_ingreso->setLlaveIngresoId(NULL);
            $seguridad_ingreso->setFIngreso('1900-01-01');
            $seguridad_ingreso->setStatus('P');
            $seguridad_ingreso->save();
            // FIN PROCESAR INGRESO
        }
        
        $conn->commit();
      } catch (Doctrine_Validator_Exception $e) {
        $conn->rollBack();
        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $seguridad_preingreso)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@seguridad_preingreso');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('@seguridad_preingreso');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

}
