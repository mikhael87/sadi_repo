<?php

require_once dirname(__FILE__).'/../lib/tipologia_documentalGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/tipologia_documentalGeneratorHelper.class.php';

/**
 * tipologia_documental actions.
 *
 * @package    siglas
 * @subpackage tipologia_documental
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tipologia_documentalActions extends autoTipologia_documentalActions
{
  public function executeDelete(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $documentos = Doctrine::getTable('Archivo_Documento')->findByTipologiaDocumentalId($id);
    
    if(count($documentos)<1){
        Doctrine::getTable('Archivo_Etiqueta')
        ->createQuery()
        ->delete()
        ->where('tipologia_documental_id = ?', $id)
        ->execute();
            
        $request->checkCSRFProtection();

        $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

        if ($this->getRoute()->getObject()->delete())
        {
          $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
        }
    } else {
        $this->getUser()->setFlash('error', 'No puede borrar esta tipologia debido a que ya registro '.count($documentos).' documentos con la misma.');
    }
    $this->redirect('@archivo_tipologia_documental');
  }
    
  public function executeRegresarSeries(sfWebRequest $request)
  {
      $this->getUser()->getAttributeHolder()->remove('serie_documental_id');
      $this->redirect(sfConfig::get('sf_app_archivo_url').'serie_documental');
  }
  
  public function executeOrdenar(sfWebRequest $request)
  {
    $tipologias = $request->getParameter('tipologias');
    
      try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $i=1;
            foreach ($tipologias as $tipologia_id) {
                $tipologia = Doctrine::getTable('Archivo_TipologiaDocumental')->find($tipologia_id);
                $tipologia->setOrden($i);
                $tipologia->save();
                $i++;
            }
            
            $conn->commit();
        
      } catch (Doctrine_Validator_Exception $e) {
          
        $conn->rollBack();
      }

    exit();
  }
  
  public function executeCuerpos(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_archivo_url').'cuerpo_documental');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'Se ha creado correctamente la tipología.' : 'Se ha editado correctamente la tipología.';

      try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $archivo_tipologia_documental = $form->save();

            $detalles = $request->getParameter('detalles');

            if($detalles['cuerpo']!='')
                $archivo_tipologia_documental->setCuerpoDocumentalId($detalles['cuerpo']);
                $archivo_tipologia_documental->save();
                
            $etiquetas = $detalles['etiquetas'];

            $i=0; $etiquetadores_good = array();
            foreach ($etiquetas as $etiquetador) {
                
                if(trim($etiquetador)!=null){
                    //echo $etiquetador; exit();
                    if(preg_match('/%%%/', $etiquetador)){
                        list($etiquetador_id,$tmp) = explode('%%%', $etiquetador);
                        $documento_etiqueta = Doctrine::getTable('Archivo_Etiqueta')->find($etiquetador_id);
                        $documento_etiqueta->setOrden($i);
                        $documento_etiqueta->save();
                    } else {
                        $etiquetador_id='#';
                        list($etiquetador_nombre,$etiquetador_tipo_dato,$etiquetador_vacio,$etiquetador_parametros) = explode('&&&', $etiquetador);
                    }

                    if($etiquetador_id=='#'){
                        $documento_etiqueta = new Archivo_Etiqueta();
                        $documento_etiqueta->setTipologiaDocumentalId($archivo_tipologia_documental->getId());
                        $documento_etiqueta->setNombre(trim($etiquetador_nombre));
                        $documento_etiqueta->setTipoDato($etiquetador_tipo_dato);
                        $documento_etiqueta->setVacio($etiquetador_vacio);
                        $documento_etiqueta->setParametros($etiquetador_parametros);
                        $documento_etiqueta->setOrden($i);
                        $documento_etiqueta->save();

                        $etiquetador_id=$documento_etiqueta->getId();
                    }

                    $etiquetadores_good[$i]=$etiquetador_id;
                    $i++;
                }
            }

            Doctrine::getTable('Archivo_Etiqueta')
            ->createQuery()
            ->delete()
            ->where('tipologia_documental_id = ?', $archivo_tipologia_documental->getId())
            ->andWhereNotIn('id',$etiquetadores_good)
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_tipologia_documental)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_tipologia_documental_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

//        $this->redirect(array('sf_route' => 'archivo_tipologia_documental_edit', 'sf_subject' => $archivo_tipologia_documental));
        $this->redirect('@archivo_tipologia_documental');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
