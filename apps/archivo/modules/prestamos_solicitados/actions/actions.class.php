<?php

require_once dirname(__FILE__).'/../lib/prestamos_solicitadosGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/prestamos_solicitadosGeneratorHelper.class.php';

/**
 * prestamos_solicitados actions.
 *
 * @package    siglas
 * @subpackage prestamos_solicitados
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class prestamos_solicitadosActions extends autoPrestamos_solicitadosActions
{
  public function executeRegresarExpediente(sfWebRequest $request)
  {
      $this->redirect(sfConfig::get('sf_app_archivo_url').'expediente');
  }  
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('header_ruta');
    // sorting
    if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
  }  
  
  public function executeExcel(sfWebRequest $request) {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters) {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->excel = $query->execute();
        $this->setLayout(false);
        $this->getResponse()->clearHttpHeaders();
    }
}
