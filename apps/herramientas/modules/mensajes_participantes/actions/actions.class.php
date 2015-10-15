<?php

require_once dirname(__FILE__).'/../lib/mensajes_participantesGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/mensajes_participantesGeneratorHelper.class.php';

/**
 * mensajes_participantes actions.
 *
 * @package    siglas-(institucion)
 * @subpackage mensajes_participantes
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mensajes_participantesActions extends autoMensajes_participantesActions
{
  public function executeFuncionarioParticipante(sfWebRequest $request)
  { 
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioReceptor(array($request->getParameter('u_id')));
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('mensajes_grupo_id');
    $this->redirect('@public_mensajes_grupo');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $public_mensajes_participantes = $form->save();
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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $public_mensajes_participantes)));

        $this->getUser()->getAttributeHolder()->remove('mensajes_grupo_id');
        $this->getUser()->setFlash('notice', $notice);
        $this->redirect('@public_mensajes_grupo');
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

}
