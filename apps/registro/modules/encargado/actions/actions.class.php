<?php

require_once dirname(__FILE__).'/../lib/encargadoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/encargadoGeneratorHelper.class.php';

/**
 * encargado actions.
 *
 * @package    siglas
 * @subpackage encargado
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class encargadoActions extends autoEncargadoActions
{
    public function executeAsistenteEncargado(sfWebRequest $request)
    {
        $this->registro_id= $request->getParameter('regid');
        
        $this->form = $this->configuration->getForm();
        $this->personas_persona = $this->form->getObject();
    }
    
    public function executeAnular(sfWebRequest $request)
    {
        $encargado_id= $request->getParameter('id');

        $registro_encargado= Doctrine::getTable('Operaciones_RegistroEncargado')->find($encargado_id);
        
        $registro_encargado->setStatus('I');
        $registro_encargado->save();
        
        $this->getUser()->setFlash('notice', 'El encargado de actividades deportivas fue anulado con exito.');
        $this->redirect('@personas_persona');
    }
    
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('@empresas_empresa_empresa');
    }
    
    public function executeBuscarEncargado(sfWebRequest $request) {
        $cedula= trim($request->getParameter('cedula'));
        $registro_id= trim($request->getParameter('regid'));

        $persona= Doctrine::getTable('Personas_Persona')->findOneByCi($cedula);

        $encargado_ar= array();
        if(count($persona) > 1) {
            //BUSCA LA LIGA ACTUAL
            $liga= Doctrine::getTable('Operaciones_Liga')->ligaActual();
            $liga_actual= '';
            foreach($liga as $value) {
                $liga_actual = $value->getLiga();
            }
            
            $existente= Doctrine::getTable('Operaciones_RegistroEncargado')->registroEncargadoPersona($persona->getId(), $liga_actual, $registro_id);

            if(count($existente) == 0) {
                //SIGNIFICA QUE EXISTE LA PERSONA PERO NO ESTA INSCRITA COMO ENCARGADO EN UNA EMPRESA Y EN UNA LIGA DETERMINADA
                $encargado_ar['status']= 'ok';
                $encargado_ar['content']['id']= $persona->getId();
                $encargado_ar['content']['ci']= $persona->getCi();
                $encargado_ar['content']['primerNombre']= $persona->getPrimerNombre();
                $encargado_ar['content']['primerApellido']= $persona->getPrimerApellido();
                $encargado_ar['content']['fNacimientoDia']= date("d", strtotime($persona->getFNacimiento()));
                $encargado_ar['content']['fNacimientoMes']= intval(date("m", strtotime($persona->getFNacimiento())));
                $encargado_ar['content']['fNacimientoAno']= date("Y", strtotime($persona->getFNacimiento()));
                $encargado_ar['content']['sexo']= $persona->getSexo();
                $encargado_ar['content']['edoCivil']= $persona->getEdoCivil();
                $encargado_ar['content']['telfMovil']= $persona->getTelfMovil();
                $encargado_ar['content']['emailPersonal']= $persona->getEmailPersonal();
            }else {
                //LA PERSONA EXISTE Y ADEMAS YA ESTA INSCRITA
                $encargado_ar['status']= 'existe';
            }
        }else {
            $data_saime= $this->saime($cedula);
            if($data_saime['persona_saime']) {
                $encargado_ar['status']= 'saime';
                $encargado_ar['content']['primerNombre']= $data_saime['primer_nombre'];
                $encargado_ar['content']['primerApellido']= $data_saime['primer_apellido'];
                $encargado_ar['content']['fNacimientoDia']= $data_saime['f_nacimiento_day'];
                $encargado_ar['content']['fNacimientoMes']= $data_saime['f_nacimiento_month'];
                $encargado_ar['content']['fNacimientoAno']= $data_saime['f_nacimiento_year'];
            }else {
                $encargado_ar['status']= 'empty';
            }
        }
        
        return $this->renderText(json_encode($encargado_ar));
    }
    
    static function saime($cedula) {
        $persona['persona_saime'] = false;
        
        $persona['cedula'] = $cedula;
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
                          WHERE ".$sf_seguridad['conexion_saime']['consulta']['campo_cedula']."=" . $cedula;

                $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                Doctrine_Manager::getInstance()->closeConnection($manager);
            } catch (Exception $e) {}
        }

        if ($result) {
            $persona['persona_saime'] = true;
            $persona['primer_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $persona['segundo_nombre'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_nombre']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $persona['primer_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            $persona['segundo_apellido'] = ucwords(strtr(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_segundo_apellido']]),"ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ","àèìòùáéíóúçñäëïöü"));
            
            $persona['f_nacimiento_day'] = date("d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $persona['f_nacimiento_month'] = date("m", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]))+0;
            $persona['f_nacimiento_year'] = date("Y", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
            
            $persona['f_nacimiento'] = date("Y-m-d", strtotime($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_f_nacimiento']]));
        }

        return($persona);
    }
    
    
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        $persona_id = $request->getParameter('persona_id');

        $registro_id= sfContext::getInstance()->getUser()->getAttribute('pae_registro_id');
        
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid())
        {
          $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

          $conn = Doctrine_Manager::connection();

          try {
                $conn->beginTransaction();

                if($persona_id == '') {
                    $personas_persona = $form->save();

                    $persona_id = $personas_persona->getId();
                }

                $operaciones_registro_encargado= new Operaciones_RegistroEncargado();

                $operaciones_registro_encargado->setRegistroId($registro_id);
                $operaciones_registro_encargado->setPersonaId($persona_id);
                $operaciones_registro_encargado->setStatus('A');

                $operaciones_registro_encargado->save();

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

          $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $personas_persona)));

          if ($request->hasParameter('_save_and_add'))
          {
            $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

            $this->redirect('@personas_persona_new');
          }
          else
          {
//            $this->getUser()->setFlash('notice', $notice);
//
//            $this->redirect(array('sf_route' => 'personas_persona_edit', 'sf_subject' => $personas_persona));
              $this->getUser()->setFlash('notice', 'El Delegado ha sido registrado con éxito.');
              $this->redirect('@personas_persona');
          }
        }
        else
        {
          $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
        }
    }


    public function executeSaveEncargado(sfWebRequest $request)
    {
        $form_personas_persona= $request->getParameter('personas_persona');
        $form_operaciones_registro= $request->getParameter('operaciones_registro');
        $form_operaciones_registro_encargado= $request->getParameter('operaciones_registro_encargado');
        $destination= $request->getParameter('destination');
//echo '<pre>';
//print_r($destination);
//echo '</pre><pre>';
//print_r($form_personas_persona);
//exit;

        //LIMPIADO DE DATOS
        $form_personas_persona['ci']= trim($form_personas_persona['ci']);
        $form_personas_persona['primer_nombre']= trim(ucfirst($form_personas_persona['primer_nombre']));
        $form_personas_persona['primer_apellido']= trim(ucfirst($form_personas_persona['primer_apellido']));
        $form_operaciones_registro_encargado['cargo']= trim(ucfirst($form_operaciones_registro_encargado['cargo']));
        
        $fecha_nacimiento= '';
        if(isset($form_personas_persona['f_nacimiento'])) {
            if(isset($form_personas_persona['f_nacimiento']['day']) && isset($form_personas_persona['f_nacimiento']['month']) && isset($form_personas_persona['f_nacimiento']['year'])) {
                $fecha_nacimiento= date('Y-m-d', strtotime($form_personas_persona['f_nacimiento']['year'].'-'.$form_personas_persona['f_nacimiento']['month'].'-'.$form_personas_persona['f_nacimiento']['day']));
                $form_personas_persona['f_nacimiento']= $fecha_nacimiento;
            }
        }
        
        $procede= FALSE;
        if($form_personas_persona['ci'] != '' && $form_personas_persona['primer_nombre'] != '' && $form_personas_persona['primer_apellido'] != '') {
            $procede= TRUE;
        }
        
        //SI EXISTE ESTE PARAMETRO ES PORQUE LA PERSONA EXISTE
        $new= TRUE;
        if($form_personas_persona['id'] != '') {
            $personas_persona= Doctrine::getTable('Personas_Persona')->find($form_personas_persona['id']);
            $new= FALSE;
        }else {
            $personas_persona= new Personas_Persona();
        }

        $conn = Doctrine_Manager::connection();
        if($procede) {
            try {
                $conn->beginTransaction();
                
                if($new) {
                    $personas_persona->setCi($form_personas_persona['ci']);
                    $personas_persona->setPrimerNombre($form_personas_persona['primer_nombre']);
                    $personas_persona->setPrimerApellido($form_personas_persona['primer_apellido']);
                    $personas_persona->setFNacimiento($form_personas_persona['f_nacimiento']);
                    $personas_persona->setSexo($form_personas_persona['sexo']);
                    $personas_persona->setEdoCivil($form_personas_persona['edo_civil']);
                    $personas_persona->setTelfMovil($form_personas_persona['telf_movil']);
                    $personas_persona->setEmailPersonal($form_personas_persona['email_personal']);

                }else {
                    //SI HAY ALGUN CAMBIO EN LOS SIGUIENTES CAMPOS ES QUE ACTUALIZA
                    if($form_personas_persona['primer_nombre'] != $personas_persona->getPrimerNombre()) { $personas_persona->setPrimerNombre($form_personas_persona['primer_nombre']); }
                    if($form_personas_persona['primer_apellido'] != $personas_persona->getPrimerApellido()) { $personas_persona->setPrimerApellido($form_personas_persona['primer_apellido']); }
                    if($form_personas_persona['telf_movil'] != $personas_persona->getTelfMovil()) { $personas_persona->setTelfMovil($form_personas_persona['telf_movil']); }
                    if($form_personas_persona['email_personal'] != $personas_persona->getEmailPersonal()) { $personas_persona->setEmailPersonal($form_personas_persona['email_personal']); }

                }

                $personas_persona->save();
                
                //CREACION DE REGISTRO O INSCRIPCION DE ENCARGADO
                $operaciones_registro_encargado= new Operaciones_RegistroEncargado();
                
                $operaciones_registro_encargado->setRegistroId($form_operaciones_registro['id']);
                $operaciones_registro_encargado->setPersonaId($personas_persona->getId());
                $operaciones_registro_encargado->setCargo($form_operaciones_registro_encargado['cargo']);
                $operaciones_registro_encargado->setStatus('A');
                
                $operaciones_registro_encargado->save();
                
                $conn->commit();
                
                switch ($destination) {
                    case '2':
                        $this->getUser()->setFlash('notice', 'El encargado de actividades deportivas ha sido agregado con exito. Ahora puede agregar otro.');
                        $this->redirect('encargado/asistenteEncargado?regid='.$form_operaciones_registro['id']);
                        break;
                    case '3':
                        $this->getUser()->setFlash('notice', 'El encargado de actividades deportivas ha sido agregado con exito. Ahora puede agregar delegados de prevención.');
                        $this->redirect('delegado/asistenteDelegado?regid='.$form_operaciones_registro['id']);
                        break;
                    default :
                        $this->getUser()->setFlash('notice', 'El encargado de actividades deportivas ha sido agregado con exito. Ahora puede agregar delegados de prevención.');
                        $this->redirect('delegado/asistenteDelegado?regid='.$form_operaciones_registro['id']);
                        break;
                }
            } catch (Doctrine_Validator_Exception $e) {
                $conn->rollBack();
                //MANIPULAR ERROR
                $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
                $this->redirect('empresa/asistente?paso=2&regid='.$form_operaciones_registro['id']);
            }
        }else {
            //MANIPULAR ERROR
            $this->getUser()->setFlash('error', 'No pudimos registrar los datos, por favor si la situacion persiste notifica al departamente de tecnología.');
            $this->redirect('empresa/asistente?paso=2&regid='.$form_operaciones_registro['id']);
        }
    }
}
