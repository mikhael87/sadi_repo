<?php

require_once dirname(__FILE__).'/../lib/serie_documentalGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/serie_documentalGeneratorHelper.class.php';

/**
 * serie_documental actions.
 *
 * @package    siglas
 * @subpackage serie_documental
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serie_documentalActions extends autoSerie_documentalActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->getUser()->getAttributeHolder()->remove('serie_documental_id');
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
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    $this->form = $this->configuration->getForm();
    $this->archivo_serie_documental = $this->form->getObject();
  }
  
  public function executeActivarSerieDocumental(sfWebRequest $request)
  {
    $serie_documental = Doctrine::getTable('Archivo_SerieDocumental')->find($request->getParameter('id'));
    $serie_documental->setStatus('A');
    $serie_documental->save();
    
    $this->getUser()->setFlash('notice', 'La serie documental fue borrada, sin embargo sigue almaceda si desea recuperarla en algun momento.');
    $this->redirect('serie_documental/seriesInactivas');
  }
  
  public function executeInactivarSerieDocumental(sfWebRequest $request)
  {
    $serie_documental = Doctrine::getTable('Archivo_SerieDocumental')->find($request->getParameter('id'));
    $serie_documental->setStatus('I');
    $serie_documental->save();
    
    $this->getUser()->setFlash('notice', 'La serie documental fue borrada, sin embargo sigue almaceda si desea recuperarla en algun momento.');
    $this->redirect('serie_documental/index');
  }
  
  public function executeSeriesInactivas(sfWebRequest $request)
  {
  }
  
  public function executeTipologiaDocumental(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('serie_documental_id', $id);
    
    $serie_documental = Doctrine::getTable('Archivo_SerieDocumental')->find($id);
    $this->getUser()->setAttribute('header_ruta', 'Serie Documental: '.$serie_documental->getNombre());
    $this->redirect('tipologia_documental/index');
  }
  

  public function executeTransferirSerie(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('serie_documental_id', $request->getParameter('id'));
    
    $this->almacenamientos = Doctrine::getTable('Archivo_Almacenamiento')->findBySerieDocumentalId($request->getParameter('id'));

    $this->form = new Organigrama_UnidadForm();
  }
  
  public function executeDuplicarSerie(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('serie_documental_id', $request->getParameter('id'));
    
    $this->serie = Doctrine::getTable('Archivo_SerieDocumental')->find($request->getParameter('id'));

    $this->form = new Archivo_SerieDocumentalForm();
  }
  
  public function executeTransferida(sfWebRequest $request)
  {
    $datos = $request->getParameter('organigrama_unidad');

    $serie_documental = Doctrine::getTable('Archivo_SerieDocumental')->find($this->getUser()->getAttribute('serie_documental_id'));
    $serie_documental->setUnidadId($datos['padre_id']);
    $serie_documental->save();
    
    $estante_notice = '';
    if($request->getParameter('transferir_estantes')=='t'){
        $almacenamientos = Doctrine::getTable('Archivo_Almacenamiento')->findBySerieDocumentalId($this->getUser()->getAttribute('serie_documental_id'));
        
        foreach ($almacenamientos as $almacenamiento) {
            $estante = $almacenamiento->getArchivo_Estante();
            $estante->setUnidadDuenaId($datos['padre_id']);
            $estante->save();
        }
        
        $estante_notice = 'en conjunto a los estantes asociados';
    }
    
    echo '<div class="notice">La serie documental fue transferida '.$estante_notice.' a la unidad seleccionada.</div>';

    $this->getUser()->getAttributeHolder()->remove('serie_documental_id');
    exit();
  }
  
  public function executeDuplicada(sfWebRequest $request)
  {
    $datos = $request->getParameter('archivo_serie_documental');

    //RECOLECCION DE INFORMACION DE SERIE A DUPLICAR
    $serie_documental_todupli = Doctrine::getTable('Archivo_SerieDocumental')->find($this->getUser()->getAttribute('serie_documental_id'));
    $cuerpo_documental_todupli = Doctrine::getTable('Archivo_CuerpoDocumental')->findBySerieDocumentalId($this->getUser()->getAttribute('serie_documental_id'));
    $clasificador_todupli = Doctrine::getTable('Archivo_Clasificador')->findBySerieDocumentalId($this->getUser()->getAttribute('serie_documental_id'));
    $tipologia_documental_todupli = Doctrine::getTable('Archivo_TipologiaDocumental')->findBySerieDocumentalId($this->getUser()->getAttribute('serie_documental_id'));
    $tipologias_array= array();
    foreach($tipologia_documental_todupli as $tipologias) {
        $tipologias_array[] = $tipologias->getId();
    }
    $etiqueta_todupli = Doctrine::getTable('Archivo_Etiqueta')->etiquetasPerTipologias($tipologias_array);
    
    $this->getUser()->getAttributeHolder()->remove('serie_documental_id');
    
    //CREACION DE DUPLICADOS
    $serie_documental_duplicated = new Archivo_SerieDocumental();
    $serie_documental_duplicated->setUnidadId($serie_documental_todupli->getUnidadId());
    $serie_documental_duplicated->setNombre(trim($datos['nombre']));
    $serie_documental_duplicated->save();

    $new_serie= $serie_documental_duplicated->getId();

    $join_padres_cuerpos_ids= array();
    foreach($cuerpo_documental_todupli as $cuerpo) {
        $cuerpo_documental_duplicated = new Archivo_CuerpoDocumental();
        //NO SETEA PADRE ID POR QUE ESTE ES DEPENDIENTE DE LA NUEVA ASIGNACION
        //$cuerpo_documental_duplicated->setPadreId($cuerpo->getPadreId());
        $cuerpo_documental_duplicated->setSerieDocumentalId($new_serie);
        $cuerpo_documental_duplicated->setNombre($cuerpo->getNombre());
        $cuerpo_documental_duplicated->setOrden($cuerpo->getOrden());
        $cuerpo_documental_duplicated->save();
        
        $join_padres_cuerpos_ids[$cuerpo->getId()]= $cuerpo_documental_duplicated->getId();
    }
    
    //LLENADO DE PADRES IDS CORRESPONDIENTES PARA CUERPOS DOCUMENTALES
    foreach($cuerpo_documental_todupli as $cuerpo) {
        if(is_int($cuerpo->getPadreId())) {
            $cuerpo_update_padre= Doctrine::getTable('Archivo_CuerpoDocumental')->find($join_padres_cuerpos_ids[$cuerpo->getId()]);
            $cuerpo_update_padre->setPadreId($join_padres_cuerpos_ids[$cuerpo->getPadreId()]);
            $cuerpo_update_padre->save();
        }
    }

    foreach($clasificador_todupli as $clasificador) {
        $clasificador_duplicated = new Archivo_Clasificador();
        $clasificador_duplicated->setSerieDocumentalId($new_serie);
        $clasificador_duplicated->setNombre($clasificador->getNombre());
        $clasificador_duplicated->setTipoDato($clasificador->getTipoDato());
        $clasificador_duplicated->setParametros($clasificador->getParametros());
        $clasificador_duplicated->setVacio($clasificador->getVacio());
        $clasificador_duplicated->setOrden($clasificador->getOrden());
        $clasificador_duplicated->save();
    }

    $join_tipologias_ids= array();
    foreach($tipologia_documental_todupli as $tipologia_documental) {
        $tipologia_documental_duplicated = new Archivo_TipologiaDocumental();
        $tipologia_documental_duplicated->setSerieDocumentalId($new_serie);
        $tipologia_documental_duplicated->setCuerpoDocumentalId($join_padres_cuerpos_ids[$tipologia_documental->getCuerpoDocumentalId()]);
        $tipologia_documental_duplicated->setNombre($tipologia_documental->getNombre());
        $tipologia_documental_duplicated->setOrden($tipologia_documental->getOrden());
        $tipologia_documental_duplicated->save();
        //RELACION ENTRE IDS VIEJOS Y NUEVOS PARA SER RECUPERADOS AL SALVAR ETIQUETAS
        $join_tipologias_ids[$tipologia_documental->getId()]= $tipologia_documental_duplicated->getId();
    }
    
    if(count($join_tipologias_ids)> 0) {
        foreach($etiqueta_todupli as $etiqueta) {
            $etiqueta_duplicated = new Archivo_Etiqueta();
            $etiqueta_duplicated->setTipologiaDocumentalId($join_tipologias_ids[$etiqueta->getTipologiaDocumentalId()]);
            $etiqueta_duplicated->setNombre($etiqueta->getNombre());
            $etiqueta_duplicated->setTipoDato($etiqueta->getTipoDato());
            $etiqueta_duplicated->setParametros($etiqueta->getParametros());
            $etiqueta_duplicated->setVacio($etiqueta->getVacio());
            $etiqueta_duplicated->setOrden($etiqueta->getOrden());
            $etiqueta_duplicated->save();
        }
    }
    
    $this->getUser()->setFlash('notice', 'La Serie Documental fue duplicada con exito.');

    $this->redirect('@archivo_serie_documental');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
        $nuevo=FALSE;
        if($form->getObject()->isNew()) $nuevo = TRUE;
                
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();
      try {
            $archivo_serie_documental = $form->save();

            $clasificadores = $request->getParameter('clasificadores');

            $i=0; $clasificadores_good = array();
            foreach ($clasificadores as $clasificador) {
                
                if(trim($clasificador)!=null){
                    //echo $clasificador; exit();
                    if(preg_match('/%%%/', $clasificador)){
                        list($clasificador_id,$tmp) = explode('%%%', $clasificador);
                        $serie_clasificador = Doctrine::getTable('Archivo_Clasificador')->find($clasificador_id);
                        $serie_clasificador->setOrden($i);
                        $serie_clasificador->save();
                    } else {
                        $clasificador_id='#';
                        list($clasificador_nombre,$clasificador_tipo_dato,$clasificador_vacio,$clasificador_parametros) = explode('&&&', $clasificador);
                    }

                    if($clasificador_id=='#'){
                        $serie_clasificador = new Archivo_Clasificador();
                        $serie_clasificador->setSerieDocumentalId($archivo_serie_documental->getId());
                        $serie_clasificador->setNombre(trim($clasificador_nombre));
                        $serie_clasificador->setTipoDato($clasificador_tipo_dato);
                        $serie_clasificador->setVacio($clasificador_vacio);
                        $serie_clasificador->setParametros($clasificador_parametros);
                        $serie_clasificador->setOrden($i);
                        $serie_clasificador->save();

                        $clasificador_id=$serie_clasificador->getId();
                    }

                    $clasificadores_good[$i]=$clasificador_id;
                    $i++;
                }
            }

            Doctrine::getTable('Archivo_Clasificador')
            ->createQuery()
            ->delete()
            ->where('serie_documental_id = ?', $archivo_serie_documental->getId())
            ->andWhereNotIn('id',$clasificadores_good)
            ->execute();
                
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_serie_documental)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_serie_documental_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);
        
        $this->getUser()->setAttribute('serie_documental_id', $archivo_serie_documental->getId());
        $this->getUser()->setAttribute('header_ruta', 'Serie Documental: '.$archivo_serie_documental->getNombre());

        if($nuevo==TRUE){
            $this->redirect('@archivo_tipologia_documental_new');
        } else {
            $this->redirect('@archivo_serie_documental');
        }
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

}
