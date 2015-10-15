<?php

require_once dirname(__FILE__).'/../lib/almacenamientoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/almacenamientoGeneratorHelper.class.php';

/**
 * almacenamiento actions.
 *
 * @package    siglas
 * @subpackage almacenamiento
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class almacenamientoActions extends autoAlmacenamientoActions
{
  public function executeRegresarEstante(sfWebRequest $request)
  {
      $this->getUser()->getAttributeHolder()->remove('estante_id');
      $this->redirect(sfConfig::get('sf_app_archivo_url').'estante');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $datos = $request->getParameter('archivo_almacenamiento');
    $tramos = $request->getParameter('tramos');
    
    $tramos_good='';
    foreach ($tramos as $key => $value)
        $tramos_good .= $key.',';
    $tramos_good .= '$%&';
    $tramos_good = str_replace(',$%&', '', $tramos_good);
                
    $datos['tramos']=$tramos_good;
    
    $request->setParameter('archivo_almacenamiento',$datos);
    
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $archivo_almacenamiento = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $archivo_almacenamiento)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@archivo_almacenamiento_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

//        $this->redirect(array('sf_route' => 'archivo_almacenamiento_edit', 'sf_subject' => $archivo_almacenamiento));
        $this->redirect('@archivo_almacenamiento');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
}
