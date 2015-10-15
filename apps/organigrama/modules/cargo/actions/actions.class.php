<?php

require_once dirname(__FILE__).'/../lib/cargoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/cargoGeneratorHelper.class.php';

/**
 * cargo actions.
 *
 * @package    siglas-(institucion)
 * @subpackage cargo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class cargoActions extends autoCargoActions
{
  public function executeMover(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('cargo_mover_id', $id);
    $this->form = new Organigrama_UnidadForm();
  }
  
  public function executeMoverMasivo(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->form = new Organigrama_UnidadForm();
  }
  
  public function executeInactivos(sfWebRequest $request)
  {
    $inactivo = $request->getParameter('inac');

    $this->getUser()->getAttributeHolder()->remove('inactivo');
    
    if($inactivo == 'true') {
        $this->getUser()->setAttribute('inactivo', TRUE);
    }
    
    $this->redirect('@organigrama_cargo');
  }
  
  public function executeAnular(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Organigrama_Cargo')->find($id);
    $cargo->setStatus('I');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El cargo ha sido anulado con exito, para reestablecerlo haga clic sobre "Cargos inactivos".');
    $this->redirect('cargo/index');
  }
  
  public function executeReactivar(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Organigrama_Cargo')->find($id);
    $cargo->setStatus('V');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El cargo ha sido reactivado con exito.');
    $this->redirect('cargo/index');
  }
  
  public function executeMovido(sfWebRequest $request)
  {
    $datos = $request->getParameter('organigrama_unidad');
    
    $cargo = Doctrine::getTable('Organigrama_Cargo')->find($this->getUser()->getAttribute('cargo_mover_id'));
    $cargo->setUnidadFuncionalId($datos['padre_id']);
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El cargo fue movido a la unidad seleccionada con exito.');
    $this->getUser()->getAttributeHolder()->remove('cargo_mover_id');
    $this->redirect('cargo/index');
  }
  
  public function executeMovidoMasivo(sfWebRequest $request)
  {
    $datos = $request->getParameter('organigrama_unidad');
    
    $unidad_funcional= $this->getUser()->getAttribute('unidad_funcional_id');
    $cargos = Doctrine::getTable('Organigrama_Cargo')->findByUnidadFuncionalId($unidad_funcional);
    
    foreach($cargos as $cargo) {
        $cargo->setUnidadFuncionalId($datos['padre_id']);
        $cargo->save();
    }
    
    $this->getUser()->setFlash('notice', 'Los cargos fueron movidos a la unidad seleccionada con exito.');
    $this->redirect('cargo/index');
  }
  
  public function executeActualizarInformacionInicialLaboral(sfWebRequest $request)
  {
    $datos_iniciales = $request->getParameter('datos_iniciales_laborales');

    $cargo = Doctrine::getTable('Organigrama_Cargo')->find($datos_iniciales['cargo_id']);
    $cargo->setPadreId($datos_iniciales['cargo_supervisor_id']);

    $cargo->save();
    
    if($request->getParameter('perfil')) {
        $this->getUser()->setFlash('notice', 'El supervisor inmediato ha sido cambiado.');
        $this->redirect(sfConfig::get('sf_app_funcionarios_url').'perfil');
    } else {
        $this->getUser()->setFlash('notice', 'Gracias por actualizar tus datos.');
        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
    }
  }
  
  public function executeTiposDeCondicion(sfWebRequest $request)
  {
      $condicion_id = $request->getParameter('condicion_id');
      
      $tipos = Doctrine::getTable('Organigrama_CargoTipo')
              ->createQuery('ct')
              ->where('ct.cargo_condicion_id = ?',$condicion_id)
              ->orderBy('nombre')
              ->execute();
      
      $cadena='<option value=""></option>';
      foreach ($tipos as $tipo)
          $cadena .= "<option value='".$tipo->getId()."'>".$tipo->getNombre()."</option>";
      echo $cadena;
      
      exit();
  }
  
  public function executeGradosDeTipos(sfWebRequest $request)
  {
      $tipo_id = $request->getParameter('tipo_id');
      
      $grados = Doctrine::getTable('Organigrama_CargoGrado')
              ->createQuery('cg')
              ->innerJoin('cg.Organigrama_CargoGradoTipo cgt')
              ->where('cgt.cargo_tipo_id = ?',$tipo_id)
              ->orderBy('cg.nombre')
              ->execute();
      
      $cadena='<option value=""></option>';
      foreach ($grados as $grado)
          $cadena .= "<option value='".$grado->getId()."'>".$grado->getNombre()."</option>";
      echo $cadena;
      
      exit();
  }
  
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $ban=0;
    if(!$form->getObject()->isNew())
    {
        $ban=1;
        $datos = $request->getParameter('organigrama_cargo');
        $datos = Doctrine::getTable('Organigrama_Cargo')->find($datos['id']);
        $perfil_anterior = $datos['perfil_id'];
    }

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $conn = Doctrine_Manager::connection();
      
      try {
        $conn->beginTransaction();
        $organigrama_cargo = $form->save();

        // SE VERIFICA SI EL PERFIL DEL CARGO SE HA CAMBIADO
        if($ban==1 && $organigrama_cargo->getPerfilId() != $perfil_anterior)
        {
            $funcionario = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findOneByCargoIdAndStatus($organigrama_cargo->getId(),'A');

            //Si existe un funcionario asignado a este cargo y se ha cambiado el perfil del cargo automaticamente se cambia el perfil del funcionario
            if($funcionario){
                $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$funcionario->getFuncionarioId());

                // BUSCA TODOS LOS CARGOS DEL FUNCIONARIO PARA COMPROBAR LOS PERFILES DE CADA CARGO ASIGNADO
                $multi_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findByFuncionarioIdAndStatus($funcionario->getFuncionarioId(),'A');

                if(count($multi_cargo)==1){ // SI EL FUNCIONARIO TIENE UN SOLO CARGO SE INACTIVA EL PERFIL ANTERIOR
                    $q = Doctrine_Query::create($conn);
                    $q->update('Acceso_UsuarioPerfil')->set('status', '?', 'I')
                      ->where('usuario_id = ?', $usuario->getId())
                      ->andWhere('perfil_id = ?', $perfil_anterior)
                      ->andWhere('status = ?', 'A')
                      ->execute();
                } else {
                    // SI EL FUNCIONARIO TIENE VARIOS CARGOS SE REALIZA UN CONTEO DE TODOS LOS PERFILES ACTIVOS DEL PERFIL QUE SE ESTA INACTIVANDO
                    $count_perfil = 0;
                    foreach ($multi_cargo as $funcionario_cargo) {
                        // BUSCAR LOS CARGO ACTIVO CON PERFIL ANTERIOR DEL CARGO MODIFICADO
                        $perfil_cargo = Doctrine::getTable('Organigrama_Cargo')->findOneByIdAndPerfilId($funcionario_cargo->getCargoId(),$perfil_anterior);

                        if($perfil_cargo) {
                            $count_perfil++;
                        }
                    }

                    // SI EL CONTEO DE PERFIL ES IGUAL A CERO (0) SE INACTIVA
                    if($count_perfil == 0) {
                        $q = Doctrine_Query::create($conn);
                        $q->update('Acceso_UsuarioPerfil')->set('status', '?', 'I')
                          ->where('usuario_id = ?', $usuario->getId())
                          ->andWhere('perfil_id = ?', $perfil_anterior)
                          ->andWhere('status = ?', 'A')
                          ->execute();
                    } else {
                        // SI EL PERFIL NO SE DEBE INACTIVAR SE BUSCA SI EL PERFIL ESTA DUPLICADO PARA CORREGIR BUG DE REGISTROS ANTES DE 17-02-2013
                        $usuario_perfil_activos = Doctrine::getTable('Acceso_UsuarioPerfil')->findByUsuarioIdAndPerfilIdAndStatus($usuario->getId(),$perfil_anterior,'A');

                        //Correccion de perfiles iguales. solo se deja uno. (para registros antes de 17-02-2013)
                        $i=1;
                        foreach ($usuario_perfil_activos as $usuario_perfil_activo) {
                            if($i>1){ // SOLO SE DEJA UN SOLO PERFIL DE ESE TIPO
                                $usuario_perfil_activo->setStatus('I');
                                $usuario_perfil_activo->save();
                            }
                            $i++;
                        }
                    }
                }

                // BUSCAR SI EL FUNCIONARIO NO TIENE EL PERFIL NUEVO YA ASOCIADO POR OTRO CARGO 
                $usuario_perfil_activos = Doctrine::getTable('Acceso_UsuarioPerfil')->findByUsuarioIdAndPerfilIdAndStatus($usuario->getId(),$organigrama_cargo->getPerfilId(),'A');
                
                if(count($usuario_perfil_activos)==0){
                    // SI EL FUNCIONARIO NO TIENE EL PERFIL NUEVO ANTERIORMENTE ASOCIADO, SE LE ASOCIA
                    $usuarioperfil = new Acceso_UsuarioPerfil();
                    $usuarioperfil->setUsuarioId($usuario->getId());
                    $usuarioperfil->setPerfilId($organigrama_cargo->getPerfilId()); //Perfil 1 = perteneciente a funcionarios publicos
                    $usuarioperfil->setStatus('A');
                    $usuarioperfil->setIdUpdate($this->getUser()->getAttribute('usuario_id'));

                    $usuarioperfil -> save();
                } elseif(count($usuario_perfil_activos)>1) {
                    //Correccion de perfiles iguales. solo se deja uno. (para registros antes de 17-02-2013)
                    $i=1;
                    foreach ($usuario_perfil_activos as $usuario_perfil_activo) {
                        if($i>1){ // SOLO SE DEJA UN SOLO PERFIL DE ESE TIPO
                            $usuario_perfil_activo->setStatus('I');
                            $usuario_perfil_activo->save();
                        }
                        $i++;
                    }
                }
            }
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $organigrama_cargo)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@organigrama_cargo_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'organigrama_cargo_edit', 'sf_subject' => $organigrama_cargo));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
