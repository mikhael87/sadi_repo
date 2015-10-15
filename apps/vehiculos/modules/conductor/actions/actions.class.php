<?php

require_once dirname(__FILE__).'/../lib/conductorGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/conductorGeneratorHelper.class.php';

/**
 * conductor actions.
 *
 * @package    siglas
 * @subpackage conductor
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class conductorActions extends autoConductorActions
{
    public function executeVolverConductores(sfWebRequest $request)
    {
      $this->redirect('conductor/index');
    }
    
    public function executeFuncionarioUnidad(sfWebRequest $request)
    {
          $this->funcionario_selected = 0;
          $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($request->getParameter('u_id')));
    }
    
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'Nuevo conductor agregado con exito.' : 'El conductor ha sido actualizado con exito.';

        try {
          $vehiculos_conductor = $form->save();
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

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $vehiculos_conductor)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' Puedes agregar otro seguidamente.');

          $this->redirect('@vehiculos_conductor_new');
        }
        else
        {
          $this->getUser()->setFlash('notice', $notice);

          $this->redirect('@vehiculos_conductor');
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ha ocurrido algun error al guardar nuevo registro.', false);
      }
    }
    
    public function executeDelete(sfWebRequest $request)
    {
      $request->checkCSRFProtection();

      $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

      if ($this->getRoute()->getObject()->delete())
      {
        $this->getUser()->setFlash('notice', 'El conductor ha sido eliminado exitosamente.');
      }

      $this->redirect('@vehiculos_conductor');
    }
}
