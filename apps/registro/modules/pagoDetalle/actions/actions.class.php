<?php

require_once dirname(__FILE__).'/../lib/pagoDetalleGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pagoDetalleGeneratorHelper.class.php';

/**
 * pagoDetalle actions.
 *
 * @package    siglas
 * @subpackage pagoDetalle
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pagoDetalleActions extends autoPagoDetalleActions
{
    public function executeIndex(sfWebRequest $request)
    {
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

      $this->registro_id= $request->getParameter('id');
      $this->pager = $this->getPager();
      $this->sort = $this->getSort();
    }
    
    public function executeVolver(sfWebRequest $request)
    {
        $this->redirect('@empresas_empresa_pago');
    }
    
    public function executeAnular(sfWebRequest $request)
    {
      $id = $request->getParameter('id');

      $cargo = Doctrine::getTable('Operaciones_RegistroPago')->find($id);
      $cargo->setStatus('I');
      $cargo->save();

      $this->getUser()->setFlash('notice', 'El pago ha sido anulado con exito".');
      $this->redirect('@operaciones_registro_pago');
    }
    
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
      $datos = $request->getParameter('operaciones_registro_pago');
      
      $datos['registro_id']= sfContext::getInstance()->getUser()->getAttribute('pae_registro_id');
      
      $request->setParameter('operaciones_registro_pago',$datos);
      
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

        try {
          $operaciones_registro_pago = $form->save();
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

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $operaciones_registro_pago)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

          $this->redirect('@operaciones_registro_pago_new');
        }
        else
        {
          $this->getUser()->setFlash('notice', $notice);

          $this->redirect('@operaciones_registro_pago');
//          $this->redirect(array('sf_route' => 'operaciones_registro_pago_edit', 'sf_subject' => $operaciones_registro_pago));
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      }
    }
}
