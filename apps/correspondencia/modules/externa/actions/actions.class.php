<?php

require_once dirname(__FILE__).'/../lib/externaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/externaGeneratorHelper.class.php';

/**
 * externa actions.
 *
 * @package    siglas-(institucion)
 * @subpackage externa
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class externaActions extends autoExternaActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('receptor_interno');
    $this->getUser()->getAttributeHolder()->remove('receptores_internos');
    $this->getUser()->getAttributeHolder()->remove('externa_receptores');
    $this->getUser()->getAttributeHolder()->remove('resumen_externo');
    $this->getUser()->getAttributeHolder()->remove('emisor_externo');
    $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
    $this->getUser()->getAttributeHolder()->remove('nueva_recepcion_edit');
    
    $this->funcionarios = Doctrine::getTable('Correspondencia_FuncionarioUnidad')
            ->findByFuncionarioIdAndStatusAndRecibir($this->getUser()->getAttribute('funcionario_id'),'A','t');
      
    $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
    if($ultima_tocada){
        $this->getUser()->setAttribute('ultima_vista_externa', $ultima_tocada->getCorrespondenciaExternaId());
    } else {
        $this->getUser()->setAttribute('ultima_vista_externa', 0);
    }
        
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

  public function executeAbrirFormato(sfWebRequest $request)
  {
    $idr = $request->getParameter('idr');
    $id = $request->getParameter('id');

    $correspondencia_receptor = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondenciayReceptorUnidadFuncionario($idr,$this->getUser()->getAttribute('funcionario_unidad_id'),$this->getUser()->getAttribute('funcionario_id'));

    if(count($correspondencia_receptor)>0){
        // Es un receptor establecido de la correspondencia o autorizado que ya la leyo
        foreach ($correspondencia_receptor as $correspondencia_receptor_list){
            if(!$correspondencia_receptor_list->getFRecepcion()) { //no lo ha leido
                $correspondencia_receptor_list->setFRecepcion(date('Y-m-d H:i:s'));
                @$correspondencia_receptor_list->save();

                $this->getUser()->setFlash('notice', ' Se ha registrado la lectura de esta correspondencia exitosamente');
            }
        }
    } else {
        // Es un receptor autorizado que no ha leido
        $correspondencia_receptor = new Correspondencia_Receptor();

        $correspondencia_receptor->setCorrespondenciaId($idr);
        $correspondencia_receptor->setUnidadId($this->getUser()->getAttribute('funcionario_unidad_id'));
        $correspondencia_receptor->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $correspondencia_receptor->setFRecepcion(date('Y-m-d H:i:s'));
        $correspondencia_receptor->setCopia('N');
        $correspondencia_receptor->setEstablecido('N');
        $correspondencia_receptor->setPrivado('S');

        @$correspondencia_receptor->save();

        $this->getUser()->setFlash('notice', ' Se ha registrado la lectura de esta correspondencia exitosamente');
    }

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($idr);

    if($correspondencia->getStatus()=='E') {

        $correspondencia->setStatus('L');
        $correspondencia->setIdCreate($correspondencia->getIdCreate());
        @$correspondencia->save();

        $this->getUser()->setFlash('notice', ' Se ha registrado la recepción de esta correspondencia exitosamente');
    }

    $this->redirect('formatos/show?idc='.$id);

  }
  
  public function executeUltimaVistaExterna($id)
  {
    $ultima_tocada = Doctrine::getTable('Correspondencia_UltimaVista')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
    if($ultima_tocada){
        $ultima_tocada->setCorrespondenciaExternaId($id);
        @$ultima_tocada->save();
    } else {
        $ultima_tocada = new Correspondencia_UltimaVista();
        $ultima_tocada->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $ultima_tocada->setCorrespondenciaEnviadaId($id);
        $ultima_tocada->setCorrespondenciaRecibidaId($id);
        $ultima_tocada->setCorrespondenciaExternaId($id);
        $ultima_tocada->save();
    }
  }
  
  public function executeSeguimiento(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->executeUltimaVistaExterna($id);
    $this->getUser()->setAttribute('correspondencia_id', $id);
    $this->getUser()->setAttribute('seguimiento_externa', 'S');
    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
    $this->getUser()->setAttribute('correspondencia_grupo', $correspondencia->get('grupo_correspondencia'));
    $this->redirect('seguimiento/index');
  }
  
  public function executeReceptorFuncionario(sfWebRequest $request)
  {
      if($request->getParameter('f_id'))
      {
          //echo "f_id";
            $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioAutorizadoCorrespondenciaSelect(array($request->getParameter('u_id')));
            $this->funcionario_selected = $request->getParameter('f_id');
      }
      else
      {
          //echo "u_id";
            $this->funcionario_selected = 0;
            $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
      }
  }
  
  public function executeNew(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('receptor_interno');
    $this->getUser()->getAttributeHolder()->remove('receptores_internos');
    $this->getUser()->getAttributeHolder()->remove('externa_receptores');
    $this->getUser()->getAttributeHolder()->remove('resumen_externo');
    $this->getUser()->getAttributeHolder()->remove('emisor_externo');
    $this->getUser()->getAttributeHolder()->remove('correspondencia_id');
    $this->getUser()->getAttributeHolder()->remove('nueva_recepcion_edit');
      
    $this->generarCorrelativoRecepcion();

    $this->form = $this->configuration->getForm();
    $this->correspondencia_correspondencia = $this->form->getObject();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('nueva_recepcion_edit', TRUE);
    $receptores = Doctrine::getTable('Correspondencia_Receptor')->findByCorrespondenciaId($request->getParameter('id'));
    
    $i=0;
    foreach ($receptores as $receptor) {
        $receptores_datos[$i] = $receptor['unidad_id'].'#'.$receptor['funcionario_id'];
        $i++;
    }
    
    $this->getUser()->setAttribute('receptores_internos', $receptores_datos);
    
    $datos_recuperados = Doctrine::getTable('Correspondencia_correspondencia')->find($request->getParameter('id'));
    if(count($datos_recuperados) > 0)
    {
        $emisor_externo = $datos_recuperados->getEmisorOrganismoId().'#'.$datos_recuperados->getEmisorPersonaId().'#'.$datos_recuperados->getEmisorPersonaCargoId();
        $this->getUser()->setAttribute('emisor_externo', $emisor_externo);
    }
    
    $resumen = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($request->getParameter('id'));
    foreach ($resumen as $resume) {
        $resumen_datos = $resume['campo_uno'];
    }
    $this->getUser()->setAttribute('resumen_externo', $resumen_datos);
    
    $this->correspondencia_correspondencia = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->correspondencia_correspondencia);
  }
  

  public function generarCorrelativoRecepcion()
  {
        $correlativo_recepcion = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneByUnidadIdAndStatusAndTipo($this->getUser()->getAttribute('funcionario_unidad_id'),'A','R');

        if($correlativo_recepcion)
        {
            $nomenclatura = $correlativo_recepcion->getNomenclador();

            //$nomenclatura = str_replace("Siglas", $correlativo_recepcion->getSiglas(), $nomenclatura);
            $nomenclatura = str_replace("Letra", $correlativo_recepcion->getLetra(), $nomenclatura);
            $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
            $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
            $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
            $nomenclatura_reinicio = str_replace("Secuencia", "1", $nomenclatura);
            $nomenclatura = str_replace("Secuencia", $correlativo_recepcion->getSecuencia(), $nomenclatura);

            $listo=0;
            $i=0;
            $secuencia_replace = $correlativo_recepcion->getSecuencia();

            // BUSCAR SI EXISTE EL CORRELATIVO CON SECUENCIA 1
            $correspondencia_find_reinicio = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura_reinicio);
            if($correspondencia_find_reinicio) {
                //SI EXISTE CORRELATIVO CON SECUENCIA 1 SIGUE PROCESO NORMAL
                while($listo == 0) {
                    $correspondencia_find = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura);

                    if($correspondencia_find) {
                        $i++;
                        $nomenclatura = $correlativo_recepcion->getNomenclador();

                        //$nomenclatura = str_replace("Siglas", $unidad->getSiglas(), $nomenclatura);
                        $nomenclatura = str_replace("Letra", $correlativo_recepcion->getLetra(), $nomenclatura);
                        $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
                        $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
                        $nomenclatura = str_replace("Día", date('d'), $nomenclatura);
                        $nomenclatura = str_replace("Secuencia", $secuencia_replace++, $nomenclatura);
                    } else {
                        $listo = 1;

                        if($i>0) {
                            $correlativos_unidad_edit = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneById($correlativo_recepcion->getId());
                            $correlativos_unidad_edit->setUltimoCorrelativo('RESTABLECIDO');
                            $correlativos_unidad_edit->setSecuencia($secuencia_replace-1);
                            $correlativos_unidad_edit->save();
                        }
                    }
                }
            } else {
                // CAMBIO DE AÑO, MES o DIA -> SE REINCIA EL CORRELATIVO

                $nomenclatura = $nomenclatura_reinicio;
                $correlativos_unidad_edit = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneById($correlativo_recepcion->getId());
                $correlativos_unidad_edit->setUltimoCorrelativo('REINICIADO');
                $correlativos_unidad_edit->setSecuencia(1);
                $correlativos_unidad_edit->save();
            }

            $this->getUser()->setAttribute('unidad_correlativo_id',$correlativo_recepcion->getId());
            $this->getUser()->setAttribute('unidad_correlativo',$nomenclatura);
        } else {
            $this->getUser()->setAttribute('unidad_correlativo_id',0);
            $this->getUser()->setAttribute('unidad_correlativo','ERROR');

            $this->getUser()->setFlash('error', 'La unidad no posee registrado un correlativo de recepción externa, 
                                                comunicate con tu supervisor inmediato para que te asigne 
                                                permiso de recibir correspondecia externa en el grupo.');
        }
  }
  
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('correspondencia_correspondencia');
    $datos['privado'] = 'N';  
    
    $ha = $request->getParameter('ha');
    if($ha == true)
        $datos['f_envio'] = date('Y-m-d H:i:s');
    elseif($datos['f_envio']==null)
        $datos['f_envio'] = date('Y-m-d H:i:s'); 
    elseif(is_array($datos['f_envio'])) {
        if($datos['f_envio']['year']!=NULL) $f_envio = $datos['f_envio']['year'].'-'; else $f_envio = date('Y').'-';
        if($datos['f_envio']['month']!=NULL) $f_envio .= $datos['f_envio']['month'].'-'; else $f_envio .= date('m').'-';
        if($datos['f_envio']['day']!=NULL) $f_envio .= $datos['f_envio']['day'].' '; else $f_envio .= date('d').' ';
        if($datos['f_envio']['hour']!=NULL) $f_envio .= $datos['f_envio']['hour'].':'; else $f_envio .= date('H').':';
        if($datos['f_envio']['minute']!=NULL) $f_envio .= $datos['f_envio']['minute'].':'; else $f_envio .= date('i').':';
        $f_envio .= date('s');
        
        $datos['f_envio'] = $f_envio;
    } 
    
    $receptor = $request->getParameter('receptor_interno');
    $receptores = $request->getParameter('receptores');
    $resumen = $request->getParameter('resumen_externo');
    
    if($receptores)
        $receptores = array_unique($receptores);
    
    $this->getUser()->setAttribute('receptor_interno', $receptor);
    $this->getUser()->setAttribute('receptores_internos', $receptores);
    $this->getUser()->setAttribute('resumen_externo', $request->getParameter('resumen_externo'));
    
    // SE ASIGNA EL CORRELATIVO DE SESION DIRECTO A LA VARIABLE YA QUE PUEDE SER EL CORRELATIVO DE EDICION O NUEVO
    $datos['n_correspondencia_emisor'] = $this->getUser()->getAttribute('unidad_correlativo'); 
    
    if(!$this->getUser()->getAttribute('nueva_recepcion_edit')){
        // EN CASO DE LA CORRESPONDENCIA NO SE EDICION ENTRA A COMPROBAR Y REACONDICONAR EL CORRELATIVO NUEVO
        $correlativo_good = false;
        while ($correlativo_good == false){
            $verificar_correlativo = Doctrine::getTable('Correspondencia_Correspondencia')
                                     ->findOneByNCorrespondenciaEmisor($datos['n_correspondencia_emisor']);
            
            if(!$verificar_correlativo) {
                $correlativo_good = true;
            } else {
                $this->generarCorrelativoRecepcion();
                $datos['n_correspondencia_emisor'] = $this->getUser()->getAttribute('unidad_correlativo');
                
                $this->getUser()->setFlash('error', 'El correlativo anteriormente asignado ya fue usado para otra recepción externa, por lo tanto se asigno un nuevo correlativo.');
            }
        }
    }

    $request->setParameter('correspondencia_correspondencia',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
            if(($receptor['unidad_id'] && $receptor['funcionario_id']) || ($receptores))
            {
                if($resumen)
                {
                      $notice = $form->getObject()->isNew() ? 'Se recibio correctamente la correspondencia con el número '.$this->getUser()->getAttribute('unidad_correlativo').'.' : 'The item was updated successfully.';

                        $conn = Doctrine_Manager::connection();

                        try {
                            $conn->beginTransaction();

                            $this->getUser()->setAttribute('nueva_recepcion', 'S');
                            $correspondencia_correspondencia = $form->save();
                            $this->getUser()->getAttributeHolder()->remove('nueva_recepcion');
                            
                            if($correspondencia_correspondencia['n_correspondencia_externa']!='')
                                $n_correspondencia_externa = "con el numero \"" .$correspondencia_correspondencia['n_correspondencia_externa']."\" ";
                            else
                                $n_correspondencia_externa = "";
                            
                            if($correspondencia_correspondencia['grupo_correspondencia']==null)
                            {
                                $correspondencia_correspondencia->setGrupoCorrespondencia($correspondencia_correspondencia['id']);
                                $correspondencia_correspondencia->save();
                            }

                            $this->executeUltimaVistaExterna($correspondencia_correspondencia['id']);

                            $receptor_fuera = array($receptor['unidad_id'] . '#' . $receptor['funcionario_id']);
                            if ($receptores) {
                                if ($receptor['unidad_id'] && $receptor['funcionario_id'])
                                    $receptores = array_merge($receptores, $receptor_fuera);

                                $receptores = array_unique($receptores);
                            }
                            else
                                $receptores = $receptor_fuera;

                            $delete_receptores = Doctrine::getTable('Correspondencia_Receptor')
                              ->createQuery()
                              ->delete()
                              ->where('correspondencia_id = ?', $correspondencia_correspondencia['id'])
                              ->execute();

                            $i=0;
                            foreach ($receptores as $receptor) {
                                list($unidad_id, $funcionario_id) = explode('#', $receptor);
                                $correspondencia_receptor = new Correspondencia_Receptor();
                                $correspondencia_receptor->setCorrespondenciaId($correspondencia_correspondencia['id']);
                                $correspondencia_receptor->setUnidadId((int)$unidad_id);
                                $correspondencia_receptor->setFuncionarioId((int)$funcionario_id);
                                $correspondencia_receptor->setCopia('N');
                                $correspondencia_receptor->setPrivado('N');
                                $correspondencia_receptor->setEstablecido('S');

                                @$correspondencia_receptor->save();
                                
                                $unidades_ids[$i] = $unidad_id;
                                $i++;
                            }

                            $formato = new Correspondencia_Formato();
                            $formato->setCorrespondenciaId($correspondencia_correspondencia['id']);
                            $formato->setTipoFormatoId(4);
                            $formato->setCampoUno($request->getParameter('resumen_externo'));

                            $formato->save();

                            $correspondencia_correspondencia_tmp = $correspondencia_correspondencia;
                            $correspondencia_correspondencia_tmp->setUnidadCorrelativoId($this->getUser()->getAttribute('unidad_correlativo_id'));
                            $correspondencia_correspondencia_tmp->save();
                            

                            
                            foreach ($request->getFiles() as $file) {
                                $ruta_classe = sfConfig::get('sf_upload_dir').'/correspondencia/externa';
                                if(!is_dir($ruta_classe)){ mkdir($ruta_classe, 0777); chmod($ruta_classe, 0777); }

                                $file = $file['adjunto'];

                                if(isset($file['nuevo'])){
                                    $libres = $file['nuevo'];
                                    foreach ($libres as $libre) {
                                        $texto_puro = new herramientas();
                                        $theFileName = $correspondencia_correspondencia->getNCorrespondenciaEmisor().'__'.str_replace(' ','_',$texto_puro->limpiar_metas($libre['name']));

                                        $unidad_archivo = Doctrine::getTable('Organigrama_Unidad')->find($this->getUser()->getAttribute('funcionario_unidad_id'));
                                        
                                        $ruta_libres = $ruta_classe.'/'.$unidad_archivo->getSiglas();
                                        $ruta_db = $unidad_archivo->getSiglas();
                                        if(!is_dir($ruta_libres)){ mkdir($ruta_libres, 0777); chmod($ruta_libres, 0777); }

                                        $ruta_libres = $ruta_libres.'/'.$correspondencia_correspondencia->getNCorrespondenciaEmisor();
                                        $ruta_db = $ruta_db.'/'.$correspondencia_correspondencia->getNCorrespondenciaEmisor();
                                        if(!is_dir($ruta_libres)){ mkdir($ruta_libres, 0777); chmod($ruta_libres, 0777); }

                                        if (file_exists($ruta_libres.'/'.$theFileName)) { 
                                            $theFileName = date('d-m-Y_h:i:s').'_'.$theFileName; 
                                            $libre['name'] = $libre['name'].' ('.date('d-m-Y h:i:s').')';
                                        }

                                        if (move_uploaded_file($libre['tmp_name'], $ruta_libres.'/'.$theFileName)){
                                            // El archivo ha sido cargado correctamente
                                            $datos_anexo_archivo = new Correspondencia_AnexoArchivo();
                                            $datos_anexo_archivo->setCorrespondenciaId($correspondencia_correspondencia->getId());
                                            $datos_anexo_archivo->setNombreOriginal($libre['name']);
                                            $datos_anexo_archivo->setRuta('externa/'.$ruta_db.'/'.$theFileName);
                                            $datos_anexo_archivo->setTipoAnexoArchivo($libre['type']);
                                            $datos_anexo_archivo->save();
                                        }else{
                                            if($libre['tmp_name']!=null)
                                                $this->getUser()->setFlash('error', 'Ocurrió algún error al subir el archivo. No pudo guardarse.');
                                        } 
                                    }
                                }
                            }
                            
                            
                            
                            
                            $anexos_fisicos = $request->getParameter('anexos_fisicos');
                            
                            if($anexos_fisicos['id']!=''){
                                $anexo_fisico = new Correspondencia_AnexoFisico();
                                $anexo_fisico->setCorrespondenciaId($correspondencia_correspondencia->getId());
                                $anexo_fisico->setTipoAnexoFisicoId($anexos_fisicos['id']);
                                $anexo_fisico->setObservacion($anexos_fisicos['observacion']);
                                $anexo_fisico->save();
                            }
                            
                            
                            if(isset($anexos_fisicos['otros'])){
                                foreach ($anexos_fisicos['otros'] as $valores) {
                                    list($tipo_fisico_id, $observacion_fisico) = explode('#',$valores);
                                    
                                    $anexo_fisico = new Correspondencia_AnexoFisico();
                                    $anexo_fisico->setCorrespondenciaId($correspondencia_correspondencia->getId());
                                    $anexo_fisico->setTipoAnexoFisicoId($tipo_fisico_id);
                                    $anexo_fisico->setObservacion($observacion_fisico);
                                    $anexo_fisico->save();
                                }
                            }
                            
                            if(isset($anexos_fisicos['delet'])){
                                if($anexos_fisicos['delet']!='.'){
                                    $fisicos_delete = str_replace('.#', '', $anexos_fisicos['delet']);
                                    $fisicos_delete = explode( "#", $fisicos_delete);

                                    // NO SE PUEDE HACER UN DELETE MULTIPLE YA QUE SE DEBE BORRAR LA CACHE DOCTRINE
                                    foreach ($fisicos_delete as $fisico_delete) {
                                        $fisico_delete_ok = Doctrine::getTable('Correspondencia_AnexoFisico')->find($fisico_delete);
                                        $fisico_delete_ok->delete();
                                    }
                                }
                            }
                            
                            

                            //################################## INICIO DE CORREO ELECTRONICO ##################################

                            if($correspondencia_correspondencia['email_externo'] || $correspondencia_correspondencia['telf_movil_externo'])
                            {
                                $archivox = Doctrine::getTable('Correspondencia_AnexoArchivo')->findOneByCorrespondenciaId($correspondencia_correspondencia['id']);
                                $unidadx = Doctrine::getTable('Organigrama_Unidad')->unidades($unidades_ids);

                                $unidades='';
                                foreach ($unidadx as $unidad) { $unidades .= $unidad['nombre'].', '; }
                                $unidades .= '$%&';

                                $unidades = str_replace(', $%&', '', $unidades);

                                $organismox = Doctrine::getTable('Organismos_Organismo')->find($correspondencia_correspondencia['emisor_organismo_id']);
                                $personax = Doctrine::getTable('Organismos_Persona')->find($correspondencia_correspondencia['emisor_persona_id']);
                                $personacargox = Doctrine::getTable('Organismos_PersonaCargo')->find($correspondencia_correspondencia['emisor_persona_cargo_id']);

                                list($fecha, $hora) = explode(' ', $correspondencia_correspondencia['f_envio']);
                                list($h, $m, $s) = explode(':', $hora);
                                list($year_now, $month_now, $day_now) = explode('-', $fecha);
                                $month_now+=0;

                                $AP = 'am';
                                if ($h > 12) {
                                    $h = $h - 12;
                                    $AP = 'pm';
                                }
                                if ($h == 12) {
                                    $AP = 'M';
                                }

                                $months = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
                                $date = $day_now . " de " . $months[$month_now] . " de " . $year_now . " a las " . $h . ":" . $m . " " . $AP;


                                if($correspondencia_correspondencia['email_externo']!=null)
                                {
                                    $mensaje['mensaje'] = sfConfig::get('sf_organismo')."<br/>";
                                    $mensaje['mensaje'] .= "Sistema Integral para la Gestión Laboral, Administrativa y de Servicios (SIGLAS)<br/><br/><br/>";

                                    $mensaje['mensaje'] .= "Srs.-<br/>";
                                    $mensaje['mensaje'] .= $organismox['nombre'] . "<br/>";
                                    $mensaje['mensaje'] .= $personax['nombre_simple'] . "<br/>";
                                    $mensaje['mensaje'] .= $personacargox['nombre'] . "<br/><br/><br/>";

                                    $mensaje['mensaje'] .= "Reciba un cordial saludo Bolivariano y Revolucionario, nos dirigimos a usted en la " .
                                            "oportunidad de informarle que la correspondencia que ha enviado a este organismo con el número \"" .
                                            $correspondencia_correspondencia['n_correspondencia_externa'] .
                                            "\" ha sido recibida y se ha redireccionado a " . $unidades .
                                            " en fecha " . $date . " con el número \"" .
                                            $correspondencia_correspondencia['n_correspondencia_emisor'] . "\".<br/><br/>";

                                    $mensaje['mensaje'] .= "<br/><br/>Con la intención de atender los planteamientos realizados e informar los resultados obtenidos " .
                                            " y al mismo tiempo reiterándole el compromiso de trabajo colectivo para la construcción de la patria socialista, " .
                                            "se despide. <br/><br/>".sfConfig::get('sf_organismo');

                                    $mensaje['emisor'] = 'Correspondencia';
                                    $mensaje['receptor'] = $personax['nombre_simple'];

                                    Email::notificacion('correspondencia', $correspondencia_correspondencia['email_externo'], $mensaje, 'inmediata');
                                }

                                //################################## FIN DE CORREO ELECTRONICO ##################################

                                //################################## INICIO DE SMS ##################################

                                if($correspondencia_correspondencia['telf_movil_externo']!=null)
                                {            
                                    $mensaje['emisor'] = 'Correspondencia';
                                    $mensaje['mensaje']="SIGLAS-".sfConfig::get('sf_siglas')." Reciba un cordial saludo, nos dirigimos a usted en la " .
                                            "oportunidad de informarle que la correspondencia ha enviado a este organismo " .
                                            $n_correspondencia_externa. "ha sido recibida y se ha redireccionado a " . $unidades .
                                            " en fecha " . $date . " con el numero \"" .
                                            $correspondencia_correspondencia['n_correspondencia_emisor'] . "\".";

                                    Sms::notificacion_sistema('correspondencia', $correspondencia_correspondencia['telf_movil_externo'], $mensaje);
                                }

                                //################################## FIN DE SMS ##################################
                            }

                            $conn->commit();

                            $this->getUser()->getAttributeHolder()->remove('unidad_correlativo');
                            $this->getUser()->getAttributeHolder()->remove('unidad_correlativo_id');
                        } catch (Doctrine_Validator_Exception $e) {
                            $conn->rollBack();
                            $errorStack = $form->getObject()->getErrorStack();

                            $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
                            foreach ($errorStack as $field => $errors) {
                                $message .= "$field (" . implode(", ", $errors) . "), ";
                            }
                            $message = trim($message, ', ');

                            $this->getUser()->setFlash('error', $message);
                            return sfView::SUCCESS;
                        }

                        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $correspondencia_correspondencia)));

                        if ($request->hasParameter('_save_and_add')) {
                            $this->getUser()->setFlash('notice', $notice . ' You can add another one below.');

                            $this->redirect('@correspondencia_correspondencia_externa_new');
                        } else {
                            $this->getUser()->setFlash('notice', $notice);

                            $this->redirect(array('sf_route' => 'correspondencia_correspondencia_externa', 'sf_subject' => $correspondencia_correspondencia));
                        }
                    } else {
                        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
                        $this->getUser()->setFlash('error_resumen', 'Debe agregar un resumen del contenido de la correspondencia que recibe.', false);
                    }
            }
            else
            {
                if (!$resumen)
                    $this->getUser()->setFlash('error_resumen', 'Debe agregar un resumen del contenido de la correspondencia que recibe.', false);

                $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
                $this->getUser()->setFlash('error_receptor', 'Debe agregar al menos un receptor.', false);
            }
    }
    else
    {
        if ($verificar_correlativo)
            $this->getUser()->setFlash('error_correlativo', 'El correlativo anteriormente asignado ya fue utilizado, se le ha asignado el nuevo correlativo resaltado.', false);
        
        if (!(($receptor['unidad_id'] && $receptor['funcionario_id']) || ($receptores)))
            $this->getUser()->setFlash('error_receptor', 'Debe agregar al menos un receptor.', false);

        if (!$resumen)
            $this->getUser()->setFlash('error_resumen', 'Debe agregar un resumen del contenido de la correspondencia que recibe.', false);

        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }

  }
 
  public function executeAnular(sfWebRequest $request)
  {
    $id = $request->getParameter('id');

    $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
    $NCorrespondenciaEmisor_anterior = $correspondencia->getNCorrespondenciaEmisor();

    if(($correspondencia->get('status')=='E' || $correspondencia->get('status')=='D') && $correspondencia->getIdCreate() == $this->getUser()->getAttribute('usuario_id'))
    {
        $correlativo = $correspondencia->getNCorrespondenciaEmisor();

        $correspondencia->setStatus('X');
        $correspondencia->setNCorrespondenciaEmisor($correspondencia->getNCorrespondenciaEmisor().'-ANULADO-'.$correspondencia->getId());
        $correspondencia->setIdDelete($this->getUser()->getAttribute('usuario_id'));
        $correspondencia->save();

        $unidad_correlativo = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->find($correspondencia->getUnidadCorrelativoId());

        $nomenclador = explode( "-", $unidad_correlativo->getNomenclador());

        $i=0;
        foreach ($nomenclador as $value) {
            if($value=='Secuencia')
                $pos_secuencia = $i;
            $i++;
        }

        $secuencia = explode( "-", $correlativo);
        $unidad_correlativo->setSecuencia($secuencia[$pos_secuencia]);
        $unidad_correlativo->save();

        $this->getUser()->setFlash('notice', ' Se ha anulado la correspondencia exitosamente y se ha liberado el número para su reutilización');        
    }
    else
    {
        $this->getUser()->setFlash('error', ' Solo se pueden anular correspondencias que esten en estatus de enviada o devuelta');
    }

    $this->redirect('externa/index');
  }


    public function executeBusquedaOrganismo(sfWebRequest $request){

        $string = $request->getParameter('organismo');

        $organismos = Doctrine::getTable('Organismos_Organismo')->getNombresSiglas($string);
        
        $select='<select name="organismo">';
        foreach ($organismos as $organismo) {
            $select.='<option value="'.$organismo->getId().'">'.$organismo->getNombre().'</option>';
        }
        $select.='</select>';
        echo $select;
        exit();

    } 
  
    public function executeOrganismos(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');

        $req = Doctrine::getTable('Organismos_Organismo')->getNombres($string);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    } 
  
    
    public function executePersonas(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');
        $organismo_id = $request->getParameter('organismo_id'); 

        $req = Doctrine::getTable('Organismos_Persona')->getNombres($string,$organismo_id);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombreSimple();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    } 
    
    public function executePersonasCargos(sfWebRequest $request){
        $this->getResponse()->setContentType('application/json');
        $string = $request->getParameter('q');
        $persona_id = $request->getParameter('persona_id'); 

        $req = Doctrine::getTable('Organismos_PersonaCargo')->getNombres($string,$persona_id);
        $results = array();
         if (count($req) > 0){
                  foreach ( $req as $result )
                   $results[$result->getId()] = $result->getNombre();
            return $this->renderText(json_encode($results));
           }else{
               $results[0] = '';
               return $this->renderText(json_encode($results));
           }
    }
    
    public function executeEstadisticas(sfWebRequest $request){
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_redactar = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'redactar');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $redactar_array= array();
        foreach($funcionario_unidades_redactar as $unidades_redactar) {
            $redactar_array[]= $unidades_redactar->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $redactar_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
        
        $this->funcionario_unidades = $funcionario_unidades;
    }
    
    public function executeEstadisticaSeleccionada(sfWebRequest $request){
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        if(!$request->getParameter('fi'))
        {
            if(!$request->getParameter('ff'))
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final= date('Y-m-d H:i:s');
            }
            else
            {
                $fecha_inicio='2005-12-18 00:00:00';
                $fecha_final=$request->getParameter('ff')." 23:59:59";
            }
        }
        elseif(!$request->getParameter('ff'))
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final= date('Y-m-d H:i:s');
        }
        else
        {
            $fecha_inicio=$request->getParameter('fi')." 00:00:00";
            $fecha_final=$request->getParameter('ff')." 23:59:59";
        }
        
        $unidad_id = $request->getParameter('unidad_id');
        $estadistica_tipo = $request->getParameter('tipo');
        
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        //DATOS QUE VIENEN POR REQUEST
        
        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION
        $boss= false;
        if($this->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));
        }

        $funcionario_unidades_leer = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->funcionarioAutorizado($this->getUser()->getAttribute('funcionario_id'),'leer');

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $leer_array= array();
        foreach($funcionario_unidades_leer as $unidades_leer) {
            $leer_array[]= $unidades_leer->getAutorizadaUnidadId();
        }

        $nonrepeat= array_merge($cargo_array, $leer_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }
        //LA UNIDAD_ID VIENE POR REQUEST SIN EMBARGO REALIZAR DOBLE VALIDACION
        

        $autorizado=false;
        foreach ($funcionario_unidades as $unidad_autorizada){
            if($unidad_autorizada == $unidad_id)
                $autorizado=true;
        }
        
        if($autorizado==true){
            $estadistica = new Correspondencia_CorrespondenciaStatistic();
            
            eval('$estadistica_datos = $estadistica->'.$estadistica_tipo.'($unidad_id, $fecha_inicio,$fecha_final);');
            
            $this->estadistica_datos = $estadistica_datos;
            $this->fecha = "Estadistica generada desde: ".date('d/m/Y',  strtotime($fecha_inicio))." Hasta: ".date('d/m/Y',  strtotime($fecha_final));
            $this->unidad_id = $unidad_id;
            
            $this->setTemplate('estadisticas/'.$estadistica_tipo);
            
        } else {
            echo "No esta autorizado para revisar las estadisticas de esta unidad";
            exit();
        }
        
                  
    }
    
    public function executeHojaRuta(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $correspondencia = Doctrine::getTable('Correspondencia_Correspondencia')->find($id);
        $correspondenciareceptor = Doctrine::getTable('Correspondencia_Receptor')->filtrarPorCorrespondencia($id);
        $correspondenciaformato = Doctrine::getTable('Correspondencia_Formato')->filtrarPorCorrespondencia($id);
        $correspondenciaanexofisico = Doctrine::getTable('Correspondencia_AnexoFisico')->filtrarPorCorrespondencia($id);

        // ################ INIZIALIZAR EL OBJETO DE PDF  #################
        $config = sfTCPDFPluginConfigHandler::loadConfig('pdf_configs.yml');
        // pdf object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // settings

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetMargins(15, PDF_MARGIN_TOP, 10);
        //$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->SetHeaderData('gob_pdf.png', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetHeaderMargin(40);
        $pdf->setFooterMargin(40);
        $pdf-> SetAutoPageBreak(True, 90);

        // init pdf doc
//        $pdf->Image('http://' . $_SERVER['SERVER_NAME'] . '/images/organismo/pdf/gob_footer_pdf2.png', 0, 700, 550, 700, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
//        $pdf->setPageMark();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        if ($correspondencia->getFEnvio() == null) {
            $f_envio = "<b>N O  S E  H A  E N V I A D O</b>";
        } else {
            $f_envio = date('d-m-Y h:i:s A', strtotime($correspondencia->getFEnvio()));
        }

        $n_envio = $correspondencia->getn_correspondencia_emisor();

        $emisor = '';
        $emisor.= $correspondencia->getOrganismos_Organismo().' / ';
        if($correspondencia->getEmisorPersonaId()){
            $emisor.= ucwords(strtolower($correspondencia->getOrganismos_Persona())). ' ('; 
        }
        $emisor.= $correspondencia->getOrganismos_PersonaCargo(). ')';
        

        $receptor = '';
        foreach ($correspondenciareceptor as $list_correspondenciareceptor) {
            $receptor.= $list_correspondenciareceptor->getUnombre() . ' / ' .
                    $list_correspondenciareceptor->getCtnombre() . ' / ' .
                    ucwords(strtolower($list_correspondenciareceptor->getPn())) . ' ' .
                    ucwords(strtolower($list_correspondenciareceptor->getPa())) . '<br/>';
        }
        if ($receptor != '') {
            $receptor .= '###';
            $receptor = str_replace('<br/>###', '', $receptor);
        }

        $receptor_externo = '';

        $formatos = '';
        foreach ($correspondenciaformato as $list_correspondenciaformato) {
            $formatos .= $list_correspondenciaformato->getTadnombre() . ', ';

            $enunciado_clave = '';
            if ($list_correspondenciaformato->getTipoFormatoId() == 1) {
                //MEMORANDO
                $enunciado_clave = '<b>Asunto: </b>' . $list_correspondenciaformato->getCampoUno();
            }elseif($list_correspondenciaformato->getTipoFormatoId() == 4) {
                //EXTERNO
                $enunciado_clave = $list_correspondenciaformato->getCampoUno();
            }
        }
        if ($formatos != '') {
            $formatos .= '###';
            $formatos = str_replace(', ###', '', $formatos);
        }

        $fisicos = '<table border="1">';
        foreach ($correspondenciaanexofisico as $list_correspondenciaanexofisico) {
            $fisicos .= '<tr><td width="100">' . $list_correspondenciaanexofisico->gettafnombre() . '</td><td width="300">' .
                    $list_correspondenciaanexofisico->getobservacion() . '</td></tr>';
        }
        $fisicos .= '</table>';

        $tbl = <<<EOD
<table width="560" "center">
       <tr>
        <td width="30"><br/><br/></td>
        <td width="500">
            <table width="500">
                <tr>
                    <td width="500" align="center"><br/><h1>Acuse de Recibo Físico</h1><br/></td>
                </tr>
                <tr>
                    <td width="500">
                        <table border="1" width="500" cellpadding="5">
                            <tr>
                                <td width="90"><b>Fecha de Envio:</b></td>
                                <td width="410">$f_envio</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Nº de Envio:</b></td>
                                <td width="410"><h2>$n_envio</h2></td>
                            </tr>
                            <tr>
                                <td width="90"><b>De:</b></td>
                                <td width="410">$emisor</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Para:</b></td>
                                <td width="410">$receptor $receptor_externo</td>
                            </tr>
                            <tr>
                                <td width="90"><b>$formatos:</b></td>
                                <td width="410">$enunciado_clave</td>
                            </tr>
                            <tr>
                                <td width="90"><b>Fisicos:</b></td>
                                <td width="410">$fisicos</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="500">
                        <br/><br/>
                        <table border="1" width="500" cellpadding="5">
                            <tr>
                                <td width="250" colspan="2" align="center"><b>MENSAJERO</b></td>
                                <td width="250" colspan="2" align="center"><b>RECEPTOR</b></td>
                            </tr>
                            <tr>
                                <td width="60"><b>Unidad:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Unidad:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Nombre:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Nombre:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Cédula:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Cédula:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Firma:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Firma:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="60"><b>Sello:</b></td>
                                <td width="190"><br/><br/><br/><br/><br/></td>
                                <td width="60"><b>Sello:</b></td>
                                <td width="190"><br/><br/><br/><br/><br/></td>
                            </tr>
                            <tr>
                                <td width="60"><b>Fecha Envío:</b></td>
                                <td width="190">&nbsp;</td>
                                <td width="60"><b>Fecha Recepción:</b></td>
                                <td width="190">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td width="30">&nbsp;</td>
    </tr>
</table>
EOD;

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output();
        return sfView::NONE;
    }
}
