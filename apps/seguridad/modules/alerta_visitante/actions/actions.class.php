<?php

require_once dirname(__FILE__).'/../lib/alerta_visitanteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/alerta_visitanteGeneratorHelper.class.php';

/**
 * alerta_visitante actions.
 *
 * @package    siglas
 * @subpackage alerta_visitante
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class alerta_visitanteActions extends autoAlerta_visitanteActions
{
  public function executeRegresar(sfWebRequest $request)
  {
    $this->redirect(sfConfig::get('sf_app_seguridad_url').'ingresa');
  }
  
    public function executeAnularAlerta(sfWebRequest $request) {
        $id = $request->getParameter('id');
        $alerta_visitante = Doctrine::getTable('Seguridad_AlertaVisitante')->find($id);
        $alerta_visitante->setStatus('I');
        $alerta_visitante->save();
        
        $this->redirect('alerta_visitante/index');
    }
}
