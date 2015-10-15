<?php

require_once dirname(__FILE__).'/../lib/funcionario_cargoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/funcionario_cargoGeneratorHelper.class.php';

/**
 * funcionario_cargo actions.
 *
 * @package    sigla-(institution)
 * @subpackage funcionario_cargo
 * @author     Livio López. liviolopez@gmail.com. (058)426-511.42.50. Venezuela-Caracas
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class funcionario_cargoActions extends autoFuncionario_cargoActions
{
    public function unidadAdscripcion($unidad_id){
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidad_id);
        if($unidad->getAdscripcion()==false){
            $unidad = $this->unidadAdscripcion($unidad->getPadreId());
        }

        return $unidad;
    }
    
  public function executeCargosVacios(sfWebRequest $request)
  {
    $unidad_id = $request->getParameter('unidad_id');
    $this->cargos_vacios = Doctrine::getTable('Organigrama_Cargo')->cargosVacios($unidad_id);
  }
  
  public function executeCertificarFirma(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('certificados_funcionario_cargo_id', $id);
    $this->redirect('funcionario_cargo_certificado/index');
  }
  
  public function executeColetilla(sfWebRequest $request)
  {
    $this->from= $request->getParameter('from');
    if($this->from == 'inicio')
        $this->getUser()->getAttributeHolder()->remove('pae_funcionario_id');
    $id= ($this->getUser()->getAttribute('pae_funcionario_id'))? $this->getUser()->getAttribute('pae_funcionario_id') : $this->getUser()->getAttribute('funcionario_id');
    $this->funcionario_cargos = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadCargoActual($id);
  }

  public function executeSaveColetilla(sfWebRequest $request)
  {
    $coletillas = $request->getParameter('coletillas');

    foreach ($coletillas as $funcionario_cargo_id => $coletilla) {


        $funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($funcionario_cargo_id);

        //LIMPIA LA COLETILLA
        $firma= preg_replace("/<p(.*?)>/i","<br/>",$coletilla);
        $firma= preg_replace("[</p>]","",$firma);
        $firma= preg_replace("/<!--(.*?)-->/","",$firma);

        $funcionario_cargo->setObservaciones($firma);
        $funcionario_cargo->setStatus('C');
        $funcionario_cargo->save();

    }

    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES
    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES
    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES

    $manager = Doctrine_Manager::getInstance();
    $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
    $cacheDriver->deleteByPrefix('correspondencia_enviada_list_funcionario_emisor_');
    $cacheDriver->deleteByPrefix('correspondencia_funcionario_emisor_list_');

    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES
    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES
    // REVISAR SI LA COLETILA AFECTA A ESTAS CACHES

    $this->getUser()->setFlash('notice', 'La coletilla de firma fue agregada con exito.');
    //BIFURCACION
    if($request->getParameter('from')== 'inicio')
        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
    else
        $this->redirect('funcionario_cargo/index');
  }

  public function executeMover(sfWebRequest $request)
  {
    $this->forward404Unless($funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find(array($request->getParameter('id'))), sprintf('Object libro does not exist (%s).', $request->getParameter('id')));
    $this->getUser()->setAttribute('cargo_mover_id', $funcionario_cargo['cargo_id']);
    $this->getUser()->setAttribute('usuario_mover_id', $request->getParameter('id'));
    $this->form = new Organigrama_UnidadForm();
  }

  public function executeMovido(sfWebRequest $request)
  {
    $datos = $request->getParameter('organigrama_unidad');

    $funcionario_id= Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($this->getUser()->getAttribute('usuario_mover_id'));

    $grupo_corresp = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->findByFuncionarioIdAndStatus($funcionario_id->getFuncionarioId(),'A');
    $grupo_archivo = Doctrine::getTable('Archivo_FuncionarioUnidad')->findByFuncionarioIdAndStatus($funcionario_id->getFuncionarioId(),'A');

    $cargo = Doctrine::getTable('Organigrama_Cargo')->find($this->getUser()->getAttribute('cargo_mover_id'));
    $cargo->setUnidadFuncionalId($datos['padre_id']);
    $cargo->save();

    //Se cambia la unidad del funcionario en grupos de CORRESPONDENCIA donde este agregado previamente
    foreach($grupo_corresp as $grupos) {
        if($grupos->getDependenciaUnidadId() != $datos['padre_id']) {
            $grupos->setDependenciaUnidadId($datos['padre_id']);
            $grupos->save();
        }
    }

    //Se cambia la unidad del funcionario en grupos de ARCHIVO donde este agregado previamente este funcionario
    foreach($grupo_archivo as $grupos) {
        if($grupos->getDependenciaUnidadId() != $datos['padre_id']) {
            $grupos->setDependenciaUnidadId($datos['padre_id']);
            $grupos->save();
        }
    }

    //INICIO VALIDAR CAMBIO DE GRUPO O UNIDAD
    //El funcionario debera ser validado por el 99 del grupo anterior (para saber si se queda o no en su grupo de CORRESPONDENCIA)
//    if(count($grupo_corresp) > 0) {
//        $result= Correspondencia_FuncionarioUnidadTable::getInstance()->EsperaValidacionUnidad($funcionario_id->getFuncionarioId(), $datos['padre_id'],  $this->getUser()->getAttribute('usuario_id'));
//    }

    //El funcionario debera ser validado por el 99 del grupo anterior (para saber si se queda o no en su grupo de ARCHIVO)
//    if(count($grupo_archivo) > 0) {
//        $result= Archivo_FuncionarioUnidadTable::getInstance()->EsperaValidacionUnidad($funcionario_id->getFuncionarioId(), $datos['padre_id'],  $this->getUser()->getAttribute('usuario_id'));
//    }
    //FIN VALIDAR CAMBIO DE GRUPO O UNIDAD
    
    //
    //COMUNICACIONES
    //
    $notificacion = new funcionario_cargoNotify();
    $notificacion->notifyDeskCorrespondencia($funcionario_id->getFuncionarioId(), $this->getUser()->getAttribute('usuario_id'));
    $notificacion->notifyDeskArchivo($funcionario_id->getFuncionarioId(), $this->getUser()->getAttribute('usuario_id'));
    //
    //FIN DE COMUNICACIONES
    //

    $this->getUser()->setFlash('notice', 'El cargo fue movido a la unidad seleccionada con exito.');
    $this->getUser()->getAttributeHolder()->remove('cargo_mover_id');
    $this->getUser()->getAttributeHolder()->remove('usuario_mover_id');
    $this->redirect('funcionario_cargo/index');
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->funcionarios_funcionario_cargo = $this->form->getObject();

    $datos = $request->getParameter('funcionarios_funcionario_cargo');
    unset($datos['unidad_funcional_id']);
    $request->setParameter('funcionarios_funcionario_cargo',$datos);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->funcionarios_funcionario_cargo = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->funcionarios_funcionario_cargo);

    $datos = $request->getParameter('funcionarios_funcionario_cargo');
    unset($datos['unidad_id']);
    $request->setParameter('funcionarios_funcionario_cargo',$datos);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }


  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $ban=0;
    if(!$form->getObject()->isNew())
    {
        $ban=1;
        $datos = $request->getParameter('funcionarios_funcionario_cargo');

        $datos_tmp = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($datos['id']);

        $datos['cargo_id'] = $datos_tmp['cargo_id'];
        $datos['f_ingreso'] = $datos_tmp['f_ingreso'];
        $datos['observaciones'] = $datos_tmp['observaciones'];
        $datos['funcionario_cargo_condicion_id'] = $datos_tmp['funcionario_cargo_condicion_id'];

        $request->setParameter('funcionarios_funcionario_cargo',$datos);
    }

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $conn = Doctrine_Manager::connection();

      try {
        $conn->beginTransaction();

        if($ban==0) { // SI ESTA ASIGNANDO EL CARGO
            $funcionarios_funcionario_cargo = $form->save();

            $cargo = Doctrine::getTable('Organigrama_Cargo')->find($funcionarios_funcionario_cargo->getCargoId());
            $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$funcionarios_funcionario_cargo->getFuncionarioId());

            // CAMBIAR EL ESTATUS DEL CARGO A OCUPADO (O)
            $q = Doctrine_Query::create($conn);
            $q->update('Organigrama_Cargo')->set('status', '?', 'O')
              ->where('id = ?', $funcionarios_funcionario_cargo->getCargoId())->execute();

            // ACTIVAR EL USUARIO EN CASO DE QUE ESTE INACTIVO
            $usuario->setStatus('A');
            $usuario->save();

            // BUSCAR SI EL FUNCIONARIO YA TIENE ACTIVO EL MISMO PERFIL POR OTRO CARGO
            $perfil_activo = Doctrine::getTable('Acceso_UsuarioPerfil')->findOneByUsuarioIdAndPerfilIdAndStatus($usuario->getId(),$cargo->getPerfilId(),'A');

            if(!$perfil_activo){
                // SI NO TIENE EL PERFIL ACTIVO CREARLE UNO NUEVO ACTIVO CON EL QUE TIENE ASIGNADO EL CARGO
                $usuario_perfil = new Acceso_UsuarioPerfil();
                $usuario_perfil->setUsuarioId($usuario->getId());
                $usuario_perfil->setPerfilId($cargo->getPerfilId());
                $usuario_perfil->setStatus('A');
                $usuario_perfil->save();
            }

        } else { // SI ESTA DESINCORPORANDO AL FUNCIONARIO DEL CARGO
            $cargo = Doctrine::getTable('Organigrama_Cargo')->find($datos_tmp['cargo_id']);
            $perfil_inactivar = $cargo->getPerfilId();

            $usuario = Doctrine::getTable('Acceso_Usuario')->findOneByEnlaceIdAndUsuarioEnlaceId(1,$datos_tmp['funcionario_id']);

            // CAMBIA EL ESTATUS DEL CARGO DEL QUE SE ESTA DESINCORPORANDO A VACIO (V)
            $q = Doctrine_Query::create($conn);
            $q->update('Organigrama_Cargo')->set('status', '?', 'V')
              ->where('id = ?', $datos_tmp['cargo_id'])->execute();

            $multi_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->findByFuncionarioIdAndStatus($datos_tmp['funcionario_id'],'A');
            if(count($multi_cargo)==1){
                // SI EL FUNCIONARIO TIENE UN SOLO CARGO INACTIVAR USUARIO Y TODOS SUS PERFILES ACTIVOS
                $usuario->setStatus('I');
                $usuario->save();

                //Inactivar todos los perfiles activos para este usuario
                $q = Doctrine_Query::create($conn);
                $q->update('Acceso_UsuarioPerfil')->set('status', '?', 'I')
                  ->where('usuario_id = ?', $usuario->getId())
                  ->andWhere('status = ?', 'A')
                  ->execute();
            } else {
                // SI EL FUINCIONARIO TIENE VARIOS CARGOS SE REALIZA UN CONTEO DE TODOS LOS PERFILES ACTIVOS
                foreach ($multi_cargo as $funcionario_cargo) {
                    $perfil_cargo = Doctrine::getTable('Organigrama_Cargo')->find($funcionario_cargo->getCargoId());

                    if(!isset($perfiles_activos[$perfil_cargo->getPerfilId()]))
                        $perfiles_activos[$perfil_cargo->getPerfilId()]=1;
                    else
                        $perfiles_activos[$perfil_cargo->getPerfilId()]++;
                }

                // RECORRER TODOS LOS PERFILES ACTIVOS
                foreach ($perfiles_activos as $perfil => $cantidad_asignados) {
                    if($perfil == $perfil_inactivar && $cantidad_asignados == 1){
                        // SI EL PERFIL QUE SE ESTA ANALIZANDO ES IGUAL AL QUE SE TIENE QUE INACTIVA Y ESE PERFIL LO TIENE UNO SOLO DE SUS CARGOS
                        // SE INACTIVA TODOS LOS PERFILES DE ESE TIPO
                        $q = Doctrine_Query::create($conn);
                        $q->update('Acceso_UsuarioPerfil')->set('status', '?', 'I')
                          ->where('usuario_id = ?', $usuario->getId())
                          ->andWhere('perfil_id = ?', $perfil_inactivar)
                          ->andWhere('status = ?', 'A')
                          ->execute();
                    } else {
                        // SI EL PERFIL NO SE DEBE INACTIVAR SE BUSCAN PERFILES DUPLICADOS PARA CORREGIR BUG DE REGISTROS ANTES DE 17-02-2013
                        $usuario_perfil_activos = Doctrine::getTable('Acceso_UsuarioPerfil')->findByUsuarioIdAndPerfilIdAndStatus($usuario->getId(),$perfil,'A');

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
            
            $funcionarios_funcionario_cargo = $form->save();
            $funcionarios_funcionario_cargo->setStatus('D');
            $funcionarios_funcionario_cargo->save();
            
            //Inactiva permisos de grupo correspondencia
            $inactivar_permiso = Doctrine_Query::create()
                ->update('Correspondencia_FuncionarioUnidad')
                ->set('status','?', 'I')
                ->where('funcionario_id = ?', $funcionarios_funcionario_cargo->getFuncionarioId())
                ->andWhere('dependencia_unidad_id = ?', $cargo->getUnidadFuncionalId())
                ->execute();
            
            //Inactiva permisos de grupo archivo
            $inactivar_permiso = Doctrine_Query::create()
                ->update('Archivo_FuncionarioUnidad')
                ->set('status','?', 'I')
                ->where('funcionario_id = ?', $funcionarios_funcionario_cargo->getFuncionarioId())
                ->andWhere('dependencia_unidad_id = ?', $cargo->getUnidadFuncionalId())
                ->execute();
            
            //VISTO BUENO: desactiva todos los visto bueno config que tengan en el cargo que se esta desincorporando
            $q = Doctrine_Query::create($conn);
            $q->update('Correspondencia_VistobuenoConfig')
              ->set('status', '?', 'D')
              ->where('funcionario_id = ?', $funcionarios_funcionario_cargo->getFuncionarioId())
              ->andWhere('funcionario_cargo_id = ?', $funcionarios_funcionario_cargo->getCargoId())
              ->andWhere('status = ?', 'A')
              ->execute();

            //Desactiva todos los vistos buenos de correspondencias que aun no hayan sido verificados
            $vistobueno= Doctrine::getTable('Correspondencia_CorrespondenciaVistobueno')->findByFuncionarioIdAndFuncionarioCargoIdAndStatus($funcionarios_funcionario_cargo->getFuncionarioId(), $funcionarios_funcionario_cargo->getCargoId(), 'E');

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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $funcionarios_funcionario_cargo)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@funcionarios_funcionario_cargo_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'funcionarios_funcionario_cargo', 'sf_subject' => $funcionarios_funcionario_cargo));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
  
  public function executeDescargarCarnetCargo(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $back = $request->getParameter('mark_b');
    $front = $request->getParameter('mark_f');

    $funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($id);
    $funcionario_datos = Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionarioCargo($funcionario_cargo->getFuncionarioId(),$funcionario_cargo->getCargoId());
    $carnet_diseno = Doctrine::getTable('Seguridad_CarnetDiseno')->disenoActivoCargoCondicion($funcionario_datos[0]->getCargoCondicionId());

    if(count($carnet_diseno)==0){
        $this->getUser()->setFlash('error', 'No se ha definido un diseño de carnet para la condicion del funcionario seleccionado');
        $this->redirect('funcionario/index');
    }
    
    if (!file_exists(sfConfig::get("sf_root_dir")."/web/images/fotos_personal/".$funcionario_datos[0]->getCi().".jpg")) {
        $this->getUser()->setFlash('error', 'No se puede generar el carnet ya que el funcionario no tiene una foto asociada');
        $this->redirect('funcionario/index');
    }

    $carnet_diseno = $carnet_diseno[0];
    $parametros = sfYaml::load($carnet_diseno->getParametros());

    if(isset($parametros['cedula']['visible'])){
        $parametros['cedula']['cadena'] = 'C.I.: '.$funcionario_datos[0]->getCi();
    } else {
        unset($parametros['cedula']);
    }

    if(isset($parametros['nombres_bloque_uno']['visible'])){
        $cadena_nombres = str_replace('primer_nombre', $funcionario_datos[0]->getPrimerNombre(), $parametros['nombres_bloque_uno']['formato']);
        $cadena_nombres = str_replace('segundo_nombre', $funcionario_datos[0]->getSegundoNombre(), $cadena_nombres);
        $cadena_nombres = str_replace('primer_apellido', $funcionario_datos[0]->getPrimerApellido(), $cadena_nombres);
        $cadena_nombres = str_replace('segundo_apellido', $funcionario_datos[0]->getSegundoApellido(), $cadena_nombres);
        
        if(isset($parametros['nombres_bloque_uno']['mayuscula'])){
            $cadena_nombres = strtr(strtoupper($cadena_nombres),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        } 
        
        $parametros['nombres_bloque_uno']['cadena'] = $cadena_nombres;
    } else {
        unset($parametros['nombres_bloque_uno']);
    }

    if(isset($parametros['nombres_bloque_dos']['visible'])){
        $cadena_nombres = str_replace('primer_nombre', $funcionario_datos[0]->getPrimerNombre(), $parametros['nombres_bloque_dos']['formato']);
        $cadena_nombres = str_replace('segundo_nombre', $funcionario_datos[0]->getSegundoNombre(), $cadena_nombres);
        $cadena_nombres = str_replace('primer_apellido', $funcionario_datos[0]->getPrimerApellido(), $cadena_nombres);
        $cadena_nombres = str_replace('segundo_apellido', $funcionario_datos[0]->getSegundoApellido(), $cadena_nombres);
        
        if(isset($parametros['nombres_bloque_dos']['mayuscula'])){
            $cadena_nombres = strtr(strtoupper($cadena_nombres),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        } 
        
        $parametros['nombres_bloque_dos']['cadena'] = $cadena_nombres;
    } else {
        unset($parametros['nombres_bloque_dos']);
    }

    if(isset($parametros['unidad']['visible'])){
        if($parametros['unidad']['tipo'] == 'adscripcion'){
            $unidad_adscripcion = $this->unidadAdscripcion($funcionario_datos[0]->getUnidadId());
            
            $cadena_unidad['completo'] = $unidad_adscripcion->getNombre();
            $cadena_unidad['reducido'] = $unidad_adscripcion->getNombreReducido();
            $cadena_unidad['siglas'] = $unidad_adscripcion->getSiglas();
        } else {
            $cadena_unidad['completo'] = $funcionario_datos[0]->getUnombre();
            $cadena_unidad['reducido'] = $funcionario_datos[0]->getUnombre();
            $cadena_unidad['siglas'] = $funcionario_datos[0]->getUsiglas();
        }
        
        if(isset($parametros['unidad']['mayuscula'])){
            $cadena_unidad['completo'] = strtr(strtoupper($cadena_unidad['completo']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
            $cadena_unidad['reducido'] = strtr(strtoupper($cadena_unidad['reducido']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
            $cadena_unidad['siglas'] = strtr(strtoupper($cadena_unidad['siglas']),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        } 
                    
        $parametros['unidad']['cadena'] = $cadena_unidad[$parametros['unidad']['formato']];
    } else {
        unset($parametros['unidad']);
    }

    if(isset($parametros['cargo_condicion']['visible'])){
        $cadena_cargo_condicion = $funcionario_datos[0]->getCcnombre();
        if(isset($parametros['cargo_condicion']['mayuscula'])){
            $cadena_cargo_condicion = strtr(strtoupper($cadena_cargo_condicion),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        } 
        
        $parametros['cargo_condicion']['cadena'] = $cadena_cargo_condicion;
    } else {
        unset($parametros['cargo_condicion']);
    }

    if(isset($parametros['cargo_tipo']['visible'])){
        $cadena_cargo_tipo = $funcionario_datos[0]->getCtnombre();
        if(isset($parametros['cargo_tipo']['mayuscula'])){
            $cadena_cargo_tipo = strtr(strtoupper($cadena_cargo_tipo),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
        } 
        
        $parametros['cargo_tipo']['cadena'] = $cadena_cargo_tipo;
    } else {
        unset($parametros['cargo_tipo']);
    }

    // create new PDF document
    $pdf = new TCPDF("P", "pt", array(204, 325), true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator('SIGLAS');
    $pdf->SetAuthor('SIGLAS');
    $pdf->SetTitle('Carnet de funcionario');
    $pdf->SetSubject('ProSoft Solutions Venezuela C.A.');
    $pdf->SetKeywords('Carnet, C.I.:'.$funcionario_datos[0]->getCi());

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0, 0, -1);

    // set auto page breaks
    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();

    // set bacground image
    $imagen_fondo= sfYAML::load($carnet_diseno->getImagenFondo());
    $img_file = sfConfig::get("sf_root_dir").'/web/images/carnet/'.$imagen_fondo['frontal'][$front];
    $pdf->Image($img_file, 0, 0, 204, 325);

    $width = str_replace('px', '', $parametros['foto']['w']);
    $pos_x = str_replace('px', '', $parametros['foto']['x']);
    $pos_y = str_replace('px', '', $parametros['foto']['y']);

    $foto= '<img src="/images/fotos_personal/'.$funcionario_datos[0]->getCi().'.jpg" width="'.$width.'"/>';
    $pdf->MultiCell($width, 0, $foto, 0, 'C', 1, 1, $pos_x, $pos_y, true, 0, true, true, 0);

    $parametros_foto = $parametros['foto'];
    unset($parametros['foto']);


    foreach ($parametros as $campo => $valores) {

        if(isset($valores['negrita'])){
            $negrita = 'bold';
        } else {
            $negrita = 'normal';
        }

        $aling['justify'] = 'J';
        $aling['center'] = 'C';
        $aling['right'] = 'R';
        $aling['left'] = 'L';

        $pos_y = str_replace('px', '', $valores['y']);

        $cedula= '<font style="font-size: '.$valores['fuente'].'; font-weight: '.$negrita.'; color: '.$valores['color'].';">'.$valores['cadena'].'</font>';
        $pdf->MultiCell(204, 0, $cedula, 0, $aling[$valores['alineacion']], 1, 1, 0, $pos_y, true, 0, true, true, 0);
    }
    
    if($back) {
        if(file_exists(sfConfig::get("sf_root_dir").'/web/images/carnet/'.$imagen_fondo['trasero'][$back])) {
            $pdf->AddPage();

            $img_file = sfConfig::get("sf_root_dir").'/web/images/carnet/'.$imagen_fondo['trasero'][$back];
            $pdf->Image($img_file, 0, 0, 204, 325);
        }
    }

    $pdf->Output('carnet__CI-'.$funcionario_datos[0]->getCi().'__'.date('d-m-Y').'.pdf','I');
    return sfView::NONE;
  }
  
  public function executeCargarFondos(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $funcionario_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->find($id);
    $funcionario_datos = Doctrine::getTable('Funcionarios_Funcionario')->datosSessionFuncionarioCargo($funcionario_cargo->getFuncionarioId(),$funcionario_cargo->getCargoId());
    $carnet_diseno = Doctrine::getTable('Seguridad_CarnetDiseno')->disenoActivoCargoCondicion($funcionario_datos[0]->getCargoCondicionId());

    $error= 'empty'; $count_f= 0;
    $str_front= '';
    $imagenes_fondo= sfYAML::load($carnet_diseno[0]->getImagenFondo());
    if($imagenes_fondo['frontal'][0] != '') {
        foreach($imagenes_fondo['frontal'] as $key => $val) {
            $str_front.= '<div style="position: relative; float: left; cursor: pointer" onclick="javascript: mark(\'f\', \''. $count_f .'\')">';
            $str_front.= '<img src="/images/carnet/'. $val .'" style="border: 1px solid; width: 63px; height: 100px"/>&nbsp;';
            $str_front.= '<div id="mark_f_'. $count_f .'" class="mark_f" style="position: absolute; top: 30px; left: 10px; '. (($count_f == 0)? '' : 'display: none') .'"><img src="/images/icon/ok48.png"/></div>';
            $str_front.= '<div style="position: absolute; right: 3px; bottom: 3px"><a href="/images/carnet/'. $val .'" class="prev" target="_blank"><img src="/images/icon/find.png"/></a></div>';
            $str_front.= '</div>';
            $count_f++;
        }
        $str_front.= '<div style="clear: both"></div>';
    }else {
        $error= 'No hay fondos asignados';
    }
    
    $str_back= ''; $count_b= 0;
    if($imagenes_fondo['trasero'][0] != '') {
        foreach($imagenes_fondo['trasero'] as $key => $val) {
            $str_back.= '<div style="position: relative; float: left; cursor: pointer" onclick="javascript: mark(\'b\', \''. $count_b .'\')">';
            $str_back.= '<img src="/images/carnet/'. $val .'" style="border: 1px solid; width: 63px; height: 100px"/>&nbsp;';
            $str_back.= '<div id="mark_b_'. $count_b .'" class="mark_b" style="position: absolute; top: 30px; left: 10px; '. (($count_b == 0)? '' : 'display: none') .'"><img src="/images/icon/ok48.png"/></div>';
            $str_back.= '<div style="position: absolute; right: 3px; bottom: 3px"><a href="/images/carnet/'. $val .'" class="prev" target="_blank"><img src="/images/icon/find.png"/></a></div>';
            $str_back.= '</div>';
            
            $count_b++;
        }
        $str_back.= '<div style="clear: both"></div>';
    }
    
    $request_list['front']= $str_front;
    $request_list['back']= $str_back;
    $request_list['error']= $error;

    return $this->renderText(json_encode($request_list));
  }
}
