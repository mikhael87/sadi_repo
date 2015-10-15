<?php

require_once dirname(__FILE__).'/../lib/gruposGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/gruposGeneratorHelper.class.php';

/**
 * grupos actions.
 *
 * @package    siglas
 * @subpackage grupos
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gruposActions extends autoGruposActions
{
  public function executeIndex(sfWebRequest $request)
  {
    if($request->getParameter('id'))
        $this->getUser()->setAttribute('pae_funcionario_unidad_id', $request->getParameter('id'));
    
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
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
  
  public function executeGruposIni(sfWebRequest $request)
  {
        $this->getUser()->getAttributeHolder()->remove('pae_funcionario_unidad_id');
        $this->redirect(sfConfig::get('sf_app_archivo_url').'grupos/index');
  }

  public function executeFuncionarioAutorizadoArchivo(sfWebRequest $request)
  {
      if($request->getParameter('ua_id')!=""){
        if($request->getParameter('f_id')) {
            //echo "f_id";
                $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioAutorizadoArchivoSelect(array($request->getParameter('u_id')));
                $this->funcionario_selected = $request->getParameter('f_id');
        } else {
            //echo "u_id";
                $this->funcionario_selected = 0;
                $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioAutorizadoArchivo(array($request->getParameter('u_id')),$request->getParameter('ua_id'));
        }
      } else {
          echo '<script>$("#error_unidad_autoriza").show();</script>
                <select id="archivo_funcionario_unidad_funcionario_id" name="archivo_funcionario_unidad[funcionario_id]"></select>';
          exit();
      }
  }
  
  public function executeEliminarFunGrupo(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    try {
        $inactivar_permiso = Doctrine_Query::create()
            ->update('Archivo_FuncionarioUnidad')
            ->set('status','?', 'I')
            ->where('id = ?', $id)
            ->execute();
        echo TRUE;
    }  catch (Doctrine_Validator_Exception $e) {
        echo FALSE;
    }
    
    exit();
    
//    $grupo_old = Doctrine::getTable('Archivo_funcionarioUnidad')->find($id);
//    if(count($grupo_old)> 1){ //si aun esta ahi (otro ya lo elimino)
//        if(!$grupo_old->getPermitido()) //Si aun no le han cambiado el status (otro ya lo permitio)
//        {
//            $grupo_old->delete();
//
//            $this->getUser()->setFlash('notice', ' Fueron eliminados los permisos sobre el Grupo, para este funcionario.');
//        }
//        else{
//            $responsable= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($grupo_old->getPermitidoFuncionarioId());
//            $msj = ' El funcionario ' . $responsable[0]['primer_nombre'] . ' ' . $responsable[0]['primer_apellido'] . '('.$responsable[0]['ctnombre'].'), con iguales privilegios que Usted en el SIGLAS, ya ha tomado esta decisión.';
//            $this->getUser()->setFlash('error', $msj);
//        }
//    }else{
//        $this->getUser()->setFlash('error', ' Otro funcionario con iguales privilegios que Usted en el SIGLAS, ya ha tomado esta decisión.');
//    }
//    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }
  
  public function executePermitirFunGrupo(sfWebRequest $request)
  {
      exit();
//    $id = $request->getParameter('id');
//
//    $grupo_old = Doctrine::getTable('Archivo_funcionarioUnidad')->find($id);
//    if(count($grupo_old)> 1){ //si aun esta ahi (otro ya lo elimino)
//        if (!$grupo_old->getPermitido()) {  //Si aun no le han cambiado el status (otro ya lo permitio)
//            $grupo_old->setPermitido('TRUE');
//            //Este campo contiene el funcionario que agrego al funcionario en otro grupo, solo cuando esta en FALSE
//            //Si esta en TRUE y tiene id es por que fue tomada la desicion, y el nuevo id pertenece al funcionario que tomo la desicion
//            $grupo_old->setPermitido_funcionario($this->getUser()->getAttribute('funcionario_id'));
//            $grupo_old->save();
//
//            $this->getUser()->setFlash('notice', ' El funcionario conservará sus privilegios en este grupo.');
//        } else {
//            $responsable= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($grupo_old->getPermitidoFuncionarioId());
//            $msj = ' El funcionario ' . $responsable[0]['primer_nombre'] . ' ' . $responsable[0]['primer_apellido'] . '('.$responsable[0]['ctnombre'].'), con iguales privilegios que Usted en el SIGLAS, ya ha tomado esta decisión.';
//            $this->getUser()->setFlash('error', $msj);
//        }
//    }else{
//        $this->getUser()->setFlash('error', ' Otro funcionario con iguales privilegios que Usted en el SIGLAS, ha decidido eliminarlo del Grupo.');
//    }
//    $this->redirect(sfConfig::get('sf_app_acceso_url') . 'usuario/session');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('archivo_funcionario_unidad');
    $datos['permitido'] = TRUE;
    $request->setParameter('archivo_funcionario_unidad',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        if($form->getObject()->isNew()){ // SI ES NUEVO LO GUARDA
            $archivo_funcionario_unidad = $form->save();
        } else {
            $funcionario_unidad_edit = Doctrine::getTable('Archivo_FuncionarioUnidad')->find($datos['id']);
            $funcionario_unidad_edit->setStatus('I');
            $funcionario_unidad_edit->setDeletedAt(date('Y-m-d h:i:s'));
            $funcionario_unidad_edit->save();
            
            $archivo_funcionario_unidad = new Archivo_FuncionarioUnidad();
            $archivo_funcionario_unidad->setAutorizadaUnidadId($funcionario_unidad_edit->getAutorizadaUnidadId());
            $archivo_funcionario_unidad->setFuncionarioId($funcionario_unidad_edit->getFuncionarioId());
            $archivo_funcionario_unidad->setDependenciaUnidadId($funcionario_unidad_edit->getDependenciaUnidadId());
            $archivo_funcionario_unidad->setStatus('A');
            $archivo_funcionario_unidad->setPermitido('TRUE');
            $archivo_funcionario_unidad->setLeer($datos['leer']);
            $archivo_funcionario_unidad->setArchivar($datos['archivar']);
            $archivo_funcionario_unidad->setPrestar($datos['prestar']);
            $archivo_funcionario_unidad->setAdministrar($datos['administrar']);
            $archivo_funcionario_unidad->setAnular($datos['anular']);
            $archivo_funcionario_unidad->save();
        }
        
        //
        //NUEVO CODIGO PARA HACER VALIDAR FUNCIONARIOS QUE PERTESCAN A GRUPOS ANTERIORES
        //
//        $migrupo= Doctrine::getTable('Archivo_FuncionarioUnidad')->findByFuncionarioId($archivo_funcionario_unidad->getFuncionarioId());
//        $count='0';
//        $ids= array();
//        //arreglo con id de grupos
//        foreach($migrupo as $value){
//            //solo coloca a usuarios a espera de validacion (por cambio de grupo) que no sean 99. Los 99 son agregados frecuentemente en otros grupos como firmantes
//            $datos_funcio= Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionario($value->getFuncionarioId());
//            if($value->getId() != $archivo_funcionario_unidad->getId() && $datos_funcio[0]['cgnombre'] != '99') { //descarta al funcionario que acaba de agregar a su grupo(solo se validaran los otros)
//                $ids[]= $value->getId();
//            }
//            $count++;
//        }
//        if($count> 1){
//            for($i=0; $i< sizeof($ids); $i++){
//                   $result= Archivo_FuncionarioUnidadTable::getInstance()->EsperaValidacionGrupo($ids[$i], $archivo_funcionario_unidad->getIdUpdate()); 
//            }
//        }
        
        //
        //COMUNICACIONES
        //
        $notificacion = new gruposNotify();
        $notificacion->notifyDesk($archivo_funcionario_unidad->getFuncionarioId(), $archivo_funcionario_unidad->getIdUpdate());
        //
        //FIN DE COMUNICACIONES
        //
        
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_funcionario_unidad)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_funcionario_unidad_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'archivo_funcionario_unidad', 'sf_subject' => $archivo_funcionario_unidad));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
  
  public function executeHistorico(sfWebRequest $request) {
      if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
        $boss= false;
        if ($this->getUser()->getAttribute('funcionario_gr') == 99) {
              $boss = true;
              $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
          }
          $funcionario_unidades_admin = Doctrine::getTable('Archivo_FuncionarioUnidad')->adminFuncionarioGrupo($this->getUser()->getAttribute('funcionario_id'));

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

          $funcionario_unidades = array();
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

        $grupo = Doctrine_Core::getTable('Archivo_FuncionarioUnidad')->grupoUnidadHistorico($unidad_id);
        $unidad = Doctrine_Core::getTable('Organigrama_Unidad')->find($unidad_id);

        $this->grupo= $grupo;
        $this->unidad= $unidad;
    }
}
