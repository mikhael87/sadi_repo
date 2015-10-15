<?php

require_once dirname(__FILE__).'/../lib/correlativosGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/correlativosGeneratorHelper.class.php';

/**
 * correlativos actions.
 *
 * @package    siglas-(institucion)
 * @subpackage correlativos
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class correlativosActions extends autoCorrelativosActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('formatos_correlativo');
    
    if($request->getParameter('id'))
        $this->getUser()->setAttribute('pae_funcionario_unidad_id', $request->getParameter('id'));
    
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
  
  public function executeCorrelativosIni(sfWebRequest $request)
  {
        $this->getUser()->getAttributeHolder()->remove('pae_funcionario_unidad_id');
        $this->redirect(sfConfig::get('sf_app_correspondencia_url').'correlativos/index');
  }
  
  public function executeCorrelativosPorDefecto(sfWebRequest $request)
  {
      $nomenclador= $request->getParameter('nomenclador');
      $formatos_selected= explode('#', $request->getParameter('formatos'));
      if(count($formatos_selected) > 0) {
          
      }
      
      if(!$this->context->getUser()->getAttribute('pae_funcionario_unidad_id')) {
            $boss= false;
            $cargo_array= array();
            $funcionario_unidades= array();
            if($this->getUser()->getAttribute('funcionario_gr') == 99) {
                $boss= true;
                $funcionario_unidades_cargo = Doctrine::getTable('Funcionarios_FuncionarioCargo')->unidadDelCargoDelFuncionario($this->getUser()->getAttribute('funcionario_id'));

                foreach($funcionario_unidades_cargo as $unidades_cargo) {
                    $cargo_array[]= $unidades_cargo->getUnidadId();
                }
            }

            $funcionario_unidades_admin = Doctrine::getTable('Correspondencia_FuncionarioUnidad')->adminFuncionarioGrupo($this->getUser()->getAttribute('funcionario_id'));

            $admin_array= array();
            for($i= 0; $i< count($funcionario_unidades_admin); $i++) {
                $admin_array[]= $funcionario_unidades_admin[$i][0];
            }

            $nonrepeat= array_merge($cargo_array, $admin_array);

            foreach ($nonrepeat as $valor){
                if (!in_array($valor, $funcionario_unidades)){
                    $funcionario_unidades[]= $valor;
                }
            }
      }else {
          $funcionario_unidades[] = $this->context->getUser()->getAttribute('pae_funcionario_unidad_id');
      }
      
      foreach( $funcionario_unidades as $unidades ) {
          
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($unidades);
        $unidad_tipo = $unidad->getUnidadTipoId();
        $unidad_id = $unidad->getId();
        
        $formatos = Doctrine::getTable('Correspondencia_TipoFormato')->findByStatusAndPrivadoAndTipo('A','N','C');  
          
        $formatos_legitimos = array();
        foreach ($formatos as $formato) {
          if($formato->getParametros() != null){
                $parametros = sfYaml::load($formato->getParametros());        

                if($parametros['emisores']['unidades']['todas']=='true') { 
                    $formatos_legitimos[]=$formato->getId();
                } else if($parametros['emisores']['unidades']['tipos']!='false') {
                    foreach ($parametros['emisores']['unidades']['tipos'] as $unidad_tipo_parametro) {
                        if($unidad_tipo_parametro == $unidad_tipo)
                            $formatos_legitimos[]=$formato->getId();
                    }
                } else if($parametros['emisores']['unidades']['especificas']!='false') {
                    foreach ($parametros['emisores']['unidades']['especificas'] as $unidad_especifica_parametro) {
                        if($unidad_especifica_parametro == $unidad_id)
                            $formatos_legitimos[]=$formato->getId();
                    }
                }
          }
        }

        if($this->getUser()->getAttribute('formatos_correlativo'))
        {
            $formatos_correlativo = $this->getUser()->getAttribute('formatos_correlativo');
            foreach ($formatos_correlativo as $formato_correlativo) 
                $formatos_asignados[$formato_correlativo->getTipoFormatoId()]='t';

            $formatos_unidad = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->filtralPorUnidad($unidad_id);
            foreach ($formatos_unidad as $formato_unidad) 
            {
                if(!isset($formatos_asignados[$formato_unidad->getTipoFormatoId()])) {
                    $key= array_search($formato_unidad->getTipoFormatoId(), $formatos_legitimos);
                    unset($formatos_legitimos[$key]);
                }
            }
        } else {
            $formatos_unidad = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->filtralPorUnidad($unidad_id);
            foreach ($formatos_unidad as $formato_unidad)  {
                $key= array_search($formato_unidad->getTipoFormatoId(), $formatos_legitimos);
                unset($formatos_legitimos[$key]);
            }
        }
        
        //SI NO HAY FORMATOS LEGITIMOS NO HARA NADA
        if(count($formatos_legitimos)>0) {
            $formatos_to_add= array();
            foreach($formatos_selected as $selected) {
                list($formato_id, $siglas, $sec) = explode('|', $selected);

                $count= 0;
                if(in_array($formato_id, $formatos_legitimos)) {
                    $formatos_to_add[$count]['id']= $formato_id;
                    $formatos_to_add[$count]['siglas']= trim($siglas);
                    $formatos_to_add[$count]['sec']= $sec;
                    $count++;
                }
            }
            echo '<pre>';
            print_r($formatos_to_add);
            exit;
        }
        
        
        foreach($formatos_to_add as $formato_add) {
            
            if($formato_add['siglas']=='' && preg_match('/Letra/', $nomenclador)) 
                $error=TRUE;

            if (!preg_match('/Letra/', $nomenclador) && $formato_add['siglas']!='') 
                $error=TRUE;
        
            if(!$error)
            {
                $conn = Doctrine_Manager::connection();

                try {
                  $conn->beginTransaction();         

                  // SALVAMOS EL FORMULARIO DEL CORRELATIVO ACTUAL
                  $correspondencia_unidad_correlativo = new Correspondencia_UnidadCorrelativo();
                  $correspondencia_unidad_correlativo->setUnidadId($unidad_id);
                  $correspondencia_unidad_correlativo->setNomenclador($nomenclador);
                  $correspondencia_unidad_correlativo->setSecuencia($formato_add['secuencia']);
                  $correspondencia_unidad_correlativo->setStatus('A');
                  $correspondencia_unidad_correlativo->setLetra($formato_add['siglas']);
                  $correspondencia_unidad_correlativo->setTipo('E');
                  $correspondencia_unidad_correlativo->setCompartido(FALSE);
                  $correspondencia_unidad_correlativo->save();

                  // BUSCAMOS LOS DATOS DE LA UNIDAD DUENA DEL CORRELATIVO PARA USAR LAS SIGLAS
                  $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correspondencia_unidad_correlativo->getUnidadId());

                  // PREARMAMOS VALORES DEL NOMENCLADOR CON DATOS REALES SIN TOMAR EN CUENTA LA SECUENCIA
                  $nomenclatura = $correspondencia_unidad_correlativo->getNomenclador();
                  $nomenclatura = str_replace("Siglas", $unidad->getSiglas(), $nomenclatura);
                  $nomenclatura = str_replace("Letra", $correspondencia_unidad_correlativo->getLetra(), $nomenclatura);
                  $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
                  $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
                  $nomenclatura = str_replace("Día", date('d'), $nomenclatura);

                  // ELIMINAMOS TODOS LOS CORRELATIVOS SIMULADOS DEL CORRELATIVO GUARDADO
                  $delete_formatos = Doctrine::getTable('Correspondencia_Correspondencia')
                    ->createQuery()
                    ->delete()
                    ->where('unidad_correlativo_id = ?', $correspondencia_unidad_correlativo->getId())
                    ->andWhere('status = ?', 'S')
                    ->execute();

                  // RECORREMOS DESDE 1 HASTA EL VALOR INICIAL GUARDADO PARA COMPROBAR CORRELATIVOS ACTIVOS ANTERIORES
                  for($i=1;$i<$correspondencia_unidad_correlativo->getSecuencia();$i++){

                      // NOMENCLADOR PREARMADO LE SUSTITUIMOS LA SECUENCIA $i
                      $nomenclatura_ok = str_replace("Secuencia", $i, $nomenclatura);

                      $correlativo = $correspondencia_unidad_correlativo->getSecuencia();

                      // BUSCAMOS EL NOMENCLADOR PREARMADO DE LA SECUENCIA $I
                      $verificacion_simulacion = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura_ok);

                      // SI NO EXISTE UN CORRELATIVO ACTIVO DE LA SECUENCIA PROCEDEMOS A CREAR UN CORRELATIVO SIMULADO
                      if(!$verificacion_simulacion) {
                          $correlativo_simulado = new Correspondencia_Correspondencia();
                          $correlativo_simulado->setNCorrespondenciaEmisor($nomenclatura_ok);
                          $correlativo_simulado->setUnidadCorrelativoId($correspondencia_unidad_correlativo->getId());
                          $correlativo_simulado->setPrivado('S');
                          $correlativo_simulado->setStatus('S');
                          $correlativo_simulado->save();
                      }
                  }

                  // ELIMINAMOS LA RELACION DEL NOMENCLADOR CREADO CON LOS FORMATOS
                  $delete_formatos = Doctrine::getTable('Correspondencia_CorrelativosFormatos')
                    ->createQuery()
                    ->delete()
                    ->where('unidad_correlativo_id = ?', $correspondencia_unidad_correlativo->getId())
                    ->execute();

                  // CREAMOS DE CERO LA NUEVA RELACION CON LOS FORMATOS ASOCIADOS AL CORRELATIVO
                  foreach ($formatos as $formato => $value) {
                      $correlativos_formatos = new Correspondencia_CorrelativosFormatos();
                      $correlativos_formatos->setUnidadCorrelativoId($correspondencia_unidad_correlativo->getId());
                      $correlativos_formatos->setTipoFormatoId($formato);
                      $correlativos_formatos->save();
                  }

                  $conn->commit();
                } catch (Doctrine_Validator_Exception $e) {
                  $conn->rollBack();
                }
            }
        }
      }
exit;
      $this->redirect('grupos/index');
  }
  
  public function executeFormatos(sfWebRequest $request)
  {
    if($request->getParameter('unidad_id') != ''){  
        $unidad = Doctrine::getTable('Organigrama_Unidad')->find($request->getParameter('unidad_id'));
        $unidad_tipo = $unidad->getUnidadTipoId();
        $unidad_id = $unidad->getId();

        $formatos = Doctrine::getTable('Correspondencia_TipoFormato')->findByStatusAndPrivadoAndTipo('A','N','C');

        $formatos_legitimos = array();
        foreach ($formatos as $formato) {
          if($formato->getParametros() != null){
                $parametros = sfYaml::load($formato->getParametros());        

                if($parametros['emisores']['unidades']['todas']=='true') { 
                    $formatos_legitimos[$formato->getId()]=$formato->getNombre();
                } else if($parametros['emisores']['unidades']['tipos']!='false') {
                    foreach ($parametros['emisores']['unidades']['tipos'] as $unidad_tipo_parametro) {
                        if($unidad_tipo_parametro == $unidad_tipo)
                            $formatos_legitimos[$formato->getId()]=$formato->getNombre();
                    }
                } else if($parametros['emisores']['unidades']['especificas']!='false') {
                    foreach ($parametros['emisores']['unidades']['especificas'] as $unidad_especifica_parametro) {
                        if($unidad_especifica_parametro == $unidad_id)
                            $formatos_legitimos[$formato->getId()]=$formato->getNombre();
                    }
                }
          }
        }

        if($this->getUser()->getAttribute('formatos_correlativo'))
        {
            $formatos_correlativo = $this->getUser()->getAttribute('formatos_correlativo');
            foreach ($formatos_correlativo as $formato_correlativo) 
                $formatos_asignados[$formato_correlativo->getTipoFormatoId()]='t';

            $formatos_unidad = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->filtralPorUnidad($unidad_id);
            foreach ($formatos_unidad as $formato_unidad) 
            {
                if(!isset($formatos_asignados[$formato_unidad->getTipoFormatoId()]))
                    unset($formatos_legitimos[$formato_unidad->getTipoFormatoId()]);
            }
            
            $this->formatos_asignados = $formatos_asignados;
        } else {
            $formatos_unidad = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->filtralPorUnidad($unidad_id);
            foreach ($formatos_unidad as $formato_unidad) 
                unset($formatos_legitimos[$formato_unidad->getTipoFormatoId()]);
        }

        if(count($formatos_legitimos)>0) {
            $this->formatos_legitimos = $formatos_legitimos;
        } else {
            echo '<div class="error">Ya se han definido todos los correlativos para esta Unidad.</div>
                  <script>$(".sf_admin_action_save").hide(); $(".sf_admin_action_save_and_add").hide();</script>';
            exit();
        }
    } else {
        echo '<div class="notice">Seleccione la unidad a la que le asignara el correlativo.</div>
              <script>$(".sf_admin_action_save").hide(); $(".sf_admin_action_save_and_add").hide();</script>';
        exit();
    }
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->correspondencia_unidad_correlativo = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->correspondencia_unidad_correlativo);
    
    $formatos_correlativo = Doctrine::getTable('Correspondencia_CorrelativosFormatos')->findByUnidadCorrelativoId($this->correspondencia_unidad_correlativo->getId());
    $this->getUser()->setAttribute('formatos_correlativo', $formatos_correlativo);
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $mensaje=null;  
    $formatos = $request->getParameter('formato');
    $datos = $request->getParameter('correspondencia_unidad_correlativo');
    $datos['letra']=trim($datos['letra']);
    if($datos['letra']=='' && preg_match('/Letra/', $datos['nomenclador'])) 
        $mensaje='Si selecciona un nomenclador con "Letra" de identificación, debe ingresar la letra que identificará el correlativo.';
        //$datos['nomenclador'] = str_replace("-Letra-", "-", $datos['nomenclador']);
    
    if (!preg_match('/Letra/', $datos['nomenclador']) && $datos['letra']!='') 
        $mensaje='Al ingresar una letra como identificador del correlativo, debe seleccionar un nomenclador que contenga la opción "Letra".';
        //$datos['letra']='';
            
    $request->setParameter('correspondencia_unidad_correlativo',$datos);
    
    $validar_correlativo = Doctrine::getTable('Correspondencia_UnidadCorrelativo')->findOneByUnidadIdAndNomencladorAndLetra($datos['unidad_id'],$datos['nomenclador'],$datos['letra']);

    if($validar_correlativo){
        if($validar_correlativo->getId() != $datos['id'])
            $mensaje='El nomenclador que ha seleccionado ya esta siendo utilizado, por favor seleccione otro nomenclador o letra.';
    }
    
    if($mensaje==null)
    {
        if(count($formatos)>0)
        {
            $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
            if ($form->isValid())
            {
              $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

              $conn = Doctrine_Manager::connection();

              try {
                $conn->beginTransaction();         
                
                // SALVAMOS EL FORMULARIO DEL CORRELATIVO ACTUAL
                $correspondencia_unidad_correlativo = $form->save();
                
                // BUSCAMOS LOS DATOS DE LA UNIDAD DUENA DEL CORRELATIVO PARA USAR LAS SIGLAS
                $unidad = Doctrine::getTable('Organigrama_Unidad')->find($correspondencia_unidad_correlativo->getUnidadId());

                // PREARMAMOS VALORES DEL NOMENCLADOR CON DATOS REALES SIN TOMAR EN CUENTA LA SECUENCIA
                $nomenclatura = $correspondencia_unidad_correlativo->getNomenclador();
                $nomenclatura = str_replace("Siglas", $unidad->getSiglas(), $nomenclatura);
                $nomenclatura = str_replace("Codigo", $unidad->getCodigoUnidad(), $nomenclatura);
                $nomenclatura = str_replace("Letra", $correspondencia_unidad_correlativo->getLetra(), $nomenclatura);
                $nomenclatura = str_replace("Año", date('Y'), $nomenclatura);
                $nomenclatura = str_replace("Mes", date('m'), $nomenclatura);
                $nomenclatura = str_replace("Día", date('d'), $nomenclatura);

                // ELIMINAMOS TODOS LOS CORRELATIVOS SIMULADOS DEL CORRELATIVO GUARDADO
                $delete_formatos = Doctrine::getTable('Correspondencia_Correspondencia')
                  ->createQuery()
                  ->delete()
                  ->where('unidad_correlativo_id = ?', $correspondencia_unidad_correlativo->getId())
                  ->andWhere('status = ?', 'S')
                  ->execute();
 
                // RECORREMOS DESDE 1 HASTA EL VALOR INICIAL GUARDADO PARA COMPROBAR CORRELATIVOS ACTIVOS ANTERIORES
                for($i=1;$i<$correspondencia_unidad_correlativo->getSecuencia();$i++){

                    // NOMENCLADOR PREARMADO LE SUSTITUIMOS LA SECUENCIA $i
                    $nomenclatura_ok = str_replace("Secuencia", $i, $nomenclatura);

                    $correlativo = $correspondencia_unidad_correlativo->getSecuencia();

                    // BUSCAMOS EL NOMENCLADOR PREARMADO DE LA SECUENCIA $I
                    $verificacion_simulacion = Doctrine::getTable('Correspondencia_Correspondencia')->findOneByNCorrespondenciaEmisor($nomenclatura_ok);

                    // SI NO EXISTE UN CORRELATIVO ACTIVO DE LA SECUENCIA PROCEDEMOS A CREAR UN CORRELATIVO SIMULADO
                    if(!$verificacion_simulacion) {
                        $correlativo_simulado = new Correspondencia_Correspondencia();
                        $correlativo_simulado->setNCorrespondenciaEmisor($nomenclatura_ok);
                        $correlativo_simulado->setUnidadCorrelativoId($correspondencia_unidad_correlativo->getId());
                        $correlativo_simulado->setPrivado('S');
                        $correlativo_simulado->setStatus('S');
                        $correlativo_simulado->save();
                    }
                }
                
                // ELIMINAMOS LA RELACION DEL NOMENCLADOR CREADO CON LOS FORMATOS
                $delete_formatos = Doctrine::getTable('Correspondencia_CorrelativosFormatos')
                  ->createQuery()
                  ->delete()
                  ->where('unidad_correlativo_id = ?', $correspondencia_unidad_correlativo->getId())
                  ->execute();

                // CREAMOS DE CERO LA NUEVA RELACION CON LOS FORMATOS ASOCIADOS AL CORRELATIVO
                foreach ($formatos as $formato => $value) {
                    $correlativos_formatos = new Correspondencia_CorrelativosFormatos();
                    $correlativos_formatos->setUnidadCorrelativoId($correspondencia_unidad_correlativo->getId());
                    $correlativos_formatos->setTipoFormatoId($formato);
                    $correlativos_formatos->save();
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

              $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $correspondencia_unidad_correlativo)));

              if ($request->hasParameter('_save_and_add'))
              {
                $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

                $this->redirect('@correspondencia_unidad_correlativo_new');
              }
              else
              {
                $this->getUser()->setFlash('notice', $notice);

                $this->redirect(array('sf_route' => 'correspondencia_unidad_correlativo_edit', 'sf_subject' => $correspondencia_unidad_correlativo));
              }
            }
            else
            {
              $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
            }
        }
        else
        {
          $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);  
          $this->getUser()->setFlash('sin_formatos', 'Debe seleccionar al menos un formato para crear el correlativo.', false);
        }
    }
    else
    {
      $this->getUser()->setFlash('error', $mensaje, false);  
      
      if(count($formatos)==0)
        $this->getUser()->setFlash('sin_formatos', 'Debe seleccionar al menos un formato para crear el correlativo.', false);
    }
  }
}