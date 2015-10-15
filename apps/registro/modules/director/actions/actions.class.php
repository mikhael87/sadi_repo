<?php

require_once dirname(__FILE__).'/../lib/directorGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/directorGeneratorHelper.class.php';

/**
 * director actions.
 *
 * @package    siglas
 * @subpackage director
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class directorActions extends autoDirectorActions
{
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('disciplina/index?id='.$this->getUser()->getAttribute('pae_registro_id'));
    }
    
    public function executeAnular(sfWebRequest $request)
    {
        $director_id= $request->getParameter('id');

        $registro_director= Doctrine::getTable('Operaciones_RegistroDirectorTecnico')->find($director_id);
        
        $registro_director->setStatus('I');
        $registro_director->save();
        
        $this->getUser()->setFlash('notice', 'El director técnico fue anulado con exito.');
        $this->redirect('@personas_persona_director');
    }
    
    public function executeBuscarDirector(sfWebRequest $request) {
        $cedula= trim($request->getParameter('cedula_verificar'));
        $registro_disciplina_id= trim($request->getParameter('registro_disciplina_id'));

        $persona= Doctrine::getTable('Personas_Persona')->findOneByCi($cedula);

        $director_ar= array();
        if(count($persona) > 1) {
            
            $existente= Doctrine::getTable('Operaciones_RegistroDirectorTecnico')->registroDirectorPersona($persona->getId(), $registro_disciplina_id);

            if(count($existente) == 0) {
                //SIGNIFICA QUE EXISTE LA PERSONA PERO NO ESTA INSCRITA COMO ENCARGADO EN UNA EMPRESA Y EN UNA LIGA DETERMINADA
                $director_ar['status']= 'ok';
                $director_ar['content']['id']= $persona->getId();
                $director_ar['content']['ci']= $persona->getCi();
                $director_ar['content']['primer_nombre']= $persona->getPrimerNombre();
                $director_ar['content']['primer_apellido']= $persona->getPrimerApellido();
                $director_ar['content']['f_nacimiento_day']= date("d", strtotime($persona->getFNacimiento()));
                $director_ar['content']['f_nacimiento_month']= intval(date("m", strtotime($persona->getFNacimiento())));
                $director_ar['content']['f_nacimiento_year']= date("Y", strtotime($persona->getFNacimiento()));
                $director_ar['content']['sexo']= $persona->getSexo();
                $director_ar['content']['edo_civil']= $persona->getEdoCivil();
                $director_ar['content']['telf_movil']= $persona->getTelfMovil();
                $director_ar['content']['email_personal']= $persona->getEmailPersonal();
            }else {
                //LA PERSONA EXISTE Y ADEMAS YA ESTA INSCRITA
                $director_ar['status']= 'existe';
            }
        }else {
            $data_saime= $this->saime($cedula);
            if($data_saime['persona_saime']) {
                $director_ar['status']= 'saime';
                $director_ar['content']['primer_nombre']= $data_saime['primer_nombre'];
                $director_ar['content']['primer_apellido']= $data_saime['primer_apellido'];
                $director_ar['content']['f_nacimiento_day']= $data_saime['f_nacimiento_day'];
                $director_ar['content']['f_nacimiento_month']= $data_saime['f_nacimiento_month'];
                $director_ar['content']['f_nacimiento_year']= $data_saime['f_nacimiento_year'];
            }else {
                $director_ar['status']= 'empty';
            }
        }
        
        return $this->renderText(json_encode($director_ar));
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

        $registro_disciplina_id= sfContext::getInstance()->getUser()->getAttribute('pae_registro_disciplina_id');
        
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
                
                $operaciones_registro_director= new Operaciones_RegistroDirectorTecnico();

                $operaciones_registro_director->setRegistroDisciplinaId($registro_disciplina_id);
                $operaciones_registro_director->setPersonaId($persona_id);
                $operaciones_registro_director->setStatus('A');

                $operaciones_registro_director->save();

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

            $this->redirect('@personas_persona_director_new');
          }
          else
          {
//            $this->getUser()->setFlash('notice', $notice);
//
//            $this->redirect(array('sf_route' => 'personas_persona_director_edit', 'sf_subject' => $personas_persona));
              $this->getUser()->setFlash('notice', 'El Delegado ha sido registrado con éxito.');
              $this->redirect('@personas_persona_director');
          }
        }
        else
        {
          $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
        }
    }
}
