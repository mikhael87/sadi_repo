<?php

require_once dirname(__FILE__).'/../lib/proveedorGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/proveedorGeneratorHelper.class.php';

/**
 * proveedor actions.
 *
 * @package    siglas
 * @subpackage proveedor
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class proveedorActions extends autoProveedorActions
{
  public function executeTiposEmpresa(sfWebRequest $request)
  {
    $this->redirect('proveedores_tipo_empresa');
  }
}
