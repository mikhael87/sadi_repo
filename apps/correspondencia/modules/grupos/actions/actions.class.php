<?php

require_once dirname(__FILE__).'/../lib/gruposGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/gruposGeneratorHelper.class.php';

/**
 * grupos actions.
 *
 * @package    siglas-(institucion)
 * @subpackage grupos
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class gruposActions extends autoGruposActions
{
  public function executeIndex(sfWebRequest $request)
  {
    if($request->getParameter('id'))
        $this->getUser()->setAttribute('pae_funcionario_unidad_id', $request->getParameter('id'));
    
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
  
  public function executeFuncionarioAutorizadoCorrespondencia(sfWebRequest $request)
  {
      if($request->getParameter('ua_id')!=""){
        if($request->getParameter('f_id')){
            //echo "f_id";
              $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioAutorizadoCorrespondenciaSelect(array($request->getParameter('u_id')));
              $this->funcionario_selected = $request->getParameter('f_id');
        } else {
            //echo "u_id";
              $this->funcionario_selected = 0;
              $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioAutorizadoCorrespondencia(array($request->getParameter('u_id')),$request->getParameter('ua_id'));
        }
      } else {
          echo '<script>$("#error_unidad_autoriza").show();</script>
                <select id="correspondencia_funcionario_unidad_funcionario_id" name="correspondencia_funcionario_unidad[funcionario_id]"></select>';
          exit();
      }
  }

  public function executeVistobuenoUnidades(sfWebRequest $request)
  {
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('unidad_id')));
  }
  
  public function executeSondeoVistoBueno(sfWebRequest $request) {
      if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
            $boss= false;
            $cargo_array= array();
            $funcionario_unidades= array();
            if($this->getUser()->getAttribute('funcionario_gr') == 99) {
                $boss= true;
                $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));

                foreach($funcionario_unidades_cargo as $unidades_cargo) {
                    $cargo_array[]= $unidades_cargo->getUnidadId();
                }
            }

            $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($this->getUser()->getAttribute('funcionario_id'));

            $admin_array= array();
            for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
                $admin_array[]= $funcionario_unidades_admin[$i][0];
            }

            $nonrepeat= array_merge($cargo_array, $admin_array);

            foreach ($nonrepeat as $valor){
                if (!in_array($valor, $funcionario_unidades)){
                    $funcionario_unidades[]= $valor;
                }
            }
      }else {
          $funcionario_unidades[] = $this->context->getUser()->getAttribute('pae_funcionario_unidad_id');
      }
      
      $sondeo= array();
      foreach($funcionario_unidades as $unidades) {
        $sondeo[]= $this->vistoBuenoRecursivo(0, NULL, $unidades);
      }
      
      $this->sondeo= $sondeo;
  }
  
  public function vistoBuenoRecursivo($k= 0, $sondeo= NULL, $unidad_partida_id) {
      //PERMITE DETERMINAR TODAS LAS UNIDADES HACIE ARRIBA CON SUS RESPECTIVOS DIRIGENTES
      $datos_unidad= Doctrine::getTable('Organigrama_Unidad')->find($unidad_partida_id);
      
      $array_boss= $this->jefeUnidad($unidad_partida_id);
      if(count($array_boss) > 0) {
        if(!$sondeo) {
            $sondeo= array();
        }
        $sondeo[$k]['unidad_id'] = $unidad_partida_id;
        $sondeo[$k]['unidad_nombre'] = $datos_unidad->getNombre();
        $sondeo[$k]['jefes'] = $array_boss;
        
        $k++;
      }
      
      if(is_int($datos_unidad->getPadreId())) {
          $array_boss= $this->vistoBuenoRecursivo($k, $sondeo, $datos_unidad->getPadreId());
      }else {
          return $sondeo;
      }
  }
  
  static function jefeUnidad($unidad_id) {
      //DEVUELVE LOS JEFES DE LA UNIDAD QUE SE SOLICITA
      $bosses= Doctrine::getTable('Funcionarios_Funcionario')->busquedaFuncionarioCargoUnidad($unidad_id);
      //Este dql debe ser reacomodado para que traiga solo jefes
      
      $jefe= array(); $i= 0;
      foreach($bosses as $boss) {
          $jefe[$i]['id'] = $boss->getId();
          $jefe[$i]['cedula'] = $boss->getCi();
          $jefe[$i]['nombre'] = $boss->getPrimerNombre();
          $jefe[$i]['apellido'] = $boss->getPrimerApellido();
          $jefe[$i]['cargo_id'] = $boss->getCargoId();
          $jefe[$i]['cargo'] = $boss->getCtnombre();
          
          $i++;
      }
      return $bosses;
  }
  
  public function executeGruposIni(sfWebRequest $request)
  {
        $this->getUser()->getAttributeHolder()->remove('pae_funcionario_unidad_id');
        $this->redirect(sfConfig::get('sf_app_correspondencia_url').'grupos/index');
  }

  public function executeEliminarFunGrupo(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    try {
        $inactivar_permiso = Doctrine_Query::create()
            ->update('Correspondencia_FuncionarioUnidad')
            ->set('status','?', 'I')
            ->where('id = ?', $id)
            ->execute();
        echo TRUE;
    }  catch (Doctrine_Validator_Exception $e) {
        echo FALSE;
    }
    
    exit();
    
//    $grupo_old = Doctrine::getTable('Correspondencia_funcionarioUnidad')->find($id);
//    if(count($grupo_old)> 1){ //si aun esta ahi (otro ya lo elimino)
//        if(!$grupo_old->getPermitido()) //Si aun no le han cambiado el status (otro ya lo permitio)
//        {
//            $grupo_old->delete();
//
//            $this->getUser()->setFlash('notice', ' Fueron eliminados los permisos sobre el Grupo, para este funcionario.');
//        }
//        else{
//            $responsable= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($grupo_old->getPermitidoFuncionarioId());
//            $msj = ' El funcionario ' . $responsable[0]['primer_nombre'] . ' ' . $responsable[0]['primer_apellido'] . '('.$responsable[0]['ctnombre'].'), con iguales privilegios que Usted, ya ha tomado esta decisión.';
//            $this->getUser()->setFlash('error', $msj);
//        }
//    }else{
//        $this->getUser()->setFlash('error', ' Otro funcionario con iguales privilegios que Usted, ya ha tomado esta decisión.');
//    }
//    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }

  public function executePermitirFunGrupo(sfWebRequest $request)
  {
      exit();
//    $id = $request->getParameter('id');
//    $id_unidad = $request->getParameter('id_unidad');
//
//    $grupo_old = Doctrine::getTable('Correspondencia_funcionarioUnidad')->find($id);
//    if(count($grupo_old)> 1){ //si aun esta ahi (otro ya lo elimino)
//        if (!$grupo_old->getPermitido()) {  //Si aun no le han cambiado el status (otro ya lo permitio)
//            $grupo_old->setPermitido('TRUE');
//            if(!empty($id_unidad))
//                $grupo_old->setDependenciaUnidadId($id_unidad);
//            //Este campo contiene el funcionario que agrego al funcionario en otro grupo, solo cuando esta en FALSE
//            //Si esta en TRUE y tiene id es por que fue tomada la desicion, y el nuevo id pertenece al funcionario que tomo la desicion
//            $grupo_old->setPermitido_funcionario($this->getUser()->getAttribute('funcionario_id'));
//            $grupo_old->save();
//
//            $this->getUser()->setFlash('notice', ' El funcionario conservará sus privilegios en este grupo.');
//        } else {
//            $responsable= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($grupo_old->getPermitidoFuncionarioId());
//            $msj = ' El funcionario ' . $responsable[0]['primer_nombre'] . ' ' . $responsable[0]['primer_apellido'] . '('.$responsable[0]['ctnombre'].'), con iguales privilegios que Usted, ya ha tomado esta decisión.';
//            $this->getUser()->setFlash('error', $msj);
//        }
//    }else{
//        $this->getUser()->setFlash('error', ' Otro funcionario con iguales privilegios que Usted, ha decidido eliminarlo del Grupo.');
//    }
//    $this->redirect(sfConfig::get('sf_app_acceso_url') . 'usuario/session');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('correspondencia_funcionario_unidad');
    $datos['permitido'] = TRUE;
    $request->setParameter('correspondencia_funcionario_unidad',$datos);

    //SE AGREGA ESTO POR SUPUESTO CAMBIO EN EL COMPORTAMIENTO DE LOS CHECKBOX
    $datos['redactar']= (isset($datos['redactar']) ? TRUE : FALSE);
    $datos['leer']= (isset($datos['leer']) ? TRUE : FALSE);
    $datos['firmar']= (isset($datos['firmar']) ? TRUE : FALSE);
    $datos['administrar']= (isset($datos['administrar']) ? TRUE : FALSE);
    $datos['recibir']= (isset($datos['recibir']) ? TRUE : FALSE);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';
      $new= ($form->getObject()->isNew())? TRUE : FALSE;

      try {
        if($form->getObject()->isNew()){ // SI ES NUEVO LO GUARDA
            $correspondencia_funcionario_unidad = $form->save();
        } else {
            $funcionario_unidad_edit = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->find($datos['id']);
            $funcionario_unidad_edit->setStatus('I');
            $funcionario_unidad_edit->setDeletedAt(date('Y-m-d h:i:s'));
            $funcionario_unidad_edit->save();
            
            $correspondencia_funcionario_unidad = new Correspondencia_FuncionarioUnidad();
            $correspondencia_funcionario_unidad->setAutorizadaUnidadId($funcionario_unidad_edit->getAutorizadaUnidadId());
            $correspondencia_funcionario_unidad->setFuncionarioId($funcionario_unidad_edit->getFuncionarioId());
            $correspondencia_funcionario_unidad->setDependenciaUnidadId($funcionario_unidad_edit->getDependenciaUnidadId());
            $correspondencia_funcionario_unidad->setStatus('A');
            $correspondencia_funcionario_unidad->setPermitido('TRUE');
            $correspondencia_funcionario_unidad->setRedactar($datos['redactar']);
            $correspondencia_funcionario_unidad->setLeer($datos['leer']);
            $correspondencia_funcionario_unidad->setFirmar($datos['firmar']);
            $correspondencia_funcionario_unidad->setAdministrar($datos['administrar']);
            $correspondencia_funcionario_unidad->setRecibir($datos['recibir']);
            $correspondencia_funcionario_unidad->save();
        }
        
        
        
        //
        //NUEVO CODIGO PARA HACER VALIDAR FUNCIONARIOS QUE PERTESCAN A GRUPOS ANTERIORES
        //
//        $migrupo= Doctrine::getTable('Correspondencia_FuncionarioUnidad')->findByFuncionarioIdAndStatus($correspondencia_funcionario_unidad->getFuncionarioId(),'A');
//        $count='0';
//        $ids= array();
//        //arreglo con id de grupos
//        foreach($migrupo as $value){
//            //solo coloca a usuarios a espera de validacion (por cambio de grupo) que no sean 99. Los 99 son agregados frecuentemente en otros grupos como firmantes
//            $datos_funcio= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($value->getFuncionarioId());
//            if($value->getId() != $correspondencia_funcionario_unidad->getId() && $datos_funcio[0]['cgnombre'] != '99') { //descarta al funcionario que acaba de agregar a su grupo(solo se validaran los otros)
//                $ids[]= $value->getId();
//            }
//            $count++;
//        }
//        if($count> 1){
//            for($i=0; $i< sizeof($ids); $i++){
//                   $result= Correspondencia_FuncionarioUnidadTable::getInstance()->EsperaValidacionGrupo($ids[$i], $correspondencia_funcionario_unidad->getIdUpdate());
//            }
//        }
        
        //
        //COMUNICACIONES
        //
        $notificacion = new gruposNotify();
        $notificacion->notifyDesk($correspondencia_funcionario_unidad->getFuncionarioId(), $correspondencia_funcionario_unidad->getIdUpdate());
        //
        //FIN DE COMUNICACIONES
        //

        // CREAR CORRELATIVO DE RECCION EXTERNA EN CASO DE QUE EL FUNCIONARIO QUE SE AGREGA TENGA PERMISOS DE RECIBIR Y LA UNIDAD NO TENGA ESE CORRELATIVO
        if($correspondencia_funcionario_unidad->getRecibir()==1){
            $correlativo_receptor = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneByUnidadIdAndStatusAndTipo($correspondencia_funcionario_unidad->getAutorizadaUnidadId(),'A','R');

            if(!$correlativo_receptor)
            {
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correspondencia_funcionario_unidad->getAutorizadaUnidadId());
                $nomenclador = 'A'.$unidad->getSiglas().'-Año-Secuencia';

                $correlativo_receptor = new Correspondencia_UnidadCorrelativo();
                $correlativo_receptor->setUnidadId($correspondencia_funcionario_unidad->getAutorizadaUnidadId());
                $correlativo_receptor->setNomenclador($nomenclador);
                $correlativo_receptor->setSecuencia(1);
                $correlativo_receptor->setTipo('R');
                $correlativo_receptor->setUltimoCorrelativo('INICIAL');

                $correlativo_receptor->save();
            }
        }

        //GUARDA VISTOS BUENOS
        if($correspondencia_funcionario_unidad->getFirmar() == 1) {
            $vb_array= Array();
            $vistobueno= $request->getParameter('vistobueno');
            
            if($vistobueno == 'A') {
                $vb_array= $request->getParameter('funcionarios_vb');
 
                if(!$new) {
                    $update_vb = Doctrine_Query::create()
                            ->update('Correspondencia_vistobuenoConfig vb')
                            ->set('vb.status', '?', 'I')
                            ->where('vb.funcionario_unidad_id = ?', $funcionario_unidad_edit->getId())
                            ->execute();
                    
                    // CODIGO PARA QUE LOS FUNCIONARIOS QUE YA NO SEAN PARTE DEL VISTO BUENO DE ESTE FIRMANTE 
                    // Y AUN TENGAN UN VISTO BUENO PENDIENTE EN CORRESPONDENCIA LO COLOQUE COMO DESINCORPORADO 
                    // DEL VISTO BUENO DE ESA CORRESPONDENCIA EN CASO DE QUE SIGA COMO VISTO BUENO LO DEJE EN ESAS CORRESPONDENCIAS
                    
                    $vistobueno_inactivados= Doctrine::getTable('Correspondencia_vistobuenoConfig')->findByFuncionarioUnidadIdAndStatus($funcionario_unidad_edit->getId(),'I');

                    foreach ($vistobueno_inactivados as $vistobueno_inactivado) {
                        
                        //VERIFICAR QUE EL FUNCIONARIO CON VISTO BUENO INACTIVADO NO SEA PARTE DEL NUEVO VISTO BUENO
                        $inactivar_vb_correspondencia = TRUE;
                        foreach($vb_array as $id) {
                            list($funcionario, $cargo, $status)= explode('#', $id);
                            if($funcionario == $vistobueno_inactivado->getFuncionarioId() && $cargo == $vistobueno_inactivado->getFuncionarioCargoId()){
                                $inactivar_vb_correspondencia = FALSE;
                            }
                        }
                        
                        //SI EL FUNCIONARIO NO FORMA PARTE DEL NUEVO VISTO BUENO INACTIVAR TODOS LOS VISTOS BUENOS DE ESE FUNCIONARIO DE ESE CARGO PARA ESA UNIDAD
                        if($inactivar_vb_correspondencia == TRUE){
                            //Desactiva todos los vistos buenos de correspondencias que aun no hayan sido verificados
                            $vistobueno= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByFuncionarioIdAndFuncionarioCargoIdAndStatus($vistobueno_inactivado->getFuncionarioId(), $vistobueno_inactivado->getFuncionarioCargoId(), 'E');


                            foreach($vistobueno as $value) {
                                $value->setStatus('D');
                                if($value->getTurno())
                                    $value->setTurno(FALSE);
                                $value->save();

                                //Verifica que todos los anteriores a este tengan visto bueno, si no es asi, solo lo desincorpora
                                $vistobueno_per_grupo= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByCorrespondenciaId($value->getCorrespondenciaId());
                                $all_check= true;
                                foreach($vistobueno_per_grupo as $val) {
                                    if($val->getOrden() < $value->getOrden()) {
                                        if($val->getStatus()== 'E') {
                                            $all_check= false;
                                        }
                                    }
                                }
                                if($all_check) {
                                    //pasa el turno al siguiente, en caso de que el turno sea del funcionario que se desincorpora
                                    $next= $value->getOrden() + 1;
                                    $new_turn= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findOneByCorrespondenciaIdAndOrden($value->getCorrespondenciaId(), $next);
                                    if($new_turn) {
                                        $new_turn->setTurno(TRUE);
                                        $new_turn->save();
                                    }
                                }

                            }
                        }
                    }
                }

                $orden= count($vb_array);
                foreach($vb_array as $id) {
                    list($funcionario, $cargo, $status)= explode('#', $id);
                    $vistosbueno = new Correspondencia_VistobuenoConfig();
                    $vistosbueno->setFuncionarioUnidadId($correspondencia_funcionario_unidad->getId());
                    $vistosbueno->setFuncionarioId($funcionario);
                    $vistosbueno->setFuncionarioCargoId($cargo);
                    $vistosbueno->setStatus($status);
                    $vistosbueno->setOrden($orden);

                    $vistosbueno->save();
                    $orden--;
                }
            }
        }

      } catch (Doctrine_Validator_Exception $e) {

        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $correspondencia_funcionario_unidad)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@correspondencia_funcionario_unidad_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'correspondencia_funcionario_unidad', 'sf_subject' => $correspondencia_funcionario_unidad));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

  public function executeDelete(sfWebRequest $request)
  {
    $update_vb = Doctrine_Query::create()
            ->update('Correspondencia_VistobuenoConfig vb')
            ->set('vb.status', '?', 'I')
            ->where('vb.funcionario_unidad_id = ?', $request->getParameter('id'))
            ->execute();
    
//    $delete_vb = Doctrine::getTable('Correspondencia_VistobuenoConfig')
//                      ->createQuery()
//                      ->delete()
//                      ->where('funcionario_unidad_id = ?', $request->getParameter('id'))
//                      ->execute();

    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

    $obj= $this->getRoute()->getObject();
    $obj->setStatus('I');
    $obj->save();
    
    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');

    $this->redirect('@correspondencia_funcionario_unidad');
  }

  public function executePermisosPorDefecto(sfWebRequest $request)
  {
      if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
            $boss= false;
            $cargo_array= array();
            $funcionario_unidades= array();
            if($this->getUser()->getAttribute('funcionario_gr') == 99) {
                $boss= true;
                $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));

                foreach($funcionario_unidades_cargo as $unidades_cargo) {
                    $cargo_array[]= $unidades_cargo->getUnidadId();
                }
            }

            $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($this->getUser()->getAttribute('funcionario_id'));

            $admin_array= array();
            for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
                $admin_array[]= $funcionario_unidades_admin[$i][0];
            }

            $nonrepeat= array_merge($cargo_array, $admin_array);

            foreach ($nonrepeat as $valor){
                if (!in_array($valor, $funcionario_unidades)){
                    $funcionario_unidades[]= $valor;
                }
            }
      }else {
          $funcionario_unidades[] = $this->context->getUser()->getAttribute('pae_funcionario_unidad_id');
      }
      
      foreach( $funcionario_unidades as $unidades ) {

        $vb_unidad_completa = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->findByAutorizadaUnidadIdAndStatus($unidades,'A');
        $vb_inactivar = array();
        foreach ($vb_unidad_completa as $vb_indivial) {
            $vb_inactivar[] = $vb_indivial->getId();
        }

        if(count($vb_inactivar)>0) {
            $update_vb = Doctrine_Query::create()
                    ->update('Correspondencia_VistobuenoConfig vb')
                    ->set('vb.status', '?', 'I')
                    ->whereIn('vb.funcionario_unidad_id',$vb_inactivar)
                    ->execute();
        }
        
        //Elimino todos los visto bueno config de esas unidades
//        $delete_vb = Doctrine::getTable('Correspondencia_VistobuenoConfig')
//                      ->createQuery()
//                      ->delete()
//                      ->where('(funcionario_unidad_id IN (SELECT fu.id
//                                FROM Correspondencia_FuncionarioUnidad fu 
//                                WHERE fu.autorizada_unidad_id = '.$unidades.'))')
//                      ->execute();
        
        
        $delete_grupo = Doctrine_Query::create()
            ->update('Correspondencia_FuncionarioUnidad')
            ->set('status','?', 'I')
            ->where('autorizada_unidad_id = ?', $unidades);
        
        if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
            if(!$boss)
                $delete_grupo->andwhereNotIn('funcionario_id', array($this->getUser()->getAttribute('funcionario_id')));
            else {
                if(in_array($unidades, $admin_array)) {
                    if(!in_array($unidades, $cargo_array))
                        $delete_grupo->andwhereNotIn('funcionario_id', array($this->getUser()->getAttribute('funcionario_id')));
                }
            }
        }
        $delete_grupo->execute();

        $funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionariosPorUnidad($unidades);

        if(count($funcionarios) > 0) {

            foreach($funcionarios as $funcionario) {

                    //ASIGNACION DE PERMISOS AUTOMATICOS
                    $created= false;
                    if($funcionario->getId() == $this->getUser()->getAttribute('funcionario_id')) {
                        if($boss) {
                            $correspondencia_funcionario_unidad = new Correspondencia_FuncionarioUnidad();

                            if($funcionario->getCgId() == 99) {
                                $correspondencia_funcionario_unidad->setFirmar(TRUE);
                                $correspondencia_funcionario_unidad->setAdministrar(TRUE);
                            }else {
                                $correspondencia_funcionario_unidad->setFirmar(FALSE);
                                $correspondencia_funcionario_unidad->setAdministrar(FALSE);
                            }

                            $correspondencia_funcionario_unidad->setAutorizadaUnidadId($unidades);
                            $correspondencia_funcionario_unidad->setFuncionarioId($funcionario->getId());
                            $correspondencia_funcionario_unidad->setDependenciaUnidadId($unidades);
                            $correspondencia_funcionario_unidad->setLeer(TRUE);
                            $correspondencia_funcionario_unidad->setRecibir(TRUE);
                            $correspondencia_funcionario_unidad->setPermitido(TRUE);
                            $correspondencia_funcionario_unidad->setRedactar(TRUE);

                            $correspondencia_funcionario_unidad->save();
                            $created= true;
                        }
                    }else {
                        $correspondencia_funcionario_unidad = new Correspondencia_FuncionarioUnidad();

                        if($funcionario->getCgId() == 99) {
                            $correspondencia_funcionario_unidad->setFirmar(TRUE);
                            $correspondencia_funcionario_unidad->setAdministrar(TRUE);
                        }else {
                            $correspondencia_funcionario_unidad->setFirmar(FALSE);
                            $correspondencia_funcionario_unidad->setAdministrar(FALSE);
                        }

                        $correspondencia_funcionario_unidad->setAutorizadaUnidadId($unidades);
                        $correspondencia_funcionario_unidad->setFuncionarioId($funcionario->getId());
                        $correspondencia_funcionario_unidad->setDependenciaUnidadId($unidades);
                        $correspondencia_funcionario_unidad->setLeer(TRUE);
                        $correspondencia_funcionario_unidad->setRecibir(TRUE);
                        $correspondencia_funcionario_unidad->setPermitido(TRUE);
                        $correspondencia_funcionario_unidad->setRedactar(TRUE);

                        $correspondencia_funcionario_unidad->save();
                        $created= true;
                    }
                    
                    //
                    //COMUNICACIONES
                    //
                    // PARA ESTA ACCION NO SE COLOCAN NOTIFICACION PUES ES MI GRUPO EL QUE ESTOY CONFIGURANDO A LAS DEMAS OFICINAS NO LES INTERESA
                    // SABER QUE LAS PERSONAS FUERON AGREGADAS AL GRUPO QUE POR DEFECTO PERTENECEN
                    //
                    //FIN DE COMUNICACIONES
                    //
                    
                    if($created) {
                        if($correspondencia_funcionario_unidad->getRecibir()==1){
                            $correlativo_receptor = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneByUnidadIdAndStatusAndTipo($correspondencia_funcionario_unidad->getAutorizadaUnidadId(),'A','R');

                            if(!$correlativo_receptor)
                            {
                                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correspondencia_funcionario_unidad->getAutorizadaUnidadId());
                                $nomenclador = 'A'.$unidad->getSiglas().'-Año-Secuencia';

                                $correlativo_receptor = new Correspondencia_UnidadCorrelativo();
                                $correlativo_receptor->setUnidadId($correspondencia_funcionario_unidad->getAutorizadaUnidadId());
                                $correlativo_receptor->setNomenclador($nomenclador);
                                $correlativo_receptor->setSecuencia(1);
                                $correlativo_receptor->setTipo('R');
                                $correlativo_receptor->setUltimoCorrelativo('INICIAL');

                                $correlativo_receptor->save();
                            }
                        }    
                    }
                    
              }
              $this->getUser()->setFlash('notice', 'Se han establecido los permisos por defecto.');
        }
      }

      $this->redirect('grupos/index');
  }
  
  public function executeHistorico(sfWebRequest $request) {
      if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
        $funcionario_unidades = array();
        $boss= false;
        if ($this->getUser()->getAttribute('funcionario_gr') == 99) {
              $boss = true;
              $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
          }
          $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($this->getUser()->getAttribute('funcionario_id'));

          $cargo_array = array();
          if ($boss) {
              foreach ($funcionario_unidades_cargo as $unidades_cargo) {
                  $cargo_array[] = $unidades_cargo->getUnidadId();
              }
          }

          $admin_array = array();
          for ($i = 0; $i < count($funcionario_unidades_admin); $i++) {
              $admin_array[] = $funcionario_unidades_admin[$i][0];
          }

          $nonrepeat = array_merge($cargo_array, $admin_array);

          foreach ($nonrepeat as $valor) {
              if (!in_array($valor, $funcionario_unidades)) {
                  $funcionario_unidades[] = $valor;
              }
          }
      }else {
          $funcionario_unidades[] = $this->context->getUser()->getAttribute('pae_funcionario_unidad_id');
      }
        
        $this->funcionario_unidades= $funcionario_unidades;
    }
    
    public function executeUnidadHistorico(sfWebRequest $request) {
        $unidad_id= $request->getParameter('id');

        $grupo = Doctrine_Core::getTable('Correspondencia_FuncionarioUnidad')->grupoUnidadHistorico($unidad_id);
        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidad_id);

        $this->grupo= $grupo;
        $this->unidad= $unidad;
    }
}
