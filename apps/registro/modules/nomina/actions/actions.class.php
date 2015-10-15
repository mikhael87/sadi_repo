<?php

require_once dirname(__FILE__).'/../lib/nominaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/nominaGeneratorHelper.class.php';

/**
 * nomina actions.
 *
 * @package    siglas
 * @subpackage nomina
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class nominaActions extends autoNominaActions
{
    public function executeNew(sfWebRequest $request)
    {
      if($this->getUser()->hasAttribute('pae_registro_disciplina_id') && $this->getUser()->hasAttribute('pae_registro_id')) {
          $this->form = $this->configuration->getForm();
          $this->personas_persona = $this->form->getObject();
      }else {
          $this->getUser()->setFlash('notice', 'Ha ocurrido un error inesperado, si persiste por favor notifíquelo.');
          $this->redirect('disciplina/index?id='.$this->getUser()->getAttribute('pae_registro_id'));
      }
    }
    
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('disciplina/index?id='.$this->getUser()->getAttribute('pae_registro_id'));
    }
    
    public function executeInactivar(sfWebRequest $request)
    {
      //FUNCION PARA ANULAR PARTICIPANTES
        $registro_persona_id= $request->getParameter('id');

        $registro_persona= Doctrine::getTable('Operaciones_RegistroPersona')->find($registro_persona_id);

        $conn = Doctrine_Manager::connection();
        try {
          
            $conn->beginTransaction();
        
            $registro_persona->setStatus('I');
            $registro_persona->save();
            
            $conn->commit();
            $this->getUser()->setFlash('notice', 'El participante ha sido anulado con exito.');
        } catch (Doctrine_Validator_Exception $e) {

          $conn->rollBack();
            
          $this->getUser()->setFlash('error', 'Ha ocurrido un error al intentar marcar recaudos como recibidos. Intente hacerlo individualmente.');
          $this->redirect('nomina/index?id='.$this->getUser()->getAttribute('pae_registro_disciplina_id'));
        }
        
        $this->redirect('nomina/index?id='.$this->getUser()->getAttribute('pae_registro_disciplina_id'));
    }
    
    public function executeMarcar(sfWebRequest $request)
    {
        //FUNCION PARA MARCAR TODOS LOS RECAUDOS PENDIENTES COMO RECIBIDOS
        $registro_persona_id= $request->getParameter('id');

        $registro_persona_requisito= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->findByRegistroPersonaIdAndStatus($registro_persona_id, 'P');

        $conn = Doctrine_Manager::connection();
        try {
          
            $conn->beginTransaction();
        
            foreach($registro_persona_requisito as $value) {
                $requisito= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->find($value->getId());
                
                $requisito->setStatus('C');
                $requisito->save();
            }
            
            $conn->commit();
            $this->getUser()->setFlash('notice', 'Todos los recaudos han sido marcados como recibidos con exito.');
        } catch (Doctrine_Validator_Exception $e) {

          $conn->rollBack();
            
          $this->getUser()->setFlash('error', 'Ha ocurrido un error al intentar marcar recaudos como recibidos. Intente hacerlo individualmente.');
          $this->redirect('nomina/index?id='.$this->getUser()->getAttribute('pae_registro_disciplina_id'));
        }
        
        $this->redirect('nomina/index?id='.$this->getUser()->getAttribute('pae_registro_disciplina_id'));
    }
    
    public function executeChangeRecaudo(sfWebRequest $request)
    {
        $id= $request->getParameter('id');
        $status= $request->getParameter('status');
        
        if($request->getParameter('status') == 'true') {
            $status= 'C';
        }else {
            $status= 'P';
        }

        $recaudo= Doctrine::getTable('Operaciones_RegistroPersonaRequisito')->find($id);

        if($recaudo) {
            if($status != $recaudo->getStatus()) {
                $recaudo->setStatus($status);
                $recaudo->save();
            }
            
            echo $recaudo->getStatus();
        }else {
            echo 'error';
        }
        exit();
    }
    
    public function executeBuscarParticipante(sfWebRequest $request) {
        $cedula= $request->getParameter('cedula');
        $rdi= $this->getUser()->getAttribute('pae_registro_disciplina_id');
        $ri= $this->getUser()->getAttribute('pae_registro_id');
        
        //DETERMINA SI ESTA PERSONA YA ESTA REGISTRADA MAS DE 3 VECES EN LA MISMA EMPRESA
        $reiterado_por_equipo= Doctrine::getTable('Operaciones_RegistroPersona')->cantidadRegistradoPorEquipo($cedula, $rdi);
        
        if($reiterado_por_equipo[0][0] == 0) {
            //LA PERSONA NO FUE ENCONTRADA EN ESTE EQUIPO
            $reiterado_por_empresa= Doctrine::getTable('Operaciones_RegistroPersona')->cantidadRegistradoPorEmpresa($cedula, $ri);

            if($reiterado_por_empresa[0][0] < 3) {
                //LA PERSONA NO ESTA REGISTRADA AUN
                $persona= Doctrine::getTable('Personas_Persona')->findOneByCi($cedula);

                $participante_ar= array();
                if(count($persona) > 1) {
                    $participante_ar['status']= 'owndb';
                    $participante_ar['content']['id']= $persona->getId();
                    $participante_ar['content']['ci']= $persona->getCi();
                    $participante_ar['content']['primerNombre']= $persona->getPrimerNombre();
                    $participante_ar['content']['primerApellido']= $persona->getPrimerApellido();
                    $participante_ar['content']['fNacimientoDia']= date("d", strtotime($persona->getFNacimiento()));
                    $participante_ar['content']['fNacimientoMes']= intval(date("m", strtotime($persona->getFNacimiento())));
                    $participante_ar['content']['fNacimientoAno']= date("Y", strtotime($persona->getFNacimiento()));
                    $participante_ar['content']['sexo']= $persona->getSexo();
                    $participante_ar['content']['edoCivil']= $persona->getEdoCivil();
                    $participante_ar['content']['telfMovil']= $persona->getTelfMovil();
                    $participante_ar['content']['emailPersonal']= $persona->getEmailPersonal();
                }else {
                    $data_saime= $this->saime($cedula);
                    if($data_saime['persona_saime']) {
                        $participante_ar['status']= 'saime';
                        $participante_ar['content']['primerNombre']= $data_saime['primer_nombre'];
                        $participante_ar['content']['primerApellido']= $data_saime['primer_apellido'];
                        $participante_ar['content']['fNacimientoDia']= $data_saime['f_nacimiento_day'];
                        $participante_ar['content']['fNacimientoMes']= $data_saime['f_nacimiento_month'];
                        $participante_ar['content']['fNacimientoAno']= $data_saime['f_nacimiento_year'];
                    }else {
                        $participante_ar['status']= 'empty';
                    }
                }
            }else {
                //SIGNIFICA QUE ESTA REGISTRADO MAS DE 3 VECES EN DISTINTOS EQUIPOS DENTRO DE LA MISMA EMPRESA
                $participante_ar['status']= 'empresa';
            }
        }else {
            //SIGNIFICA QUE LA PERSONA YA ESTA REGISTRADA EN ESTE EQUIPO
            $participante_ar['status']= 'equipo';
        }
        
        return $this->renderText(json_encode($participante_ar));
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
      $datos = $request->getParameter('personas_persona');

      //LIMPIADO DE DATOS
      $datos['ci']= trim($datos['ci']);
      $datos['primer_nombre']= trim(ucfirst($datos['primer_nombre']));
      $datos['primer_apellido']= trim(ucfirst($datos['primer_apellido']));

      $fecha_nacimiento= '';
      if(isset($datos['f_nacimiento'])) {
        if(isset($datos['f_nacimiento']['day']) && isset($datos['f_nacimiento']['month']) && isset($datos['f_nacimiento']['year'])) {
            $fecha_nacimiento= date('Y-m-d', strtotime($datos['f_nacimiento']['year'].'-'.$datos['f_nacimiento']['month'].'-'.$datos['f_nacimiento']['day']));
            $datos['f_nacimiento']= $fecha_nacimiento;
        }
      }

      $exist= FALSE;
      if($datos['id']!=''){
        $personas_persona = Doctrine::getTable('Personas_Persona')->find($datos['id']);
        $exist= TRUE;
      }else {
        $personas_persona= new Personas_Persona();
      }

      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'El participante ha sido inscrito con exito.' : 'The item was updated successfully.';

        $conn = Doctrine_Manager::connection();
        try {
          
          $conn->beginTransaction();
            
          if(!$exist) {
            $personas_persona->setCi($datos['ci']);
            $personas_persona->setPrimerNombre($datos['primer_nombre']);
            $personas_persona->setPrimerApellido($datos['primer_apellido']);
            $personas_persona->setFNacimiento($datos['f_nacimiento']);
            $personas_persona->setSexo($datos['sexo']);
            $personas_persona->setEdoCivil($datos['edo_civil']);
            $personas_persona->setTelfMovil($datos['telf_movil']);
            $personas_persona->setEmailPersonal($datos['email_personal']);

            $personas_persona->save();
         }else {
            //SI HAY ALGUN CAMBIO EN LOS SIGUIENTES CAMPOS ES QUE ACTUALIZA
            if($datos['primer_nombre'] != $personas_persona->getPrimerNombre()) { $personas_persona->setPrimerNombre($datos['primer_nombre']); }
            if($datos['primer_apellido'] != $personas_persona->getPrimerApellido()) { $personas_persona->setPrimerApellido($datos['primer_apellido']); }
            if($datos['telf_movil'] != $personas_persona->getTelfMovil()) { $personas_persona->setTelfMovil($datos['telf_movil']); }
            if($datos['email_personal'] != $personas_persona->getEmailPersonal()) { $personas_persona->setEmailPersonal($datos['email_personal']); }
         }

          //CREACION DE PARTICIPANTE
          $registro_persona = new Operaciones_RegistroPersona();
          
          $registro_persona->setPersonaId($personas_persona->getId());
          $registro_persona->setRegistroDisciplinaId($this->getUser()->getAttribute('pae_registro_disciplina_id'));
          $registro_persona->setStatus('A');
          
          $registro_persona->save();
          
          //CREACION DE RECAUDOS DE PARTICIPANTES
          $recaudos= Doctrine::getTable('Operaciones_Requisito')->findByTipoAndStatus('P','A');

          foreach($recaudos as $recaudo) {
              $registro_persona_requisito = new Operaciones_RegistroPersonaRequisito();
              
              $registro_persona_requisito->setRegistroPersonaId($registro_persona->getId());
              $registro_persona_requisito->setRequisitoId($recaudo->getId());
              $registro_persona_requisito->setStatus('P');
              
              $registro_persona_requisito->save();
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

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $personas_persona)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' Ahora puedes inscribir otro.');

          $this->redirect('@personas_persona_nomina_new');
        }
        else
        {
          $this->getUser()->setFlash('notice', $notice);

//          $this->redirect(array('sf_route' => 'personas_persona_nomina_edit', 'sf_subject' => $personas_persona));
          $this->redirect('disciplina/index?id='.$this->getUser()->getAttribute('pae_registro_disciplina_id'));
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      }
    }
}
