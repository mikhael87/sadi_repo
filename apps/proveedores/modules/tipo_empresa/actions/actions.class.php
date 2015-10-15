<?php

require_once dirname(__FILE__).'/../lib/tipo_empresaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/tipo_empresaGeneratorHelper.class.php';

/**
 * tipo_empresa actions.
 *
 * @package    siglas
 * @subpackage tipo_empresa
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tipo_empresaActions extends autoTipo_empresaActions
{
  public function executeRegresarProveedores(sfWebRequest $request)
  {
      $this->redirect('proveedores_proveedor');
  }
}
