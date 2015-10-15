<?php

require_once dirname(__FILE__).'/../lib/articulo_ingresoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/articulo_ingresoGeneratorHelper.class.php';

/**
 * articulo_ingreso actions.
 *
 * @package    siglas
 * @subpackage articulo_ingreso
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class articulo_ingresoActions extends autoArticulo_ingresoActions
{
  public function executeRegresarInventario(sfWebRequest $request)
  {
      $this->redirect('inventario_articulo');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $articulos = $request->getParameter('articulos');
        
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $conn = Doctrine_Manager::connection();
      try {
        $conn->beginTransaction();
        $inventario_articulo_ingreso = $form->save();
        
        foreach ($articulos as $articulo) {
            list($articulo_id,$cantidad) = explode('#',$articulo);
            
            $inventario = new Inventario_Inventario();
            $inventario->setAlmacenId(1);
            $inventario->setArticuloId($articulo_id);
            $inventario->setArticuloIngresoId($inventario_articulo_ingreso->getId());
            $inventario->setCantidadInicial($cantidad);
            $inventario->setCantidadActual($cantidad);
            $inventario->save();
        }

        $conn->commit();

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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $inventario_articulo_ingreso)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@inventario_articulo_ingreso_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'inventario_articulo_ingreso', 'sf_subject' => $inventario_articulo_ingreso));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
