<?php

require_once dirname(__FILE__).'/../lib/unidad_tipoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/unidad_tipoGeneratorHelper.class.php';

/**
 * unidad_tipo actions.
 *
 * @package    siglas-(institucion)
 * @subpackage unidad_tipo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 0.1. 2011-01-23 18:33:00 livio.lopez $
 */
class unidad_tipoActions extends autoUnidad_tipoActions
{
  public function executeRegresarUnidades(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_organigrama_url').'unidad');
  }
}
