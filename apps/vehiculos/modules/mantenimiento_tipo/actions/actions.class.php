<?php

require_once dirname(__FILE__).'/../lib/mantenimiento_tipoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/mantenimiento_tipoGeneratorHelper.class.php';

/**
 * mantenimiento_tipo actions.
 *
 * @package    siglas
 * @subpackage mantenimiento_tipo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mantenimiento_tipoActions extends autoMantenimiento_tipoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }
    
    public function executeVolverTipoServicio(sfWebRequest $request)
    {
      $this->redirect('mantenimiento_tipo/index');
    }
    
    protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'Nuevo servicio agregado con exito.' : 'El servicio fue modificado con exito.';

      try {
        $vehiculos_mantenimiento_tipo = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $vehiculos_mantenimiento_tipo)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' Puedes agregar otro seguidamente.');

        $this->redirect('@vehiculos_mantenimiento_tipo_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $this->redirect('@vehiculos_mantenimiento_tipo');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ha ocurrido algun error al guardar nuevo registro.', false);
    }
  }
}
