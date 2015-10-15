<?php

/**
 * organismo actions.
 *
 * @package    siglas
 * @subpackage organismo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class organismoActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  public function executeSaveOrganismo(sfWebRequest $request)
  {
        $organismo = $request->getParameter('organismo');
        $siglas = $request->getParameter('siglas');
        $tipo = $request->getParameter('tipo');
        $this->id_save = 0;

        try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();

            $organismo_n = new Organismos_Organismo();
            $organismo_n->setNombre($organismo);
            $organismo_n->setSiglas($siglas);
            $organismo_n->setOrganismoTipoId($tipo);
            @$organismo_n->save();

            $this->id_save = $organismo_n->getId();

            $this->getUser()->setFlash('notice_organismo', 'Organismo agregado con éxito.');
            $conn->commit();
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_organismo', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }

        $this->organismo_name = $request->getParameter('organismo_name');
        $this->persona_name = $request->getParameter('persona_name');
        $this->cargo_name = $request->getParameter('cargo_name');
        $this->setTemplate('listarOrganismos');
  }

  public function executeListarOrganismos(sfWebRequest $request)
  {
        $this->persona_name = $request->getParameter('persona_name');
        $this->cargo_name = $request->getParameter('cargo_name');
        $this->organismo_name = $request->getParameter('organismo_name');
        $this->id_save = $request->getParameter('id_select');
  }

  public function executeListarPersonas(sfWebRequest $request)
  {
        $this->persona_name = $request->getParameter('persona_name');
        $this->cargo_name = $request->getParameter('cargo_name');
        $this->id_save = $request->getParameter('id_old');

        $personas = Doctrine_Query::create()
                    ->select("id, nombre_simple")
                    ->from('Organismos_Persona')
                    ->where('organismo_id = ?',$request->getParameter('o_id'))
                    ->andWhere('status = ?','A')
                    ->orderBy('nombre_simple')
                    ->execute(array(), Doctrine::HYDRATE_NONE);

        $personas_array=array();
        foreach ($personas as $persona) {
            $personas_array[$persona[0]] = $persona[1];
        }

        $this->personas = $personas_array;
  }

  public function executeVerificarOrganismos(sfWebRequest $request)
  {
        $datos= $request->getParameter('datos');

        $datos_nombre= $datos['organismo'];
        $datos_siglas= $datos['siglas'];
        $datos_tipo= $datos['tipo'];

        $organismo_name= $request->getParameter('organismo_name');
        $persona_name= $request->getParameter('persona_name= $request');
        $cargo_name= $request->getParameter('cargo_name');

        // SE REALIZA UNA BUSQUEDA CON LEVENSHTEIN SOBRE NOMBRE DE ORGANISMO PARECIDA CON DISTANCIA DE 2 PASOS
        $organismos_old = Doctrine_Query::create()
            ->select("o.id, o.nombre, o.siglas,
                      levenshtein(
                            translate(lower(o.nombre),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_nombre."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) as distancia_nombre")
            ->from('Organismos_Organismo o')
            ->Where('o.status = ?','A')
            ->andWhere("levenshtein(
                            translate(lower(o.nombre),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_nombre."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) <= 2")
//            ->orWhere("levenshtein(
//                            translate(lower(siglas),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
//                            translate(lower('".$datos_siglas."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) <= 2")
            ->orderBy("levenshtein(
                            translate(lower(o.nombre),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_nombre."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'))")
            ->execute(array(), Doctrine::HYDRATE_NONE);

        // SE ARMA EL ARRAY DE LOS ORGANISMOS ENCONTRADAS
        $organismos_verificar=array();
        $i= 0;
        foreach ($organismos_old as $organismo_old) {
            $organismos_verificar[$i]['id'] = $organismo_old[0];
            $organismos_verificar[$i]['nombre'] = $organismo_old[1];
//            $organismos_verificar[$i]['nombre']['distancia'] = $organismo_old[3];
            $organismos_verificar[$i]['siglas'] = $organismo_old[2];
//            $organismos_verificar[$i]['siglas']['distancia'] = $organismo_old[4];

            $i++;
        }

        if(count($organismos_verificar)==0){
            // SI NO ENCUENTRA ORGANISMOS PROCEDE A GUARDAR
            $this->redirect("organismo/saveOrganismo?organismo=".$datos_nombre.
                                                  "&siglas=".$datos_siglas.
                                                  "&tipo=".$datos_tipo.
                                                  "&organismo_name=".$organismo_name.
                                                  "&persona_name=".$persona_name.
                                                  "&cargo_name=".$cargo_name);
        } else {
                // SI ENCUENTRA VARIOS ORGANISMOS CON DISTANCIAS CERO O SUPERIOR MUESTRA DIV DE VERIFICACION
                $this->datos_organismo_nombre = $datos_nombre;
                $this->datos_organismo_siglas = $datos_siglas;
                $this->datos_organismo_tipo = $datos_tipo;
                $this->organismos_verificar = $organismos_verificar;

                $this->setTemplate('verificarOrganismos');
        }
  }

  public function executeVerificarCedula(sfWebRequest $request)
  {
        $visitante['persona_id'] = "";
        
        if ($request->getParameter('cedula_verificar') != null) {
            $persona = Doctrine::getTable('Organismos_Persona')->findOneByOrganismoIdAndCi($request->getParameter('datos_organismo_id'),$request->getParameter('cedula_verificar'));

            if (!$persona) {
                $visitante['cedula'] = $request->getParameter('cedula_verificar');
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
                    $visitante['primer_nombre'] = ucwords(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_nombre']]));
                    $visitante['primer_apellido'] = ucwords(strtolower($result[0][$sf_seguridad['conexion_saime']['consulta']['campo_primer_apellido']]));
                    
                    $organismos_persona = new Organismos_Persona();
                    $organismos_persona->setCi($visitante['cedula']);
                    $organismos_persona->setOrganismoId($request->getParameter('datos_organismo_id'));
                    $organismos_persona->setNombreSimple($visitante['primer_nombre'].' '.$visitante['primer_apellido']);
                    $organismos_persona->setPrimerNombre($visitante['primer_nombre']);
                    $organismos_persona->setPrimerApellido($visitante['primer_apellido']);
                    $organismos_persona->save();
                    
                    $visitante['persona_id'] = $organismos_persona->getId();
                }
            } else {
                $visitante['persona_id'] = $persona->getId();
                $visitante['primer_nombre'] = $persona->getPrimerNombre();
                $visitante['primer_apellido'] = $persona->getPrimerApellido();
            }
        }
        
        $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        sleep(1);
        return $this->renderText(json_encode($visitante));
  }
  
  public function executeVerificarPersonas(sfWebRequest $request)
  {
        $datos_persona = $request->getParameter('datos_persona');
        $datos_organismo_id = $request->getParameter('datos_organismo_id');

        // SE REALIZA UNA BUSQUEDA CON LEVENSHTEIN SOBRE NOMBRE DE PERSONAS PARECIDA CON DISTANCIA DE 2 PASOS
        $personas_old = Doctrine_Query::create()
            ->select("id, nombre_simple,
                      levenshtein(
                            translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_persona."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) as distancia")
            ->from('Organismos_Persona')
            ->where('organismo_id = ?',$datos_organismo_id)
            ->andWhere('status = ?','A')
            ->andWhere("levenshtein(
                            translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_persona."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou')) <= 2")
            ->orderBy("levenshtein(
                            translate(lower(nombre_simple),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'),
                            translate(lower('".$datos_persona."'),'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ&-,. ','aeiouaeiouaeiouaeiou'))")
            ->execute(array(), Doctrine::HYDRATE_NONE);

        // SE ARMA EL ARRAY DE LAS PERSONAS ENCONTRADAS
        $personas_verificar=array(); $personas_distancia=array(); $personas_distacia_cero=0;
        foreach ($personas_old as $persona_old) {
            $personas_verificar[$persona_old[0]] = $persona_old[1];
            $personas_distancia[$persona_old[0]] = $persona_old[2];

            if($persona_old[2]==0){
                $personas_distacia_cero++;
                $personas_distacia_cero_id = $persona_old[0];
            }
        }

        if(count($personas_verificar)==0){
            // SI NO ENCUENTRA PERSONAS PROCEDE A GUARDAR
            $this->redirect("organismo/savePersona?datos_persona=".$datos_persona.
                                                  "&datos_organismo_id=".$datos_organismo_id.
                                                  "&persona_name=".$request->getParameter('persona_name').
                                                  "&cargo_name=".$request->getParameter('cargo_name'));
        } else {
            if($personas_distacia_cero==1){
                // SI ENCUENTRA UNA SOLA PERSONA CON DISTANCIA CERO LA SELECCIONA POR DEFECTO
                $this->persona_name = $request->getParameter('persona_name');
                $this->cargo_name = $request->getParameter('cargo_name');

                $personas = Doctrine_Query::create()
                            ->select("id, nombre_simple")
                            ->from('Organismos_Persona')
                            ->where('organismo_id = ?',$datos_organismo_id)
                            ->andWhere('status = ?','A')
                            ->orderBy('nombre_simple')
                            ->execute(array(), Doctrine::HYDRATE_NONE);

                $personas_array=array();
                foreach ($personas as $persona) {
                    $personas_array[$persona[0]] = $persona[1];
                }

                $this->personas = $personas_array;



                $this->id_save = $personas_distacia_cero_id;

                $this->setTemplate('listarPersonas');

            } else {
                // SI ENCUENTRA VARIAS PERSONAS CON DISTANCIAS CERO O SUPERIOR MUESTRA DIV DE VERIFICACION
                $this->datos_persona = $datos_persona;
                $this->datos_organismo_id = $datos_organismo_id;
                $this->persona_name = $request->getParameter('persona_name');
                $this->cargo_name = $request->getParameter('cargo_name');
                $this->personas_verificar = $personas_verificar;

                $this->setTemplate('verificarPersonas');
            }
        }
  }

  public function executeSavePersona(sfWebRequest $request)
  {
        $this->id_save = 0;

        try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();

            $persona = new Organismos_Persona();
            $persona->setNombreSimple($request->getParameter('datos_persona'));
            $persona->setOrganismoId($request->getParameter('datos_organismo_id'));
            @$persona->save();

            $this->id_save = $persona->getId();

            $this->getUser()->setFlash('notice_persona', 'Persona agregada con éxito.');
            $conn->commit();
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_persona', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }

        $this->persona_name = $request->getParameter('persona_name');
        $this->cargo_name = $request->getParameter('cargo_name');

        $personas = Doctrine_Query::create()
                    ->select("id, nombre_simple")
                    ->from('Organismos_Persona')
                    ->where('organismo_id = ?',$request->getParameter('datos_organismo_id'))
                    ->andWhere('status = ?','A')
                    ->orderBy('nombre_simple')
                    ->execute(array(), Doctrine::HYDRATE_NONE);

        $personas_array=array();
        foreach ($personas as $persona) {
            $personas_array[$persona[0]] = $persona[1];
        }

        $this->personas = $personas_array;

        $this->setTemplate('listarPersonas');
  }

  public function executeListarCargos(sfWebRequest $request)
  {
        $this->cargo_name = $request->getParameter('cargo_name');

        $this->cargos = Doctrine::getTable('Organismos_PersonaCargo')
                            ->createQuery('a')
                            ->where('persona_id = ?',$request->getParameter('p_id'))
                            ->andWhere('status = ?','A')
                            ->orderBy('nombre')->execute();
  }

  public function executeSaveCargo(sfWebRequest $request)
  {
        $datos = $request->getParameter('datos');
        $this->id_save = 0;

        try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();

            $persona_cargo = new Organismos_PersonaCargo();
            $persona_cargo->setNombre($datos['cargo']);
            $persona_cargo->setPersonaId($datos['persona_id']);
            @$persona_cargo->save();

            $this->id_save = $persona_cargo->getId();

            $this->getUser()->setFlash('notice_cargo', 'Cargo agregado con éxito.');
            $conn->commit();
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_cargo', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }

        $this->cargo_name = $request->getParameter('cargo_name');

        $this->cargos = Doctrine::getTable('Organismos_PersonaCargo')
                            ->createQuery('a')
                            ->where('persona_id = ?',$datos['persona_id'])
                            ->andWhere('status = ?','A')
                            ->orderBy('nombre')->execute();
        $this->setTemplate('listarCargos');
  }
}
