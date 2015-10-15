<?php

require_once dirname(__FILE__).'/../lib/estanteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/estanteGeneratorHelper.class.php';

/**
 * estante actions.
 *
 * @package    siglas
 * @subpackage estante
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class estanteActions extends autoEstanteActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
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
  
  public function executeAlmacenamiento(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('estante_id', $id);
    $this->redirect('almacenamiento/index');
  }
  
  public function executeSaveEstanteModelo(sfWebRequest $request)
  { 
        try {
            $conn = Doctrine_Manager::connection();
            $conn->beginTransaction();
            
            $estante_modelo = new Archivo_EstanteModelo();
            $estante_modelo->setNombre($request->getParameter('nombre'));
            $estante_modelo->save();
            
            $this->getUser()->setFlash('notice_cargo', 'Modelo de estante agregado con éxito.');
            $conn->commit();
        } catch(Exception $e){
            $conn->rollBack();
            $this->getUser()->setFlash('error_cargo', 'ha ocurrido un error inesperado al guardar, por favor intente nuevamente. si el problema persiste comuniquelo al departamento de tecnología.');
        }
        
        $this->setTemplate('listarEstanteModelo');

  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $archivo_estante = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_estante)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_estante_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

//        $this->redirect(array('sf_route' => 'archivo_estante_edit', 'sf_subject' => $archivo_estante));
        $this->redirect('@archivo_estante');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
