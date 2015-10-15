<?php

require_once dirname(__FILE__).'/../lib/mantenimientoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/mantenimientoGeneratorHelper.class.php';

/**
 * mantenimiento actions.
 *
 * @package    siglas
 * @subpackage mantenimiento
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mantenimientoActions extends autoMantenimientoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }
    
    public function executeVolverServicios(sfWebRequest $request)
    {
      $this->redirect('mantenimiento/index');
    }
    
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
        $notice = $form->getObject()->isNew() ? 'Servicio agregado con exito.' : 'Servicio actualizado con exito.';

        try {
          $vehiculos_mantenimiento = $form->save();
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

        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $vehiculos_mantenimiento)));

        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $notice.' Ahora puedes agregar otro seguidamente.');

          $this->redirect('@vehiculos_mantenimiento_new');
        }
        else
        {
          $this->getUser()->setFlash('notice', $notice);

          $this->redirect('@vehiculos_mantenimiento');
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'No puedo guardarse el servicio debido a un error.', false);
      }
    }
}
