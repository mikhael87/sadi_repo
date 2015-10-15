<?php

require_once dirname(__FILE__).'/../lib/mensajes_grupoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/mensajes_grupoGeneratorHelper.class.php';

/**
 * mensajes_grupo actions.
 *
 * @package    siglas-(institucion)
 * @subpackage mensajes_grupo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mensajes_grupoActions extends autoMensajes_grupoActions
{
  public function executeRegresarMensajes(sfWebRequest $request)
  {
    $this->redirect('mensajes/index');
  }
  
  public function executeParticipante(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->getUser()->setAttribute('mensajes_grupo_id', $id);
           
    $this->redirect('mensajes_participantes/new');
  }
  
  public function executeDeleteParticipante(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    
    $participante = Doctrine_Core::getTable('Public_MensajesParticipantes')->find($id);
    $participante->delete();

    $this->getUser()->setFlash('notice', 'El participante se ha borrado correctamente del grupo.');
    $this->redirect('mensajes_grupo/index');
  }
}
