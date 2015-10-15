<?php

require_once dirname(__FILE__).'/../lib/expedienteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/expedienteGeneratorHelper.class.php';

/**
 * expediente actions.
 *
 * @package    siglas
 * @subpackage expediente
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class expedienteActions extends autoExpedienteActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('correspondencia_archivar_id');

    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoLeer($this->getUser()->getAttribute('funcionario_id'));

    if(count($unidades_autorizadas)==0){
        $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de archivo
            con el permiso de leer,
            por lo tanto no podrás leer o visualizar expedientes archivados.
            Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
            "Permisos de Grupos" del submenú de herramientas de "Archivo".');
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

  public function executeNew(sfWebRequest $request)
  {
    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoArchivar($this->getUser()->getAttribute('funcionario_id'));

    if(count($unidades_autorizadas)>0)
    {
        $this->form = $this->configuration->getForm();
        $this->archivo_expediente = $this->form->getObject();
    } else {
        $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de archivo
            con el permiso de archivar,
            por lo tanto no podrás crear y archivar expedientes o sus documentos.
            Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
            "Permisos de Grupos" del submenú de herramientas de "Archivo".');

        $this->redirect('expediente/index');
    }
  }

  public function executeAnular(sfWebRequest $request)
  {
    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoAnular($this->getUser()->getAttribute('funcionario_id'));

    if(count($unidades_autorizadas)>0)
    {
        $id = $request->getParameter('id');

        $expediente = Doctrine::getTable('Archivo_Expediente')->find($id);

        $correlativo_anulado = explode('-', $expediente->getCorrelativo());
        $correlativo_anulado = $correlativo_anulado[count($correlativo_anulado)-1];

        $expediente->setCorrelativo($expediente->getCorrelativo().'-ANULADO-'.$expediente->getId());
        $expediente->setStatus('I');
        $expediente->save();

        $unidad_correlativo = Doctrine::getTable('Archivo_UnidadCorrelativos')->find($expediente->getUnidadCorrelativosId());
        $unidad_correlativo->setSecuenciaExpediente($correlativo_anulado);
        $unidad_correlativo->save();



        $this->getUser()->setFlash('notice', 'Expediente anulado con exito.');
    } else {
        $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de archivo
            con el permiso de anular,
            por lo tanto no podrás anular expedientes o sus documentos.
            Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
            "Permisos de Grupos" del submenú de herramientas de "Archivo".');
    }
    $this->redirect('expediente/index');
  }

  public function executeAdjuntarDocumento(sfWebRequest $request)
  {
    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoArchivar($this->getUser()->getAttribute('funcionario_id'));

    if(count($unidades_autorizadas)>0)
    {
        $id = $request->getParameter('id');

        $expediente = Doctrine::getTable('Archivo_Expediente')->find($id);

        $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->findBySerieDocumentalId($expediente->getSerieDocumentalId());

        if(count($tipologias) > 0){
            $this->getUser()->setAttribute('serie_documental_id', $expediente->getSerieDocumentalId());
            $this->getUser()->setAttribute('header_ruta', 'Expediente: '.$expediente->getCorrelativo());
            $this->getUser()->setAttribute('expediente_id', $id);

            $this->redirect('documento/new');
        } else {
            $this->getUser()->setFlash('error', 'El expediente seleccionado pertenece a una Serie Documental
                                                a la cual no se le han definido Tipologias Documentales,
                                                por lo tanto no podra agregar documentos al mismo,
                                                Comunícate con tu supervisor inmediato para que definan las mismas
                                                mediante la opción "Series Documentales" del menú de herramientas,
                                                seguidamente la opción "Tipologias Documentales" de la serie seleccionada.');
            $this->redirect('expediente/index');
        }
    } else {
        $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de archivo
            con el permiso de archivar,
            por lo tanto no podrás crear y archivar expedientes o sus documentos.
            Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
            "Permisos de Grupos" del submenú de herramientas de "Archivo".');

        $this->redirect('expediente/index');
    }
  }
  
  public function executeAdjuntarCorrespondencia(sfWebRequest $request)
  {
    $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoArchivar($this->getUser()->getAttribute('funcionario_id'));

    if(count($unidades_autorizadas)>0)
    {
        $id = $request->getParameter('e_id');

        $expediente = Doctrine::getTable('Archivo_Expediente')->find($id);

        $tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->findBySerieDocumentalId($expediente->getSerieDocumentalId());

        if(count($tipologias) > 0){
            $this->getUser()->setAttribute('serie_documental_id', $expediente->getSerieDocumentalId());
            $this->getUser()->setAttribute('expediente_id', $id);

            echo $this->getPartial('documento/identificacion');
        } else {
            $this->getUser()->setFlash('error', 'El expediente seleccionado pertenece a una Serie Documental
                                                a la cual no se le han definido Tipologias Documentales,
                                                por lo tanto no podra agregar documentos al mismo,
                                                Comunícate con tu supervisor inmediato para que definan las mismas
                                                mediante la opción "Series Documentales" del menú de herramientas,
                                                seguidamente la opción "Tipologias Documentales" de la serie seleccionada.');
            echo $this->getPartial('documento/identificacion');
        }
    } else {
        $this->getUser()->setFlash('error', 'Actualmente no perteneces a ningún grupo de archivo
            con el permiso de archivar,
            por lo tanto no podrás crear y archivar expedientes o sus documentos.
            Comunícate con tu supervisor inmediato para que te de los permisos necesarios mediante la opción
            "Permisos de Grupos" del submenú de herramientas de "Archivo".');

        echo $this->getPartial('documento/identificacion');
    }
    exit();
  }

  public function executePrestarDocumento(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('expediente_id', $id);

    $expediente = Doctrine::getTable('Archivo_Expediente')->find($id);
    $this->getUser()->setAttribute('header_ruta', 'Expediente: '.$expediente->getCorrelativo());
    $this->redirect('prestamo/index');
  }

  public function executePrestamosSolicitados(sfWebRequest $request)
  {
    $this->redirect('prestamos_solicitados/index');
  }

  public function executeCompartidos(sfWebRequest $request)
  {
    $this->redirect('expedientes_compartidos/index');
  }

  public function executeEditarDocumento(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('expediente_id', $id);

    $expediente = Doctrine::getTable('Archivo_Expediente')->find($id);
    $this->getUser()->setAttribute('serie_documental_id', $expediente->getSerieDocumentalId());

    $documento_id = $request->getParameter('doc');

    $this->redirect('documento/edit?id='.$documento_id);
  }

  public function executeEliminarDocumento(sfWebRequest $request)
  {
    $documento_id = $request->getParameter('doc');

    try
    {
        $manager = Doctrine_Manager::getInstance();
        $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();

        $documento = Doctrine::getTable('Archivo_Documento')->find($documento_id);
        $ruta_old = $documento->getRuta();

        $ruta = explode('/',$documento->getRuta());

        $ruta[count($ruta)-1] = 'ELIMINADO_'.$ruta[count($ruta)-1];
        $ruta_new = null;
        for($i=0;$i<count($ruta);$i++)
            $ruta_new .= $ruta[$i].'/';
        $ruta_new .= '$%&';
        $ruta_new = str_replace('/$%&', '', $ruta_new);


        $secuencia = explode("-", $documento->getCorrelativo());
        $secuencia = $secuencia[count($secuencia)-1];


        $unidad_correlativos = Doctrine::getTable('Archivo_UnidadCorrelativos')->find($documento->getUnidadCorrelativosId());

        if($secuencia<$unidad_correlativos->getSecuenciaAnexoDocumento()){
            $unidad_correlativos->setSecuenciaAnexoDocumento($secuencia);
            $unidad_correlativos->save();
        }

        $documento->setCorrelativo($documento->getCorrelativo().'_ELIMINADO_'.date('Y-m-d_h:i:s'));
        $documento->setStatus('E');
        $documento->setRuta($ruta_new);
        $documento->save();

        rename('uploads/archivo/'.$ruta_old, 'uploads/archivo/'.$ruta_new);

        $conn->commit();

    } catch(Exception $e){
        $conn->rollBack();
        $this->getUser()->setFlash('error', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnologia.');
    }


    $this->redirect('expediente/index');
  }

  public function executeListarUnidades(sfWebRequest $request)
  {
        $unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad();

        $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoArchivar($this->getUser()->getAttribute('funcionario_id'));

        $i=0; $unidades_ids=array();
        foreach ($unidades_autorizadas as $unidad_autorizada)
            {$unidades_ids[$i] = $unidad_autorizada->getAutorizadaUnidadId(); $i++;}

        $unidades_fisicas = Doctrine::getTable('Archivo_Estante')->estantesDeSerieDeUnidadesDuenas($unidades_ids,$request->getParameter('s_id'));
        $unidades_fisicas_ids = '';
        foreach ($unidades_fisicas as $unidad_fisica)
            {$unidades_fisicas_ids .= '-'.$unidad_fisica->getUnidadFisicaId().'-';}
        
        $unidades_tmp = $unidades;
        foreach ($unidades_tmp as $clave => $valor) {
            if(!(preg_match('/-'.$clave.'-/', $unidades_fisicas_ids))) { 
                unset($unidades[$clave]);
            }
        }
        
        $this->unidades = $unidades;
  }

  public function executeListarEstantes(sfWebRequest $request)
  {
        $unidades_autorizadas = Doctrine::getTable('Archivo_FuncionarioUnidad')->funcionarioAutorizadoArchivar($this->getUser()->getAttribute('funcionario_id'));

        $i=0; $unidades_ids=array();
        foreach ($unidades_autorizadas as $unidad_autorizada)
            {$unidades_ids[$i] = $unidad_autorizada->getAutorizadaUnidadId(); $i++;}

        $this->estantes = Doctrine::getTable('Archivo_Estante')
                            ->createQuery('a')
                            ->where('a.unidad_fisica_id = ?',$request->getParameter('u_id'))
                            ->andWhereIn('a.unidad_duena_id',$unidades_ids)
                            ->andWhere('a.id IN (SELECT al.estante_id FROM Archivo_Almacenamiento al
                                        WHERE al.serie_documental_id = '.$request->getParameter('s_id').')')
                            ->orderBy('a.identificador')->execute();
  }

  public function executeListarTramos(sfWebRequest $request)
  {
        $estante_almacenamiento = Doctrine::getTable('Archivo_Almacenamiento')->findByEstanteIdAndSerieDocumentalId($request->getParameter('e_id'),$request->getParameter('s_id'));
        $this->tramos = $estante_almacenamiento[0]->getTramos();
        $this->e_id = $request->getParameter('e_id');
  }

  public function executeListarCajas(sfWebRequest $request)
  {
      $this->cajas = Doctrine::getTable('Archivo_Caja')->findByEstanteIdAndTramo($request->getParameter('e_id'),$request->getParameter('t_id'));
  }

  public function executeListarValores(sfWebRequest $request)
  {
      $this->clasificadores = Doctrine::getTable('Archivo_Clasificador')->detallesClasificadores($request->getParameter('s_id'));
      $this->cuerpos = Doctrine::getTable('Archivo_CuerpoDocumental')->cuerposDeSerie($request->getParameter('s_id'));

      // SETEO DE VARIBLE SI ES UNA SOLICITUD DE PRESTAMO DE EXPEDIENTE
      if($request->getParameter('arch')){
          $this->correspondencia_solicitud = 1;
          if($request->getParameter('c_id') != ''){
              $descriptores= Doctrine::getTable('Correspondencia_Formato')->findOneByCorrespondenciaId($request->getParameter('c_id'));
              $this->descriptores_salvados = sfYaml::load($descriptores->getCampoCinco());
          }
      } else {
          $this->correspondencia_solicitud = 0;
      }
  }

  public function executeMultipleBandeja(sfWebRequest $request)
  {
      switch ($request->getParameter('bandeja_archivo')) {
          case 0:
              $this->redirect('expediente/index');
              break;
          case 1:
              $this->redirect('expediente/prestamosSolicitados');
          case 2:
              $this->redirect('expediente/compartidos');
              break;
          default:
              $this->redirect('expediente/index');
              break;
      }
  }

  public function executeListarClasificadoresFilter(sfWebRequest $request)
  {
      $this->clasificadores = Doctrine::getTable('Archivo_Clasificador')->detallesClasificadores($request->getParameter('s_id'));
  }

  public function executeListarTipologiasFilter(sfWebRequest $request)
  {
      $this->serie_documental_id = $request->getParameter('s_id');
  }
  
  public function executeListarCuerposFilter(sfWebRequest $request)
  {
      $this->cuerpos = Doctrine::getTable('Archivo_CuerpoDocumental')->cuerposDeSerie($request->getParameter('s_id'));
  }

  public function executeListarEtiquetasFilter(sfWebRequest $request)
  {
      $this->etiquetas = Doctrine::getTable('Archivo_Etiqueta')->findByTipologiaDocumentalId($request->getParameter('t_id'));
  }

  public function executeSaveCaja(sfWebRequest $request)
  {
      $datos = $request->getParameter('datos');

      $unidad_correlativos = Doctrine::getTable('Archivo_UnidadCorrelativos')->findOneByUnidadId($datos['unidad_correlativo']);

      if(!$unidad_correlativos){
          $unidad_correlativos = new Archivo_UnidadCorrelativos();
          $unidad_correlativos->setUnidadId($datos['unidad_correlativo']);
          $unidad_correlativos->setSecuenciaCaja(1);
          $unidad_correlativos->setSecuenciaExpediente(1);
          $unidad_correlativos->setSecuenciaAnexoDocumento(1);
          $unidad_correlativos->save();
      }

      $unidad = Doctrine::getTable('Organigrama_Unidad')->find($datos['unidad_correlativo']);
      $correlativo_actual = $unidad->getSiglas().'-AC-'.date('Y').'-'.$unidad_correlativos->getSecuenciaCaja();

      $caja = new Archivo_Caja();
      $caja->setEstanteId($datos['estante_id']);
      $caja->setTramo($datos['tramo']);
      $caja->setCorrelativo($correlativo_actual);
      $caja->setUnidadCorrelativosId($unidad_correlativos->getId());
      $caja->save();

      $unidad_correlativos->setSecuenciaCaja($unidad_correlativos->getSecuenciaCaja()+1);
      $unidad_correlativos->save();

      $this->redirect('expediente/listarCajas?e_id='.$datos['estante_id'].'&t_id='.$datos['tramo']);
  }

  public function executeFilter(sfWebRequest $request)
  {
    $this->setPage(1);

    $datos = $request->getParameter('archivo_expediente_filters');

    $datos['cuerpo_documental_id']=$request->getParameter('cuerpo_documental_id');
    $tipologia_documental_id = $request->getParameter('tipologia_documental');

//    if($tipologia_documental_id)
        $datos['tipologia_documental_id']=$tipologia_documental_id;

    $valores_expediente = $request->getParameter('valores_expediente');
    if($valores_expediente){
        $valores = array();
        foreach ($valores_expediente as $key => $value) {
            if(preg_match('/#f/', $key)){
                list($key,$tmp) = explode('#',$key);
                $value = $value['day'].'-'.$value['month'].'-'.$value['year'];
                if($value=='--') $value='';
            }

            if(trim($value)!='')
                $valores[$key] = trim($value);
        }
        $datos['valores_expediente']=$valores;
    }

    $valores_documento = $request->getParameter('valores_documento');
    if($valores_documento){
        $valores = array();
        foreach ($valores_documento as $key => $value) {
            if(preg_match('/#f/', $key)){
                list($key,$tmp) = explode('#',$key);
                $value = $value['day'].'-'.$value['month'].'-'.$value['year'];
                if($value=='--') $value='';
            }

            if(trim($value)!='')
                $valores[$key] = trim($value);
        }
        $datos['valores_documento']=$valores;
    }

    if(count($datos)>0)
        $this->getUser()->setAttribute('expediente_filters', $datos);
    else
        $this->getUser()->getAttributeHolder()->remove('expediente_filters');

    $request->setParameter('archivo_expediente_filters',$datos);

    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());

      $this->redirect('@archivo_expediente');
    }

    $this->filters = $this->configuration->getFilterForm($this->getFilters());

    $this->filters->bind($request->getParameter($this->filters->getName()));
    if ($this->filters->isValid())
    {
      $this->setFilters($this->filters->getValues());

      $this->redirect('@archivo_expediente');
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

    $this->setTemplate('index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('archivo_expediente');
    $unidad_correlativos = Doctrine::getTable('Archivo_UnidadCorrelativos')->findOneByUnidadId($this->getUser()->getAttribute('funcionario_unidad_id'));

    if(!$unidad_correlativos){
        $unidad_correlativos = new Archivo_UnidadCorrelativos();
        $unidad_correlativos->setUnidadId($this->getUser()->getAttribute('funcionario_unidad_id'));
        $unidad_correlativos->setSecuenciaCaja(1);
        $unidad_correlativos->setSecuenciaExpediente(1);
        $unidad_correlativos->setSecuenciaAnexoDocumento(1);
        $unidad_correlativos->save();
    }

    $unidad = Doctrine::getTable('Organigrama_Unidad')->find($this->getUser()->getAttribute('funcionario_unidad_id'));

    $good = false;
    $secuencia_tmp = $unidad_correlativos->getSecuenciaExpediente();

    while($good == false){
        $correlativo_actual = $unidad->getSiglas().'-AE-'.date('Y').'-'.$secuencia_tmp;

        $correlativos_existentes = Doctrine::getTable('Archivo_Expediente')->findByCorrelativo($correlativo_actual);

        if(count($correlativos_existentes)>0){
            $secuencia_tmp++;
        } else {
            $good = true;
        }
    }

    $datos['correlativo']=$correlativo_actual;
    $datos['unidad_correlativos_id']=$unidad_correlativos->getId();

    $request->setParameter('archivo_expediente',$datos);

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'Se ha creado correctamente la serie.' : 'Se ha editado correctamente la serie.';

      try {
        $archivo_expediente = $form->save();

        $unidad_correlativos->setSecuenciaExpediente($secuencia_tmp+1);
        $unidad_correlativos->save();

        $valores = $request->getParameter('valores');

        foreach ($valores as $key => $valor) {
            if(preg_match('/#f/', $key)){
                list($key,$tmp) = explode('#',$key);
                $valor = $valor['day'].'-'.$valor['month'].'-'.$valor['year'];
            }

            if(trim($valor)!=''){
                $expediente_clasificador = new Archivo_ExpedienteClasificador();
                $expediente_clasificador->setClasificadorId($key);
                $expediente_clasificador->setExpedienteId($archivo_expediente->getId());
                $expediente_clasificador->setValor(trim($valor));
                $expediente_clasificador->save();
            }
        }
        
        $cuerpos = $request->getParameter('cuerpos');

        foreach ($cuerpos as $cuerpo) {
            $expediente_cuerpo_documental = new Archivo_ExpedienteCuerpoDocumental();
            $expediente_cuerpo_documental->setExpedienteId($archivo_expediente->getId());
            $expediente_cuerpo_documental->setCuerpoDocumentalId($cuerpo);
            $expediente_cuerpo_documental->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_expediente)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_expediente_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('expediente/adjuntarDocumento?id='.$archivo_expediente->getId());
//        $this->redirect(array('sf_route' => 'archivo_expediente_edit', 'sf_subject' => $archivo_expediente));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

  public function executeExcel(sfWebRequest $request) {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters) {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->excel = $query->execute();
        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();
    }

    public function executeConfiguracion(sfWebRequest $request) {
        $boss= false;
        if($this->context->getUser()->getAttribute('funcionario_gr') == 99) {
            $boss= true;
            $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->context->getUser()->getAttribute('funcionario_id'));
        }
        $funcionario_unidades_admin = Doctrine::getTable('Archivo_FuncionarioUnidad')->adminFuncionarioGrupo($this->context->getUser()->getAttribute('funcionario_id'));

        $cargo_array= array();
        if($boss) {
            foreach($funcionario_unidades_cargo as $unidades_cargo) {
                $cargo_array[]= $unidades_cargo->getUnidadId();
            }
        }

        $admin_array= array();
        for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
            $admin_array[]= $funcionario_unidades_admin[$i][0];
        }

        $nonrepeat= array_merge($cargo_array, $admin_array);

        $funcionario_unidades= array();
        foreach ($nonrepeat as $valor){
            if (!in_array($valor, $funcionario_unidades)){
                $funcionario_unidades[]= $valor;
            }
        }

        $unidades_parametros= Array();
        foreach($funcionario_unidades as $unidades_id) {
            //Carga la configuracion de archivo de la unidad a la que pertenece el 99
            $configuracion_db= Doctrine::getTable('Archivo_Compartir')->findByUnidadId($unidades_id);
            //Extrae los parametros de configuracion yml
            if(count($configuracion_db) > 0)
                $parametros= $configuracion_db[0]['parametros'];
            else
                $parametros= "compartir:
                            privado: true
                            sub_unidades: false
                            unidades: []" ;
            $unidades_parametros[$unidades_id]= sfYaml::load($parametros);
        }

        $this->sf_config= $unidades_parametros;
        $this->unidades = Doctrine::getTable('Organigrama_Unidad')->combounidad(TRUE);
    }

    public function executeSaveConfiguracion(sfWebRequest $request) {
        $config = $request->getParameter('configuracion_archivo');
        $unidad_autorizada_id = $request->getParameter('unidad_autorizada');

        //Compartir expedientes
        $config= $config['compartir'];
        $compartido= false;

        switch ($config['modo']) {
            case 'privado':
                $sf_config['compartir']['privado'] = TRUE;
                $sf_config['compartir']['sub_unidades'] = FALSE;
                $sf_config['compartir']['unidades'] = Array();
                break;
            case 'sub_unidades':
                $sf_config['compartir']['privado'] = FALSE;
                $sf_config['compartir']['sub_unidades'] = TRUE;
                $hijos_all= Array();
                $hijos1= Doctrine::getTable('Organigrama_Unidad')->findByPadreId($unidad_autorizada_id);
                foreach($hijos1 as $j){
                    $hijos_all[]= $j->getId();
                    $hijos2= Doctrine::getTable('Organigrama_Unidad')->findByPadreId($j->getId());
                    foreach($hijos2 as $k) {
                        $hijos_all[]= $k->getId();
                        $hijos3= Doctrine::getTable('Organigrama_Unidad')->findByPadreId($k->getId());
                        foreach($hijos3 as $y) {
                            $hijos_all[]= $y->getId();
                            $hijos4= Doctrine::getTable('Organigrama_Unidad')->findByPadreId($y->getId());
                            foreach($hijos4 as $z){
                                $hijos_all[]= $z->getId();
                            }
                        }
                    }
                }

                if(count(!$hijos_all > 0)) {
                    $this->getUser()->setFlash('error', 'La unidad a configurar no posee otras unidades subordinadas según organigra.');
                    $this->redirect(sfConfig::get('sf_app_archivo_url').'expediente/configuracion');
                }

                $funcionario_ids = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionariosDesdeUnidad($hijos_all);

                $sf_config['compartir']['unidades'] = $hijos_all;
                $compartido= true;
                break;
            case 'unidades':
                $sf_config['compartir']['privado'] = FALSE;
                $sf_config['compartir']['sub_unidades'] = FALSE;
                if($config['unidades']['unico'] != '')
                    $config['unidades'][]= $config['unidades']['unico'];
                unset($config['unidades']['unico']);
                $sf_config['compartir']['unidades'] = $config['unidades'];

                $funcionario_ids = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionariosDesdeUnidad($config['unidades']);
                $compartido= true;
                break;
        }

        $cadena = sfYAML::dump($sf_config);
        $cadena = str_replace("'true'", "true", $cadena);
        $cadena = str_replace("'false'", "false", $cadena);

        //GUARDAR PARAMETROS Y REGISTROS DE UNIDADES Y FUNCIONARIOS AUTORIZADOS

        $parametros_compartir= Doctrine::getTable('Archivo_Compartir')->findOneByUnidadId($unidad_autorizada_id);
        if(count($parametros_compartir) > 1) {
            $parametros_compartir->setUnidadId($unidad_autorizada_id);
            $parametros_compartir->setParametros($cadena);
            $parametros_compartir->save();
        }else {
            $parametros_compartir= new Archivo_Compartir();
            $parametros_compartir->setUnidadId($unidad_autorizada_id);
            $parametros_compartir->setParametros($cadena);
            $parametros_compartir->save();
        }

        $borrar = Doctrine::getTable('Archivo_CompartirFuncionario')->findByCompartirId($parametros_compartir->getId());
        $borrar->delete();

        if($compartido) {
            foreach($funcionario_ids as $val) {
                //obvia funcionarios que no existan en tabla funcionario (caso de desfases en tabla funcionario_cargo)
                $exist= Doctrine::getTable('Funcionarios_Funcionario')->find($val->getFuncionarioId());
                if($exist) {
                    $compartir_funcionario_nuevo_registro= new Archivo_CompartirFuncionario();
                    $compartir_funcionario_nuevo_registro->setCompartirId($parametros_compartir->getId());
                    $compartir_funcionario_nuevo_registro->setFuncionarioId($val->getFuncionarioId());
                    $compartir_funcionario_nuevo_registro->save();
                }

            }
        }

        $this->getUser()->setFlash('notice', ' Configuraciones de modulo Archivo actualizadas.');
        $this->redirect('expediente/configuracion');
    }
}
