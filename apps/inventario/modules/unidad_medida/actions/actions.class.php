<?php

require_once dirname(__FILE__).'/../lib/unidad_medidaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/unidad_medidaGeneratorHelper.class.php';

/**
 * unidad_medida actions.
 *
 * @package    siglas
 * @subpackage unidad_medida
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class unidad_medidaActions extends autoUnidad_medidaActions
{
  public function executeRegresarInventario(sfWebRequest $request)
  {
      $this->redirect('inventario_articulo');
  }
}
