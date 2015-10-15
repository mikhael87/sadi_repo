<?php

require_once dirname(__FILE__).'/../lib/vehiculo_tipoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vehiculo_tipoGeneratorHelper.class.php';

/**
 * vehiculo_tipo actions.
 *
 * @package    siglas
 * @subpackage vehiculo_tipo
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vehiculo_tipoActions extends autoVehiculo_tipoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }
}
