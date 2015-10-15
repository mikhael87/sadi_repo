<?php

require_once dirname(__FILE__).'/../lib/ingresaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/ingresaGeneratorHelper.class.php';

/**
 * ingresa actions.
 *
 * @package    siglas
 * @subpackage ingresa
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ingresaActions extends autoIngresaActions
{  
  protected function CalculaEdad( $fecha ) {
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('seguridad_preingreso');
    $this->getUser()->getAttributeHolder()->remove('preparacion_preingreso_id');
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

  public function executePasesIngreso(sfWebRequest $request)
  {
    $this->redirect('llave_ingreso/index');
  }
  
  public function executeAlertaVisitante(sfWebRequest $request)
  {
    $this->redirect('alerta_visitante/index');
  }
  
  public function executePreingreso(sfWebRequest $request)
  {
    $this->redirect('preingreso/index');
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
                    $visitante['primer_nombre'] = ucwords(strtolower($result[0]['dnombre_1']));
                    $visitante['primer_apellido'] = ucwords(strtolower($result[0]['dapellido_1']));
                    $visitante['nacionalidad'] = $result[0]['dnacionalidad'];
                    $visitante['f_nacimiento'] = $result[0]['ffecha_nac'];
                    $visitante['edad'] = $this->CalculaEdad(date('Y-m-d', strtotime($result[0]['ffecha_nac'])));
                    $visitante['telefono'] = "";
                    $visitante['correo_electronico'] = "";
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
                
                $this->ingresos = Doctrine::getTable('Seguridad_Ingreso')->ingresosAnteriores($persona->getId(),'A');
                
                $this->preingresos = Doctrine::getTable('Seguridad_Ingreso')->preingresos($persona->getId(),'P');
            }
            
            $alerta_visitante = Doctrine::getTable('Seguridad_AlertaVisitante')->findOneByCiAndStatus($request->getParameter('cedula'),'A');
            $this->alerta_visitante = $alerta_visitante;
        }
        $this->visitante = $visitante;
  }
  
  public function executeFuncionarioRecibe(sfWebRequest $request)
  {
    $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($request->getParameter('u_id')));
  }
  
  public function executeEquiposDePersona(sfWebRequest $request)
  {
    $this->equipos = Doctrine::getTable('Seguridad_Equipo')->equiposDePersona($request->getParameter('persona_id'));
  }
  
  public function executeAjaxAddItemMotivo(sfWebRequest $request){
      //recibo el parametro
      $_valor = $request->getParameter('valor');
      //se realiza consulta, si el objeto existe no se hace el insert,
      //pero igual retora todos los registros
      $datos = Doctrine_Core::getTable('Seguridad_Motivo')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();      
      if (trim($_valor) != ''){
          //Validar que no exista un registro igual
          $validar = Doctrine_Core::getTable('Seguridad_Motivo')
              ->createQuery('a')
              ->where('a.descripcion ilike \'%'.$_valor.'%\'')
              ->execute();
          if(!count($validar)){
              //instancio un objeto, cargo el valor y salvo
              $marca= new Seguridad_Motivo();
              $marca->setDescripcion(ucfirst($_valor));
              $marca->save();
              //se consultan los registros
              $datos = Doctrine_Core::getTable('Seguridad_Motivo')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();
          }
      }
      //realizo una consulta y retorno un arreglo como json
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($datos));
  }
  
  public function executeAjaxAddItemTipo(sfWebRequest $request){
      //recibo el parametro
      $_valor = $request->getParameter('valor');
      //se realiza consulta, si el objeto existe no se hace el insert,
      //pero igual retora todos los registros
      $datos = Doctrine_Core::getTable('Seguridad_Tipo')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();      
      if (trim($_valor) != ''){
          //Validar que no exista un registro igual
          $validar = Doctrine_Core::getTable('Seguridad_Tipo')
              ->createQuery('a')
              ->where('a.descripcion ilike \'%'.$_valor.'%\'')
              ->execute();
          if(!count($validar)){
              //instancio un objeto, cargo el valor y salvo
              $tipo= new Seguridad_Tipo();
              $tipo->setDescripcion(ucfirst($_valor));
              $tipo->save();
              //se consultan los registros
              $datos = Doctrine_Core::getTable('Seguridad_Tipo')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();
          }
      }
      //realizo una consulta y retorno un arreglo como json
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($datos));
  }
  
  public function executeAjaxAddItemMarca(sfWebRequest $request){
      //recibo el parametro
      $_valor = $request->getParameter('valor');
      //se realiza consulta, si el objeto existe no se hace el insert,
      //pero igual retora todos los registros
      $datos = Doctrine_Core::getTable('Seguridad_Marca')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();      
      if (trim($_valor) != ''){
          //Validar que no exista un registro igual
          $validar = Doctrine_Core::getTable('Seguridad_Marca')
              ->createQuery('a')
              ->where('a.descripcion ilike \'%'.$_valor.'%\'')
              ->execute();
          if(!count($validar)){
              //instancio un objeto, cargo el valor y salvo
              $marca= new Seguridad_Marca();
              $marca->setDescripcion(ucfirst($_valor));
              $marca->save();
              //se consultan los registros
              $datos = Doctrine_Core::getTable('Seguridad_Marca')
              ->createQuery('a')
              ->setHydrationMode(Doctrine::HYDRATE_SCALAR)//se carga el objeto como arreglo
              ->execute();
          }
      }
      //realizo una consulta y retorno un arreglo como json
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($datos));
  }
  
  public function executeExtencionesTelefonicasUnidad(sfWebRequest $request)
  {  
    $extenciones = 'Extenciones telefonicas: ';
    foreach ($request->getParameter('cargos_unidad') as $cargo) {
        if($cargo != 'undefined' && $cargo != ''){
            $funcionario_extenciones = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($cargo);

            if(count($funcionario_extenciones)>0){
                foreach ($funcionario_extenciones as $extencion) {
                    $extenciones .= $extencion->getTelefono().' - ';
                }
            }
        }
    }
    
    if($extenciones == 'Extenciones telefonicas: '){
        $extenciones = 'La unidad seleccionado no tiene extenciones telefonicas registradas';
    }
      
    echo $extenciones;
    exit();
  }
  
  public function executeExtencionesTelefonicasFuncionario(sfWebRequest $request)
  {  
      $funcionario_extenciones = Doctrine::getTable('Organigrama_TelefonoCargo')->findByCargoId($request->getParameter('cargo_id'));
      
      if(count($funcionario_extenciones)>0){
          $extenciones = 'Extenciones telefonicas: ';
          foreach ($funcionario_extenciones as $extencion) {
              $extenciones .= $extencion->getTelefono().' - ';
          }
      } else {
          $extenciones = 'El funcionario seleccionado no tiene extenciones telefonicas registradas';
      }
      
      echo $extenciones;
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
            
            
            // INICIO PROCESAR PERSONA
            if($ingreso['persona_id']==''){
                $seguridad_persona = new Seguridad_Persona();
                $seguridad_persona->setCi($persona['ci']);
                $seguridad_persona->setNacionalidad($persona['nacionalidad']);
                $seguridad_persona->setPrimerNombre($persona['primer_nombre']);
                $seguridad_persona->setPrimerApellido($persona['primer_apellido']);
                $seguridad_persona->setFNacimiento($persona['f_nacimiento']);
                $seguridad_persona->setTelefono($persona['telefono']);
                $seguridad_persona->setCorreoElectronico($persona['correo_electronico']);
                $seguridad_persona->save();
                
                $ingreso['persona_id'] = $seguridad_persona->getId();
            } else {
                $seguridad_persona =  Doctrine::getTable('Seguridad_Persona')->find($ingreso['persona_id']);
                $seguridad_persona->setTelefono($persona['telefono']);
                $seguridad_persona->setCorreoElectronico($persona['correo_electronico']);
                $seguridad_persona->save();
            }
            // FIN PROCESAR PERSONA

            // INICIO PROCESAR INGRESO
            if($this->getUser()->getAttribute('seguridad_preingreso')){ 
                $seguridad_ingreso =  Doctrine::getTable('Seguridad_Ingreso')->find($this->getUser()->getAttribute('seguridad_preingreso'));
                $this->getUser()->getAttributeHolder()->remove('seguridad_preingreso');
            } else { 
                $seguridad_ingreso = new Seguridad_Ingreso();
            }
            
            $seguridad_ingreso->setPersonaId($ingreso['persona_id']);
            if($ingreso['list_funcionario']!=''){
                $seguridad_ingreso->setFuncionarioId($ingreso['list_funcionario']);
                $datos_funcionario =  Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($ingreso['list_funcionario']);
                $seguridad_ingreso->setUnidadId($datos_funcionario[0]['unidad_id']);
            }else{
                $seguridad_ingreso->setUnidadId($ingreso['unidad_id']);
                if($ingreso['funcionario_id']!='')
                    $seguridad_ingreso->setFuncionarioId($ingreso['funcionario_id']);
            } 
            $seguridad_ingreso->setImagen($ingreso['imagen']);
            $seguridad_ingreso->setMotivoId($ingreso['motivo_id']);
            $seguridad_ingreso->setMotivoVisita($ingreso['motivo_visita']);
            $seguridad_ingreso->setLlaveIngresoId($ingreso['llave_ingreso_id']);
            $seguridad_ingreso->setFIngreso(date('Y-m-d H:i:s'));
            $seguridad_ingreso->setRegistradorId($this->getUser()->getAttribute('usuario_id'));
            $seguridad_ingreso->setStatus('A');
            $seguridad_ingreso->save();
            
            rename('uploads/fotos_temporales/'.$ingreso['imagen'], 'uploads/seguridad/'.$ingreso['imagen']);
            
            
            // FIN PROCESAR INGRESO

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
  
  public function executeRegistrarSalida(sfWebRequest $request)
  {
    try {
        $manager = Doctrine_Manager::getInstance();
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();
        
        $_ingreso = Doctrine_Core::getTable('Seguridad_Ingreso')->find(array($request->getParameter('valor')));
        $_ingreso->setFEgreso(date('Y-m-d H:i:s'));
        $_ingreso->setDespachadorId($this->getUser()->getAttribute('usuario_id'));
        $_ingreso->save();
      
        $_llave_ingreso = Doctrine_Core::getTable('Seguridad_LlaveIngreso')->find($_ingreso->getLlaveIngresoId());
        $_llave_ingreso->setStatus('D');
        $_llave_ingreso->save();
      
        $conn->commit();
    } catch(Exception $e) {
        $conn->rollBack();
        echo $e;
    }
        
    $datos =array('id'=>$_ingreso->getId(),'f_egreso' =>  date('d-m-Y h:i A', strtotime($_ingreso->getFEgreso())));
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    sleep(1);
    return $this->renderText(json_encode($datos));
  }
  
  public function executeRegistrarSalidaEquipo(sfWebRequest $request)
  {
      $_ingresoEquipo = Doctrine_Core::getTable('Seguridad_IngresoEquipo')->find(array($request->getParameter('valor')));
      $_ingresoEquipo->setFEgreso(date('Y-m-d H:i:s'));
      $_ingresoEquipo->save();
      $datos =array('id'=>$_ingresoEquipo->getId(),'f_egreso' =>  date('d-m-Y h:i A', strtotime($_ingresoEquipo->getFEgreso())));
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      sleep(1);
      return $this->renderText(json_encode($datos));
  }

  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@seguridad_ingreso');
    }

    $this->filters = $this->configuration->getFilterForm($this->getFilters());

    $this->filters->bind($request->getParameter($this->filters->getName()));
    if ($this->filters->isValid())
    {
      $this->setFilters($this->filters->getValues());

      $this->redirect('@seguridad_ingreso');
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

    $this->setTemplate('index');
  }
  
  public function executeGetFuncionarios(sfWebRequest $request){
      $this->getResponse()->setContentType('application/json');
      $string = $request->getParameter('q');

      $req = Doctrine::getTable('Funcionarios_Funcionario')->getDataWhere($string);
      $results = array();
      if (count($req) > 0){
          foreach ( $req as $result )
              $results[$result->getId()] = $result->getPrimerNombre().' '.$result->getSegundoNombre().' '.$result->getPrimerApellido().' '.$result->getSegundoApellido();
          return $this->renderText(json_encode($results));
      }else{
          $results[0] = 'No se encontraron resultados';
          return $this->renderText(json_encode($results));
      }
  } 
  
  public function executeGetPersonas(sfWebRequest $request){
      $this->getResponse()->setContentType('application/json');
      $string = $request->getParameter('q');

      $req = Doctrine::getTable('Seguridad_Persona')->getDataWhere($string);
      $results = array();
      if (count($req) > 0){
          foreach ( $req as $result )
              $results[$result->getId()] = $result->getPrimerNombre().' '.$result->getSegundoNombre().' '.$result->getPrimerApellido().' '.$result->getSegundoApellido();
          return $this->renderText(json_encode($results));
      }else{
          $results[0] = 'No se encontraron resultados';
          return $this->renderText(json_encode($results));
      }
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
  
  public function executeEstadisticas(sfWebRequest $request)
  {
      $this->opcion = $request->getParameter('opcion');
  }
  
  public function executeOpcionesEstadisticas(sfWebRequest $request)
  {
      $opcion = $request->getParameter('opcion');
      
      $visitas = Doctrine_Query::create()
            ->select('i.*, u.nombre as unidad, m.descripcion as motivo')
            ->from('Seguridad_Ingreso i')
            ->innerJoin('i.Seguridad_Motivo m')
            ->innerJoin('i.Organigrama_Unidad u')
            ->where('i.status = ?','A')
            ->execute();
      /********************************************MOTIVOS***************************************/
      
      if($opcion == "motivos")
      {
        $motivos=array();
        foreach ($visitas as $visita) {
            if(!isset($motivos[$visita->getMotivo()]))
                $motivos[$visita->getMotivo()] = 0;   
            
            $motivos[$visita->getMotivo()]++;
        }
        $this->motivos = $motivos;
      }
    /********************************************UNIDADES***************************************/
      
      elseif($opcion == "unidades")
      {
        $unidades=array();
        foreach ($visitas as $visita) {
            if(!isset($unidades[$visita->getUnidad()]))
                $unidades[$visita->getUnidad()] = 0;   
            
            $unidades[$visita->getUnidad()]++;
        }
        $this->unidades = $unidades;
      }
      
      
      $this->setTemplate('estadisticas/'.$opcion);
  }
}
