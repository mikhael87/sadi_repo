<?php

/**
 * temas actions.
 *
 * @package    siglas-(institucion)
 * @subpackage temas
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class temasActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
  
  public function executeAgregar(sfWebRequest $request)
  {
    $tema = $request->getParameter('t');
    $usuario = Doctrine::getTable('Acceso_Usuario')->find($this->getUser()->getAttribute('usuario_id'));
    $usuario->setTema($tema);
    $usuario->save();
    
    $session_usuario = $this->getUser()->getAttribute('session_usuario');
    $session_usuario['tema'] = $tema;
    $this->getUser()->setAttribute('session_usuario', $session_usuario);
    
    $this->redirect('temas/index');
  }
}
