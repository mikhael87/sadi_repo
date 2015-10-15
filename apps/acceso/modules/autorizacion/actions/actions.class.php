<?php

require_once dirname(__FILE__).'/../lib/autorizacionGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/autorizacionGeneratorHelper.class.php';

/**
 * autorizacion actions.
 *
 * @package    siglas-(institucion)
 * @subpackage autorizacion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class autorizacionActions extends autoAutorizacionActions
{
  public function executeFuncionario(sfWebRequest $request)
  { 
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
  }
  
  public function executeRegresar(sfWebRequest $request)
  {
      if($this->getUser()->getAttribute('autorizacion') == 'punto_cuenta')
      {
        $this->getUser()->getAttributeHolder()->remove('autorizacion');
        $this->redirect(sfConfig::get('sf_app_seguimiento_url').'punto');
      }
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $acceso_autorizacion_modulo = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $acceso_autorizacion_modulo)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@acceso_autorizacion_modulo_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect(array('sf_route' => 'acceso_autorizacion_modulo'));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
