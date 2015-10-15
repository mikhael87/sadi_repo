<?php

require_once dirname(__FILE__).'/../lib/conductor_vehiculoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/conductor_vehiculoGeneratorHelper.class.php';

/**
 * conductor_vehiculo actions.
 *
 * @package    siglas
 * @subpackage conductor_vehiculo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class conductor_vehiculoActions extends autoConductor_vehiculoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }
}
