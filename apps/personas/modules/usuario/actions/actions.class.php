<?php

require_once dirname(__FILE__).'/../lib/usuarioGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/usuarioGeneratorHelper.class.php';

/**
 * usuario actions.
 *
 * @package    siglas
 * @subpackage usuario
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class usuarioActions extends autoUsuarioActions
{
  public function executeIndex(sfWebRequest $request)
  {
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
  
  public function executeInactivos(sfWebRequest $request)
  {
    $inactivo = $request->getParameter('inac');

    $this->getUser()->getAttributeHolder()->remove('func_inactivo');
    
    if($inactivo == 'true') {
        $this->getUser()->setAttribute('func_inactivo', TRUE);
    }
    
    $this->redirect('@personas_persona');
  }
  
  public function executeAnular(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Personas_Persona')->find($id);
    $cargo->setStatus('I');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El usuario ha sido anulado con exito, para reestablecerlo haga clic sobre "Usuarios inactivos".');
    $this->redirect('@personas_persona');
  }
  
  public function executeReactivar(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $cargo = Doctrine::getTable('Personas_Persona')->find($id);
    $cargo->setStatus('A');
    $cargo->save();
    
    $this->getUser()->setFlash('notice', 'El usuario ha sido reactivado con exito.');
    $this->redirect('@personas_persona');
  }

  public function executePasswd(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByPersonaId($id);
    $usuario->setClave(crypt(strtolower($usuario->getNombre()),strtolower($usuario->getNombre())));
    $usuario->setStatus('A');
    $usuario->save();

    $this->getUser()->setFlash('notice', 'Contraseña reiniciada al mismo nombre de usuario con exito.');
    $this->redirect('@personas_persona');
  }
  
  public function executeChequearCedulaExistente(sfWebRequest $request)
  {
    $cedula = trim($request->getParameter('ci'));

    $existente['status'] = false;
    $funcionario = Doctrine::getTable('Personas_Persona')->findOneByCi($cedula);
    if($funcionario){
        $existente['status'] = true;
    }

    return $this->renderText(json_encode($existente));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $usuario_perfil_id = $request->getParameter('personas_persona_perfil');

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
	  if($form->getObject()->isNew())
	  {
              $notice = 'El usuario se ha registrado correctamente.';

              $conn = Doctrine_Manager::connection();

              try
              {
                  $conn->beginTransaction();
                  $personas_persona = $form->save();

                  $nombre_usuario = new herramientas();
                  $nombre_usuario = $nombre_usuario->generarUsuario($personas_persona->getPrimerNombre(), $personas_persona->getSegundoNombre(), $personas_persona->getPrimerApellido(), $personas_persona->getSegundoApellido());

                  $usuario = new Acceso_Usuario();
                  $usuario->setPersonaId($personas_persona->getId());
                  $usuario->setNombre(strtolower($nombre_usuario));
                  $usuario->setClave(crypt(strtolower($nombre_usuario),strtolower($nombre_usuario)));
                  $usuario->setVisitas(0);
                  $usuario->setUltimaconexion(date('Y-m-d h:i:s'));
                  $usuario->setUltimocambioclave(date('Y-m-d h:i:s'));
                  $usuario->setStatus('A');
                  $usuario->setTema('estandar');
                  $usuario->save();

                  $usuario_perfil = new Acceso_UsuarioPerfil();
                  $usuario_perfil->setUsuarioId($usuario->getId());
                  $usuario_perfil->setPerfilId($usuario_perfil_id);
                  $usuario_perfil->setStatus('A');
                  $usuario_perfil->save();

                  $conn->commit();
              }
              catch (Doctrine_Validator_Exception $e)
              {
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
	  }
	  else
	  {
                try
                {
                    $notice = 'Los datos del Usuario se han actualizado correctamente.';
                    $personas_persona = $form->save();
                    
                    //ACTUALIZA PERFIL SI HA CAMBIADO
                    $perfil= Doctrine::getTable('Acceso_Perfil')->perfilesActivosPerPersona($personas_persona->getId());
                    $perfi_actual= ''; $usuario_id= '';
                    foreach($perfil as $value) {
                        $perfi_actual= $value->getId();
                        $usuario_id= $value->getUsuarioId();
                    }
                    if($perfi_actual !== $usuario_perfil_id) {
                        $perfil= Doctrine::getTable('Acceso_UsuarioPerfil')->findOneByUsuarioId($usuario_id);
                        
                        $perfil->setPerfilId($usuario_perfil_id);
                        $perfil->save();
                    }
                    
                }
                catch (Doctrine_Validator_Exception $e)
                {
                    $errorStack = $form->getObject()->getErrorStack();

                    $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
                    foreach ($errorStack as $field => $errors) {
                        $message .= "$field (" . implode(", ", $errors) . "), ";
                    }
                    $message = trim($message, ', ');

                    $this->getUser()->setFlash('error', $message);
                    return sfView::SUCCESS;
                }
	  }



      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $personas_persona)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' Puede continuar registrando otro usuario.');

        $this->redirect('@personas_persona_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('@personas_persona');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'Los datos del Usuario no se han registrado ya que existen algunos errores.', false);
    }
  }
  
  public function executeFoto(sfWebRequest $request)
  {
    $persona = Doctrine::getTable('Personas_Persona')->find($request->getParameter('id'));
    $this->getUser()->setAttribute('foto_cambio', $persona->getCi());

    $this->redirect(sfConfig::get('sf_app_personas_url').'foto?from=foto');
  }

  public function executeCheckUser(sfWebRequest $request)
  {
      $id_usr = $request->getParameter('id_usr');
      
      $nombre= trim($request->getParameter('ext1')).'.'.trim($request->getParameter('ext2'));
      $usuario = Doctrine::getTable('Acceso_Usuario')->findByNombre($nombre);
      
      $this->usuario= count($usuario);
      $this->id_usr= $id_usr;
      $this->nombre= $nombre;
      $this->tipo_user= $request->getParameter('tipo_user');
  }
  
  public function executeSaveUser(sfWebRequest $request)
  {
      $id_usr = $request->getParameter('id_usr');
      $nombre = trim($request->getParameter('nombre'));
      $tipo_user = $request->getParameter('tipo_user');
      
      $usuario = Doctrine::getTable('Acceso_Usuario')->find($id_usr);
      
      $usuario->setClave(crypt(strtolower($usuario->getNombre()),strtolower($usuario->getNombre())));
      $usuario->setStatus('A');
      $usuario->setNombre(strtolower(trim($nombre)));
      $usuario->save();
      
      $this->id_usr= $id_usr;
      
      $this->getUser()->setFlash('notice', ' La clave de ingreso de usuario ha sido reestablecida.');
  }
  
  public function executeVerificarCedula(sfWebRequest $request)
  {
        $funcionario['persona_saime'] = false;
        
        $funcionario['cedula'] = $request->getParameter('cedula_verificar');
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
                          WHERE ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula']."=" . $request->getParameter('cedula_verificar');

                $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                Doctrine_Manager::getInstance()->closeConnection($manager);
            } catch (Exception $e) {}
        }

        if ($result) {
            $funcionario['persona_saime'] = true;
            $funcionario['primer_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['segundo_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['primer_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $funcionario['segundo_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            
            $funcionario['f_nacimiento_day'] = date("d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $funcionario['f_nacimiento_month'] = date("m", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $funcionario['f_nacimiento_year'] = date("Y", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
            
            $funcionario['f_nacimiento'] = date("Y-m-d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
        }
        
        $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        sleep(1);
        return $this->renderText(json_encode($funcionario));
  }
}

class ConPie extends TCPDF {
     public function Footer() {
        $this->Image('http://'.$_SERVER['SERVER_NAME'].'/images/organismo/pdf/gob_footer_pdf.png',0,750,450,70,'','','N','','','C');
    }
}
