<?php

require_once dirname(__FILE__).'/../lib/vehiculo_tipo_usoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/vehiculo_tipo_usoGeneratorHelper.class.php';

/**
 * vehiculo_tipo_uso actions.
 *
 * @package    siglas
 * @subpackage vehiculo_tipo_uso
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vehiculo_tipo_usoActions extends autoVehiculo_tipo_usoActions
{
    public function executeVolver(sfWebRequest $request)
    {
      $this->redirect('vehiculo/index');
    }
}
